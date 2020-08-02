<?php
    require_once('helpers/helpers.php');

    //Producer
    $producer = new \RdKafka\Producer($conf);

    //Consumer
    $consumer = new \RdKafka\Consumer($conf);

    //Broker
    $consumer->addBrokers("kafka:9094");

    //Postgres Consumer Topic
    $topic = $consumer->newTopic("dbserver1.helloprint.requests");

    //Start Consuming
    $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

    //Save last request to avoid loops. For some reason connector doesnt return the "before".
    $dataJson = NULL;

    echo "Consuming from Postgres\n";
    while (true) {
        $msg = $topic->consume(0, 1000);
        if (!$msg || $msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
            continue;
        } elseif ($msg->err) {
            echo $msg->errstr(), "\n";
            break;
        } else {
            if ($msg->payload) {
                echo "Message Received from Connector Source.\n";
                $data = json_decode($msg->payload);
                if (!$dataJson) {
                    $dataJson = json_encode($data->payload->after);

                    sendMessage($producer, "TopicA", $dataJson);
                    sendMessage($producer, "Requester", $dataJson);

                    echo "Sent message to TopicA and Requester\n".$dataJson."\n\n";
                } else {
                    $dataJson = json_encode($data->payload->after);

                    sendMessage($producer, "Broker", $dataJson);
                    
                    echo "Sent Message to Broker: \n".$dataJson."\n\n";
                    $dataJson = NULL;
                }
                
            }
        }
    }