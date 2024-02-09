<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once 'components_helper.php';
require_once MODELS_PATH . 'app_model.php';

class AppHelper extends ComponentsHelper {
    public function __construct() {
        parent::__construct();
        $this->appModel = new AppModel();
        $this->widgetsPath = VIEWS_PATH . 'app/widgets/';
    }

    public function folders() {
        $this->html = $this->get_widget(__FUNCTION__);

        $folders = $this->appModel->select(TABLE_FOLDERS.'.*, COUNT('.TABLE_NOTES.'.id) AS total_notes')->left_join(TABLE_NOTES, [TABLE_FOLDERS.'.id' => TABLE_NOTES.'.folder_id'])->where([TABLE_FOLDERS.'.user_id' => $this->user->ID])->group_by(TABLE_FOLDERS.'.id')->get_results();
        
        $data = ['folders' => $folders];

        $this->send_template_json($data);
    }

}