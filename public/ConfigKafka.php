<?php

namespace Helloprint;

class ConfigKafka
{
    const BROKER_ADDRESS = "kafka:9094";
    const BOOTSTRAP_SERVERS = "bootstrap.servers";
    private $config;

    public function __construct()
    {
        $this->config = new \RdKafka\Conf();
        $this->setConfig(self::BOOTSTRAP_SERVERS, self::BROKER_ADDRESS);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig($param, $value)
    {
        $this->config->set($param, $value);
    }

    public function getBrokerAddress()
    {
        return self::BROKER_ADDRESS;
    }

}
