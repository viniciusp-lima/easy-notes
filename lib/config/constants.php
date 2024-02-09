<?php

if(!defined('ABSPATH')) {
    exit;
}

class AppConstants {
    protected $prefix;

    public function __construct() {
        global $wpdb;
        $this->prefix = $wpdb->prefix . 'app_';
        $this->init_constants();
    }

    public function init_constants() {
        define('STATUS_SUCCESS', true);
        define('STATUS_ERROR', false);

        /* PATHS */
        define('CONTROLLERS_PATH', dirname(__FILE__, 2) . '/controllers/');
        define('HELPERS_PATH', dirname(__FILE__, 2) . '/helpers/');
        define('MODELS_PATH', dirname(__FILE__, 2) . '/models/');
        define('VIEWS_PATH', dirname(__FILE__, 2) . '/views/');
        define('PAGES_PATH', dirname(__FILE__, 2) . '/pages/');
        define('UTILS_PATH', dirname(__FILE__, 2) . '/utils/');
        define('CORE_PATH', dirname(__FILE__, 2) . '/core/');
        define('COMPONENTS_PATH', dirname(__FILE__, 2) . '/components/');
        define('LANGUAGES_PATH', dirname(__FILE__, 3) . '/languages/');
        define('PLUGIN_ROOT', dirname(__FILE__, 3));
        define('PLUGINS_PATH', dirname(__FILE__, 4));
        define('CONFIG_PATH', dirname(__FILE__) . '/');
        define('IMG_PATH', dirname(__FILE__, 3) . '/public/img/');


        /* TABLE NAMES */
        define('TABLE_NOTES', $this->prefix . 'notes');
        define('TABLE_FOLDERS', $this->prefix . 'folders');
    }
}