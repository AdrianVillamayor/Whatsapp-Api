<?php

declare(strict_types=1);

namespace Adrii\Whatsapp\Actions;

use Adrii\Whatsapp\OAuth\Config;
use Adrii\Whatsapp\Http\Request;
use Exception;

class Messages
{
    private $config;
    private $uri = "/messages";
    private $contacts = [];
    private $components = [];

    public function __construct(Config $config)
    {
        $this->config        = $config;
        $this->http_request  = new Request();
    }

    /**
     * https://developers.facebook.com/docs/whatsapp/on-premises/reference/messages?locale=en_US#text-object
     * @param string $message
     * @param string $recipientId
     * @param string $recipientType
     * @param bool $previewUrl
     * @return mixed
     */
    public function text(string $message, string $recipientId, string $recipientType = "individual", bool $previewUrl = true)
    {
        $data = [
            "messaging_product" => "whatsapp",
            "recipient_type"    => $recipientType,
            "to"                => $recipientId,
            "type"              => "text",
            "text"              => ["preview_url" => $previewUrl, "body" => $message]
        ];

        $url     = $this->config->getApiUri($this->uri);
        $bearer  = $this->config->getAccessToken();
        $headers = ["Authorization" => "Bearer {$bearer}"];

        $response = $this->http_request->post($url, $data, $headers);

        return $response;
    }

    /**
     * https://developers.facebook.com/docs/whatsapp/cloud-api/guides/send-message-templates#media-based
     * @param array ...$components
     * @return array
     */
    public function addComponent(array ...$components)
    {
        if (empty($components)) throw new \Exception("Component cannot be empty");

        foreach ($components as $component) {
            $this->components[] = $component;
        }

        return $this->components;
    }

    /**
     * https://developers.facebook.com/docs/whatsapp/on-premises/reference/messages?locale=en_US#template-object
     * @param string $template
     * @param string $recipientId
     * @param string $lang
     * @param ?array $components
     * @return mixed
     */
    public function template(string $template, string $recipientId, string $lang = "en_US")
    {
        $data = [
            "messaging_product" => "whatsapp",
            "to"                => $recipientId,
            "type"              => "template",
            "template"          => [
                "name"      => $template,
                "language"  => ["code" => $lang]
            ],
        ];

        if (!empty($this->components)) {
            $data['template']["components"] =  $this->components;
            $this->components = [];
        }

        $url     = $this->config->getApiUri($this->uri);
        $bearer  = $this->config->getAccessToken();
        $headers = ["Authorization" => "Bearer {$bearer}"];

        $response = $this->http_request->post($url, $data, $headers);

        return $response;
    }

    /**
     * https://developers.facebook.com/docs/whatsapp/on-premises/reference/messages?locale=en_US#location-object
     * @param string $lat
     * @param string $long
     * @param string $name
     * @param string $address
     * @param string $recipientId
     * @return mixed
     */
    public function location(string $lat, string $long, string $name, string $address, string $recipientId)
    {
        $data = [
            "messaging_product" => "whatsapp",
            "to"                => $recipientId,
            "type"              => "location",
            "location"          => [
                "latitude"  => $lat,
                "longitude" => $long,
                "name"      => $name,
                "address"   => $address,
            ],
        ];

        $url     = $this->config->getApiUri($this->uri);
        $bearer  = $this->config->getAccessToken();
        $headers = ["Authorization" => "Bearer {$bearer}"];


        $response = $this->http_request->post($url, $data, $headers);

        return $response;
    }


    /**
     * https://developers.facebook.com/docs/whatsapp/cloud-api/guides/send-message-templates#media-based
     * @param array ...$components
     * @return array
     */
    public function addContact(array ...$contacts): array
    {
        if (empty($contacts)) throw new \Exception("Contacts cannot be empty");

        foreach ($contacts as $contact) {
            if (empty($contact)) throw new \Exception("Contact cannot be empty");

            $obj = array();

            if (!isset($contact['name']) && empty($contact['name'])) {
                throw new \Exception("Contact name is required");
            }

            if (!isset($contact['name']['formatted_name']) || (!isset($contact['name']['first_name']) && !isset($contact['name']['middle_name']))) {
                throw new \Exception("Contact name, formatted_name and one of the following parameters are required: first_namestring or middle_namestring.");
            }

            $obj['name'] = $contact['name'];

            unset($contact['name']);

            if (isset($contact['birthday']) && !empty($contact['birthday'])) {
                $timestamp = \strtotime($contact['birthday']);

                if ($timestamp) {
                    $birthday = date("Y-m-d", \strtotime($contact['birthday']));

                    $obj['birthday'] = $birthday;

                    unset($contact['birthday']);
                } else {
                    throw new \Exception("Contact birthday format is not correct, YYYYY-MM-DD formatted string.");
                }
            }

            $keys = array("addresses", "birthday", "name", "org", "phones", "urls");

            foreach ($contact as $key => $value) {
                if (in_array($key, $keys) && !empty($contact[$key])) {

                    if ($key == "addresses" || $key == "phones" || $key == "urls") {
                        if (!isset($contact[$key][0]) || is_array($contact[$key][0]) === false) {
                            throw new \Exception("Contact {$key} must be an array.");
                        }
                    }

                    $obj[$key] = $value;
                }
            }

            $this->contacts[] = $obj;
        }

        return $this->contacts;
    }


    /**
     * https://developers.facebook.com/docs/whatsapp/on-premises/reference/messages?locale=en_US#contacts-object
     * @param string $recipientId
     * @return mixed
     */

    public function contact(string $recipientId)
    {
        try {
            if (empty($this->contacts)) throw new \Exception("Contacts cannot be empty");

            $data = [
                "messaging_product" => "whatsapp",
                "to"                => $recipientId,
                "type"              => "contacts",
                "contacts"          => $this->contacts
            ];

            $url     = $this->config->getApiUri($this->uri);
            $bearer  = $this->config->getAccessToken();
            $headers = ["Authorization" => "Bearer {$bearer}"];


            $response = $this->http_request->post($url, $data, $headers);

            return $response;
        } catch (Exception $e) {
            echo 'Caught Exception: ',  $e->getMessage(), "\n";
        }
    }

    /**
     * https://developers.facebook.com/docs/whatsapp/on-premises/reference/media
     * @param string $type [audio, document, image, sticker, or video]
     * @param string $media
     * @param string $recipientId
     * @param string $recipientType
     * @param ?string $caption Describes the specified image or video media.
     * @param ?string $filename Describes the filename for the specific document.
     * @param bool $link only with HTTP/HTTPS URLs
     * @return mixed
     */
    public function media(string $type, string $media, string $recipientId, string $recipientType = "individual",  bool $link = true, ?string $caption = null, ?string $filename = null)
    {
        try {
            $required_types = ["audio", "document", "image", "sticker", "video"];

            if (empty($type) || !in_array($type, $required_types)) throw new \Exception("Type {$type} is not supported.");

            if ($link) {
                $parse_url = parse_url($media);
                if (isset($parse_url['scheme']) === false || ($parse_url['scheme'] !== "https" && $parse_url['scheme'] !== "http")) throw new \Exception("The protocol and URL of the media to be sent. Use only with HTTP/HTTPS URLs.");
            }

            $obj_id = ($link) ? "link" : "id";

            $data = [
                "messaging_product" => "whatsapp",
                "recipient_type"    => $recipientType,
                "to"                => $recipientId,
                "type"              => $type,
                $type               => [
                    $obj_id     => $media
                ]
            ];

            switch ($type) {
                case 'image':
                case 'video':
                    if (!empty($caption)) $data[$type]['caption'] = $caption;
                    break;

                case 'document':
                    if (!empty($filename)) $data[$type]['filename'] = $filename;
                    break;
            }

            $url     = $this->config->getApiUri($this->uri);
            $bearer  = $this->config->getAccessToken();
            $headers = ["Authorization" => "Bearer {$bearer}"];

            $response = $this->http_request->post($url, $data, $headers);

            return $response;
        } catch (Exception $e) {
            echo 'Caught Exception: ',  $e->getMessage(), "\n";
        }
    }


    /**
     * @param array $button
     * @return array
     */
    public function createInteraction(array $button, string $type): array
    {
        $elem =  [
            "type"      => $type,
            "body"      => ["text" => $button["body"]],
            "action"    => $button["action"]
        ];

        if (isset($button['header'])) {
            $elem["header"] = ["type" => "text", "text" => $button["header"]];
        }

        if (isset($button['footer'])) {
            $elem["footer"] = ["text" => $button["footer"]];
        }

        return $elem;
    }

    /**
     * https://developers.facebook.com/docs/whatsapp/on-premises/reference/messages?locale=en_US#interactive-object
     * @param $button
     * @param $recipientId
     * @return mixed
     */
    public function interactive(array $button, string $recipientId, string $type = "list")
    {
        $data = [
            "messaging_product" => "whatsapp",
            "to"                => $recipientId,
            "type"              => "interactive",
            "interactive"       => $this->createInteraction($button, $type),
        ];

        $url     = $this->config->getApiUri($this->uri);
        $bearer  = $this->config->getAccessToken();
        $headers = ["Authorization" => "Bearer {$bearer}"];

        $response = $this->http_request->post($url, $data, $headers);

        return $response;
    }
}
