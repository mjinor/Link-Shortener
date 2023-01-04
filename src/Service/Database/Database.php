<?php

namespace Mjinor\MjaShorturl\Service\Database;

/**
 * @Author MJavad Amirbeiki
 * @property string $host
 * @property string $port
 * @property string $name
 * @property string $username
 * @property string $password
 */
interface Database {
    public function connect();
    public function select($select,$from,$condition);
    public function insert($columns,$value,$table);
    public function update($table,$new_values,$condition);
    public function delete($table,$condition);
    public function getConnection();
}