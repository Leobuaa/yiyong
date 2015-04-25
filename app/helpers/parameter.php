<?php namespace helpers;
/**
 * 该类是用于获取传递参数的帮助类
 * Class Parameter
 * @package helpers
 */
class Parameter {

    /**
     * 获取通过GET或者POST方法传递的参数
     * 参数为参数名或者含有多个参数名的字符串数组，若不存在则返回null
     * @param $data
     * @return array|null
     */
    public static function getParameter($data) {
        if (!is_array($data)) {
            if (isset($_GET[$data]))
                return $_GET[$data];
            else if (isset($_POST[$data]))
                return $_POST[$data];
            else
                return null;
        }

        $result = array();
        foreach($data as $value) {
            if (isset($_GET[$value]))
                $result[$value] = $_GET[$value];
            else if (isset($_POST[$value]))
                $result[$value] = $_POST[$value];
            else
                $result[$value] = null;
        }

        return $result;
    }
}