# Whatsapp-Api

[![Latest Stable Version](http://img.shields.io/packagist/v/adrii/whatsapp-api.svg)](https://packagist.org/packages/adrii/whatsapp-api)
[![Total Downloads](http://img.shields.io/packagist/dt/adrii/whatsapp-api.svg)](https://packagist.org/packages/adrii/whatsapp-api)
[![License](http://img.shields.io/packagist/l/adrii/whatsapp-api.svg)](https://packagist.org/packages/adrii/whatsapp-api)

WhatsApp-Api is a lightweight library to easily interact with cloud APIs of the WhatsApp Business Platform.
## Installation

Use [Composer](https://getcomposer.org/) to install the library.

```bash
composer require adrii/whatsapp-api
```

### Composer
```php
use Adrii\Whatsapp\Whatsapp;
```

## Configutation

```php
$graph_version    = "v14.0";
$phone_number_id  = "{phone_number_id}";
$access_token     = "{access_token}";
$recipient_id     = "{recipient_id}";

$ws = new Whatsapp($phone_number_id, $access_token, $graph_version);
```

## Webhook
To be alerted when you receive a message or when the status of a message changes, you need to set up a webhook connection point for your app.

This method handles the entire connection request on its own.
The access_token is used to validate the connection.

```php
$ws->webhook()->connect($_GET);
```

## Messages

Send basic text (emojis allowed).
```php
$ws->send_message()->text("Aloha 🍍", $recipient_id);
```

Send message templates defined in the Meta manager.
```php
$ws->send_message()->template("hello_world", $recipient_id );
```

Sends a location, through a longitude, latitude and an address.
```php
$ws->send_message()->location("41.403191", "2.174840", "La Sagrada Família", "C/ De Mallorca, 401, 08013 Barcelona", $recipient_id);
```

Send an image, as a link or as multimedia
```php
$ws->send_message()->image("https://avatars.githubusercontent.com/u/29653964?v=4", $recipient_id);

$ws->send_message()->image("https://avatars.githubusercontent.com/u/29653964?v=4", $recipientId, "individual", null, false);
```

Send an audio, as a link or as multimedia
```php
$ws->send_message()->audio("https://file-examples.com/storage/fe783a5cbb6323602a28c66/2017/11/file_example_MP3_1MG.mp3", $recipient_id);

$ws->send_message()->audio("https://file-examples.com/storage/fe783a5cbb6323602a28c66/2017/11/file_example_MP3_1MG.mp3", $recipientId, "individual", null, false);
```

Send an video, as a link or as multimedia
```php
$ws->send_message()->video("https://file-examples.com/storage/fe783a5cbb6323602a28c66/2017/04/file_example_MP4_480_1_5MG.mp4", $recipient_id);

$ws->send_message()->video("https://file-examples.com/storage/fe783a5cbb6323602a28c66/2017/04/file_example_MP4_480_1_5MG.mp4", $recipientId, "individual", null, false);

```

Send an document, as a link or as multimedia
```php
$ws->send_message()->document("https://file-examples.com/storage/fe783a5cbb6323602a28c66/2017/10/file-sample_150kB.pdf", $recipient_id);

$ws->send_message()->document("https://file-examples.com/storage/fe783a5cbb6323602a28c66/2017/10/file-sample_150kB.pdf", $recipientId, "individual", null, false);

```

Send an interactive message with reply buttons
```php
$button = [
        "header" => "Header",
        "body"   => "Body",
        "footer" => "Footer",
        "action" => [
            "buttons" => [
                [
                    "type" => "reply",
                    "reply" => [
                        "id" => "UNIQUE_BUTTON_ID_1",
                        "title" => "BUTTON_TITLE_1"
                    ]
                ],
                [
                    "type" => "reply",
                    "reply" => [
                        "id" => "UNIQUE_BUTTON_ID_2",
                        "title" => "BUTTON_TITLE_2"
                    ]
                ]
            ]
        ]
    ];

$ws->send_message()->interactive($button, $recipient_id, "button");

```


Send an interactive message with list of buttons
```php
$list = [
    "header" => "Test Header",
    "body"   => "Test Body",
    "footer" => "Test Footer",
    "action" => [
        "button" => "BUTTON_TEXT",
        "sections" => [
            [
                "title" => "SECTION_1_TITLE",
                "rows" =>
                [
                    [
                        "id" => "SECTION_1_ROW_1_ID",
                        "title" => "SECTION_1_ROW_1_TITLE",
                        "description" => "SECTION_1_ROW_1_DESCRIPTION"
                    ],
                    [
                        "id" => "SECTION_1_ROW_2_ID",
                        "title" => "SECTION_1_ROW_2_TITLE",
                        "description" => "SECTION_1_ROW_2_DESCRIPTION"
                    ]
                ]
            ],
            [
                "title" => "SECTION_2_TITLE",
                "rows" => [
                    [
                        "id" => "SECTION_2_ROW_1_ID",
                        "title" => "SECTION_2_ROW_1_TITLE",
                        "description" => "SECTION_2_ROW_1_DESCRIPTION"
                    ],
                    [
                        "id" => "SECTION_2_ROW_2_ID",
                        "title" => "SECTION_2_ROW_2_TITLE",
                        "description" => "SECTION_2_ROW_2_DESCRIPTION"
                    ]
                ]
            ]
        ]
    ]
];

$ws->send_message()->interactive($list, $recipient_id, "list");

```


# Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

# License
[MIT](https://github.com/AdrianVillamayor/Whatsapp-Api/blob/master/LICENSE)

### Thanks for your help! 🎉
