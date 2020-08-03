<?php

namespace Helloprint;

require 'ConfigKafka.php';
require 'Consumer.php';
require 'Producer.php';

class Connector
{
    private $dataJson = NULL;
    private $config = NULL;
    private $consumer = NULL;
    private $producerRequester = NULL;
    private $producerTopicA = NULL;
    private $producerBroker = NULL;

    public function __construct() {
        //Configuration
        $this->config = new ConfigKafka();

        //Consumer
        $this->consumer = new Consumer($this->config, "dbserver1.helloprint.requests");

        //Producers
        $this->producerRequester = new Producer($this->config, "Requester");
        $this->producerTopicA = new Producer($this->config, "TopicA");
        $this->producerBroker = new Producer($this->config, "Broker");

        $this->consumer->topicConsumeStart();
        $this->consume();
    }

    public function consume() {
        while (true) {
            $msg = $this->consumer->topicConsumeMessage();

            if ($msg->payload) {
                echo "Message Received from Connector Source.\n";
                $data = json_decode($msg->payload);

                if (!$this->$dataJson) {
                    $this->$dataJson = json_encode($data->payload->after);

                    $this->produceInitialMessages();
                } else {
                    $this->$dataJson = json_encode($data->payload->after);
                    
                    $this->produceFinalMessage();
                    
                    $this->$dataJson = NULL;
                }
                
            }
        }
    }

    public function produceInitialMessages() {
        //Produce
        $this->producerRequester->sendMessageToTopic($this->$dataJson);

        //Produce
        $this->producerTopicA->sendMessageToTopic($this->$dataJson);
    }

    public function produceFinalMessage() {
        //Produce
        $this->producerBroker->sendMessageToTopic($this->$dataJson);
    }
}

new Connector();