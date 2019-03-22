<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class PlexController
{
    /**
     * PlexController constructor.
     *
     * @param $lifx
     */
    public function __construct($lifx)
    {
        $this->lifx = $lifx;
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

        // Handle the Play event
        if ($payload->event === 'media.play') {
            // Power off all the lights in the LIFX Warm Night scene over 30 seconds
            $this->lifx->activateScene('warm_night', 30, ['power' => 'off']);

            return $response->withJson('Play event handled.');
        }

        // Handle the Pause event
        if ($payload->event === 'media.pause') {
            // Turn on the LIFX Warm Night scene over 3 seconds
            $this->lifx->activateScene('warm_night', 3);

            return $response->withJson('Pause event handled.');
        }

        // Handle the Resume event
        if ($payload->event === 'media.resume') {
            // Power off all the lights in the LIFX Warm Night scene over 3 seconds
            $this->lifx->activateScene('warm_night', 3, ['power' => 'off']);

            return $response->withJson('Resume event handled.');
        }

        // Handle the Stop event
        if ($payload->event === 'media.stop') {
            // Turn on the LIFX Warm Night scene over 15 seconds
            $this->lifx->activateScene('warm_night', 15);

            return $response->withJson('Stop event handled.');
        }

        return $response->withJson($payload['event'] . ' webhook unhandled.', 422);
    }
}
