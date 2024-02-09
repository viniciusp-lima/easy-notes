<?php

if(!defined('ABSPATH')) {
    exit;
}

class SystemConfig {
    public function identify_browser_language() {
        $languageCode = '';
        $systemLanguages = ['en', 'pt'];

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $browserLanguage = strtolower(substr(chop($languages[0]), 0, 2));
        
            // Verifica se há uma variante de idioma na configuração
            if (isset($languages[0]) && strlen($languages[0]) > 2 && $languages[0][2] == '-') {
                $languageCode = explode('-', $languages[0])[0];
                $browserLanguage .= '_' . strtoupper(substr($languages[0], 3));
            }

            
            $language = in_array($languageCode, $systemLanguages) ? $browserLanguage : 'en_US';
            return $language;
        }
    }
}