<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once UTILS_PATH . 'render.php';

class System extends Render {
    protected $user;
    protected $data;
    protected $lightbox;
    protected $urlParams;
    protected $html;
    protected $widgetsPath;

    public function __construct() {
        $this->user = wp_get_current_user();
        $this->userRenderData = $this->user;
        $this->data = $this->check_array_data();
        $this->urlParams = $this->get_url_params();
        $this->lightbox = isset($_POST['lightbox']) ? sanitize_text_field($_POST['lightbox']) : false;
    }

    public function send_json($data) {
        if(isset($_GET['route_name']))  {
            $this->page_not_found();
            exit;
        };
        wp_send_json($data);
    }

    public function send_template_json($data=[]) {
        $this->html = $this->render_component($this->html, $data);
        if($this->lightbox) $this->html = call_user_func([$this, $this->lightbox]);
        $this->send_json(['success' => STATUS_SUCCESS, 'html' => $this->html]);
    }

    public function get_widget($fileName) {
        return file_get_contents($this->widgetsPath . $fileName . '.twig');
    }

    public function get_component($fileName) {
        return file_get_contents(COMPONENTS_PATH . $fileName . '.twig');
    }

    public function get_url_params() {
        $data = $_GET;
        $newData = [];
        if(count($data)) {
            foreach($data as $key => $value) $newData[$key] = $this->data_sanitization($value);
        }
        return (object) $newData;
    }

    public function check_array_data() {
        if(isset($_POST['data'])) {
            $data = $_POST['data'];
            $newData = new stdClass;
    
            foreach($data as $key => $value) {
                $keys = explode('[', str_replace(']', '', $key));
                $currentObject = $newData;
    
                foreach($keys as $index => $newKey) {
                    if($index === count($keys) - 1) {
                        if (!property_exists($currentObject, 'user_id')) {
                            $currentObject->user_id = $this->user->ID;
                        }
                        $currentObject->{$newKey} = $this->data_sanitization($value);
                    } else {
                        if(!isset($currentObject->{$newKey})) {
                            $currentObject->{$newKey} = new stdClass;
                        }
                        $currentObject = $currentObject->{$newKey};
                    }
                }
            }
    
            return $newData;
        }
    }

    public function data_sanitization($value) {
        $newValue;

        switch($value) {
            case is_string($value):
                $newValue = sanitize_text_field($value);
                break;
            case is_int($value):
                $newValue = intval($value);
                break;
            case is_float($value):
                $newValue = floatval($value);
                break;
            case is_bool($value):
                $newValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            default:
                $newValue = sanitize_text_field($value);
                break;
        }

        return trim($newValue);
    }

    public function page_not_found() {
        $this->viewsFolder = '/';
        $this->render(__FUNCTION__);
    }
}