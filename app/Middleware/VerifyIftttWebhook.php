<?php

namespace App\Middleware;

use JsonSchema\Validator;

class VerifyIftttWebhook
{
    private $container;

    /**
     * VerifyIftttWebhook constructor.
     *
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Validate request data for IFTTT webhooks.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        // Get the request object
        $payload = (object) $request->getParsedBody();

        // Validate the payload schema
        $validator = new Validator();
        $validator->validate($payload, (object) ['$ref' => 'file://' . base_path('config/schema/ifttt.json')]);

        if (! $validator->isValid()) {
            return $response->withJson('Invalid payload.', 422);
        }

        // Ensure the key sent is our IFTTT Maker key
        if ($payload->key !== $this->container->config['ifttt']['key']) {
            return $response->withJson('Well that didn\'t work.', 401);
        }

        // Unset the key before passing the request object on
        unset($payload->key);

        return $next($request->withParsedBody($payload), $response);
    }
}
