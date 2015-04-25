<?php namespace controllers;
/**
 * 后台系统管理员控制类
 * Class Admin
 * @package controllers
 */
class Admin extends \core\controller {

    private $model;
    private $response;
    private $responseMsg;

    public function __construct() {
        parent::__construct();
        $this->model = new \models\admin();
        $this->response = array(
            'success' => false,
            'msg' => ' ',
        );
        $this->responseMsg = array(
            'registerSucceed' => '注册管理员成功',
            'registerFailed' => '注册管理员失败, 该用户名已经存在',
            'loginSucceed' => '登录管理员成功',
            'loginFailed' => '登录管理员失败',
            'logoutSucceed' => '登出管理员成功',
            'dataIsNotValid' => '输入的数据格式有误',
            'passwordWrong' => '用户名或者密码错误',
            'isLogin' => '管理员已经登录',
            'isNotLogin' => '管理员尚未登录',
        );
        header("Content-Type: application/json;charset=UTF-8"); // 将返回的结果设置为json格式
    }

    public function register() {
        $data = \helpers\parameter::getParameter(array('username', 'password'));
        $isValid = \helpers\gump::is_valid($data, array(
            'username' => 'required|alpha_numeric',
            'password' => 'required|min_len,6|max_len,20',
        ));

        if ($isValid === true) {
            if ($this->model->register($data)) {
                $this->response['success'] = true;
                $this->response['msg'] = $this->responseMsg['registerSucceed'];
            } else {
                $this->response['msg'] = $this->responseMsg['registerFailed'];
            }
        } else {
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
            $this->response['data'] = $isValid;
        }

        echo json_encode($this->response);
    }

    public function login() {
        // 判断是否已经登录，已经登录则直接返回已经登录信息
        if (\helpers\session::get('username')) {
            $this->response['success'] = true;
            $this->response['msg'] = $this->responseMsg['isLogin'];
            $this->response['data'] = array(
                'username' => \helpers\session::get('username'),
            );
            echo json_encode($this->response);
            return;
        }

        $data = \helpers\parameter::getParameter(array('username', 'password'));
        $isValid = \helpers\gump::is_valid($data, array(
            'username' => 'required',
            'password' => 'required',
        ));

        if ($isValid === true) {
            if ($this->model->login($data)) {
                \helpers\session::init();
                \helpers\session::set('username', $data['username']);
                $this->response['success'] = true;
                $this->response['msg'] = $this->responseMsg['loginSucceed'];
            } else {
                $this->response['msg'] = $this->responseMsg['loginFailed'];
                $this->response['data'] = array(
                    'errorCode' => '1',
                    'errorMsg' => $this->responseMsg['passwordWrong'],
                );
            }
        } else {
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
            $this->response['data'] = $isValid;
        }

        echo json_encode($this->response);
    }

    public function logout() {
        if (\helpers\session::get('username')) {
            \helpers\session::destroy('username');
        }
        $this->response['success'] = true;
        $this->response['msg'] = $this->responseMsg['logoutSucceed'];

        echo json_encode($this->response);
    }

    public function isLogin() {
        if (\helpers\session::get('username')) {
            $this->response['success'] = true;
            $this->response['msg'] = $this->responseMsg['isLogin'];
            $this->response['data'] = array(
              'username' => \helpers\session::get('username'),
            );
        } else {
            $this->response['msg'] = $this->responseMsg['isNotLogin'];
        }

        echo json_encode($this->response);
    }
}