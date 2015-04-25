<?php namespace models;

class Admin extends \core\model {

    public function __construct() {
        parent::__construct();
    }

    public function register($data) {
        if ($this->isExist($data['username']))
            return false;
        $data['password'] = md5($data['password']);
        return $this->_db->insert('administrator', $data);
    }

    public function login($data) {
        $data['password'] = md5($data['password']);
        $user = $this->_db->select('SELECT username, password FROM administrator WHERE username = :username',
                                    array(':username' => $data['username']));
        if ($data['username'] == $user[0]->username && $data['password'] == $user[0]->password)
            return true;
        else
            return false;
    }

    private function isExist($username) {
        if (count($this->_db->select('SELECT username FROM administrator WHERE username = :username',
                                      array(':username' => $username))) > 0)
            return true;

        return false;
    }
}