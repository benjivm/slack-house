<?php

namespace App\Middleware;

use JsonSchema\Validator;

class VerifyPlexWebhook
{
    private $logger;

    private $config;

    private $validator;

    public function __construct($container)
    {
        $this->logger = $container->logger;

        $this->config = $container->config['plex'];

        $this->validator = $container->validator;
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
        if (!$this->validator->isValid()) {
            $this->logger->warning("\n[RESULT] Invalid payload.");

            return $response->withJson('Invalid payload.', 422);
        }

        $isPlayerAllowed = in_array($payload->Player->uuid, $this->config['players']);
        if (!$isPlayerAllowed) {
            $this->logger->warning("\n[RESULT] Invalid player or media type.");

            return $response->withJson('Well that didn\'t work.', 401);            
        }

        // Ensure both the player's UUID and the media type are allowed
        // Log and fail otherwise
        $isMediaTypeAllowed = in_array($payload->Metadata->librarySectionType, $this->config['allowedMedia']);
        if (!$isMediaTypeAllowed) {
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
        $payload = $request->getParsedBody()['payload'];

        $message = sprintf("\n[%s] %s\n[PAYLOAD]\n%s", $routeInfo[0], $routeInfo[1], $payload);

        $this->logger->info($message);
    }
}
