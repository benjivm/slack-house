<?php

// Load Slack House config
$slackHouseValueStore = Spatie\Valuestore\Valuestore::make(PROJECT_ROOT . '/config/slack-house.json');

return [
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
