<?php

// Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Project root global
define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

// Load Slack House config
$slackHouseValueStore = Spatie\Valuestore\Valuestore::make(PROJECT_ROOT . '/config/slack-house.json');

$settings = [
    'settings' => [
        'displayErrorDetails'    => $slackHouseValueStore->get('app')['display_errors'],
        'addContentLengthHeader' => true,

        // Monolog settings
        'logger' => [
            'name'  => 'slack-house',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Merge Slack House settings
        'slack-house' => $slackHouseValueStore->all(),
    ],
];
