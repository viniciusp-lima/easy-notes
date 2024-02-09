<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once 'query_builder_model.php';

class FolderModel extends QueryBuilder {
    protected $user;

    public function __construct($user) {
        $this->user = $user;
        $this->tableName = TABLE_FOLDERS;
    }

    public function get_folders() {
        $folders = $this->select(TABLE_FOLDERS.'.*, COUNT('.TABLE_NOTES.'.id) AS total_notes')->left_join(TABLE_NOTES, [TABLE_FOLDERS.'.id' => TABLE_NOTES.'.folder_id'])->where([TABLE_FOLDERS.'.user_id' => $this->user->ID])->group_by(TABLE_FOLDERS.'.id')->get_results();
        return $folders;
    }

    public function new_folder($data) {
        $result = $this->insert($data)->query();
        return $result;
    }

    public function rename_folder($data) {
        $result = $this->update(['name' => $data->name])->where(['id' => $data->id])->query();
        return $result;
    }

    public function delete_folder($id) {
        $result = $this->delete()->where(['id' => $id])->query();
        return $result;
    }
}