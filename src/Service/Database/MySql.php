<?php

namespace Mjinor\MjaShorturl\Service\Database;

use PDO;
use PDOException;

class MySql implements Database {

    private $connection;

    function __construct() {
        $this->host = get_config("db_host");
        $this->port = get_config("db_port");
        $this->name = get_config("db_name");
        $this->username = get_config("db_username");
        $this->password = get_config("db_password");
    }

    public function connect() {
        try {
            $this->connection = new PDO("mysql:host=$this->host;dbname=$this->name", $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit();
        }
    }

    public function insert($columns, $value, $table)
    {
        $columns = implode(',',$columns);
        $value = implode(',',$value);
        $sql = "INSERT INTO $table ($columns) VALUES ($value)";
        $this->connection->exec($sql);
        return 1;
    }

    public function update($table,$new_values,$condition = [])
    {
        $new = "";
        $counter = 0;
        foreach ($new_values as $key => $new_value) {
            $new = $new . $key ."=" . $new_value;
            if (sizeof($new_values) - 1 > $counter)
                $new = $new . ",";
            $counter++;
        }
        $sql = "UPDATE $table SET $new";
        $sql = $this->addConditionToSql($sql,$condition);
        $statement = $this->connection->prepare($sql);
        $statement->execute();
    }

    public function delete($table, $condition = []) {
        $sql = "DELETE FROM $table";
        $sql = $this->addConditionToSql($sql,$condition);
        $this->connection->exec($sql);
    }

    public function select($select, $from, $condition = []) {
        if ($select != "*")
            $select = implode(',',$select);
        $sql = "SELECT $select FROM $from";
        $sql = $this->addConditionToSql($sql,$condition);
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        return $statement->fetchAll();
    }

    public function addConditionToSql($sql,$condition) {
        if (!empty($condition)) {
            $sql = $sql . " WHERE ";
            foreach ($condition as $key => $cond) {
                $sql = $sql . "$cond[0] $cond[1] '$cond[2]'";
                if ((sizeof($condition ) - 1) != $key)
                    $sql = $sql . " AND ";
            }
        }
        return $sql;
    }

    public function beginTransaction() {
        $this->connection->beginTransaction();
    }

    public function rollBack() {
        $this->connection->rollBack();
    }

    public function commit() {
        $this->connection->commit();
    }

    public function getConnection() {
        return $this->connection;
    }
}