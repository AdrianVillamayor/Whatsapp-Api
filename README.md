# Whatsapp-Api

[![Latest Stable Version](http://img.shields.io/packagist/v/adrii/whatsapp-api.svg)](https://packagist.org/packages/adrii/whatsapp-api)
[![Total Downloads](http://img.shields.io/packagist/dt/adrii/whatsapp-api.svg)](https://packagist.org/packages/adrii/whatsapp-api)
[![License](http://img.shields.io/packagist/l/adrii/whatsapp-api.svg)](https://packagist.org/packages/adrii/whatsapp-api)

WhatsApp-Api is a lightweight library to easily interact with cloud APIs of the [WhatsApp Business Platform](https://developers.facebook.com/docs/whatsapp/business-management-api/get-started).



| INDEX |
|-------|
| [Installation](https://github.com/AdrianVillamayor/Whatsapp-Api#installation)   |
| [Configutation](https://github.com/AdrianVillamayor/Whatsapp-Api#configutation) |
| [Webhook](https://github.com/AdrianVillamayor/Whatsapp-Api#webhook)             |
| [Messages](https://github.com/AdrianVillamayor/Whatsapp-Api#messages)           |
| [License](https://github.com/AdrianVillamayor/Whatsapp-Api#license)             |
 

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
$graph_version    = "v15.0";
$phone_number_id  = "{phone_number_id}";
$access_token     = "{access_token}";
$recipient_id     = "{recipient_id}";

$ws = new Whatsapp($phone_number_id, $access_token, $graph_version);
```
</br>

## Webhook
To be alerted when you receive a message or when the status of a message changes, you need to set up a webhook connection point for your app.

This method handles the entire connection request on its own.
The access_token is used to validate the connection.

```php
$ws->webhook()->connect($_GET);
```
</br>

## Messages

| Status | Type  |
| ------ | ----------------------------------------------------------------------------- |
|   ✅   | [Text](https://github.com/AdrianVillamayor/Whatsapp-Api#text)                 |
|   ✅   | [Template](https://github.com/AdrianVillamayor/Whatsapp-Api#template)         |
|   ✅   | [Location](https://github.com/AdrianVillamayor/Whatsapp-Api#location)         |
|   ✅   | [Contact](https://github.com/AdrianVillamayor/Whatsapp-Api#contact)           |
|   ✅   | [Media](https://github.com/AdrianVillamayor/Whatsapp-Api#media)               |
|   ✅   | [Interactive](https://github.com/AdrianVillamayor/Whatsapp-Api#interactive)   |

### Text
Send basic text (emojis allowed).
```php
$ws->send_message()->text("Aloha 🍍", $recipient_id);
```

### Template
Send message templates defined in the Meta manager.
```php
$ws->send_message()->template("hello_world", $recipient_id);
```

Send message templates defined in the Meta manager with parameters
```php
$component_header = array(
    "type" => "header",
    "parameters" => array(
        array(
            "type" => "image",
            "image" => array(
                "link" => "https://avatars.githubusercontent.com/u/29653964?v=4"
            )
        ),
    )
);

$component_body = array(
    "type" => "body",
    "parameters" => array(
        array(
            "type" => "text",
            "text" => "Adrii 🍍"
        )
    )
);

$ws->send_message()->addComponent($component_header, $component_body);

$response = $ws->send_message()->template("sample_purchase_feedback", $recipient_id);
```

### Location

Sends a location, through a longitude, latitude and an address.
```php
$ws->send_message()->location("41.403191", "2.174840", "La Sagrada Família", "C/ De Mallorca, 401, 08013 Barcelona", $recipient_id);
```

### Contact

Send a contact message

The name is the only required parameter, the other data are optional.
```php
    $contact = array(
        "addresses" => array(
            array(
                "city"          => "city name",
                "country"       => "country name",
                "country_code"  => "code",
                "state"         => "Contact's State",
                "street"        => "Contact's Street",
                "type"          => "Contact's Address Type",
                "zip"           => "Contact's Zip Code"
            )
        ),

        "birthday" => "14-02-1997",
        "emails" => array(
            array(
                "email" => "email",
                "type" => "HOME"
            ),
            array(
                "email" => "email",
                "type" => "WORK"
            )
        ),
        "name" => array(
            "formatted_name" => "formatted name value",
            "middle_name" => "last name value",
        ),
        "phones" => array(
            array(
                "phone" => "654034823",
                "type" => "MAIN"
            ),
            array(
                "phone" => "Phone number",
                "type" => "HOME"
            ),
            array(
                "phone" => "Phone number",
                "type" => "WORK"
            )
        ),
        "urls" => array(
            array(
                "url" => "some url",
                "type" => "WORK"
            )
        )
    );


$ws->send_message()->addContact($contact);

$response = $ws->send_message()->contact($recipient_id);

```

You can concatenate as many contacts as you want
```php
    $ws->send_message()->addContact($contact_0, $contact_1, ...);
```

### Media

Send a media, as a link or id
```php
$ws->send_message()->media("image", "https://avatars.githubusercontent.com/u/29653964?v=4", $recipient_id);

$ws->send_message()->media("video", "https://file-examples.com/storage/fe4658769b6331540b05587/2017/04/file_example_MP4_480_1_5MG.mp4", $recipient_id);

$ws->send_message()->media("document", "https://file-examples.com/storage/fe4658769b6331540b05587/2017/10/file-sample_150kB.pdf", $recipient_id);

$ws->send_message()->media("audio", "https://file-examples.com/storage/fe4658769b6331540b05587/2017/11/file_example_MP3_700KB.mp3", $recipient_id);

$ws->send_message()->media("sticker", "https://img-03.stickers.cloud/packs/210a9e68-b249-405f-8ea1-9af015ef074a/webp/c5b7bded-e0f0-4f79-86aa-ffd825aba680.webp", $recipient_id);
```

Describes the specified image or video media with caption.
```php
$ws->send_message()->media("image", "https://avatars.githubusercontent.com/u/29653964?v=4", $recipient_id, "individual", true, "your-image-caption-to-be-sent");

$ws->send_message()->media("video", "https://file-examples.com/storage/fe4658769b6331540b05587/2017/04/file_example_MP4_480_1_5MG.mp4", $recipient_id, "individual", true, "your-video-caption-to-be-sent");
```

Describes the filename for the specific document.
```php
$ws->send_message()->media("document", "https://file-examples.com/storage/fe4658769b6331540b05587/2017/10/file-sample_150kB.pdf", $recipient_id, "individual", true, null, "example_filename.pdf");
```

### Interactive

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

> ### [Data examples for each message](https://developers.facebook.com/docs/whatsapp/on-premises/webhooks/inbound#mentions)

</br>

# Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

# License
[MIT](https://github.com/AdrianVillamayor/Whatsapp-Api/blob/master/LICENSE)

### Thanks for your help! 🎉
