<?php

namespace Helloprint;

require 'ConfigKafka.php';
require 'Consumer.php';
require 'Producer.php';

class Requester
{
    private $config = NULL;
    private $producer = NULL;
    private $consumer = NULL;
    private $message = NULL;

    public function __construct() {
        //Configuration
        $this->config = new ConfigKafka();

        //Producer
        $this->producer = new Producer($this->config, "helloprint.requests");

        //Produce Message
        $this->produceMessageToTopic();

        //Consume Response
        $this->consumeInitialResponse();
    }

    public function produceMessageToTopic() {
        $dataJson = $this->producer->buildJsonMessage($this->getFields(), $this->getMessages());

        $this->producer->sendMessageToTopic($dataJson);
    }

    public function consumeInitialResponse() {
        //Configuration
        $this->config = new ConfigKafka();

        //Consumer
        $this->consumer = new Consumer($this->config, "Requester");
        $this->consumer->topicConsumeStart();

        echo "Consuming Initial Response\n";

        while(true) {
            $msg = $this->consumer->topicConsumeMessage();
            $this->message = json_decode($msg->payload) ?? $this->message;
            if (!$msg) break;
        }

        if ($this->message->id) {
            $this->consumeFinalMessage();
        } else {
            echo 'ERROR: Failed to retrieve ID.';
            exit;
        }
    }

    public function consumeFinalMessage() {
        //Configuration
        $this->config = new ConfigKafka();

        //Consumer
        $this->consumer = new Consumer($this->config, "Broker");
        $this->consumer->topicConsumeStart();

        echo "Consuming Final Response\n";

        $start_time = round(microtime(true) * 1000);

        while(true) {
            $msg = $this->consumer->topicConsumeMessage(50);

            $time = round(microtime(true) * 1000);

            if (($time - $start_time) > 1000) {
                echo "TIMEOUT: No reponse for ID: ".$message->id."\n";
                exit;
            } else if ($msg){
                $data = json_decode($msg->payload);
                if ($this->message->id == $data->id) {
                    echo $data->message;
                    exit;
                }
            }
        }
    }

    public function getMessages() {
        return [
            "id" => uniqid(),
            "message" => "Hi, "
        ];
    }

    public function getFields() {
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

new Requester();