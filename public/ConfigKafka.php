<?php

namespace Helloprint;

class ConfigKafka
{
    private $config;
    private $brokerAddress = "kafka:9094";
    private $bootstrapServersConfig = "bootstrap.servers";

    public function __construct() {
        $this->config = new \RdKafka\Conf();
        $this->setConfig($this->bootstrapServersConfig, $this->brokerAddress);
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
