<?php
    $message = "";
    $bye = "Bye.";

    //Configuration
    $conf = new \RdKafka\Conf();
    $conf->set("bootstrap.servers", "kafka:9094");

    //Consumer
    $consumer = new \RdKafka\Consumer($conf);
    $consumer->setLogLevel(LOG_DEBUG);

    //Broker
    $consumer->addBrokers("kafka:9094");

    //TopicB Topic
    $topic = $consumer->newTopic("TopicB");

    //Start Consuming
    $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

    echo "Consuming Topic: TopicB\n";
    while (true) {
        //Consume Interval
        $msg = $topic->consume(0, 1000);

        //Message exists
        if ($msg->payload) {
            $message = $msg->payload;
            echo "Message Received: ".$msg->payload."\n";

            //Producer
            $producer = new \RdKafka\Producer($conf);
            $producer->setLogLevel(LOG_DEBUG);

            sendMessage($producer, $message, $bye);
        }
    }

    function sendMessage($producer, $message, $bye) {
        //Broker
        if ($producer->addBrokers("kafka:9094") < 1) {
            echo "Failed adding brokers\n";
            exit;
        }

        //TopicA Topic
        $topic = $producer->newTopic("Requester");

        if (!$producer->getMetadata(false, $topic, 2000)) {
            echo "Failed to get metadata, is broker down?\n";
            exit;
        }

        //Send Message to Topic
        $formatted_message = $message . $bye;
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $formatted_message);

        echo "Message Sent to Requester: ".$formatted_message."\n";
    }