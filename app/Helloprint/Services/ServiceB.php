<?php

namespace Helloprint\Services;

use Helloprint\Utils\Utils as Utils;
use Helloprint\Utils\ConfigKafka as ConfigKafka;
use Helloprint\Requests\Producer as Producer;
use Helloprint\Requests\CoNsumer as Consumer;

class ServiceB
{
    private $bye = "Bye.";

    public function __construct()
    {
        //Configuration
        $this->config = new ConfigKafka();

        //Consumer
        $this->consumer = new Consumer($this->config, "TopicB");

        //Producer
        $this->producer = new Producer($this->config, "helloprint.requests");

        $this->consumer->topicConsumeStart();
        $this->consume();
    }

    public function consume()
    {
        while (true) {
            $msg = $this->consumer->topicConsumeMessage();

            if ($msg->payload) {
                $this->data = json_decode($msg->payload);

                echo "Message Received: ".$this->data->message."\n\n";
                
                $this->produceMessageToConnector();
            }
        }
    }

    public function produceMessageToConnector()
    {
        //Build Json with formatted message
        $dataJson = Utils::buildJsonMessage($this->getFields(), $this->getMessages());

        //Produce
        $this->producer->sendMessageToTopic($dataJson);
    }

    public function getMessages()
    {
        return [
            "id" => $this->data->id,
            "message" => $this->data->message . $this->bye
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