<?php

declare(strict_types=1);

namespace Adrii\Whatsapp\Actions;

use Adrii\Whatsapp\OAuth\Config;

class Webhook
{
    private $config;
    private $uri = "/webhook";

    public function __construct(Config $config)
    {
        $this->config        = $config;
    }

    public function connect($get_params)
    {
        if (!empty($get_params)) {
            $mode       = isset($get_params["hub.mode"])          ? $get_params["hub.mode"] : $get_params["hub_mode"];
            $token      = isset($get_params["hub.verify_token"])  ? $get_params["hub.verify_token"] : $get_params["hub_verify_token"];
            $challenge  = isset($get_params["hub.challenge"])     ? $get_params["hub.challenge"] : $get_params["hub_challenge"];

            if (!empty($mode) && !empty($token)) {
                // Check the mode and token sent are correct
                if ($mode === "subscribe" && $token === $this->token) {
                    // Respond with 200 OK and challenge token from the request
                    $http_code = 200;
                    $data = intval($challenge);
                } else {
                    // Responds with '403 Forbidden' if verify tokens do not match
                    $http_code = 403;
                }
            }
        }

        header('Content-Type: application/json');
        header('HTTP/1.1: ' . $http_code);
        header('Status: ' . $http_code);
        exit(json_encode($data));
    }
}
