<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once 'components_helper.php';
require_once MODELS_PATH . 'note_model.php';
require_once MODELS_PATH . 'folder_model.php';

class AppHelper extends ComponentsHelper {
    public function __construct() {
        parent::__construct();
        $this->noteModel = new NoteModel($this->user);
        $this->folderModel = new FolderModel($this->user);
        $this->widgetsPath = VIEWS_PATH . 'app/widgets/';
    }

    public function folders() {
        $this->html = $this->get_widget(__FUNCTION__);

        $folders = $this->folderModel->get_folders();
        $data = ['folders' => $folders];

        $this->send_template_json($data);
    }

    public function notes() {
        $this->html = $this->get_widget(__FUNCTION__);

        $notes = $this->noteModel->get_notes($this->data->folder_id);
        $data = ['notes' => $notes, 'folder_id' => $this->data->folder_id];

        $this->send_template_json($data);
    }

}