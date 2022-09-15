<?php

declare(strict_types=1);

namespace Adrii\Whatsapp\OAuth;

class Config
{
    const API_URL      = 'https://graph.facebook.com/';

    private $phone_number_id = null;
    private $access_token = null;
    private $api_version = null;

    public function __construct($phone_number_id, $access_token, $api_version = "v14.0")
    {
        $this->phone_number_id  = $phone_number_id;
        $this->access_token     = $access_token;
        $this->api_version      = $api_version;
    }

    public function getPhoneNumberId(): int
    {
        return $this->phone_number_id;
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    public function getApiVersion(): string
    {
        return $this->api_version;
    }

    public function getApiUri(string $url = ""): ?string
    {
        return self::API_URL . $this->getApiVersion() . "/" . $this->getPhoneNumberId() . $url;
    }
}
