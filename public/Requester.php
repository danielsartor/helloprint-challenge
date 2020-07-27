<?php
    //Producer
    $producer = new \RdKafka\Producer();
    $producer->setLogLevel(LOG_DEBUG);

    //Broker
    if ($producer->addBrokers("kafka:9092") < 1) {
        echo "Failed adding brokers\n";
        exit;
    }

    //TopicA Topic
    $topic = $producer->newTopic("TopicA");

    if (!$producer->getMetadata(false, $topic, 2000)) {
        echo "Failed to get metadata, is broker down?\n";
        exit;
    }

    //Send Message to Topic
    $message = "Hi, ";
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

    echo "Message Sent to TopicA: ".$message."\n";

    //Consume Response
    startConsumingMessage();

    function startConsumingMessage() {
        //Consumer
        $consumer = new \RdKafka\Consumer();
        $consumer->setLogLevel(LOG_DEBUG);

        //Broker
        $consumer->addBrokers("kafka:9092");

        //Requester Topic
        $topic = $consumer->newTopic("Requester");

        //Start Consuming
        $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

        echo "Consuming Topic: Requester\n";
        while (true) {
            //Consume Interval
            $msg = $topic->consume(0, 1000);

            //Message exists
            if ($msg->payload) {
                echo $msg->payload, "\n";
            }
        }
    }