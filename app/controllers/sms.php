<?php namespace controllers;

class SMS extends \core\controller {

    private $response;
    private $responseMsg;

    public function __construct() {
        $this->response = array(
            'success' => false,
            'msg' => ' ',
        );
        $this->responseMsg = array(
            'dataIsNotValid' => '输入的数据格式有误',
            'sendSucceed' => '发送短信成功',
            'sendFailed' => '发送短信失败',
        );
    }

    public function send() {
        $data = \helpers\parameter::getParameter(array('content', 'phones'));

        $isValid = \helpers\gump::is_valid($data, array('content' => 'required', 'phones' => 'required'));

        if ($isValid) {
            $smsData = array(
                'username' => SMS_USERNAME,
                'pwd' => md5(SMS_PWD),
                'content' => $data['content'],
                'p' => $data['phones'],
                'isUrlEncode' => 'no',
            );
        } else {
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
        }
    }
}