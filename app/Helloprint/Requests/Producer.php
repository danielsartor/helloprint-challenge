<?php

namespace Helloprint\Requests;

class Producer
{
    private $configKafka;
    private $brokerKafka;
    private $producer;
    private $topic;
    private $topicName;

    public function __construct($config, $topicName)
    {
        $this->configKafka = $config->getConfig();
        $this->brokerKafka = $config::BROKER_ADDRESS;
        $this->topicName = $topicName;
        //Producer
        $this->producer = new \RdKafka\Producer($this->configKafka);

        //Broker
        if ($this->producer->addBrokers($this->brokerKafka) < 1) {
            echo "Failed adding brokers\n";
            exit;
        }
    }

    public function sendMessageToTopic($message)
    {
        $this->topic = $this->producer->newTopic($this->topicName);

        if (!$this->producer->getMetadata(false, $this->topic, 2000)) {
            echo "Failed to get metadata, is broker down?\n";
            exit;
        }
        
        $this->topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        echo "Produced message $message for Topic $this->topicName \n\n";
    }
}