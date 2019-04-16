<?php

$container = $app->getContainer();

/*
 * Slackhouse configuration.
 *
 * @return array
 */
$container['config'] = function ($container) {
    return $container->settings['slack-house'];
};

/*
 * Monolog configuration.
 *
 * @param $container
 * @return \Monolog\Logger
 */
$container['logger'] = function ($container) {
    $loggerSettings = $container->settings['logger'];
    $config = $container->config;

    $logger = new Monolog\Logger($loggerSettings['name']);
    $logger->pushHandler(new DiscordHandler\DiscordHandler([$config['discord']['webhook_url']], 'slack', 'house', 'DEBUG'));

    return $logger;
};

/*
 * App config interface.
 *
 * @return \App\Services\AppCommand
 */
$container['appCommand'] = function ($container) {
    $config = $container->config;
    $configPath = PROJECT_ROOT . '/config/slack-house.json';

    return new App\Services\AppCommand($config, $configPath);
};

/*
 * The IFTTT API client.
 *
 * @return \GuzzleHttp\Client
 */
$container['services.ifttt_client'] = $container->factory(function () {
    return new GuzzleHttp\Client([
        'base_uri'    => 'https://maker.ifttt.com/trigger/',
        'http_errors' => false,
        'headers'     => [
            'accept' => 'application/json',
        ],
    ]);
});

/*
 * The LIFX API client.
 *
 * @return \GuzzleHttp\Client
 */
$container['services.lifx_client'] = $container->factory(function ($container) {
    return new GuzzleHttp\Client([
        'base_uri'    => 'https://api.lifx.com/v1/',
        'http_errors' => false,
        'headers'     => [
            'Authorization' => 'Bearer ' . $container->config['lifx']['token'],
        ],
    ]);
});

/*
 * JSON validator.
 *
 * @return \JsonSchema\Validator
 */
$container['services.validator'] = $container->factory(function () {
    return new JsonSchema\Validator();
});

/*
 * IFTTT middleware.
 *
 * @return \App\Middleware\VerifyIftttWebhook
 */
$container['middleware.verify_ifttt_webhook'] = function ($container) {
    $logger = $container->get('logger');
    $config = $container->get('config');
    $validator = $container->get('services.validator');

    return new App\Middleware\VerifyIftttWebhook($logger, $config, $validator);
};

/*
 * Plex middleware.
 *
 * @return \App\Middleware\VerifyPlexWebhook
 */
$container['middleware.verify_plex_webhook'] = function ($container) {
    $logger = $container->get('logger');
    $config = $container->get('config');
    $validator = $container->get('services.validator');

    return new App\Middleware\VerifyPlexWebhook($logger, $config, $validator);
};

/*
 * The IFTTT service.
 *
 * @return \App\Services\Ifttt
 */
$container['ifttt'] = function ($container) {
    $config = $container->config['ifttt'];
    $client = $container->get('services.ifttt_client');

    return new App\Services\Ifttt($config, $client);
};

/*
 * The LIFX service.
 *
 * @return \App\Services\Ifttt
 */
$container['lifx'] = function ($container) {
    $config = $container->config['lifx'];
    $client = $container->get('services.lifx_client');

    return new App\Services\Lifx($config, $client);
};

/*
 * Controllers
 */
$container['controller.ifttt'] = function ($container) {
    $appCommand = $container->get('appCommand');
    $lifx = $container->get('lifx');
    $ifttt = $container->get('ifttt');

    return new App\Controllers\IftttController($appCommand, $lifx, $ifttt);
};

$container['controller.plex'] = function ($container) {
    $lifx = $container->get('lifx');

    return new App\Controllers\PlexController($lifx);
};
