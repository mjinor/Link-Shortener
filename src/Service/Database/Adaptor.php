<?php

namespace Mjinor\MjaShorturl\Service\Database;

class Adaptor {

    private $database;

    const MYSQL_CONNECTION = 'mysql';

    function __construct(Database $database) {
        $database->connect();
        $this->database = $database;
    }

    function insert($columns, $value, $table) {
        $this->database->insert($columns, $value, $table);
    }

    function delete($table, $condition = []) {
        $this->database->delete($table,$condition);
    }

    function update($table,$new_values,$condition = []) {
        $this->database->update($table,$new_values,$condition);
    }

    function select($select, $from, $condition = [],$as = null) {
        $result = $this->database->select($select,$from,$condition);
        if (!empty($as)) {
            $collection = [];
            foreach ($result as $item) {
                $obj = new $as;
                foreach ($item as $key => $value) {
                    $obj->$key = $value;
                }
                array_push($collection,$obj);
            }
            return $collection;
        }
        return $result;
    }

    public function saveWithTransAction($objects) {
        $this->getDatabase()->beginTransaction();
        try {
            foreach ($objects as $object)
                $object->save();
            $this->getDatabase()->commit();
        } catch (\Exception $e) {
            $this->getDatabase()->rollBack();
            response_json([
                'status' => 'Failed',
                'message' => $e->getMessage()
            ],500);
        }
    }

    function getDatabase() {
        return $this->database;
    }
}