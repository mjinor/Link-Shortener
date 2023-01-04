<?php

namespace Mjinor\MjaShorturl\App\Model;

use Mjinor\MjaShorturl\Service\Database\Adaptor;
use ReflectionObject;
use ReflectionProperty;

class Model {

    private $adaptor_instance;
    private $timestamp = true;

    public function save() {
        $vars = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);
        $vars = array_column($vars,'name');
        $table_name = $this->getNameOfClassAsTable();
        $values = [];
        foreach ($vars as $var) {
            if ($var == "password")
                $value = password_hash(((array) $this)[$var],PASSWORD_BCRYPT);
            else
                $value = ((array) $this)[$var];
            array_push($values,"'" . $value . "'");
        }
        if ($this->timestamp && !isset($this->id)) {
            $vars[] = "created_at";
            $vars[] = "updated_at";
            array_push($values,time());
            array_push($values,time());
        }
        if (isset($this->id)) {
            $new_values = [];
            foreach ($vars as $key => $var) {
                if ($var == "created_at") continue;
                $new_values[$var] = $values[$key];
            }
            $new_values["updated_at"] = strtotime("now");
            $this->get_adaptor_instance()->update($table_name,$new_values,[
                ["id",'=',$this->id]
            ]);
        } else {
            $this->get_adaptor_instance()->insert($vars,$values,$table_name);
        }
    }

    public function delete() {
        if (!isset($this->id)) throw new \Exception("Id is not set!");
        $table_name = $this->getNameOfClassAsTable();
        $this->get_adaptor_instance()->delete($table_name,[
            ['id','=',$this->id]
        ]);
    }

    private function get_adaptor_instance() {
        if (empty($this->adaptor_instance))
            $this->adaptor_instance = getDBService();
        return $this->adaptor_instance;
    }

    private function getNameOfClassAsTable() {
        $with_name_space = explode("\\",strtolower(static::class) . "s");
        return end($with_name_space);
    }
}