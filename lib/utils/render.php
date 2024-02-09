<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once CONFIG_PATH . 'system.php';
require_once PLUGIN_ROOT . '/vendor/autoload.php';

use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

class Render {
    protected $userRenderData;
    protected $viewsFolder;
    protected $renderData = [];

    public function translator_creation($twig) {
        $systemConfig = new SystemConfig();
        $language = $systemConfig->identify_browser_language();
        
        $translator = new Translator($language);
        $translator->addLoader('yaml', new YamlFileLoader());
        $twig->addExtension(new TranslationExtension($translator));

        $translator->addResource('yaml', LANGUAGES_PATH . $language.'.yaml', $language);
    }

    public function render($fileName) {
        $fileName .= '.twig';
        $loader = new \Twig\Loader\FilesystemLoader();

        $loader->addPath(PAGES_PATH);
        $loader->addPath(VIEWS_PATH . $this->viewsFolder);

        $this->define_default_user_data();

        $twig = new \Twig\Environment($loader);
        
        $this->translator_creation($twig);

        $html = $twig->render($fileName, $this->renderData);
        echo $html;
    }

    public function render_component($html, $data=[]) {
        $loader = new \Twig\Loader\ArrayLoader(['template' => $html]);
        $twig = new \Twig\Environment($loader);

        $this->translator_creation($twig);

        $html = $twig->render('template', $data);
        return $html;
    }

    public function define_default_user_data() {
        if(isset($this->user->ID)) {
            $this->renderData['user']['id']             = $this->userRenderData->ID;
            $this->renderData['user']['display_name']   = $this->userRenderData->display_name;
            $this->renderData['user']['email']          = $this->userRenderData->user_email;
            $this->renderData['user']['avatar']         = get_avatar_url($this->userRenderData->ID, ['size' => 96]);
        }
    }
}