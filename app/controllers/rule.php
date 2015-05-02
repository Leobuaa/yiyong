<?php namespace controllers;

class Rule extends \core\controller {

    private $model;
    private $response;
    private $responseMsg;

    public function __construct() {
        parent::__construct();
        $this->model = new \models\rule();
        $this->response = array(
            'success' => false,
            'msg' => ' ',
        );
        $this->responseMsg = array(
            'dataIsNotValid' => '输入的数据格式有误',
            'getSucceed' => '获取兑换规则成功',
            'getFailed' => '获取兑换规则失败',
            'addSucceed' => '添加兑换规则成功',
            'addFailed' => '添加兑换规则失败',
            'deleteSucceed' => '删除兑换规则成功',
            'deleteFailed' => '删除兑换规则失败',
            'updateSucceed' => '更新兑换规则成功',
            'updateFailed' => '更新兑换规则失败',
        );
    }

    public function get() {
        $data = \helpers\parameter::getParameter(array('page', 'size'));

        if ($data['page'] == null) $data['page'] = 1; // 若未传参则设定默认值
        if ($data['size'] == null || $data['size'] == 0) $data['size'] = 10;

        $isValid = \helpers\gump::is_valid($data, array(
            'page' => 'required|integer',
            'size' => 'required|integer',
        ));

        if ($isValid === true) {
            $this->response['success'] = true;
            $this->response['msg'] = $this->responseMsg['getSucceed'];
            $this->response['page'] = ceil($this->model->number() / $data['size']);
            $this->response['data'] = $this->model->get($data);
        } else {
            $this->response['msg'] = $this->responseMsg['getFailed'];
        }

        echo json_encode($this->response);
    }

    public function add() {
        $data = \helpers\parameter::getParameter(array('name', 'content'));

        $isValid = \helpers\gump::is_valid($data, array(
            'name' => 'required',
            'content' => 'required',
        ));

        if ($isValid === true) {
            if ($this->model->add($data)) {
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
        $data = \helpers\parameter::getParameter(array('id', 'name', 'content'));

        $isValid = \helpers\gump::is_valid($data, array(
            'id' => 'required|integer',
            'name' => 'required',
            'content' => 'required',
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