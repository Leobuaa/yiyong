<?php namespace controllers;

class Present extends \core\controller {

    private $model;
    private $response;
    private $responseMsg;

    public function __construct() {
        parent::__construct();
        $this->model = new \models\present();
        $this->response = array(
            'success' => false,
            'msg' => ' ',
        );
        $this->responseMsg = array(
            'dataIsNotValid' => '输入的数据格式有误',
            'imgError' => '上传的图片不符合要求, 出现错误',
            'getSucceed' => '获取礼品成功',
            'getFailed' => '获取礼品失败',
            'addSucceed' => '添加礼品成功',
            'addFailed' => '添加礼品失败',
            'deleteSucceed' => '删除礼品成功',
            'deleteFailed' => '删除礼品失败',
            'updateSucceed' => '更新礼品成功',
            'updateFailed' => '更新礼品失败',
        );
        require_once('app/helpers/upyun.php');
    }

    public function get() {
        $data = \helpers\parameter::getParameter(array('page', 'size'));

        if ($data['page'] == null) $data['page'] = 1; // 若未传参则设定默认值
        if ($data['size'] == null) $data['size'] = 10;

        $isValid = \helpers\gump::is_valid($data, array(
            'page' => 'required|integer',
            'size' => 'required|integer',
        ));

        if ($isValid === true) {
            $this->response['success'] = true;
            $this->response['msg'] = $this->responseMsg['getSucceed'];
            $this->response['data'] = $this->model->get($data);
        } else {
            $this->response['msg'] = $this->responseMsg['getFailed'];
        }

        echo json_encode($this->response);
    }

    public function add() {
        $data = \helpers\parameter::getParameter(array('name', 'description', 'credit'));

        $isSaved = $this->saveImg($data);

        $isValid = \helpers\gump::is_valid($data, array(
            'name' => 'required',
            'credit' => 'required|integer',
            'picture_url' => 'required',
        ));

        if ($isValid === true && $isSaved == true) {
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
            if ($this->deleteImg($data) && $this->model->delete($data)) {
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
        $data = \helpers\parameter::getParameter(array('id', 'name', 'description', 'credit'));

        //存储图片
        $isSaved = true;
        if (!empty(($_FILES['picture']))) {
            $isSaved = $this->saveImg($data);
            if ($isSaved)
                $this->deleteImg($data); // 只有在新的照片存储成功之后才会删除旧的照片
        }

        $isValid = \helpers\gump::is_valid($data, array(
            'id' => 'required|integer',
            'name' => 'required',
            'credit' => 'required|integer',
        ));

        if ($isValid === true && $isSaved == true) {
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

    private function saveImg(& $data) {
        // 检查上传的文件是否有误
        if ((!empty($_FILES['picture'])) && ($_FILES['picture']['error'] == 0)) {
            $filename = basename($_FILES['picture']['name']);
            $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
            // 检查文件的格式是否为图片
            if ($_FILES['picture']['size'] < 350000) {
                if (($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') && ($_FILES['picture']['type'] == 'image/jpeg' ||
                        $_FILES['picture']['type'] == 'image/png' || $_FILES['picture']['type'] == 'image/png')){
                    //存储到又拍云
                    $upyun = new \UpYun(BUCKET_NAME, OPERATOR_NAME, OPERATOR_PWD);
                    $fileHandler = fopen($_FILES['picture']['tmp_name'], 'r');
                    $path = '/present/';
                    $date = date('YmdHis',time());
                    $filename = $date.$filename; // 为图片添加唯一时间的标识
                    try {
                        $upyun->writeFile($path.$filename, $fileHandler, true);
                    } catch(\Exception $e) {
                        return false;
                    }
                    fclose($fileHandler);

                    $data['picture_url'] = IMG_URL.$path.$filename;
                    return true;
                }
            }
        }

        $this->response['msg'] = $this->responseMsg['imgError'];
        return false;
    }

    private function deleteImg($data) {
        $path = '/present/';
        $filename = $this->model->getFilename($data['id']);
        $upyun = new \UpYun(BUCKET_NAME, OPERATOR_NAME, OPERATOR_PWD);
        try {
            $upyun->delete($path.$filename);
        } catch(\Exception $e) {
            return false;
        }

        return true;
    }
}