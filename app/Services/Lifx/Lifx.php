<?php

namespace App\Services\Lifx;

use GuzzleHttp\Client;

class Lifx
{
    private $config;

    /**
     * LIFX constructor.
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
     * Convert a given string from JSON to an Object.
     *
     * @param $request
     *
     * @return mixed
     */
    private function createResponse($request)
    {
        return json_decode($request->getBody()->getContents());
    }

    /**
     * Convert a given string to JSON.
     *
     * @param $data
     * @param bool $assoc
     *
     * @return string
     */
    private function toJson($data, $assoc = false)
    {
        return json_encode($data, $assoc);
    }

    /**
     * Create a PUT request.
     *
     * @param $uri
     * @param null $data
     *
     * @return string
     */
    private function put($uri, $data = null)
    {
        $request = $this->client->put($uri, ['body' => $this->toJson($data)]);

        return $this->createResponse($request);
    }

    /**
     * Create a GET request.
     *
     * @param $uri
     *
     * @return string
     */
    private function get($uri)
    {
        $request = $this->client->get($uri);

        return $this->createResponse($request);
    }

    /**
     * @return string
     */
    public function getLights()
    {
        return $this->get('lights/all');
    }

    /**
     * @param $selector
     *
     * @return mixed
     */
    public function getLight(string $selector)
    {
        return $this->get('lights/' . $selector)[0];
    }

    /**
     * @return string
     */
    public function getScenes()
    {
        return $this->get('scenes');
    }

    /**
     * Activate a scene.
     *
     * @param string $sceneName
     * @param int    $duration
     * @param array  $overrides
     */
    public function activateScene(string $sceneName, int $duration = 1, array $overrides = [])
    {
        $scene_uuid = $this->config['scenes'][$sceneName];

        $options = [
            'fast' => true,
            'duration' => $duration,
        ];

        if (! empty($overrides)) {
            $options['overrides'] = $overrides;
        }

        $this->put('scenes/scene_id:' . $scene_uuid . '/activate', $options);
    }
}
