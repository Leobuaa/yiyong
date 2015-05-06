<?php namespace controllers;

class User extends \core\controller {

    private $model;
    private $response;
    private $responseMsg;

    public function __construct() {
        parent::__construct();
        $this->model = new \models\user();
        $this->response = array(
            'success' => false,
            'msg' => ' ',
        );
        $this->responseMsg = array(
            'dataIsNotValid' => '输入的数据格式有误',
            'registerFailed' => '注册出现错误，请再次注册。',
            '1' => '用户不存在',
            '2' => '用户已经注册但是仍在审核过程中',
            '3' => '用户存在并且已经通过审核',
            '4' => '成功提交用户信息，请耐心等待审核结果，审核结果将以手机短信的形式通知。',
            '5' => '该微信用户已经注册，尚未通过审核，请耐心等待。',
            '6' => '该微信用户已经注册，并且通过审核，请不要重复注册。',
            '7' => '该公司已经注册, 但未通过审核。',
            '8' => '该公司已经注册, 并且通过审核，请不要重复注册。',
        );
    }

    /**
     * 用户注册
     */
    public function register() {
        $data = \helpers\parameter::getParameter(array('name', 'company', 'phone', 'landline', 'birthdate', 'gender', 'password', 'wechatId'));

        $isValid = \helpers\gump::is_valid($data, array(
            'name' => 'required',
            'company' => 'required',
            'phone' => 'required|integer',
            'landline' => 'required',
            'password' => 'required',
            'wechatId'=> 'required',
        ));

        if ($isValid === true) {
            $userStatus = $this->model->getUserStatus($data);
            $companyStatus = $this->model->isUserExist($data['company']);
            if ($userStatus == 1 && $companyStatus == 0) {
                if ($this->model->add($data)) {
                    $this->response['success'] = true;
                    $this->response['code'] = 1;
                    $this->response['msg'] = $this->responseMsg['4'];
                } else {
                    $this->response['code'] = 6;
                    $this->response['msg'] = $this->responseMsg['registerFailed'];
                }
            } else if ($userStatus == 2) {
                $this->response['code'] = 2;
                $this->response['msg'] = $this->responseMsg['5'];
            } else if ($userStatus == 3) {
                $this->response['code'] = 3;
                $this->response['msg'] = $this->responseMsg['6'];
            } else if ($companyStatus == 1) {
                $this->response['code'] = 4;
                $this->response['msg'] = $this->responseMsg['7'];
            } else if ($companyStatus == 2) {
                $this->response['code'] = 5;
                $this->response['msg'] = $this->responseMsg['8'];
            }
        } else {
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
        }

        echo json_encode($this->response);
    }

    /**
     * 用户登录
     */
    public function login() {

    }

    /**
     * 用户兑换积分, 给客户经理发送信息, 根据回复的内容确定是否添加积分, 先在数据库中存储该记录, 客户经理回复有效后再添加积分
     */
    public function creditExchange() {

    }

    /**
     * 用户兑换礼品
     */
    public function presentExchange() {

    }

    public function getUserStatus() {
        $data = \helpers\parameter::getParameter(array('wechatId'));

        $isValid = \helpers\gump::is_valid($data, array('wechatId' => 'required'));

        if ($isValid === true) {
            $this->response['code'] = $this->model->getUserStatus($data);
            $this->response['msg'] = $this->responseMsg[$this->response['code']];
        } else {
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
        }

        if ($this->response['code'] == 3) $this->response['success'] = true;

        echo json_encode($this->response);
    }
}