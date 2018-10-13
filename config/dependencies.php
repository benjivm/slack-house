<?php

$container = $app->getContainer();

/*
 * Slackhouse config.
 *
 * @return array
 */
$container['config'] = function () {
    $config = require base_path('config/slackhouse.php');

    return $config;
};

/*
 * Monolog configuration.
 *
 * @param $container
 * @return \Monolog\Logger
 */
$container['logger'] = function ($container) {
    $settings = $container->settings['logger'];
    $config = $container->config;

    $logger = new Monolog\Logger($settings['name']);
    $logger->pushHandler(new DiscordHandler\DiscordHandler([$config['discord']['webhookUrl']], 'slack', 'house', 'DEBUG'));

    return $logger;
};

/*
 * App config interface.
 *
 * @return \App\Interfaces\AppCommandInterface
 */
$container['appCommand'] = function () {
    return new App\Interfaces\AppCommandInterface();
};

$container['app.services.ifttt_client'] = $container->factory(function($container) {
    return new GuzzleHttp\Client([
        'base_uri' => 'https://maker.ifttt.com/trigger/',
        'http_errors' => false,
        'headers' => [
            'accept' => 'application/json',
        ],
    ]);
});

$container['app.services.lifx_client'] = $container->factory(function($container) {
    return new GuzzleHttp\Client([
        'base_uri' => 'https://api.lifx.com/v1/',
        'http_errors' => false,
        'headers' => [
            'Authorization' => 'Bearer ' . $container->config['ifttt']['token'],
        ],
    ]);
});

$container['app.services.validator'] = $container->factory(function($container) {
    return new JsonSchema\Validator();
});

$container['app.middleware.verify_ifttt_webhook'] = function($container) {
    $logger = $container->get('logger');
    $config = $container->get('config');
    $validator = $container->get('app.services.validator');

    return new App\Middleware\VerifyPlexWebhook($logger, $config, $validator);
};

$container['app.middleware.verify_plex_webhook'] = function($container) {
    $logger = $container->get('logger');
    $config = $container->get('config');
    $validator = $container->get('app.services.validator');

    return new App\Middleware\VerifyIftttWebhook($logger, $config, $validator);
};

/*
 * IFTTT Service.
 *
 * @param $container
 * @return \App\Services\Ifttt\Ifttt
 */
$container['ifttt'] = function ($container) {
    $config = $container->config['ifttt'];
    $client = $container->get('app.services.ifttt_client');
    
    return new App\Services\Ifttt\Ifttt($config, $client);
};

/*
 * LIFX Service.
 *
 * @param $container
 * @return \App\Services\Lifx\Lifx
 */
$container['lifx'] = function ($container) {
    $config = $container->config['lifx'];
    $client = $container->get('app.services.lifx_client');

    return new App\Services\Lifx\Lifx($config, $client);
};

$container['app.controller.ifttt'] = function($container) {
    $appCommand = $container->get('lifx');
    $lifx = $container->get('lifx');
    $ifttt = $container-get('ifttt');

    return new App\Controllers\IftttController($appCommand, $lifx, $ifttt);
};

$container['app.controller.plex'] = function($container) {
    $lifx = $container->get('lifx');

    return new App\Controllers\PlexController();
};
