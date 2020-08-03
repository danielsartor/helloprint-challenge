<?php

namespace Helloprint\Services;

use Helloprint\Utils\ConfigKafka as ConfigKafka;
use Helloprint\Requests\Producer as Producer;
use Helloprint\Requests\Consumer as Consumer;

class Connector
{
    private $dataJson = null;
    private $config = null;
    private $consumer = null;
    private $producerRequester = null;
    private $producerTopicA = null;
    private $producerBroker = null;

    public function __construct()
    {
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

    public function consume()
    {
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
                    
                    $this->$dataJson = null;
                }
            }
        }
    }

    public function produceInitialMessages()
    {
        //Produce
        $this->producerRequester->sendMessageToTopic($this->$dataJson);

        //Produce
        $this->producerTopicA->sendMessageToTopic($this->$dataJson);
    }

    public function produceFinalMessage()
    {
        //Produce
        $this->producerBroker->sendMessageToTopic($this->$dataJson);
    }
}