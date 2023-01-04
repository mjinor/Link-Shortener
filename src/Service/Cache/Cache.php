<?php

namespace Mjinor\MjaShorturl\Service\Cache;

/**
 * @Author MJavad Amirbeiki
 * @property string $scheme
 * @property string $host
 * @property string $port
 */
interface Cache {
    public function set($key,$value,$expiration);
    public function get($key);
    public function delete($key);
}