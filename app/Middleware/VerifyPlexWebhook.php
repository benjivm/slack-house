<?php

namespace App\Middleware;

class VerifyPlexWebhook
{
    private $logger;

    private $config;

    private $validator;

    /**
     * VerifyPlexWebhook constructor.
     *
     * @param $logger
     * @param $config
     * @param $validator
     */
    public function __construct($logger, $config, $validator)
    {
        $this->logger = $logger;
        $this->config = $config['plex'];
        $this->validator = $validator;
    }

    /**
     * Validate request data for Plex webhooks.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        // Log the invocation of this middleware
        $this->log($request);

        // Immediately eject if Plex webhooks are disabled
        if ($this->config['webhooks'] === 'disabled') {
            return $response->withJson('Plex webhooks are disabled.', 500);
        }

        // Get the payload from the request
        $payload = json_decode($request->getParsedBody()['payload']);

        // Load the Plex JSON schema
        $this->validator->validate($payload, (object) ['$ref' => 'file://' . base_path('config/schema/plex.json')]);

        // Validate the JSON payload against the Plex schema
        // Log and fail otherwise
        if (! $this->validator->isValid()) {
            $this->logger->warning("\n[RESULT] Invalid payload.");

            return $response->withJson('Invalid payload.', 422);
        }

        $isPlayerAllowed = in_array($payload->Player->uuid, $this->config['players']);
        if (! $isPlayerAllowed) {
            $this->logger->warning("\n[RESULT] Invalid player or media type.");

            return $response->withJson('Well that didn\'t work.', 401);
        }

        // Ensure both the player's UUID and the media type are allowed
        // Log and fail otherwise
        $isMediaTypeAllowed = in_array($payload->Metadata->librarySectionType, $this->config['allowed_media']);
        if (! $isMediaTypeAllowed) {
            $this->logger->warning("\n[RESULT] Invalid player or media type.");

            return $response->withJson('Well that didn\'t work.', 401);
        }

        // Successfully verified webhook.
        $this->logger->info("\n[RESULT] Plex webhook verified.");

        return $next($request->withParsedBody($payload), $response);
    }

    /**
     * Log this request.
     *
     * @param $request
     */
    private function log($request)
    {
        $routeInfo = $request->getAttribute('routeInfo')['request'];
        $payload = json_decode($request->getParsedBody()['payload'], true);

        $minimalPayload = json_encode([
            'event' => $payload['event'],
            'Player' => $payload['Player'],
            'Metadata' => [
                'title' => $payload['Metadata']['title'],
            ],
        ], JSON_PRETTY_PRINT);

        $message = sprintf("\n[%s] %s\n[PAYLOAD]\n%s", $routeInfo[0], $routeInfo[1], $minimalPayload);

        $this->logger->info($message);
    }
}
