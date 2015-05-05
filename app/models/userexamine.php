<?php namespace models;
/**
 * Created by PhpStorm.
 * User: sicklich
 * Date: 15/4/26
 * Time: 下午4:05
 */


class UserExamine extends \core\Model{
    public function __construct(){
        parent::__construct();
    }

    public function get($data){
        $limitStatement = " LIMIT ".(($data['page']-1)*$data['size']).", ".$data['size'];
        return $this->_db->select("SELECT * FROM user WHERE verified = 0".$limitStatement);
    }

    public function examine($data){
        $id = $data['id'];
        if($this->_db->update('user',$data,array('id'=>$id))){
            return true;
        }else{
            return false;
        }
    }
}