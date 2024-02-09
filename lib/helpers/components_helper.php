<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once CORE_PATH . 'system.php';

class ComponentsHelper extends System {

    protected $modalSize = '';

    public function toast($data) {
        $component = $this->get_component(__FUNCTION__);
        return $this->render_component($component, ['data' => $data]);
    }

    public function alert($data) {
        $component = $this->get_component(__FUNCTION__);
        return $this->render_component($component, ['data' => $data]);
    }

    public function modal() {
        $component = $this->get_component(__FUNCTION__);
        return $this->render_component($component, ['content' => $this->html, 'modalSize' => $this->modalSize]);
    }

    public function offcanvas() {
        $component = $this->get_component(__FUNCTION__);
        return $this->render_component($component, ['content' => $this->html]);
    }
}