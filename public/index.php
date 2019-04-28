<?php

// Bootstrap
require __DIR__ . '/../config/slim.php';

// Boot the app
$app = new Slim\App($settings);

// Register dependencies
require PROJECT_ROOT . '/src/dependencies.php';

// Register routes
require PROJECT_ROOT . '/src/routes.php';

$app->run();
