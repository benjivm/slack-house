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
    $settings = $container->get('settings')['logger'];
    $config = $container->get('config');

    $logger = new Monolog\Logger($settings['name']);
    $logger->pushHandler(new DiscordHandler\DiscordHandler([$config['discord']['webhookUrl']], 'slackhouse', 'webhooks', 'DEBUG'));

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

/*
 * IFTTT Service.
 *
 * @param $container
 * @return \App\Services\Ifttt\Ifttt
 */
$container['ifttt'] = function ($container) {
    $config = $container->config['ifttt'];
    $ifttt = new App\Services\Ifttt\Ifttt($config);

    return $ifttt;
};

/*
 * LIFX Service.
 *
 * @param $container
 * @return \App\Services\Lifx\Lifx
 */
$container['lifx'] = function ($container) {
    $config = $container->config['lifx'];
    $lifx = new App\Services\Lifx\Lifx($config);

    return $lifx;
};
