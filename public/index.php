<?php

// Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Define project root directory
define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

// Load Slim config
$slimConfig = require PROJECT_ROOT . '/config/slim.php';

// Boot the app
$app = new Slim\App($slimConfig);

// Register dependencies
require PROJECT_ROOT . '/src/dependencies.php';

// Register routes
require PROJECT_ROOT . '/src/routes.php';

$app->run();
