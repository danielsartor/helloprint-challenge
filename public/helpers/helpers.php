<?php
    //Configuration
    $conf = new \RdKafka\Conf();
    $conf->set("bootstrap.servers", "kafka:9094");

    function sendMessage($producer, $topic, $message) {
        if ($producer->addBrokers("kafka:9094") < 1) {
            echo "Failed adding brokers\n";
            exit;
        }

        $topic = $producer->newTopic($topic);

        if (!$producer->getMetadata(false, $topic, 2000)) {
            echo "Failed to get metadata, is broker down?\n";
            exit;
        }

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
    }

    function buildJsonMessage($fields, $messages) {
        return json_encode([
            "schema" => [
                "type" => "struct",
                "fields" => (array) $fields,
                "optional" => false,
                "name" => "requests"
            ],
            "payload" => (array) $messages
        ]);
    }