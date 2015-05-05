<?php namespace controllers;

/**
 * 客户经理控制类
 * Class ManagerAdmin
 * @package controllers
 */
class ManagerAdmin extends \core\controller {

    private $model;
    private $response;
    private $responseMsg;

    public function __construct() {
        parent::__construct();
        $this->model = new \models\ManagerAdmin();
        $this->response = array(
            'success' => false,
            'msg' => ' ',
        );
        $this->responseMsg = array(
            'dataIsNotValid' => '输入的数据格式有误',
            'getSucceed' => '获取客户经理信息成功',
            'getFailed' => '获取客户经理信息失败',
            'addSucceed' => '添加客户经理成功',
            'addFailed' => '添加客户经理失败',
            'deleteSucceed' => '删除客户经理成功',
            'deleteFailed' => '删除客户经理失败',
            'updateSucceed' => '更新客户经理信息成功',
            'updateFailed' => '更新客户经理信息失败',
        );
        header("Content-Type: application/json;charset=UTF-8"); // 将返回的结果设置为json格式
    }

    public function get() {
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

    public function add() {
        $data = \helpers\parameter::getParameter(array('name','phone'));

        $isValid = \helpers\gump::is_valid($data, array(
            'name' => 'required',
            'phone' => 'required',
        ));

        if ($isValid === true) {
            if ($this->model->add($data)){
                $this->response['success'] = true;
                $this->response['msg'] = $this->responseMsg['addSucceed'];
                echo json_encode($this->response);
                return;
            }
        }

        $this->response['msg'] = $this->responseMsg['addFailed'];
        echo json_encode($this->response);
    }

    public function delete() {
        $data = \helpers\parameter::getParameter(array('id'));

        $isValid = \helpers\gump::is_valid($data, array('id' => 'required|integer'));

        if ($isValid === true) {
            if ($this->model->delete($data)) {
                $this->response['success'] = true;
                $this->response['msg'] = $this->responseMsg['deleteSucceed'];
                echo json_encode($this->response);
                return;
            }
        }

        $this->response['msg'] = $this->responseMsg['deleteFailed'];
        echo json_encode($this->response);
    }

    public function update() {
        $data = \helpers\parameter::getParameter(array('id', 'name','phone'));

        $isValid = \helpers\gump::is_valid($data, array(
            'id' => 'required|integer',
            'name' => 'required',
            'phone' => 'required',
        ));

        if ($isValid === true) {
            if ($this->model->update($data)) {
                $this->response['success'] = true;
                $this->response['msg'] = $this->responseMsg['updateSucceed'];
                echo json_encode($this->response);
                return;
            }
        }

        $this->response['msg'] = $this->responseMsg['updateFailed'];
        echo json_encode($this->response);
    }
}