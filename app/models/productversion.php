<?php namespace models;

class ProductVersion extends \core\model {

    public function __construct() {
        parent::__construct();
    }

    public function get($data) {
        $limitStatement = " LIMIT ".(($data['page'] - 1) * $data['size']).", ".$data['size'];
        if ($data['categoryId'] == 0) // 产品类别id若为0则返回所有产品类别的产品版本
            return $this->_db->select("SELECT PV.id, PV.category_id AS categoryId, PC.name AS categoryName, PV.name FROM product_version AS PV
                                       JOIN product_category AS PC ON PV.category_id = PC.id".$limitStatement);
        else
            return $this->_db->select("SELECT PV.id, PV.category_id AS categoryId, PC.name AS categoryName, PV.name FROM product_version AS PV
                                       JOIN product_category AS PC ON PV.category_id = PC.id WHERE PV.category_id = :categoryId".$limitStatement,
                                   array(':categoryId' => $data['categoryId']));
    }

    public function add($data) {
        if ($this->isCategoryExist($data['categoryId'])) {
            $this->changeKey('category_id', 'categoryId', $data);
            return $this->_db->insert('product_version', $data);
        } else
            return false;
    }

    public function delete($data) {
        if ($this->isExist($data['id'])) {
            $this->_db->delete('product_version', $data);
            return true;
        } else {
            return false;
        }
    }

    public function update($data) {
        $id = $data['id'];

        if ($this->isExist($id) && $this->isCategoryExist($data['categoryId'])) {
            $this->changeKey('category_id', 'categoryId', $data);
            $this->_db->update('product_version', $data, array('id' => $id));
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断产品类别的id是否存在
     * @param $id
     * @return bool
     */
    private function isCategoryExist($id) {
        if (count($this->_db->select("SELECT id FROM product_category WHERE id = :id", array(':id' => $id))) > 0)
            return true;

        return false;
    }

    /**
     * 判断产品版本是否存在, 在删除与更新的时候需要用到
     * @param $id
     * @return bool
     */
    private function isExist($id) {
        if (count($this->_db->select("SELECT id FROM product_version WHERE id = :id", array(':id' => $id))) > 0)
            return true;

        return false;
    }

    /**
     * 修改数组的键的名称, 由于数据库变量命名采用下划线分割而传递的参数采用的是驼峰命名法, 故需要进行变键名转换
     * @param $newKey
     * @param $oldKey
     * @param $data
     */
    private function changeKey($newKey, $oldKey, & $data){
        $data[$newKey] = $data[$oldKey];
        unset($data[$oldKey]);
    }
}