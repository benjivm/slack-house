# Slack House

This app triggers API functions when Google assistant is given custom commands (created through IFTTT's voice command applet) in conjunction with [Plex webhooks](https://support.plex.tv/articles/115002267687-webhooks/) like `media.play`, `media.pause`, and `media.stop`.

This is a barebones and personal app, but you may find it a useful starting point for your own needs.

# Installation

#### Requirements

- PHP 7+
- [Composer](https://getcomposer.org/)

The only endpoint that must be accessible outside your private network is `/webhook/*` so that IFTTT and Plex can interact with the app.

- Create an [Apache](https://httpd.apache.org/docs/2.4/vhosts/examples.html) or [Nginx](https://www.nginx.com/resources/wiki/start/topics/examples/full/) host file for the app (**important**: the document root should be `/public`)
- Clone or download the repo to the appropriate directory you setup in the previous step (again, ensure the `/public` directory is your document root)
- Install dependencies: `composer install --no-dev`
- Create an `.env` file: `cp .env.example .env`
- Run migrations and seed the db: `./vendor/bin/phinx migrate;./vendor/bin/phinx seed:run`

# Usage

This app interacts with the following products: Plex, LIFX, Harmony, and IFTTT. But it should be trivial to integrate any other products so long as they provide an HTTP API or IFTTT can interact with them.

First set your configuration options in `.env`, and then run the following command to generate the config file:

`php slack config:generate`

**Note**: The config file is rewritten entirely every time the `config:generate` command is called, so do not ever edit this file directly. Instead, edit the `.env` file and then run the command to save your changes to `/config/slackhouse.php`.

Plex players that are allowed to trigger events are verified by `UUID` in the `PLEX_PLAYERS` option (see the `/webhook/plex` endpoint for an example of how this is used). Plex media types allowed to trigger events are listed in the `PLEX_ALLOWED_MEDIA` setting. Both settings can be a single value or a comma separated list of values (e.g., `PLEX_ALLOWED_MEDIA="movie,show"`, or `PLEX_ALLOWED_MEDIA=artist`.)

You can hack up the IFTTT and LIFX API wrappers as needed, they're located in the `/app/Services` directory and use the [Guzzle](https://github.com/guzzle/guzzle) client for requests.

Now open `public/index.php`, you'll see two routes:

```
$app->post('/webhook/ifttt', function ($request, $response) {
    ...
});

$app->post('/webhook/plex', function ($request, $response) {
    ...
});
```

These routes handle the commands sent by Plex webhooks or IFTTT applets. The IFTTT commands are formed like this:

```
{
    "key": "ifttt_maker_key_here",
    "event": "event_name_here",
    "command": "command_name_here"
}
```

So, for example, if I want to activate movie time, I tell my Google Assistant: "It's movie time.", and it triggers the IFTTT webhook for movie time, which is processed here:

```
if ($payload['event'] === 'home_command') {
    // Movie time!
    // 1. Re-enable Plex webhooks so lights respond (in case they are disabled)
    // 2. Activate the LIFX Movie Time scene over 5 seconds
    // 3. Turn on the Kasa smart plug for the TV, receiver, and speakers
    // 4. Tell Harmony to activate the Shield TV activity
    if ($payload['command'] === 'activate_movie_time') {
        $this->get('services')['database']->setSetting('plex_webhooks', 'enabled');
        $lifx->activateScene('movie_time_scene', 5);
        $ifttt->trigger('turn_tv_plug_on');
        $ifttt->trigger('start_shield_activity');

        return $response->withJson($request->getParsedBody()['command'] . ' webhook fired.');
    }
}
```

Plex sends its payloads as JSON in a URL encoded POST request, so we need to run `json_decode()` on the `payload` after we receive it. Plex payloads look like this:

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

So, for example, if I press play on a movie in Plex the webhook will be handled here:

```
// Handle the Play event
if ($payload['event'] === 'media.play') {
    // Power off all the lights in the LIFX Warm Night scene over 30 seconds
    $lifx->activateScene('warm_night_scene', 30, ['power' => 'off']);

    return $response->withJson('Play event handled.');
}
```

As you can see this is pretty straightforward to use and is easily extensible.

Have fun!

Pull requests suggestions are welcome.
