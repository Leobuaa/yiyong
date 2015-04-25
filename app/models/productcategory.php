<?php namespace models;

class ProductCategory extends \core\model {

    public function __construct() {
        parent::__construct();
    }

    public function get($data) {
        $limitStatement = " LIMIT ".(($data['page'] - 1) * $data['size']).", ".$data['size'];
        return $this->_db->select("SELECT * FROM product_category".$limitStatement);
    }

    public function add($data) {
        return $this->_db->insert('product_category', $data);
    }

    public function delete($data) {
        $id = $data['id'];

        if ($this->isExist($id)) {
            // 删除该产品类别下的所有产品版本
            $statement = "DELETE FROM product_version WHERE category_id = $id";
            $this->_db->exec($statement);
            // 删除该产品类别
            $this->_db->delete('product_category', $data);
            return true;
        } else {
            return false;
        }
    }

    public function update($data) {
        $id = $data['id'];

        if ($this->isExist($id)) {
            $this->_db->update('product_category', $data, array('id' => $id));
            return true;
        } else {
            return false;
        }
    }

    private function isExist($id) {
        if (count($this->_db->select("SELECT id FROM product_category WHERE id = :id", array(':id' => $id))) > 0)
            return true;

        return false;
    }
}