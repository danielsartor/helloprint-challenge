<?php

namespace Helloprint\Services;

use Helloprint\Utils\Utils as Utils;

class ServiceB
{
    private $consumer = null;
    private $producer = null;
    private $bye = "Bye.";

    public function __construct($consumer, $producer)
    {
        //Consumer
        $this->consumer = $consumer;

        //Producer
        $this->producer = $producer;

        $this->consume();
    }
    
    public function consume()
    {
        $this->consumer->topicConsumeStart();

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
        $dataJson = Utils::buildJsonMessage("requests", $this->getFields(), $this->getMessages());

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