<?php
namespace frontend\helpers;
use app\models\Farms;
use app\models\User;

/**
 * Created by PhpStorm.
 * User: liujiaming
 * Date: 2017/7/16
 * Time: 14:08
 */
class whereHandle
{
    public static function whereToArray($condition) {
        if (! is_array ( $condition )) {
            return $condition;
        }

        if (! isset ( $condition [0] )) {
            // hash format: 'column1' => 'value1', 'column2' => 'value2', ...
            foreach ( $condition as $name => $value ) {
                if (self::isEmpty ( $value )) {
                    unset ( $condition [$name] );
                }
            }
            return $condition;
        }
        $operator = array_shift ( $condition );
        switch (strtoupper ( $operator )) {
            case 'NOT' :
            case 'AND' :
            case 'OR' :
                foreach ( $condition as $i => $operand ) {
                    $subcondition = self::whereToArray( $operand );
                    if (self::isEmpty ( $subcondition )) {
                        unset ( $condition [$i] );
                    } else {
                        $condition [$i] = $subcondition;
                    }
                }
                break;
            case 'LIKE' :
                return [
                    $condition [0] => $condition [1]
                ];
                break;
            case 'BETWEEN':
                return [
                    $condition [0] => [$condition [1],$condition [2]]
                ];
                break;
            default :
                $condition = null;
        }
        $result = [];
        if(is_array($condition)) {
            foreach ($condition as $value) {
                foreach ($value as $k => $v) {
                    $result[$k] = $v;
                }
            }
        } else {
            $result = $condition;
        }
        return $result;
    }
    protected static function isEmpty($value) {
        return $value === '' || $value === [ ] || $value === null || is_string ( $value ) && trim ( $value ) === '';
    }
    
    public static function getOnlyWhere($condition,$only)
    {
        $result = [];
        $array = self::whereToArray($condition);
        foreach ($array as $key => $value) {
            foreach ($only as $val) {
                if($key == $val) {
                    $result[$val] = $value;
                }
            }
        }
        return $result;
    }
    
    public static function toFarmsWhere($condition)
    {
        $result = ['year'=>User::getYear()];
        $array = self::whereToArray($condition);
//        var_dump($array);exit;
        if(is_array($array)) {
            foreach ($array as $key => $value) {

                switch ($key) {
                    case 'management_area':
                        $result['management_area'] = $value;
                        break;
                    case 'state':
                        if($value)
                            $result['state'] = $value;
                        else {
                            $result['state'] = [1,2,3,4,5];
                        }
                        break;
                    case 'farmerpinyin':
                    case 'farmername':
                    case 'pinyin':
                    case 'farmname':
                        $farmsid = [];
                        $farms = Farms::find()->andFilterWhere($condition)->all();
                        foreach ($farms as $farm) {
                            $farmsid[] = $farm['id'];
                        }
                        $result['id'] = $farmsid;
                        break;
                }
            }
        }
        return $result;
    }

    public static function toPlantWhere($condition)
    {
//        var_dump($condition);
        $result['year'] = User::getYear();
        $array = self::whereToArray($condition);
//        var_dump($array);exit;
        if(is_array($array)) {
            foreach ($array as $key => $value) {

                switch ($key) {
                    case 'contractarea':
                        $result['contractarea'] = $value;
                        break;
                    case 'management_area':
                        $result['management_area'] = $value;
                        break;
                    case 'state':
                        $new = [];
                        if(is_array($value)) {
                            for ($i=$value[0];$i<=$value[1];$i++) {
                                $new[] = $i;
                            }
                            $result['state'] = $new;
                        } else {
                            $result['state'] = $value;
                        }

                        break;
//                    case 'year':
//                        unset($condition['year']);
//                        break;
                    case 'farmerpinyin':
                        $where[$key] = $value;
                    case 'farmername':
                        $where[$key] = $value;
                    case 'pinyin':
                        $where[$key] = $value;
                    case 'farmname':
                        $where[$key] = $value;
                        $farmsid = [];
                        $farms = Farms::find()->andFilterWhere($where)->all();
                        foreach ($farms as $farm) {
                            $farmsid[] = $farm['id'];
                        }
                        $result['farms_id'] = $farmsid;
                        break;
                    case 'id':
                        $result['farms_id'] = $value;
                        break;
                }
            }
        }
        return $result;
    }
}