<?php

if(!defined('ABSPATH')) {
    exit;
}

class ProcessAjax {
    public function init() {
        add_action('wp_ajax_ajax_action', [$this, 'process_ajax']);
        add_action('wp_ajax_nopriv_ajax_action', [$this, 'process_ajax']);
    }

    public function process_ajax() {
        $file              = isset($_POST['file']) ? sanitize_text_field($_POST['file']) : '';
        $action            = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : '';
        $actionType        = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
        $folder            = isset($_POST['category']) ? sanitize_text_field($_POST['category'] . 's') : '';
        $fileExtension     = isset($_POST['category']) ? '_' . substr($folder, 0, -1) . '.php' : '';
    
        if ($action === 'ajax_action') {
            $filePath = $this->build_file_path($folder, $file, $fileExtension);
    
            if (file_exists($filePath)) {
                $this->require_and_process($filePath, $file, $actionType, $folder);
            } else {
                if(!isset($_GET['route_name'])) {
                    $response = 'O arquivo não existe.';
                    wp_send_json_error($response);
                } else {
                    $this->page_not_found();
                }
            }
        }
    }

    public function build_file_path($folder, $file, $fileExtension) {
        return dirname(__FILE__, 3) . '/lib/' . $folder . '/' . $file . $fileExtension;
    }
    
    public function require_and_process($filePath, $file, $actionType, $folder) {
        require_once $filePath;
    
        $prepareClassName = str_replace('_', '', ucwords($file, '_'));
        $className = $prepareClassName . ($folder === 'controllers' ? 'Controller' : 'Helper');
    
        if (class_exists($className)) {
            $this->invoke_method($className, $actionType);
        } else {
            echo $className;
            $response = 'A classe não existe.';
            wp_send_json_error($response);
        }
    }
    
    public function invoke_method($className, $actionType) {
        $reflectionClass = new ReflectionClass($className);
    
        if (method_exists($className, $actionType)) {
            $obj = $reflectionClass->newInstance();
            call_user_func([$obj, $actionType]);
        } else {
            if(!isset($_GET['route_name'])) {
                $response = 'O método não existe.';
                wp_send_json_error($response);
            } else {
                $this->page_not_found();
            }
        }
    }

    public function page_not_found() {
        require_once CORE_PATH . 'system.php';
        $system = new System();
        $system->page_not_found();
    }
}