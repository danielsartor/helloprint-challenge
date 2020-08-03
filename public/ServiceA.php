<?php

namespace Helloprint;

require 'ConfigKafka.php';
require 'Consumer.php';
require 'Producer.php';

class ServiceA
{
    private $names = ["Joao", "Bram", "Gabriel", "Fehim", "Eni", "Patrick", "Micha", "Mirzet", "Liliana", "Sebastien"];
    private $data = NULL;

    public function __construct() {
        //Configuration
        $this->config = new ConfigKafka();

        //Consumer
        $this->consumer = new Consumer($this->config, "TopicA");

        //Producer
        $this->producer = new Producer($this->config, "TopicB");

        $this->consumer->topicConsumeStart();
        $this->consume();
    }

    public function consume() {
        while (true) {
            $msg = $this->consumer->topicConsumeMessage();

            if ($msg->payload) {
                $this->data = json_decode($msg->payload);
                
                echo "Message Received: ".$this->data->message."\n";

                $this->produceMessageToTopicB();
            }
        }
    }

    public function produceMessageToTopicB() {
        
        //Format Message
        $formatted_message = $this->data->message . $this->names[array_rand($this->names)].". ";

        var_dump($this->data, $formatted_message);
        //Update message
        $this->data->message = $formatted_message;

        //Encode to Json
        $dataJson = json_encode($this->data);

        //Produce
        $this->producer->sendMessageToTopic($dataJson);
    }
}

new ServiceA();