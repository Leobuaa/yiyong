<?php namespace models;

class Rule extends \core\model {

    public function __construct() {
        parent::__construct();
    }

    public function get($data) {
        $limitStatement = " LIMIT ".(($data['page'] - 1) * $data['size']).", ".$data['size'];
        return $this->_db->select("SELECT * FROM rule".$limitStatement);
    }

    public function add($data) {
        return $this->_db->insert('rule', $data);
    }

    public function delete($data) {
        if ($this->isExist($data['id'])) {
            $this->_db->delete('rule', array('id' => $data['id']));
            return true;
        } else {
            return false;
        }
    }

    public function update($data) {
        if ($this->isExist($data['id'])) {
            $this->_db->update('rule', $data, array('id' => $data['id']));
            return true;
        } else {
            return false;
        }
    }

    public function isExist($id) {
        if (count($this->_db->select("SELECT id FROM rule WHERE id = :id", array(':id' => $id))) > 0)
            return true;

        return false;
    }
}