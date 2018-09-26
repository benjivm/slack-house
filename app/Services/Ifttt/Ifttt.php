<?php

namespace App\Services\Ifttt;

use GuzzleHttp\Client;

class Ifttt
{
    private $config;

    /**
     * IFTTT constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->client = new Client([
            'base_uri' => 'https://maker.ifttt.com/trigger/',
            'http_errors' => false,
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Trigger an event.
     *
     * @param $event
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function trigger($event)
    {
        $request = $this->client->post($event . '/with/key/' . $this->config['key']);

        return json_decode($request->getBody()->getContents());
    }
}
