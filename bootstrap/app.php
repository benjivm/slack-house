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