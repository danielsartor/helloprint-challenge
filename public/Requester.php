<?php
    
    //Configuration
    $conf = new \RdKafka\Conf();
    $conf->set("bootstrap.servers", "kafka:9094");

    //Producer
    $producer = new \RdKafka\Producer($conf);
    $producer->setLogLevel(LOG_DEBUG);

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
    $message = "Hi, ";
    $data = ["schema" => [
           "type" => "struct",
           "fields" => [
              [
                 "type" => "struct",
                 "fields" => [
                    [
                       "type" => "int32",
                       "optional" => false,
                       "field" => "id"
                    ],
                    [
                       "type" => "string",
                       "optional" => false,
                       "field" => "message"
                    ],
                    [
                       "type" => "string",
                       "optional" => true,
                       "field" => "response"
                    ]
                 ],
                 "optional" => true,
                 "name" => "dbserver1.helloprint.requests.Value"
              ]
           ]
            ],
        "payload" => [
           "message" => "Hiya,"
        ]
    ];


    $dataJson = json_encode($data);

    $topic->produce(RD_KAFKA_PARTITION_UA, 0, "Hi :D ? :C");

    echo "Message Sent to TopicA: ".$message."\n";

    //Consumer
    $consumer = new \RdKafka\Consumer($conf);
    $consumer->setLogLevel(LOG_DEBUG);
    
    //Consume Response
    startConsumingMessage($consumer);

    function startConsumingMessage($consumer) {
        //Broker
        $consumer->addBrokers("kafka:9094");

        //Requester Topic
        $topic = $consumer->newTopic("dbserver1.helloprint.requests");

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