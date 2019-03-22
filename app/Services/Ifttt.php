<?php

namespace App\Services;

use GuzzleHttp\Client;

class Ifttt
{
    private $config;

    private $client;

    /**
     * IFTTT constructor.
     *
     * @param array  $config
     * @param Client $client
     */
    public function __construct(array $config, Client $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Trigger an event.
     *
     * @param string $event
     * @param string $value1
     * @param string $value2
     * @param string $value3
     *
     * @return string
     */
    public function trigger(string $event, string $value1 = null, string $value2 = null, string $value3 = null)
    {
        $parameters = [
            'value1' => $value1,
            'value2' => $value2,
            'value3' => $value3,
        ];

        $request = $this->client->post($event . '/with/key/' . $this->config['key'], ['form_params' => $parameters]);

        return $request->getBody()->getContents();
    }
}
