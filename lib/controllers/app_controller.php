<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once CORE_PATH . 'system.php';
require_once MODELS_PATH . 'note_model.php';
require_once MODELS_PATH . 'folder_model.php';
require_once HELPERS_PATH . 'components_helper.php';

class AppController extends System {
    protected $comp;
    protected $messageStatus;

    public function __construct() {
        parent::__construct();
        $this->viewsFolder = 'app/';
        $this->comp = new ComponentsHelper();
        $this->noteModel = new NoteModel($this->user);
        $this->folderModel = new FolderModel($this->user);
    }

    public function index() {
        $folders = $this->folderModel->get_folders();

        $this->renderData = ['folders' => $folders];
        $this->render(__FUNCTION__);
    }

    public function new_folder() {
        $result = $this->folderModel->new_folder($this->data);

        if($result) {
            $this->messageStatus = STATUS_SUCCESS;
            $message = $this->comp->alert(['message' => 'A new folder has been created', 'type' => 'alert-success']);
        } else {
            $this->messageStatus = STATUS_ERROR;
            $message = $this->comp->alert(['message' => 'Unable to create folder', 'type' => 'alert-danger']);
        }
        
        $this->send_json(['success' => $this->messageStatus, 'message' => $message]);
    }

    public function rename_folder() {
        $result = $this->folderModel->rename_folder($this->data);

        if($result) {
            $this->messageStatus = STATUS_SUCCESS;
            $message = $this->comp->alert(['message' => 'Folder name has been updated', 'type' => 'alert-success']);
        } else {
            $this->messageStatus = STATUS_ERROR;
            $message = $this->comp->alert(['message' => 'Unable to update folder name', 'type' => 'alert-danger']);
        }
        
        $this->send_json(['success' => $this->messageStatus, 'message' => $message]);
    }

    public function delete_folder() {
        $result = $this->folderModel->delete_folder($this->data->id);
        
        if($result) {
            $this->messageStatus = STATUS_SUCCESS;
            $message = $this->comp->alert(['message' => 'Deleted folder', 'type' => 'alert-success']);
        } else {
            $this->messageStatus = STATUS_ERROR;
            $message = $this->comp->alert(['message' => 'Unable to delete folder', 'type' => 'alert-danger']);
        }
        
        $this->send_json(['success' => $this->messageStatus, 'message' => $message]);
    }

    public function new_note() {
        $result = $this->noteModel->new_note($this->data);

        if($result) {
            $this->messageStatus = STATUS_SUCCESS;
            $message = $this->comp->alert(['message' => 'New note created', 'type' => 'alert-success']);
        } else {
            $this->messageStatus = STATUS_ERROR;
            $message = $this->comp->alert(['message' => 'Unable to create this note', 'type' => 'alert-danger']);
        }
        
        $this->send_json(['success' => $this->messageStatus, 'message' => $message]);
    }

    public function rename_note() {
        $result = $this->noteModel->rename_note($this->data);

        if($result) {
            $this->messageStatus = STATUS_SUCCESS;
            $message = $this->comp->alert(['message' => 'Note name has been updated', 'type' => 'alert-success']);
        } else {
            $this->messageStatus = STATUS_ERROR;
            $message = $this->comp->alert(['message' => 'Unable to update note name', 'type' => 'alert-danger']);
        }
        
        $this->send_json(['success' => $this->messageStatus, 'message' => $message]);
    }

    public function delete_note() {
        $result = $this->noteModel->delete_note($this->data->id);
        
        if($result) {
            $this->messageStatus = STATUS_SUCCESS;
            $message = $this->comp->alert(['message' => 'Deleted note', 'type' => 'alert-success']);
        } else {
            $this->messageStatus = STATUS_ERROR;
            $message = $this->comp->alert(['message' => 'Unable to delete note', 'type' => 'alert-danger']);
        }
        
        $this->send_json(['success' => $this->messageStatus, 'message' => $message]);
    }
}