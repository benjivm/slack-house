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
    $app->post('/ifttt', IftttController::class)
        ->add('App\Middleware\VerifyIftttWebhook');

    /*
    * Handle PLEX webhooks.
    *
    * @param $request
    * @param $response
    * @return JSON response
    */
    $app->post('/plex', PlexController::class)
        ->add('App\Middleware\VerifyPlexWebhook');
});
