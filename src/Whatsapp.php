<?php

namespace Adrii\Whatsapp;

use Exception;

use Adrii\Whatsapp\OAuth\Config;

use Adrii\Whatsapp\Actions\Messages;
use Adrii\Whatsapp\Actions\Webhook;

class Whatsapp
{
    private $config;
    private $messages;
    private $webhook;

    /**
     * @param string $phone_number_id
     * @param string $access_token
     * @param string $api_version
     * @throws Exception
     */
    public function __construct(string $phone_number_id, string $access_token, string $api_version = "v14.0")
    {
        if (empty($phone_number_id) || empty($access_token)) {
            throw new Exception('phone_number_id and access_token are required');
        }

        $this->config       = new Config($phone_number_id, $access_token, $api_version);

        $this->messages     = new Messages($this->config);
        $this->webhook      = new Webhook($this->config);
    }

    public function send_message()
    {
        return $this->messages;
    }
    
    public function webhook()
    {
        return $this->webhook;
    }


    /**
     * @param $data
     * @return mixed
     */
    public function preprocess($data)
    {
        return $data["entry"][0]["changes"][0]["value"];
    }

    /**
     * @param $data
     * @return mixed|void
     */
    public function getMobile($data)
    {
        $data = $this->preprocess($data);
        if (array_key_exists("contacts", $data)) {
            return $data["contacts"][0]["wa_id"];
        }
    }

    /**
     * @param $data
     * @return mixed|void
     */
    public function getName($data)
    {
        $contact = $this->preprocess($data);
        if ($contact) {
            return $contact["contacts"][0]["profile"]["name"];
        }
    }

    /**
     * @param $data
     * @return mixed|void
     */
    public function getMessage($data)
    {
        $data = $this->preprocess($data);
        if (array_key_exists("messages", $data)) {
            return $data["messages"][0]["text"]["body"];
        }
    }

    /**
     * @param $data
     * @return mixed|void
     */
    public function getMessageId($data)
    {
        $data = $this->preprocess($data);
        if (array_key_exists("messages", $data)) {
            return $data["messages"][0]["id"];
        }
    }

    /**
     * @param $data
     * @return mixed|void
     */
    public function getMessageTimestamp($data)
    {
        $data = $this->preprocess($data);
        if (array_key_exists("messages", $data)) {
            return $data["messages"][0]["timestamp"];
        }
    }

    /**
     * @param $data
     * @return mixed|void
     */
    public function getInteractiveResponse($data)
    {
        $data = $this->preprocess($data);
        if (array_key_exists("messages", $data)) {
            return $data["messages"][0]["interactive"]["list_reply"];
        }
    }

    /**
     * @param $data
     * @return mixed|void
     */
    public function getMessageType($data)
    {
        $data = $this->preprocess($data);
        if (array_key_exists("messages", $data)) {
            return $data["messages"][0]["type"];
        }
    }

    /**
     * @param $data
     * @return mixed|void
     */
    public function getDelivery($data)
    {
        $data = $this->preprocess($data);
        if (array_key_exists("statuses", $data)) {
            return $data["statuses"][0]["status"];
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function changedField($data)
    {
        return $data["entry"][0]["changes"][0]["field"];
    }
}
