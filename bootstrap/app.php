<?php

use App\Services\Lifx\Lifx;
use App\Services\Ifttt\Ifttt;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(base_path());
$dotenv->load();

$config['settings'] = require base_path('config/slim.php');

// Boot the app
$app = new Slim\App($config);

// Register services
$container = $app->getContainer();

$container['config'] = function () {
    $config = require base_path('config/slackhouse.php');

    return $config;
};

$container['ifttt'] = function ($container) {
    $config = $container->config['ifttt'];
    $ifttt = new Ifttt($config);

    return $ifttt;
};

$container['lifx'] = function ($container) {
    $config = $container->config['lifx'];
    $lifx = new Lifx($config);

    return $lifx;
};

// Catch all errors and return a generic message if APP_ENV=production
if ($container->config['environment'] === 'production') {
    $container['errorHandler'] = function ($container) {
        return function ($request, $response, $exception) use ($container) {
            return $container['response']
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode('Well that didn\'t work.'));
        };
    };

    $container['notAllowedHandler'] = function ($container) {
        return function ($request, $response, $methods) use ($container) {
            return $container['response']
                ->withStatus(405)
                ->withHeader('Allow', implode(', ', $methods))
                ->withHeader('Content-type', 'application/json')
                ->write(json_encode('Well that didn\'t work.'));
        };
    };

    $container['notFoundHandler'] = function ($container) {
        return function ($request, $response) use ($container) {
            return $container['response']
                ->withStatus(404)
                ->withHeader('Content-type', 'application/json')
                ->write(json_encode('Well that didn\'t work.'));
        };
    };
}
