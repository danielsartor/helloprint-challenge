<?php
    require_once('helpers/helpers.php');

    $bye = "Bye.";

    //Producer
    $producer = new \RdKafka\Producer($conf);

    //Consumer
    $consumer = new \RdKafka\Consumer($conf);

    //Broker
    $consumer->addBrokers("kafka:9094");

    //TopicB Topic
    $topic = $consumer->newTopic("TopicB");
    
    //Start Consuming
    $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

    echo "Consuming from TopicB\n";
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

            //Data
            $formatted_message = $data->message . $bye;

            $messages = [
                "id" => $data->id,
                "message" => $formatted_message
            ];

            $fields = [
                [
                    "type" => "string",
                    "optional" => false,
                    "field" => "id"
                ],
                [
                    "type" => "string",
                    "optional" => false,
                    "field" => "message"
                ]
            ];

            $dataJson = buildJsonMessage($fields, $messages);

            sendMessage($producer, "helloprint.requests", $dataJson);

            echo "Message Sent to Connector Sink: ".$formatted_message."\n\n";
        }
    }