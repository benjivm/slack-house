<?php

return [
    'settings' => [
        'displayErrorDetails' => false,
        'addContentLengthHeader' => false,

        // Monolog settings
        'logger' => [
            'name' => 'slackhouse',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
