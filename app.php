<?php

/* 
    Plugin Name: App
    Author: Vinícius Lima
    Version: 1.0.0
    Description: My app
*/


if(!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/lib/config/constants.php';
require_once __DIR__ . '/lib/core/process_ajax.php';
require_once __DIR__ . '/lib/config/table_builder.php';
require_once __DIR__ . '/lib/config/wp_submenu.php';


class App extends AppConstants {
    protected $processAjax;
    protected $version = '1.0.0';

    public function __construct() {
        parent::__construct();
        $this->init_hooks();
        $this->init_proccess_ajax();
    }

    public function start_plugin() {
        $tableBuilder = new TableBuilder();
        $tableBuilder->create_tables();
    }

    public function init() {
        $this->disable_admin_bar();
        $this->enqueue_styles();
        $this->enqueue_scripts();
        $this->localize_scripts();
    }

    public function init_proccess_ajax() {
        $this->processAjax = new ProcessAjax();
        $this->processAjax->init();
    }

    public function disable_admin_bar() {
        add_filter('show_admin_bar', '__return_false');
    }

    public function custom_rewrite_rules() {
        add_rewrite_rule('^app/?', 'index.php?custom_route=app', 'top');
        add_rewrite_rule('^login/?', 'index.php?custom_route=login', 'top');
        add_rewrite_rule('^signup/?', 'index.php?custom_route=signup', 'top');
        add_rewrite_rule('^forgot/?', 'index.php?custom_route=forgot', 'top');
    }

    public function custom_query_vars($vars) {
        $vars[] = 'custom_route';
        return $vars;
    }

    public function enqueue_styles() {
        if(is_admin()) return;

        wp_register_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', [], '5.3.2', false);
        wp_enqueue_style('bootstrap');

        wp_register_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', [], '1.11.3', false);
        wp_enqueue_style('bootstrap-icons');

        wp_register_style('global', plugins_url('public/css/global.css', __FILE__), [], $this->version, false);
        wp_enqueue_style('global');

        wp_register_style('main-front', plugins_url('public/css/main_front.css', __FILE__), [], $this->version, false);
        wp_enqueue_style('main-front');

        wp_register_style('main-back', plugins_url('public/css/main_back.css', __FILE__), [], $this->version, false);
        wp_enqueue_style('main-back');
    }

    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        
        wp_register_script('bootstrap-script', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], '5.3.2', true);
        wp_enqueue_script('bootstrap-script');

        wp_register_script('tinymce', 'https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js', [], '5.0.0', true);
        wp_enqueue_script('tinymce');

        wp_register_script('sweet-alert', 'https://unpkg.com/sweetalert/dist/sweetalert.min.js', [], false, true);
        wp_enqueue_script('sweet-alert');

        wp_register_script('main-front', plugins_url('public/js/main_front.js', __FILE__), ['jquery'], $this->version, true);
        wp_enqueue_script('main-front');

        wp_register_script('main-back', plugins_url('public/js/main_back.js', __FILE__), ['jquery'], $this->version, true);
        wp_enqueue_script('main-back');
    }

    public function localize_scripts() {
        $data = ['ajax_url' => admin_url('admin-ajax.php')];
        wp_localize_script('main-back', 'ajax_object', $data);
    }

    // Manipula as diferentes rotas personalizadas com base no valor da variável 'custom_route'
    public function handle_custom_route() {
        $auth = ['login', 'signup', 'forgot'];
        $custom_route = get_query_var('custom_route', false);

        if($custom_route === 'app') {
            $this->handle_app_route();
        }

        if(in_array($custom_route, $auth)) {
            $this->handle_auth_route($custom_route);
        }
    }

    // Manipula a rota 'app', incluindo a lógica para processar parâmetros e incluir arquivos
    private function handle_app_route() {
        if(is_user_logged_in()) {
            $route_name = isset($_GET['route_name']) ? sanitize_key($_GET['route_name']) : false;
            $folder = 'controllers';
            $file = $route_name ? explode('__', $route_name)[0] : 'app';
            $actionType = $route_name ? explode('__', $route_name)[1] : 'index';
            $fileExtension = '_controller.php';
            $filePath = $this->processAjax->build_file_path($folder, $file, $fileExtension);
        
            echo $this->get_header_plugin();
            file_exists($filePath) ? $this->processAjax->require_and_process($filePath, $file, $actionType, $folder) : $this->processAjax->page_not_found();
            echo $this->get_footer_plugin();
        
            exit;
        } else {
            header('Location: /login');
        }
    }

    // Manipula as rotas de autenticação, exibindo conteúdo específico para essa rota
    private function handle_auth_route($custom_route) {
        if(!is_user_logged_in()) {
            $filePath = $this->processAjax->build_file_path('controllers', 'auth', '_controller.php');

            if (file_exists($filePath)) {
                echo $this->get_header_plugin();
                $this->processAjax->require_and_process($filePath, 'auth', $custom_route, 'controllers');
                echo $this->get_footer_plugin();
            }
            exit;
        } else {
            header('Location: /app');
        }
    }

    public function add_body_class() {
        $newClass = [];
        $custom_route = get_query_var('custom_route', false);

        switch($custom_route) {
            case 'app':
                $newClass[] = 'bg-body-tertiary app-page';
                break;
            case 'login':
                $newClass[] = 'login-page';
                break;
            case 'signup':
                $newClass[] = 'signup-page';
                break;
            case 'forgot':
                $newClass[] = 'forgot-page';
                break;
        }
        
        return $newClass;
    }

    public function get_header_plugin() {
        include(plugin_dir_path(__FILE__) . '/header.php');
    }
    
    public function get_footer_plugin() {
        include(plugin_dir_path(__FILE__) . '/footer.php');
    }

    public function init_hooks() {
        register_activation_hook(__FILE__, [$this, 'start_plugin']);

        add_action('init', [$this, 'init']);

        add_action('init', [$this, 'custom_rewrite_rules']);
        add_filter('query_vars', [$this, 'custom_query_vars']);
        add_action('template_redirect', [$this, 'handle_custom_route']);

        add_filter('body_class', [$this, 'add_body_class']);
    }
}
$app = new App();