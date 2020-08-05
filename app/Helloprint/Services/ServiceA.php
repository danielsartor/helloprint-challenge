<?php

namespace Helloprint\Services;

class ServiceA
{
    private $consumer = null;
    private $producer = null;
    private $data = null;
    private $names = ["Joao", "Bram", "Gabriel", "Fehim", "Eni", "Patrick", "Micha", "Mirzet", "Liliana", "Sebastien"];

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

                $this->produceMessageToTopic();
            }
        }
    }

    public function produceMessageToTopic()
    {
        //Produce
        $this->producer->sendMessageToTopic($this->getFormattedMessage());
    }

    public function getFormattedMessage()
    {
        //Format Message
        $formatted_message = $this->data->message . $this->names[array_rand($this->names)].". ";

        //Update message
        $this->data->message = $formatted_message;

        return json_encode($this->data);
    }
}