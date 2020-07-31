<?php
    require_once('helpers/helpers.php');

    $names = ["Joao", "Bram", "Gabriel", "Fehim", "Eni", "Patrick", "Micha", "Mirzet", "Liliana", "Sebastien"];

    //Producer
    $producer = new \RdKafka\Producer($conf);
    $producer->setLogLevel(LOG_DEBUG);

    //Consumer
    $consumer = new \RdKafka\Consumer($conf);
    $consumer->setLogLevel(LOG_DEBUG);

    //Broker
    $consumer->addBrokers("kafka:9094");

    //TopicA Topic
    $topic = $consumer->newTopic("TopicA");

    //Start Consuming
    $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

    echo "Consuming from TopicA\n";
    while (true) {
        $msg = $topic->consume(0, 1000);

        if (null === $msg || $msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
            continue;
        } elseif ($msg->err) {
            echo $msg->errstr(), "\n";
            break;
        } else {
            $data = json_decode($msg->payload);
            echo "Message Received: ".$data->message."\n";

            //Format Message
            $formatted_message = $data->message . $names[array_rand($names)].". ";

            $data->message = $formatted_message;

            $dataJson = json_encode($data);

            sendMessage($producer, "TopicB", $dataJson);

            echo "Message Sent to TopicB: ".$formatted_message."\n";
        }
    }