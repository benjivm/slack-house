<?php

use App\Controllers\IftttController;
use App\Controllers\PlexController;

$app->group('/webhook', function () use ($app) {
    /*
    * Handle IFTTT webhooks.
    *
    * @param $request
    * @param $response
    * @return JSON response
    */
    $app->post('/ifttt', 'app.controller.ifttt')
        ->add('app.middleware.verify_ifttt_webhook');

    /*
    * Handle PLEX webhooks.
    *
    * @param $request
    * @param $response
    * @return JSON response
    */
    $app->post('/plex', 'app.controller.plex')
        ->add('app.middleware.verify_plex_webhook');
});
