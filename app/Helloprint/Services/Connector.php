<?php

namespace Helloprint\Services;

class Connector
{
    private $dataJson = null;
    private $consumer = null;
    private $producerRequester = null;
    private $producerTopicA = null;
    private $producerBroker = null;

    public function __construct($consumer, $producerRequester, $producerTopicA, $producerBroker)
    {
        //Consumer
        $this->consumer = $consumer;

        //Producers
        $this->producerRequester = $producerRequester;
        $this->producerTopicA = $producerTopicA;
        $this->producerBroker = $producerBroker;

        $this->consume();
    }
    
    public function consume()
    {
        $this->consumer->topicConsumeStart();
        while (true) {
            $msg = $this->consumer->topicConsumeMessage();

            if ($msg->payload) {
                echo "Message Received from Connector Source. \n\n";
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