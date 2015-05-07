<?php namespace models;

class User extends \core\model {

    public function __construct() {
        parent::__construct();
    }

    public function getUserStatus($data) {
        $wechatId = $data['wechatId'];
        $userBinding = $this->_db->select("SELECT * FROM user_binding WHERE third_part_id = :wechatId", array(':wechatId' => $wechatId));
        if (count($userBinding) > 0) {
            $userId = $userBinding[0]->user_id;
            $user = $this->_db->select("SELECT verified FROM user WHERE id = :userId AND availability = 1", array(':userId' => $userId));
            if (count($user) > 0 && $user[0]->verified == 1) {
                return 3;
            } else {
                return 2;
            }
        } else {
            return 1;
        }
    }

    public function add($data) {
        $userBinding = array('third_part_id' => $data['wechatId']);
        $user = $data;
        $user['password'] = md5($user['password']);
        unset($user['wechatId']);

        $userBinding['user_id'] = $this->_db->insert('user', $user); // 添加公司

        if($this->_db->insert('user_binding', $userBinding)) // 绑定用户
            return true;
        else
            return false;
    }

    public function login($data) {
        $user = $this->_db->select("SELECT id, password FROM user WHERE company = :company AND availability = 1", array(':company' => $data['company']));
        if (count($user) > 0 && $user[0]->password == md5($data['password'])) {
            $userBinding['user_id'] = $user[0]->id;
            $userBinding['third_part_id'] = $data['wechatId'];
            $this->_db->insert('user_binding', $userBinding);
            return true;
        }
        else
            return false;
    }

    /**
     * 判断该第三方用户是否绑定
     * @param $wechatId
     * @return bool
     */
    public function isUserBindingExist($wechatId) {
        if (count($this->_db->select("SELECT * FROM user_binding WHERE third_part_id = :wechatId", array(':wechatId' => $wechatId))) > 0)
            return true;
        else
            return false;
    }

    /**
     * 通过公司名称判断其状态
     * @param $company
     * @return int
     */
    public function isUserExist($company) {
        $user = $this->_db->select("SELECT verified FROM user WHERE company = :company AND availability = 1", array(':company' => $company));
        if (count($user) > 0) {
            if ($user[0]->verified == 1)
                return 2; // 该公司存在并且通过审核
            else
                return 1; // 该公司存在但尚未通过审核
        } else {
            return 0; // 该公司尚未注册
        }
    }
}