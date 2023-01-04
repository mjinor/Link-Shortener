<?php

namespace Mjinor\MjaShorturl\Service;

class Instance {
    private $instance = null;
    private $name;
    private $loader;

    public function __construct($name,$instance){

        $this->name = $name;

        if ($instance instanceof \Closure) {
            $this->loader = $instance;
        } else {
            $this->instance = $instance;
        }
    }

    public function getService() {
        if ($this->instance === null) {
            $loaderClosure = $this->loader;
            $this->instance = $loaderClosure();
        }
        return $this->instance;
    }
}