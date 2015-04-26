<?php namespace models;

class ConsumptionType extends \core\model {

    public function __construct() {
        parent::__construct();
    }

    public function get($data) {
        $limitStatement = " LIMIT ".(($data['page'] - 1) * $data['size']).", ".$data['size'];
        return $this->_db->select("SELECT * FROM consumption_type".$limitStatement);
    }

    public function add($data) {
        return $this->_db->insert('consumption_type', $data);
    }

    public function delete($data) {
        if ($this->isExist($data['id'])) {
            $this->_db->delete('consumption_type', array('id' => $data['id']));
            return true;
        } else {
            return false;
        }
    }

    public function update($data) {
        if ($this->isExist($data['id'])) {
            $this->_db->update('consumption_type', $data, array('id' => $data['id']));
            return true;
        } else {
            return false;
        }
    }

    public function isExist($id) {
        if (count($this->_db->select("SELECT id FROM consumption_type WHERE id = :id", array(':id' => $id))) > 0)
            return true;

        return false;
    }
}