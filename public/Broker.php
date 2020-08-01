<?php
    require_once('helpers/helpers.php');

    //Producer
    $producer = new \RdKafka\Producer($conf);
    $producer->setLogLevel(LOG_DEBUG);

    //Consumer
    $consumer = new \RdKafka\Consumer($conf);
    $consumer->setLogLevel(LOG_DEBUG);

    //Consumer from Postgres
    //Broker
    $consumer->addBrokers("kafka:9094");

    //Postgres Consumer Topic
    $topic = $consumer->newTopic("dbserver1.helloprint.requests");

    //Start Consuming
    $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

    //Temporary solution to avoid loops ¯\_(ツ)_/¯
    $gotData = false;

    echo "Consuming from Postgres\n";
    while (true) {
        $msg = $topic->consume(0, 1000);
        if (null === $msg || $msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
            continue;
        } elseif ($msg->err) {
            echo $msg->errstr(), "\n";
            break;
        } else {
            if ($msg->payload) {
                $data = json_decode($msg->payload);
                if (!$gotData) {
                    $gotData = true;
                    echo "Initial Message\n";
                    sendMessage($producer, "Requester", json_encode($data->payload->after));
                    sendMessage($producer, "TopicA", json_encode($data->payload->after));
                } else {
                    $gotData = false;
                    echo "Response\n";
                    sendMessage($producer, "Broker", json_encode($data->payload->after));
                }
                
            }
        }
    }