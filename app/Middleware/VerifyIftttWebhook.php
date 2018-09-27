<?php

namespace App\Middleware;

use JsonSchema\Validator;

class VerifyIftttWebhook
{
    private $logger;

    private $config;

    public function __construct($container)
    {
        $this->logger = $container->logger;

        $this->config = $container->config['ifttt'];
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
        // Log the invocation of this middleware
        $this->log($request);

        // Get the request object
        $payload = (object) $request->getParsedBody();

        // Load the IFTTT JSON schema
        $validator = new Validator();
        $validator->validate($payload, (object) ['$ref' => 'file://' . base_path('config/schema/ifttt.json')]);

        // Validate the JSON payload against the IFTTT schema
        // Log and fail otherwise
        if (! $validator->isValid()) {
            $this->logger->warning("\n[RESULT] Invalid payload.");

            return $response->withJson('Invalid payload.', 422);
        }

        // Ensure the key sent is our IFTTT Maker key
        // Log and fail otherwise
        if ($payload->key !== $this->config['key']) {
            $this->logger->warning("\n[RESULT] Invalid key.");

            return $response->withJson('Well that didn\'t work.', 401);
        }

        // Successfully verified webhook.
        $this->logger->info("\n[RESULT] IFTTT webhook verified.");

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
        $payload = json_encode($request->getParsedBody(), JSON_PRETTY_PRINT);

        $message = sprintf("\n[%s] %s\n[PAYLOAD]\n%s", $routeInfo[0], $routeInfo[1], $payload);

        $this->logger->info($message);
    }
}
