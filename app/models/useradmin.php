<?php namespace models;
/**
 * Created by PhpStorm.
 * User: sicklich
 * Date: 15/4/28
 * Time: 上午9:42
 * 审核通过用户的管理
 */


class UserAdmin extends \core\Model{
    public function __construct(){
        parent::__construct();
    }

    public function get($data){
        $limitStatement = " LIMIT ".(($data['page']-1)*$data['size']).", ".$data['size'];
        return $this->_db->select("SELECT * FROM user WHERE verified = 1 and availability = 1".$limitStatement);
    }

    public function add($data){
        return $this->_db->insert('user',$data);
    }

    public function delete($data){
        $id=$data['id'];
        if ($this->isExist($id)){
            $stmt="UPDATE user SET availability=0 WHERE id=$id";
            $this->_db->exec($stmt);

            return true;
        }else{
            return false;
        }

    }

    public function update($data){
        $id=$data['id'];
        if($this->isExist($id)){
            $this->_db->update('user',$data,array('id'=>$id));//$id不止一个？数组？
            return true;
        }else{
            return false;
        }


    }

    /**
     * 判断用户是否存在，在删除和编辑用户时均需用到
     * @param $id
     * @return bool
     */
    private function isExist($id) {
        if (count($this->_db->select("SELECT id FROM user WHERE id = :id and verified=1 and avaiability=1 ", array(':id' => $id))) > 0)//需不需要加上availability?
            return true;

        return false;
    }
}




