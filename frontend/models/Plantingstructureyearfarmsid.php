<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%plantingstructureyearfarmsid}}".
 *
 * @property integer $id
 * @property integer $farms_id
 * @property integer $state
 * @property double $contractarea
 * @property string $contractnumber
 * @property integer $isfinished
 * @property string $year
 */
class Plantingstructureyearfarmsid extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plantingstructureyearfarmsid}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['farms_id', 'state', 'isfinished','create_at','management_area'], 'integer'],
            [['contractarea'], 'number'],
            [['contractnumber', 'year','farmname','farmername','pinyin','farmerpinyin','cardid','telephone'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'farms_id' => 'Farms ID',
            'cardid' => '身份证号',
            'telephone' => '联系电话',
            'state' => 'State',
            'contractarea' => '合同面积',
            'contractnumber' => '合同号',
            'isfinished' => 'Isfinished',
            'year' => 'Year',
            'create_at' => '创建日期',
            'farmname' => '农场名称',
            'farmername' => '法人姓名',
            'management_area' => '管理区',
            'pinyin' => 'pinyin',
            'farmerpinyin' => 'farmerpinyin',
        ];
    }
}
