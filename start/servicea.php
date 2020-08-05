<?php

use Helloprint\Utils\Topics as Topics;
use Helloprint\Utils\ConfigKafka as ConfigKafka;
use Helloprint\Requests\Producer as Producer;
use Helloprint\Requests\Consumer as Consumer;
use Helloprint\Services\ServiceA as ServiceA;

require_once __DIR__.'/../app/index.php';

//Config
$config = new ConfigKafka();

//Consumer
$consumer = new Consumer($config, Topics::TOPIC_A);

//Producer
$producer = new Producer($config, Topics::TOPIC_B);

new ServiceA($consumer, $producer);