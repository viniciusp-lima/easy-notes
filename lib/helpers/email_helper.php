<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once CORE_PATH . 'system.php';

class EmailHelper extends System {
    protected $emailContent;
    protected $emailData;

    public function __construct($data) {
        parent::__construct();
        $this->emailData = $data;
    }

    /* MODELO PADRÃO DO EMAIL */
    public function default_email_template() {
        ob_start();
        ?>
            <div class="email-wrapper">
                <div class="email-header">
                    <h1>Cabeçalho do E-mail</h1>
                </div>
                <div class="email-body">
                    {{ content | raw }}
                </div>
                <div class="email-footer">

                </div>
            </div>
        <?php
        $html = ob_get_clean();
        return $this->render_component($html, ['content' => $this->emailContent]);
    }

    public function forgot_password() {
        ob_start();
        ?>
            <p>{{ 'Your new password:' | trans }} {{ password }}</p>
        <?php
        $html = ob_get_clean();
        $this->emailContent = $this->render_component($html, $this->emailData);
        return $this->default_email_template();
    }
}