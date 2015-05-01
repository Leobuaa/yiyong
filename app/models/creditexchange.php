<?php namespace models;

class CreditExchange extends \core\model {

    public function __construct() {
        parent::__construct();
    }

    public function get($data) {
        $limitStatement = " LIMIT ".(($data['page'] - 1) * $data['size']).", ".$data['size'];
        return $this->_db->select("SELECT * FROM credit_exchange WHERE availability = 1".$limitStatement);
    }

    public function delete($data) {
        if ($this->isExist($data['id'])) {
            $change = array('availability' => 0); //将该记录的有效性改为0
            $this->_db->update('credit_exchange', $change, array('id' => $data['id']));
            // todo 扣除该用户的积分
            return true;
        } else {
            return false;
        }
    }

    public function isExist($id) {
        if (count($this->_db->select("SELECT id FROM credit_exchange WHERE id = :id", array(':id' => $id))) > 0)
            return true;

        return false;
    }
}