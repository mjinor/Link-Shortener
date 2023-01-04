<?php

namespace Mjinor\MjaShorturl\App;

use Mjinor\MjaShorturl\Service\Cache\Redis;
use Mjinor\MjaShorturl\Service\Container;
use Mjinor\MjaShorturl\Service\Database\Adaptor;
use Mjinor\MjaShorturl\Service\Database\MySql;
use Mjinor\MjaShorturl\Service\Instance;

/**
 * @author MJavad Amirbeiki
 * this class loads service container
 */
class Bootstrap {
    public static function boot() {
        $container = Container::i();
        $container->put("redis",new Instance("redis",new Redis()));
        $default_database = get_config('default_database');
        switch ($default_database) {
            case Adaptor::MYSQL_CONNECTION:
                $database = new MySql();
                break;
            default:
                throw new \Exception("Default database not defined.");
        }
        $container->put("db",new Instance("db",new Adaptor($database)));
    }
}