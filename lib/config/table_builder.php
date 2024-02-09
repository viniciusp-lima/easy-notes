<?php

if(!defined('ABSPATH')) {
    exit;
}

class TableBuilder {
    public function create_tables() {
        global $wpdb;
    
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($this->tables() as $key => $value) {
            $table_name = $wpdb->prefix . 'app_' . $key;
            $query = 'CREATE TABLE IF NOT EXISTS ' . $table_name . $value;
            dbDelta($query);
        }
    }    

    public function tables() {
        $sql;

        $sql['folders'] = "(
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT NOT NULL,
            name VARCHAR(150) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL)";
        
        $sql['notes'] = "(
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id BIGINT NOT NULL,
            folder_id INT NOT NULL,
            name VARCHAR(150) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
            FOREIGN KEY (folder_id) REFERENCES wp_app_folders(id) ON DELETE CASCADE)";
            

        return $sql;
    }
}