<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once 'query_builder_model.php';

class NoteModel extends QueryBuilder {
    protected $user;

    public function __construct($user) {
        $this->user = $user;
        $this->tableName = TABLE_NOTES;
    }

    public function get_notes($folder_id) {
        $notes = $this->select()->where(['folder_id' => $folder_id])->get_results();
        return $notes;
    }

    public function new_note($data) {
        $result = $this->insert($data)->query();
        return $result;
    }

    public function rename_note($data) {
        $result = $this->update(['name' => $data->name])->where(['id' => $data->id])->query();
        return $result;
    }

    public function delete_note($id) {
        $result = $this->delete()->where(['id' => $id])->query();
        return $result;
    }
}