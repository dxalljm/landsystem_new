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
 * @property integer $create_at
 * @property string $farmname
 * @property string $farmername
 * @property integer $management_area
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
            [['farms_id', 'state', 'isfinished', 'create_at', 'management_area'], 'integer'],
            [['contractarea'], 'number'],
            [['contractnumber', 'year', 'farmname', 'farmername'], 'string', 'max' => 500],
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
            'state' => 'State',
            'contractarea' => 'Contractarea',
            'contractnumber' => 'Contractnumber',
            'isfinished' => 'Isfinished',
            'year' => 'Year',
            'create_at' => 'Create At',
            'farmname' => 'Farmname',
            'farmername' => 'Farmername',
            'management_area' => 'Management Area',
        ];
    }
}
