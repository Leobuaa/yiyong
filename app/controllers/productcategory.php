<?php namespace controllers;

/**
 * 产品类别控制类
 * Class ProductCategory
 * @package controllers
 */
class ProductCategory extends \core\controller {

    private $model;
    private $response;
    private $responseMsg;

    public function __construct() {
        parent::__construct();
        $this->model = new \models\productcategory();
        $this->response = array(
            'success' => false,
            'msg' => ' ',
        );
        $this->responseMsg = array(
            'dataIsNotValid' => '输入的数据格式有误',
            'getSucceed' => '获取产品类别成功',
            'getFailed' => '获取产品类别失败',
            'addSucceed' => '添加产品类别成功',
            'addFailed' => '添加产品类别失败',
            'deleteSucceed' => '删除产品类别及改产品类别下的产品版本成功',
            'deleteFailed' => '删除产品类别失败',
            'updateSucceed' => '更新产品类别成功',
            'updateFailed' => '更新产品类别失败',
        );
    }

    public function get() {
        $data = \helpers\parameter::getParameter(array('page', 'size'));
        if ($data['page'] == null) $data['page'] = 1;   // 若未传参则设定默认值
        if ($data['size'] == null || $data['size'] == 0) $data['size'] = 10;

        $isValid = \helpers\gump::is_valid($data, array(
            'page' => 'required|integer',
            'size' => 'required|integer',
        )); // 检验传递数据的有效性

        if ($isValid === true) {
            $this->response['success'] = true;
            $this->response['msg'] = $this->responseMsg['getSucceed'];
            $this->response['page'] = ceil($this->model->number() / $data['size']);
            $this->response['data'] = $this->model->get($data);
        } else {
            $this->response['msg'] = $this->responseMsg['dataIsNotValid'];
        }

        echo json_encode($this->response);
    }

    public function add() {
        $data = \helpers\parameter::getParameter(array('name'));

        $isValid = \helpers\gump::is_valid($data, array('name' => 'required'));

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
        $data = \helpers\parameter::getParameter(array('id', 'name'));

        $isValid = \helpers\gump::is_valid($data, array(
            'id' => 'required|integer',
            'name' => 'required',
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