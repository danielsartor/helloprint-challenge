<?php

use Helloprint\Utils\Topics as Topics;
use Helloprint\Utils\ConfigKafka as ConfigKafka;
use Helloprint\Requests\Producer as Producer;
use Helloprint\Requests\Consumer as Consumer;
use Helloprint\Services\Requester as Requester;

require_once __DIR__.'/../app/index.php';

//Config
$config = new ConfigKafka();

//Consumers
$consumerRequester = new Consumer($config, Topics::REQUESTER);
$consumerBroker = new Consumer($config, Topics::BROKER);

//Producer
$producer = new Producer($config, Topics::HELLOPRINT_REQUESTS);

new Requester($producer, $consumerRequester, $consumerBroker);