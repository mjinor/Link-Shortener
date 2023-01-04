<?php

namespace Mjinor\MjaShorturl\Service;

class Container {
    // singletone stuff to access container from everywhere
    private static $instance = null;

    /**
     * @return Container
     */
    public static function i(){
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @var Instance[]
     */
    private $allocator = [];

    public function put($name,$service) {
        $this->allocator[$name] = $service;
    }
    public function get($name) {
        if (!isset($this->allocator[$name])) {
            throw new \Exception("No such object in `".$name."` service container");
        }
        return isset($this->allocator,$name)?$this->allocator[$name]:null;
    }
}