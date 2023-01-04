<?php

namespace Mjinor\MjaShorturl\Service\Cache;

use Predis\Client;

class Redis implements Cache {

    private $connection;

    function __construct() {
        $this->scheme = get_config("redis_scheme");
        $this->host = get_config("redis_host");
        $this->port = get_config("redis_port");
        $this->connect();
    }

    private function connect() {
        $this->connection = new Client([
            'scheme' => $this->scheme,
            'host'   => $this->host,
            'port'   => $this->port,
        ]);
    }

    public function set($key, $value, $expiration = null) {
        $res = null;
        if (!empty($expiration))
            $res = "EX";
        $this->connection->set($key,$value,$res,$expiration);
    }

    public function get($key) {
        return $this->connection->get($key);
    }

    public function delete($key) {
        $this->connection->del($key);
    }
}