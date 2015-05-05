<?php namespace controllers;
/**
 * Created by PhpStorm.
 * User: sicklich
 * Date: 15/4/26
 * Time: 下午4:04
 * 用户审查控制类
 */

class UserExamine extends \core\controller{
    private $model;
    private $response;
    private $responseMsg;

    public function __construct(){
        parent::__construct();
        $this->model = new \models\UserExamine();
        $this->response = array(
            'success'=>false,
            'msg'=>'',
        );
        $this->responseMsg = array(
            'dataIsNotValid' => '输入的数据格式有误',
            'getSucceed' => '获取待审核用户成功',
            'getFailed' => '获取待审核用户失败',
            'examineSucceed' => '审核成功',
            'examineFailed' => 'examine failed',
        );

        header("Content-Type: application/json;charset=UTF-8"); // 将返回的结果设置为json格式
    }

    public function get(){
        $data = \helpers\parameter::getParameter(array('page', 'size'));
        if ($data['page'] == null) $data['page'] = 1;   // 若未传参则设定默认值
        if ($data['size'] == null) $data['size'] = 10;

        $isValid = \helpers\gump::is_valid($data, array(
            'page' => 'required|integer',
            'size' => 'required|integer',
        )); // 检验传递数据的有效性

        if ($isValid === true) {
            $this->response['success'] = true;
            $this->response['msg'] = $this->responseMsg['getSucceed'];
            $this->response['data'] = $this->model->get($data);
        } else {
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
        }

        echo json_encode($this->response);
    }

    public function examine(){
        $data = \helpers\Parameter::getParameter(array('id','verifiedCode'));

        $isValid = \helpers\gump::is_valid($data, array(
            'id' => 'required|integer',
            'verifiedCode' => 'required|integer',
        ));

        if ($isValid === true) {
            if ($this->model->examine($data)) {
                $this->response['success'] = true;
                $this->response['msg'] = $this->responseMsg['examineSucceed'];
                echo json_encode($this->response);
                return;
            }
        }

        $this->response['msg'] = $this->responseMsg['examineFailed'];

        echo json_encode($this->response);
    }
}