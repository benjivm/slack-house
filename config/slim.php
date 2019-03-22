<?php

// Load Slack House config
$slackHouseValueStore = Spatie\Valuestore\Valuestore::make(base_path('config/slack-house.json'));

return [
    'settings' => [
        'displayErrorDetails'    => $slackHouseValueStore->get('app')['display_errors'],
        'addContentLengthHeader' => true,

        // Monolog settings
        'logger' => [
            'name'  => 'slackhouse',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Merge Slack House settings
        'slackHouse' => $slackHouseValueStore->all(),
    ],
];
