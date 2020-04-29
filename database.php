<?php
namespace Mayer;

use Bitrix\Main\Application;

class MyDataBase {

    private $connection;
    private $sqlHelper;

    public function __construct() {
        $this->connection = Application::getConnection();
        $this->sqlHelper = $this->connection->getSqlHelper();
    }

    private function query($query) {
        return $this->connection->query($query);
    }

    public function select($table_name, $fields, $where = "", $order = "", $up = true, $limit = "") {
        $fieldsString = array();
        foreach ($fields as $key=>$field) {
            if($field !== "*") {
                $fieldsString[$key] = "`".$fields."`";
            }
        }
        $fields = implode(",", $fields);
        if(!$order) $order = "ORDER BY `id`";
        else {
            if($order != "RAND()") {
                $order = "ORDER BY `$order`";
                if(!$up) $order .= " DESC";
            } else {
                $order = "ORDER BY $order";
            }
        }
        if($limit) $limit = "LIMIT $limit";
        if($where) $query = "SELECT $fields FROM $table_name WHERE $where $order $limit";
        else $query = "SELECT $fields FROM $table_name $order $limit";

        $result_set = $this->query($query);
        if(!$result_set) return false;
        $i = 0;
        $data = array();
        while($row = $result_set->fetch()) {
            $data[$i] = $row;
            $i++;
        }
        return $data;
    }

    public function insert($table_name, $new_values) {
        $query = "INSERT INTO $table_name (";
        foreach($new_values as $field => $value) {
            $query .= "`".$field."`,";
        }
        $query = substr($query, 0, -1);
        $query .= ") VALUES (";
        foreach($new_values as $value) {
            $query .= "'".addslashes($value)."',";
        }
        $query = substr($query, 0, -1);
        $query .= ")";
        return $this->query($query);
    }

    public function update($table_name, $upd_fields, $where) {
        $query = "UPDATE `$table_name` SET ";
        foreach($upd_fields as $field => $value) {
            $query .= "`$field` = '".addslashes($value)."',";
        }
        $query = substr($query, 0, -1);
        if($where) {
            $query .= " WHERE $where";
            return $this->query($query);
        } else return false;
    }

    public function delete($table_name, $where = "") {
        if($where) {
            $query = "DELETE FROM $table_name WHERE $where";
            return $this->query($query);
        } else return false;
    }

    public function getElementOnId($table_name, $id) {
        $array = $this->select($table_name, array("*"), "`id`='".$id."'");
        return $array[0];
    }

    public function getRandomElements($table_name, $count) {
        return $this->select($table_name, array("*"), "", "RAND()", true, $count);
    }

    public function isExists($table_name, $field, $value) {
        $data = $this->select($table_name, array("*"), "`$field`='".addslashes($value)."'");
        if(count($data) === 0) return false;
        return true;
    }

}

?>
