<?php namespace models;

class ManagerAdmin extends \core\model {

    public function __construct() {
        parent::__construct();
    }

    public function get($data) {
        $limitStatement = " LIMIT ".(($data['page'] - 1) * $data['size']).", ".$data['size'];
        return $this->_db->select("SELECT * FROM manager".$limitStatement);
    }

    public function add($data) {
        return $this->_db->insert('manager', $data);
    }

    public function delete($data) {
        $id = $data['id'];

        if ($this->isExist($id)) {
            $this->_db->delete('manager', $data); // 删除该产品类别
            return true;
        } else {
            return false;
        }
    }

    public function update($data) {
        $id = $data['id'];

        if ($this->isExist($id)) {
            $this->_db->update('manager', $data, array('id' => $id));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断改产品类别是否存在, 在删除与更新时候均需要用到
     * @param $id
     * @return bool
     */
    private function isExist($id) {
        if (count($this->_db->select("SELECT id FROM manager WHERE id = :id", array(':id' => $id))) > 0)
            return true;

        return false;
    }
}