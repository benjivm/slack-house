<?php

return [
    'environment' => getenv('APP_ENV'),
    'ifttt' => [
        'key' => getenv('IFTTT_KEY'),
    ],
    'lifx' => [
        'token' => getenv('LIFX_TOKEN'),
        'scenes' => [
            'movieTime' => getenv('LIFX_MOVIE_TIME_SCENE_UUID'),
            'warmNight' => getenv('LIFX_WARM_NIGHT_SCENE_UUID'),
        ],
    ],
    'plex' => [
        'webhooks' => getenv('PLEX_WEBHOOKS'),
        'server' => getenv('PLEX_SERVER'),
        'token' => getenv('PLEX_TOKEN'),
        'username' => getenv('PLEX_USERNAME'),
        'password' => getenv('PLEX_PASSWORD'),
        'players' => explode(',', getenv('PLEX_PLAYERS')),
        'allowedMedia' => explode(',', getenv('PLEX_ALLOWED_MEDIA')),
    ],
];
