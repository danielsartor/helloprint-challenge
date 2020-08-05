<?php

namespace Helloprint\Services;

use Helloprint\Utils\Utils as Utils;

class Requester
{
    private $producer = null;
    private $consumerRequester = null;
    private $consumerBroker = null;
    private $message = null;

    public function __construct($producer, $consumerRequester, $consumerBroker)
    {
        //Producer
        $this->producer = $producer;

        //Consumers
        $this->consumerRequester = $consumerRequester;
        $this->consumerBroker = $consumerBroker;

        //Produce Message
        $this->produceMessageToTopic();

        //Consume Response
        $this->consumeInitialResponse();
    }

    public function produceMessageToTopic()
    {
        $dataJson = Utils::buildJsonMessage("requests", $this->getFields(), $this->getMessages());

        $this->producer->sendMessageToTopic($dataJson);
    }

    public function consumeInitialResponse()
    {
        $this->consumerRequester->topicConsumeStart();

        while (true) {
            $msg = $this->consumerRequester->topicConsumeMessage();
            $this->message = json_decode($msg->payload) ?? $this->message;
            if (!$msg) {
                break;
            }
        }

        if ($this->message->id) {
            echo "Received ID from Connector \n\n";
            $this->consumeFinalMessage();
        } else {
            echo 'ERROR: Failed to retrieve ID.';
            exit;
        }
    }

    public function consumeFinalMessage()
    {
        $this->consumerBroker->topicConsumeStart();

        $start_time = round(microtime(true) * 1000);

        while (true) {
            $msg = $this->consumerBroker->topicConsumeMessage(50);

            $time = round(microtime(true) * 1000);

            if (($time - $start_time) > 1000) {
                echo "TIMEOUT: No reponse for ID: ".$message->id."\n";
                exit;
            } elseif ($msg) {
                $data = json_decode($msg->payload);
                if ($this->message->id == $data->id) {
                    echo $data->message;
                    exit;
                }
            }
        }
    }

    public function getMessages()
    {
        return [
            "id" => uniqid(),
            "message" => "Hi, "
        ];
    }

    public function getFields()
    {
        return [
            [
                "type" => "string",
                "optional" => false,
                "field" => "id"
            ],[
                "type" => "string",
                "optional" => false,
                "field" => "message"
            ]
        ];
    }
}