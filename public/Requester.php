<?php
    require_once('helpers/helpers.php');

    //Producer
    $producer = new \RdKafka\Producer($conf);
    $producer->setLogLevel(LOG_DEBUG);

    //Consumer
    $consumer = new \RdKafka\Consumer($conf);
    $consumer->setLogLevel(LOG_DEBUG);

    //Data
    $messages = [
        "id" => uniqid(),
        "message" => "Hi, "
    ];

    $fields = [
        [
            "type" => "string",
            "optional" => false,
            "field" => "id"
        ],[
            "type" => "string",
            "optional" => false,
            "field" => "message"
        ]
    ];

    $dataJson = buildJsonMessage($fields, $messages);

    sendMessage($producer, "helloprint.requests", $dataJson);
    echo "Message Sent to Postgres: ".$messages['message']."\n";

    //Consume Response
    startConsumingMessage($consumer);

    function startConsumingMessage($consumer) {
        //Broker
        $consumer->addBrokers("kafka:9094");

        //Requester Topic
        $topic = $consumer->newTopic("Requester");

        //Start Consuming
        $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);
        
        //Message ID
        $id =  NULL;

        while (true) {
            //Consume Interval
            $msg = $topic->consume(0, 1000);
        
            if (null === $msg || $msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
                continue;
            } elseif ($msg->err) {
                echo $msg->errstr(), "\n";
                break;
            } else {
                if ($msg->payload) {
                    $data = json_decode($msg->payload);

                    if (!$id) {
                        $id = $data->id;
                        echo "Received ID: ".$id."\n";
                        break;
                    }
                }
            }
        }

        //Broker Topic
        $topicConf = new RdKafka\TopicConf();
        $topicConf->set("request.timeout.ms", 1000);
        $topic = $consumer->newTopic("Broker", $topicConf);

        //Start Consuming
        $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

        echo "Consuming from Broker\n";

        while (true) {
            //Consume Interval
            $msg = $topic->consume(0, 50);
        
            if (null === $msg || $msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
                continue;
            } else if ($msg->err == RD_KAFKA_RESP_ERR__TIMED_OUT) {
                echo "No reponse\n";
                exit;
            } elseif ($msg->err) {
                echo $msg->errstr(), "\n";
                exit;
            } else {
                if ($msg->payload) {
                    $data = json_decode($msg->payload);
                    if ($id == $data->id) {
                        echo $data->message;
                        $id = NULL;
                        exit;
                    }
                }
            }
        }
    }