<?php namespace models;

class CreditUsage extends \core\model {

    public function __construct() {
        parent::__construct();
    }

    public function get($data) {
        $limitStatement = " LIMIT ".(($data['page'] - 1) * $data['size']).", ".$data['size'];
        return $this->_db->select("SELECT CU.id, CU.user_id AS userId, P.id AS presentId, P.name AS presentName, P.picture_url AS pictureUrl, description, credit, use_time AS useTime
                                   FROM credit_usage AS CU JOIN present AS P ON CU.present_id = P.id".$limitStatement);
    }
}