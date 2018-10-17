<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class IftttController
{
    private $appCommand;

    private $lifx;

    private $ifttt;

    /**
     * IftttController constructor.
     *
     * @param $appCommand
     * @param $lifx
     * @param $ifttt
     */
    public function __construct($appCommand, $lifx, $ifttt)
    {
        $this->appCommand = $appCommand;
        $this->lifx = $lifx;
        $this->ifttt = $ifttt;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $payload = $request->getParsedBody();
        
        // Handle app events
        if ($payload->event === 'app_command') {
            // Enable plex webhooks
            if ($payload->command === 'enable_plex_webhooks') {
                $this->appCommand->changeConfigSetting('PLEX_WEBHOOKS', 'enabled');

                return $response->withJson('Plex webhooks enabled.');
            }

            // Disable plex webhooks
            if ($payload->command === 'disable_plex_webhooks') {
                $this->appCommand->changeConfigSetting('PLEX_WEBHOOKS', 'disabled');

                return $response->withJson('Plex webhooks disabled.');
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
                $this->appCommand->changeConfigSetting('PLEX_WEBHOOKS', 'enabled');
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
    }
}
