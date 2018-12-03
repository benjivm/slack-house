<?php

/*
 * Greetings.
 */
$app->get('/', function ($request, Slim\Http\Response $response) {
    return $response->withJson(['Slack House says' => 'Hello, world!']);
});

/*
 * App '/webhook' endpoints.
 */
$app->group('/webhook', function () use ($app) {
    /*
     * Handle IFTTT webhooks.
     */
    $app->post('/ifttt', 'app.controller.ifttt')
        ->add('app.middleware.verify_ifttt_webhook');

    /*
     * Handle PLEX webhooks.
     */
    $app->post('/plex', 'app.controller.plex')
        ->add('app.middleware.verify_plex_webhook');
});
