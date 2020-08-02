<?php
    require_once('helpers/helpers.php');

    //Producer
    $producer = new \RdKafka\Producer($conf);

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

    echo "Message Sent to Connector Sink: ".$messages['message']."\n";

    //Consume Response
    startConsumingMessage();

    function startConsumingMessage() {
        //Configuration
        $conf = new \RdKafka\Conf();
        $conf->set("bootstrap.servers", "kafka:9094");

        //Consumer
        $consumer = new \RdKafka\Consumer($conf);
        
        //Broker
        $consumer->addBrokers("kafka:9094");
        
        //Requester Topic
        $topic = $consumer->newTopic("Requester");
        
        //Start Consuming
        $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);
        
        //Sometimes RD_KAFKA_OFFSET_END would fail to retrive messages.
        $message = NULL;
        
        while(true) {
            $msg = $topic->consume(0, 1000);
            $message = json_decode($msg->payload) ?? $message;
            if (!$msg) break;
        }

        if ($message->id) {
            //Configuration
            $conf = new \RdKafka\Conf();
            $conf->set("bootstrap.servers", "kafka:9094");

            //Consumer
            $consumer = new \RdKafka\Consumer($conf);

            //Broker Topic
            $topic = $consumer->newTopic("Broker");

            //Start Consuming
            $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

            echo "Consuming from Broker\n";

            //Temporary solution for time out. 
            //RD_KAFKA_OFFSET_BEGINNING needs to consume every message in the stack in order to reach the end
            //RD_KAFKA_OFFSET_END is returning null always.
            $start_time = round(microtime(true) * 1000);
            while(true) {
                $msg = $topic->consume(0, 50);
                $time = round(microtime(true) * 1000);
                if (($time - $start_time) > 1000) {
                    echo "TIMEOUT: No reponse for ID: ".$message->id."\n";
                    exit;
                } else if ($msg){
                    $data = json_decode($msg->payload);
                    if ($message->id == $data->id) {
                        echo $data->message;
                        exit;
                    }
                }
            }
        } else {
            echo 'ERROR: Failed to retrieve ID.';
            exit;
        }
    }