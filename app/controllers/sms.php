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
                'msg' => $data['content'],
                'p' => $data['phones'],
                'isUrlEncode' => 'no',
                'charSetStr' => 'utf',
            );
            $url = 'http://api.app2e.com/smsBigSend.api.php';
            $response = json_decode($this->sendPost($url, $smsData));
            if ($response->status == '100') {
                $this->response['success'] = true;
                $this->response['msg'] = $this->responseMsg['sendSucceed'];
            } else {
                $this->response['msg'] = $this->responseMsg['sendFailed'];
            }
        } else {
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
        }

        echo json_encode($this->response);
    }

    /**
     * 发送post请求
     * @param string $url 请求地址
     * @param array $postDataArray post键值对数据
     * @return string
     */
    function sendPost($url, $postDataArray) {

        $postData = http_build_query($postDataArray);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postData,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        return $result;
    }
}