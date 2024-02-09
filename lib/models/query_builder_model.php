<?php

if(!defined('ABSPATH')) {
    exit;
}

class QueryBuilder {
    protected $tableName;
    protected $query;
    protected $data = [];

    public function select($column = '*') {
        $columns = is_array($column) ? implode(', ', $column) : $column;
        $this->query .= 'SELECT ' . $columns . ' FROM ' . $this->tableName;
        return $this;
    }

    public function where($where = []) {
        $this->query .= ' WHERE 1 = 1';
        if(count($where)) {
            foreach($where as $key => $value) {
                $this->query .= ' AND ' . $key . ' = ' . $this->check_data_type($value);
            }
        }

        $this->prepare_data($where);

        return $this;
    }

    public function insert($data) {
        $data = get_object_vars($data);

        $columns = array_keys($data);
        $values = array_values($data);

        $type_values = array_map(function($value) {
            return $this->check_data_type($value);
        },$values);

        $this->query .= 'INSERT INTO ' . $this->tableName . '(' . implode(',', $columns) . ') VALUES (' . implode(',', $type_values) . ')';

        $this->prepare_data($data);

        return $this;
    }

    public function update($data = []) {
        $this->query .= 'UPDATE ' . $this->tableName . ' SET';

        foreach($data as $key => $value) {
            $this->query .= ' ' . $key . ' = ' . $this->check_data_type($value) . ',';
        }

        $this->query = rtrim($this->query, ',');

        $this->prepare_data($data);

        return $this;
    }

    public function count($column = '*') {
        $columns = is_array($column) ? implode(', ', $column) : $column;
        $this->query .= 'SELECT COUNT(' . $columns . ') FROM ' . $this->tableName;
        return $this;
    }

    public function sum($data = []) {
        $this->query .= 'SELECT';

        foreach($data as $key => $value) {
            $this->query .= !is_int($key) ? ' SUM(' . $key . ') AS ' . $value . ',' : ' SUM(' . $value . '),';
        }

        $this->query = rtrim($this->query, ',');
        $this->query .= ' FROM ' . $this->tableName;

        $this->prepare_data($data);

        return $this;
    }

    public function inner_join($tableName, $onConditions = []) {
        return $this->join('INNER', $tableName, $onConditions);
    }

    public function left_join($tableName, $onConditions = []) {
        return $this->join('LEFT', $tableName, $onConditions);
    }

    public function join($type, $tableName, $onConditions = []) {
        $this->query .= ' ' . $type . ' JOIN ' . $tableName . ' ON ';

        if (count($onConditions)) {
            $conditions = [];

            foreach ($onConditions as $key => $value) {
                $conditions[] = $key . ' = ' . $value;
            }
            
            $this->query .= implode(' AND ', $conditions);
        }

        return $this;
    }

    public function group_by($name) {
        $this->query .=  ' GROUP BY ' . $name;
        return $this;
    }

    public function delete() {
        $this->query .= ' DELETE FROM ' . $this->tableName;
        return $this;
    }

    public function check_data_type($value) {
        switch($value) {
            case is_int($value):
                return '%d';
                break;
            case is_float($value):
                return '%f';
                break;
            case is_string($value):
                return '%s';
                break;
        }
    }

    public function prepare_data($data) {
        $data = !is_array($data) ? get_object_vars($data) : $data;
        $dataValues = array_values($data);
        $this->data = array_merge($this->data, $data);
    }

    public function query() {
        global $wpdb;
        $sql = $wpdb->prepare($this->query, $this->data);
        return $wpdb->query($sql);
    }

    public function get_results() {
        global $wpdb;
        $sql = $wpdb->prepare($this->query, $this->data);
        return $wpdb->get_results($sql);
    }
}