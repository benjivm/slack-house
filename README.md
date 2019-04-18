# Slack House

This app triggers API functions when Google assistant is given custom commands (created through IFTTT's voice command applet) in conjunction with [Plex webhooks](https://support.plex.tv/articles/115002267687-webhooks/) like `media.play`, `media.pause`, and `media.stop`.

This is a barebones and personal app, but you may find it a useful starting point for your own needs.

### Requirements

- PHP 7+
- [Composer](https://getcomposer.org/)
- [Plex Pass](https://www.plex.tv/plex-pass/) (if you want to use Plex webhooks)
- A web server

### Setup
- Create an [Apache](https://httpd.apache.org/docs/2.4/vhosts/examples.html) or [Nginx](https://www.nginx.com/resources/wiki/start/topics/examples/full/) host file for the app (**important**: the document root should be `/public`)
- Clone or download the repo to the appropriate directory you setup in the previous step (again, ensure the `/public` directory is your document root)
- Install dependencies: `composer install --no-dev`
- Create your `settings.ini` file: `cp settings.ini.example settings.ini` and edit the contents with your desired configuration.
- Now generate your unique configuration file by executing `php slack config:generate` from a terminal. **Note**: The config file (`config/slack-house.json`) will be regenerated every time the `slack config:generate` command is called, so make sure your `settings.ini` file is up-to-date before you run the command, or use the `appCommand` function (see `$this->appCommand(...)` used in `app/Controllers/IftttController.php`).

### Usage

This app interacts primarily with and [Plex](https://plex.tv), but in my configuration, through [IFTTT](https://ifttt.com) webhooks it also sends commands to [Harmony](https://www.logitech.com/en-us/harmony-universal-remotes), [Shield TV](https://www.nvidia.com/en-us/shield/), and [TP Link](https://www.tp-link.com/us/kasa-smart/kasa.html) smart plugs. It should be trivial to integrate any other products so long as they provide an HTTP API or IFTTT can interact with them.

[Monolog](https://github.com/Seldaek/monolog) messages are sent to my Discord server, but there are [many other ways](https://github.com/Seldaek/monolog/blob/master/doc/02-handlers-formatters-processors.md#handlers) to catch them.

There are only two endpoints: `/webhook/plex` and `/webhook/ifttt`, see the [`routes/routes.php`](https://github.com/benjivm/slack-house/blob/master/src/routes.php) file. Both endpoints have their own middleware to handle requests, see [`src/Middleware`](https://github.com/benjivm/slack-house/tree/master/src/Middleware). 

Plex players that are allowed to trigger events are verified by `UUID` (obtained from your Plex server's settings page) in the `players[]` ini option (see the [`VerifyPlexWebhook`](https://github.com/benjivm/slack-house/blob/master/src/Middleware/VerifyPlexWebhook.php#L60) middleware file for an example of how this is used). Plex media types allowed to trigger events are listed in the `allowed_media[]` setting. Both settings can be a single value or a comma separated list of values (e.g., `allowed_media[]=movie`)

You can hack up the IFTTT and LIFX API wrappers as needed, they're located in the [`src/Services`](https://github.com/benjivm/slack-house/tree/master/src/Services) directory and use the [Guzzle](https://github.com/guzzle/guzzle) client for requests, though this too can easily be swapped out if you prefer another client.

#### API Endpoints
```
$app->group('/webhook', function () use ($app) {
    // IFTTT
    $app->post('/ifttt', 'app.controller.ifttt')
        ->add('app.middleware.verify_ifttt_webhook');
    
    // Plex
    $app->post('/plex', 'app.controller.plex')
        ->add('app.middleware.verify_plex_webhook');
});
```

Only two routes are needed to handle the commands sent by Plex webhooks or IFTTT applets. The IFTTT commands must have a valid payload in order to pass schema validation (see [`src/Middleware/VerifyIftttWebhook.php`](https://github.com/benjivm/slack-house/blob/master/src/Middleware/VerifyIftttWebhook.php)):

```
{
    "key": "ifttt_maker_key_here",
    "event": "event_name_here",
    "command": "command_name_here"
}
```

So, for example, if I want to activate movie time, I tell my Google Assistant: "It's movie time.", and it triggers the IFTTT webhook for movie time, handled in [`src/Controllers/IftttController.php`](https://github.com/benjivm/slack-house/blob/master/src/Controllers/IftttController.php):

```
// Handle home events
if ($payload->event === 'home_command') {
    // Movie time!
    // 1. Re-enable Plex webhooks so lights respond (in case they are disabled)
    // 2. Activate the LIFX Movie Time scene over 5 seconds
    // 3. Turn on the Kasa smart plug for the TV, receiver, and speakers
    // 4. Tell Harmony to activate the Shield TV activity
    if ($payload->command === 'activate_movie_time') {
        $this->appCommand->changeSetting('plex.webhooks', 'enabled');
        $this->lifx->activateScene('movie_time', 5);
        $this->ifttt->trigger('turn_tv_plug_on');
        $this->ifttt->trigger('start_shield_activity');

        return $response->withJson($payload->command . ' webhook fired.');
    }
}
```

Plex sends its payloads as JSON in a URL encoded POST request, so we need to run `json_decode()` on the `payload` after we receive it. The Plex middleware (see [`src/Middleware/VerifyPlexWebhook.php`](https://github.com/benjivm/slack-house/blob/master/src/Middleware/VerifyPlexWebhook.php))) validates the Plex payloads, which look like this:

```
{
   "event": "media.play",
   "user": true,
   "owner": true,
   "Account": {
      "id": 1,
      "thumb": "https://plex.tv/users/1022b120ffbaa/avatar?c=1465525047",
      "title": "elan"
   },
   "Server": {
      "title": "Office",
      "uuid": "54664a3d8acc39983675640ec9ce00b70af9cc36"
   },
   "Player": {
      "local": true,
      "publicAddress": "200.200.200.200",
      "title": "Plex Web (Safari)",
      "uuid": "r6yfkdnfggbh2bdnvkffwbms"
   },
   "Metadata": {
      "librarySectionType": "artist",
      "ratingKey": "1936545",
      "key": "/library/metadata/1936545",
      "parentRatingKey": "1936544",
      "grandparentRatingKey": "1936543",
      "guid": "com.plexapp.agents.plexmusic://gracenote/track/7572499-91016293BE6BF7F1AB2F848F736E74E5/7572500-3CBAE310D4F3E66C285E104A1458B272?lang=en",
      "librarySectionID": 1224,
      "type": "track",
      "title": "Love The One You're With",
      "grandparentKey": "/library/metadata/1936543",
      "parentKey": "/library/metadata/1936544",
      "grandparentTitle": "Stephen Stills",
      "parentTitle": "Stephen Stills",
      "summary": "",
      "index": 1,
      "parentIndex": 1,
      "ratingCount": 6794,
      "thumb": "/library/metadata/1936544/thumb/1432897518",
      "art": "/library/metadata/1936543/art/1485951497",
      "parentThumb": "/library/metadata/1936544/thumb/1432897518",
      "grandparentThumb": "/library/metadata/1936543/thumb/1485951497",
      "grandparentArt": "/library/metadata/1936543/art/1485951497",
      "addedAt": 1000396126,
      "updatedAt": 1432897518
   }
}
```

Pressing play on a movie or show in Plex sends a webhook to our `/webhook/plex` endpoint which, if it passed validation, is handled in [`src/Controllers/PlexController.php`](https://github.com/benjivm/slack-house/blob/master/src/Controllers/PlexController.php):

```
// Handle the Play event
if ($payload->event === 'media.play') {
    // Power off all the lights in the LIFX Warm Night scene over 30 seconds
    $lifx->activateScene('warm_night', 30, ['power' => 'off']);

    return $response->withJson('Play event handled.');
}
```

As you can see this is pretty straightforward to use and is easily extensible, especially if you're familiar with [Slim's container](http://www.slimframework.com/docs/v3/concepts/di.html) and basic PHP.

Pull requests and suggestions are welcome.
