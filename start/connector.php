<?php

use Helloprint\Utils\Topics as Topics;
use Helloprint\Utils\ConfigKafka as ConfigKafka;
use Helloprint\Requests\Producer as Producer;
use Helloprint\Requests\Consumer as Consumer;
use Helloprint\Services\Connector as Connector;

require_once __DIR__.'/../app/index.php';

//Config
$config = new ConfigKafka();

//Consumer
$consumer = new Consumer($config, Topics::DBSERVER_1_HELLOPRINT_REQUESTS);

//Producers
$producerRequester = new Producer($config, Topics::REQUESTER);
$producerTopicA = new Producer($config, Topics::TOPIC_A);
$producerBroker = new Producer($config, Topics::BROKER);

new Connector($consumer, $producerRequester, $producerTopicA, $producerBroker);