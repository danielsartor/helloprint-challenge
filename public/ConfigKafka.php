<?php

namespace Helloprint;

class ConfigKafka
{
    private $config;
    private $brokerAddress;

    public function __construct() {
        $this->brokerAddress = "kafka:9094";

        $this->config = new \RdKafka\Conf();
        $this->setConfig("bootstrap.servers", $this->brokerAddress);
    }

    public function getConfig() {
        return $this->config;
    }

    public function setConfig($param, $value) {
        $this->config->set($param, $value);
    }

    public function getBrokerAddress() {
        return $this->brokerAddress;
    }

}
