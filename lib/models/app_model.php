<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once 'query_builder_model.php';

class AppModel extends QueryBuilder {
    public function __construct() {
        $this->tableName = TABLE_FOLDERS;
    }
}