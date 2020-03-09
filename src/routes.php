<?php

/*
 * App '/webhook' endpoints.
 */
$app->group('/webhook', function () use ($app) {

    /*
     * Handle IFTTT webhooks.
     */
    $app->post('/ifttt', 'controller.ifttt')
        ->add('middleware.verify_ifttt_webhook');

    /*
     * Handle PLEX webhooks.
     */
    $app->post('/plex', 'controller.plex')
        ->add('middleware.verify_plex_webhook');
});

/*
 * Catch unregistered routes.
 */
$app->get('/[{path:.*}]', function ($request, $response, $path = null) {
    return $response->withJson([
        'error'   => 404,
        'message' => 'Not found.',
    ], 404);
});
