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
     * https://developers.facebook.com/docs/whatsapp/on-premises/reference/messages?locale=en_US#template-object
     * @param string $template
     * @param string $recipientId
     * @param string $lang
     * @param ?array $components
     * @return mixed
     */
    public function template(string $template, string $recipientId, string $lang = "en_US", ?array $components = null)
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

        $url     = $this->config->getApiUri($this->uri);
        $bearer  = $this->config->getAccessToken();
        $headers = ["Authorization" => "Bearer {$bearer}"];

        $response = $this->http_request->post($url, $data, $headers);

        return $response;
    }

    /**
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

    //create button

    /**
     * @param array $button
     * @return array
     */
    public function createInteraction(array $button, string $type)
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
