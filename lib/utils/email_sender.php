<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once CORE_PATH . 'system.php';
require_once HELPERS_PATH . 'email_helper.php';

class EmailSender extends System {
    protected $emailTemplate;

    public function __construct($template) {
        parent::__construct();
        $this->emailTemplate = $template;
    }
    
    public function send_email($email=[], $emailData=[]) {

        $appEmail = get_option('support_email');

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: App <'.$appEmail.'>',
        ];

        $emailHelper = new EmailHelper($emailData);
        $message = $emailHelper->{$this->emailTemplate}();

        $result = wp_mail($email['to'], $email['subject'], $message, $headers);
        return $result;
    }
}