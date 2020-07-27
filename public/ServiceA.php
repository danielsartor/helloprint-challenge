<?php
    $message = "";
    $names = ["Joao", "Bram", "Gabriel", "Fehim", "Eni", "Patrick", "Micha", "Mirzet", "Liliana", "Sebastien"];

    //Consumer
    $consumer = new \RdKafka\Consumer();
    $consumer->setLogLevel(LOG_DEBUG);

    //Broker
    $consumer->addBrokers("kafka:9092");

    //TopicA Topic
    $topic = $consumer->newTopic("TopicA");

    //Start Consuming
    $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

    echo "Consuming Topic: TopicA\n";
    while (true) {
        //Consume Interval
        $msg = $topic->consume(0, 1000);

        //Message exists
        if ($msg->payload) {
            $message = $msg->payload;
            echo "Message Received: ".$msg->payload."\n";
            sendMessage($message, $names);
        }
    }

    function sendMessage($message, $names) {
        //Producer
        $producer = new \RdKafka\Producer();
        $producer->setLogLevel(LOG_DEBUG);

        //Broker
        if ($producer->addBrokers("kafka:9092") < 1) {
            echo "Failed adding brokers\n";
            exit;
        }

        //TopicA Topic
        $topic = $producer->newTopic("TopicB");

        if (!$producer->getMetadata(false, $topic, 2000)) {
            echo "Failed to get metadata, is broker down?\n";
            exit;
        }

        //Send Message to Topic
        $formatted_message = $message . $names[array_rand($names)].". ";
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $formatted_message);

        echo "Message Sent to TopicB: ".$formatted_message."\n";
    }