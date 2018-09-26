<?php

namespace App\Middleware;

use JsonSchema\Validator;

class VerifyPlexWebhook
{
    private $container;

    private $config;

    /**
     * VerifyIftttWebhook constructor.
     *
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;

        $this->config = $container->config['plex'];
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
        // Immediately eject if Plex webhooks are disabled
        if ($this->config['webhooks'] === 'disabled') {
            return $response->withJson('Plex webhooks are disabled.', 500);
        }

        // Get the payload from the request
        $payload = json_decode($request->getParsedBody()['payload']);

        // Validate the payload schema
        $validator = new Validator();
        $validator->validate($payload, (object) ['$ref' => 'file://' . base_path('config/schema/plex.json')]);

        if (! $validator->isValid()) {
            return $response->withJson('Invalid payload.', 422);
        }

        // Ensure both the player's UUID and the media type are allowed
        if (! in_array($payload->Player->uuid, $this->config['players']) ||
            ! in_array($payload->Metadata->librarySectionType, $this->config['allowedMedia'])) {
            return $response->withJson('Well that didn\'t work.', 401);
        }

        return $next($request->withParsedBody($payload), $response);
    }
}
