<?php 

namespace Helloprint\Requests;

class Consumer
{
    private $configKafka;
    private $brokerKafka;
    private $consumer;
    private $topic;
    private $topicName;

    public function __construct($config, $topicName)
    {
        $this->configKafka = $config->getConfig();
        $this->brokerKafka = $config::BROKER_ADDRESS;
        $this->topicName = $topicName;

        //Consumer
        $this->consumer = new \RdKafka\Consumer($this->configKafka);

        //Broker
        $this->consumer->addBrokers($this->brokerKafka);
    }

    public function topicConsumeStart($offset = RD_KAFKA_OFFSET_BEGINNING, $partition = 0)
    {
        $this->topic = $this->consumer->newTopic($this->topicName);

        $this->topic->consumeStart($partition, $offset);

        echo "Consuming for Topic $this->topicName has started\n";
    }

    public function topicConsumeMessage($timeout = 1000, $partition = 0)
    {
        return $this->topic->consume($partition, $timeout);
    }
}