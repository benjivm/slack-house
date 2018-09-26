<?php

use App\Middleware\VerifyPlexWebhook;
use App\Middleware\VerifyIftttWebhook;

require __DIR__ . '/../bootstrap/app.php';

/*
* Handle IFTTT webhooks.
*
* @param $request
* @param $response
* @return JSON response
*/
$app->post('/webhook/ifttt', function ($request, $response) {
    $payload = $request->getParsedBody();

    // Handle app events
    if ($payload->event === 'app_command') {
        // Disable plex webhooks
        if ($payload->command === 'disable_plex_webhooks') {
            change_env('PLEX_WEBHOOKS', 'disabled');

            return $response->withJson('Plex webhooks disabled.');
        }

        // Enable plex webhooks
        if ($payload->command === 'enable_plex_webhooks') {
            change_env('PLEX_WEBHOOKS', 'enabled');

            return $response->withJson('Plex webhooks enabled.');
        }
    }

    // Handle home events
    if ($payload->event === 'home_command') {
        // Movie time!
        // 1. Re-enable Plex webhooks so lights respond (in case they are disabled)
        // 2. Activate the LIFX Movie Time scene over 5 seconds
        // 3. Turn on the Kasa smart plug for the TV, receiver, and speakers
        // 4. Tell Harmony to activate the Shield TV activity
        if ($payload->command === 'activate_movie_time') {
            change_env('PLEX_WEBHOOKS', 'enabled');
            $this->lifx->activateScene('movieTime', 5);
            $this->ifttt->trigger('turn_tv_plug_on');
            $this->ifttt->trigger('start_shield_activity');

            return $response->withJson($payload->command . ' webhook fired.');
        }

        // Bed time!
        // 1. Turn off the Kasa smart plug for the TV, receiver, and speakers
        // 2. Fade all the lights off over 15 minutes
        if ($payload->command === 'activate_bed_time') {
            $this->ifttt->trigger('turn_tv_plug_off');
            $this->ifttt->trigger('fade_all_lights_off');

            return $response->withJson($payload->command . ' webhook fired.');
        }
    }

    return $response->withJson($payload->command . ' unhandled.', 422);
})->add(new VerifyIftttWebhook($container));

/*
* Handle PLEX webhooks.
*
* @param $request
* @param $response
* @return JSON response
*/
$app->post('/webhook/plex', function ($request, $response) {
    $lifx = $this->get('lifx');
    $payload = $request->getParsedBody();

    // Handle the Play event
    if ($payload->event === 'media.play') {
        // Power off all the lights in the LIFX Warm Night scene over 30 seconds
        $lifx->activateScene('warmNight', 30, ['power' => 'off']);

        return $response->withJson('Play event handled.');
    }

    // Handle the Pause event
    if ($payload->event === 'media.pause') {
        // Turn on the LIFX Warm Night scene over 3 seconds
        $lifx->activateScene('warmNight', 3);

        return $response->withJson('Pause event handled.');
    }

    // Handle the Resume event
    if ($payload->event === 'media.resume') {
        // Power off all the lights in the LIFX Warm Night scene over 3 seconds
        $lifx->activateScene('warmNight', 3, ['power' => 'off']);

        return $response->withJson('Resume event handled.');
    }

    // Handle the Stop event
    if ($payload->event === 'media.stop') {
        // Turn on the LIFX Warm Night scene over 15 seconds
        $lifx->activateScene('warmNight', 15);

        return $response->withJson('Stop event handled.');
    }

    return $response->withJson($payload['event'] . ' webhook unhandled.', 422);
})->add(new VerifyPlexWebhook($container));

$app->run();
