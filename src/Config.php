<?php
return [
    // configs
    'default_database' => $_ENV['DEFAULT_DATABASE'],

    'db_host' => $_ENV['DATABASE_HOST'],
    'db_port' => $_ENV['DATABASE_PORT'],
    'db_name' => $_ENV['DATABASE_NAME'],
    'db_username' => $_ENV['DATABASE_USERNAME'],
    'db_password' => $_ENV['DATABASE_PASSWORD'],

    'redis_scheme' => $_ENV['REDIS_SCHEME'],
    'redis_host' => $_ENV['REDIS_HOST'],
    'redis_port' => $_ENV['REDIS_PORT'],
];