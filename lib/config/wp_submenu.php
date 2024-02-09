<?php

if(!defined('ABSPATH')) {
    exit;
}

class WPSubmenu {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_submenu_settings'));
        add_action('admin_init', array($this, 'initialize_app_settings'));
    }

    public function add_submenu_settings() {
        add_submenu_page(
            'options-general.php',
            'App Settings',
            'App Config',
            'manage_options',
            'app-settings',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h2>App Config</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('app_options');
                do_settings_sections('app_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function initialize_app_settings() {
        register_setting('app_options', 'support_email');
        register_setting('app_options', 'stripe_trial_period');
        register_setting('app_options', 'stripe_secret_key');
        register_setting('app_options', 'stripe_subscription_secret');
        register_setting('app_options', 'stripe_product_id');

        // CONFIGURAÇÕES PARA CONFIGURAÇÕES DE E-MAILS --------------------------------------------------------
        add_settings_section('app_settings', 'Configurações de E-mails', '', 'app_settings');

        add_settings_field('support_email', 'E-mail do Suporte', array($this, 'render_email_field'), 'app_settings', 'app_settings');

        // SEÇÃO PARA CONFIGURAÇÕES STRIPE --------------------------------------------------------------------
        add_settings_section('app_stripe_settings', 'Configurações Stripe', '', 'app_settings');

        add_settings_field('stripe_trial_period', '
        Período de Teste (dias)', array($this, 'render_trial_period_field'), 'app_settings', 'app_stripe_settings');
        add_settings_field('stripe_secret_key', 'Chave Secreta', array($this, 'render_stripe_secret_key_field'), 'app_settings', 'app_stripe_settings');
        add_settings_field('stripe_subscription_secret', 'Segredo de Assinatura', array($this, 'render_subscription_secret_field'), 'app_settings', 'app_stripe_settings');
        add_settings_field('stripe_product_id', 'ID do Produto', array($this, 'render_stripe_product_id'), 'app_settings', 'app_stripe_settings');
    }

    public function render_email_field() {
        $value = get_option('support_email');
        echo '<input type="text" name="support_email" value="' . esc_attr($value) . '" />';
    }

    public function render_trial_period_field() {
        $value = get_option('stripe_trial_period');
        echo '<input type="number" name="stripe_trial_period" value="' . esc_attr($value) . '" />';
    }

    public function render_stripe_secret_key_field() {
        $value = get_option('stripe_secret_key');
        echo '<input type="text" name="stripe_secret_key" value="' . esc_attr($value) . '" />';
    }

    public function render_subscription_secret_field() {
        $value = get_option('stripe_subscription_secret');
        echo '<input type="text" name="stripe_subscription_secret" value="' . esc_attr($value) . '" />';
    }

    public function render_stripe_product_id() {
        $value = get_option('stripe_product_id');
        echo '<input type="text" name="stripe_product_id" value="' . esc_attr($value) . '" />';
    }
}

new WPSubmenu();