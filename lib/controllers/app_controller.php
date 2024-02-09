<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once CORE_PATH . 'system.php';
require_once MODELS_PATH . 'app_model.php';
require_once HELPERS_PATH . 'components_helper.php';

class AppController extends System {
    protected $comp;
    protected $messageStatus;

    public function __construct() {
        parent::__construct();
        $this->viewsFolder = 'app/';
        $this->appModel = new AppModel();
        $this->comp = new ComponentsHelper();
    }

    public function index() {
        $folders = $this->appModel->select(TABLE_FOLDERS.'.*, COUNT('.TABLE_NOTES.'.id) AS total_notes')->left_join(TABLE_NOTES, [TABLE_FOLDERS.'.id' => TABLE_NOTES.'.folder_id'])->where([TABLE_FOLDERS.'.user_id' => $this->user->ID])->group_by(TABLE_FOLDERS.'.id')->get_results();

        $this->renderData = ['folders' => $folders];
        $this->render(__FUNCTION__);
    }

    public function new_folder() {
        $result = $this->appModel->insert($this->data->folder)->query();

        if($result) {
            $this->messageStatus = STATUS_SUCCESS;
            $message = $this->comp->alert(['message' => 'A new folder has been created', 'type' => 'alert-success']);
        } else {
            $this->messageStatus = STATUS_ERROR;
            $message = $this->comp->alert(['message' => 'Unable to create folder', 'type' => 'alert-danger']);
        }
        
        $this->send_json(['success' => $this->messageStatus, 'message' => $message]);
    }

    public function delete_folder() {
        $result = $this->appModel->delete()->where(['user_id' => $this->data->user_id, 'id' => $this->data->id])->query();
        
        if($result) {
            $this->messageStatus = STATUS_SUCCESS;
            $message = $this->comp->alert(['message' => 'Deleted folder', 'type' => 'alert-success']);
        } else {
            $this->messageStatus = STATUS_ERROR;
            $message = $this->comp->alert(['message' => 'Unable to delete folder', 'type' => 'alert-danger']);
        }
        
        $this->send_json(['success' => $this->messageStatus, 'message' => $message]);
    }
}