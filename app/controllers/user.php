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
            '3' => '用户存在并且已经通过审核',   // getUserStatus()
//            '4' => '成功提交用户信息，请耐心等待审核结果，审核结果将以手机短信的形式通知。',
//            '5' => '该微信用户已经注册，尚未通过审核，请耐心等待。',
//            '6' => '该微信用户已经注册，并且通过审核，请不要重复注册。',
//            '7' => '该公司已经注册, 但未通过审核。',
//            '8' => '该公司已经注册, 并且通过审核，请不要重复注册。',  // register()
//            '9' => '登录成功',
//            '10' => '公司名称或密码错误，请重新输入后登录',
//            '11' => '该公司尚未注册，请先进行注册',
//            '12' => '该公司已经注册，还未通过审核',
//            '13' => '该微信用户已经绑定公司，可直接登录',  // login()
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
                    $this->response['msg'] = '成功提交用户信息，请耐心等待审核结果，审核结果将以手机短信的形式通知。';
                    // todo 发送短信给管理审核的人员
                } else {
                    $this->response['code'] = 6;
                    $this->response['msg'] = '注册出现错误，请再次注册。';
                }
            } else if ($userStatus == 2) {
                $this->response['code'] = 2;
                $this->response['msg'] = '该微信用户已经注册，尚未通过审核，请耐心等待。';
            } else if ($userStatus == 3) {
                $this->response['code'] = 3;
                $this->response['msg'] = '该微信用户已经注册，并且通过审核，请不要重复注册。';
            } else if ($companyStatus == 1) {
                $this->response['code'] = 4;
                $this->response['msg'] = '该公司已经注册, 但未通过审核。';
            } else if ($companyStatus == 2) {
                $this->response['code'] = 5;
                $this->response['msg'] = '该公司已经注册, 并且通过审核，请不要重复注册。';
            }
        } else {
            $this->response['code'] = 7;
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
        }

        echo json_encode($this->response);
    }

    /**
     * 用户登录
     */
    public function login() {
        $data = \helpers\parameter::getParameter(array('company', 'password', 'wechatId'));

        $isValid = \helpers\gump::is_valid($data, array(
            'company' => 'required',
            'password' => 'required',
            'wechatId' => 'required',
        ));

        if ($isValid === true) {
            $userStatus = $this->model->isUserBindingExist($data['wechatId']);
            $companyStatus = $this->model->isUserExist($data['company']);
            if ($userStatus) { // 该微信用户已经绑定了公司
                $this->response['code'] = 5;
                $this->response['msg'] = '该微信用户已经绑定公司，可直接登录';
            } else {  // 该微信用户尚未绑定公司，状态为可用
                if ($companyStatus == 2) { // 该公司已经注册并且通过审核
                    if ($this->model->login($data)) {
                        $this->response['code'] = 1;
                        $this->response['msg'] = '登录成功';
                    } else {
                        $this->response['code'] = 2;
                        $this->response['msg'] = '公司名称或密码错误，请重新输入后登录';
                    }
                } else if ($companyStatus == 1) { // 该公司已经注册尚未通过审核
                    $this->response['code'] = 4;
                    $this->response['msg'] = '该公司已经注册，还未通过审核';
                } else if ($companyStatus == 0) { // 该公司尚未注册
                    $this->response['code'] = 3;
                    $this->response['msg'] = '该公司尚未注册，请先进行注册';
                }
            }
        } else {
            $this->response['code'] = 6;
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
        }

        echo json_encode($this->response);
    }

    /**
     * 用户兑换积分, 给客户经理发送信息, 计算客户所得的积分数量, 根据回复的内容确定是否添加积分, 先在数据库中存储该记录, 客户经理回复有效后再给用户(公司)添加积分
     */
    public function creditExchange() {
        $data = \helpers\parameter::getParameter(array('productCategory', 'productVersion', 'consumptionType', 'managerName', 'money', 'wechatId'));

        $isValid = \helpers\gump::is_valid($data, array(
            'productCategory' => 'required',
            'productVersion' => 'required',
            'consumptionType' => 'required',
            'managerName' => 'required',
            'money' => 'required|numeric',
            'wechatId' => 'required',
        ));

        if ($isValid === true) {
            if ($this->model->getUserStatus($data) == 3) {
                if ($this->model->addCreditExchange($data)) {
                    // todo 给指定客户经理发送短信

                    // todo 根据回复的短信内容确定是否给用户添加积分

                    $this->response['success'] = true;
                    $this->response['msg'] = '积分兑换记录已经提交，请耐心等待审核';
                    $this->response['code'] = 1;
                } else {
                    $this->response['msg'] = '提交积分兑换记录失败，请再次提交';
                    $this->response['code'] = 4;
                }
            } else {
                $this->response['msg'] = '该用户或公司暂不具备兑换积分的资格';
                $this->response['code'] = 2;
            }
        } else {
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
            $this->response['code'] = 3;
        }

        echo json_encode($this->response);
    }

    /**
     * 用户兑换礼品
     */
    public function presentExchange() {

    }

    /**
     * 获取用户的状态
     */
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