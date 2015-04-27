<?php namespace models;

class Present extends \core\model {

    public function __construct() {
        parent::__construct();
    }

    public function get($data) {
        $limitStatement = " LIMIT ".(($data['page'] - 1) * $data['size']).", ".$data['size'];
        return $this->_db->select("SELECT * FROM present".$limitStatement);
    }

    public function add($data) {
        return $this->_db->insert('present', $data);
    }

    public function delete($data) {
        if ($this->isExist($data['id'])) {
            $this->_db->delete('present', array('id' => $data['id']));
            return true;
        } else {
            return false;
        }
    }

    public function update($data) {
        if ($this->isExist($data['id'])) {
            $this->_db->update('present', $data, array('id' => $data['id']));
            return true;
        } else {
            return false;
        }
    }

    public function isExist($id) {
        if (count($this->_db->select("SELECT id FROM present WHERE id = :id", array(':id' => $id))) > 0)
            return true;

        return false;
    }
}