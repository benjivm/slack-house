<?php

require __DIR__.'/../vendor/autoload.php';

// Load Slim config
$slimConfig = require base_path('config/slim.php');

// Boot the app
$app = new Slim\App($slimConfig);

// Register dependencies
require base_path('config/dependencies.php');

// Register routes
require base_path('routes/routes.php');
