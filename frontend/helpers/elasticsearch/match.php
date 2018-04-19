<?php
namespace frontend\helpers;
/**
 * Created by PhpStorm.
 * User: liujiaming
 * Date: 2018/4/1
 * Time: 16:44
 */
class match
{
    public static $expression;
    public static function expression($str)
    {
        self::$expression = $str;
        return $this;
    }
    public function getNumber($str)
    {
        preg_match_all('/\\d+\/', $str, $temp);// 正则查找[数字]
        return $temp;
    }
}