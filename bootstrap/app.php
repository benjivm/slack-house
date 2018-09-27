<?php


require __DIR__ . '/../vendor/autoload.php';

// Load the slim config
$config = require base_path('config/slim.php');

// Boot the app
$app = new Slim\App($config);

// Register dependencies
require base_path('config/dependencies.php');

// Register middleware
require base_path('config/middleware.php');

// Register routes
require base_path('routes/routes.php');

// Catch all errors and return a generic message if APP_ENV=production
if ($container->config['app']['env'] === 'production') {
    $container['errorHandler'] = function ($container) {
        return function () use ($container) {
            return $container['response']
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode('Well that didn\'t work.'));
        };
    };

    $container['notAllowedHandler'] = function ($container) {
        return function () use ($container) {
            return $container['response']
                ->withStatus(405)
                ->withHeader('Content-type', 'application/json')
                ->write(json_encode('Well that didn\'t work.'));
        };
    };

    $container['notFoundHandler'] = function ($container) {
        return function () use ($container) {
            return $container['response']
                ->withStatus(404)
                ->withHeader('Content-type', 'application/json')
                ->write(json_encode('Well that didn\'t work.'));
        };
    };
}
