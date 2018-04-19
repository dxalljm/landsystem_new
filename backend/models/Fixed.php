<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%fixed}}".
 *
 * @property integer $id
 * @property integer $farms_id
 * @property string $name
 * @property string $unit
 * @property integer $number
 * @property string $state
 * @property string $remarks
 */
class Fixed extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fixed}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['farms_id', 'number'], 'integer'],
            [['remarks'], 'string'],
            [['name', 'unit', 'state'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'id' => 'ID',
            'farms_id' => '农场ID',
            'name' => '名称',
            'unit' => '单位',
            'number' => '养殖数量',
            'state' => '状态',
            'remarks' => '备注',
        ];
    }
}
