<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%machineapply}}".
 *
 * @property integer $id
 * @property integer $farms_id
 * @property string $farmername
 * @property integer $age
 * @property string $sex
 * @property string $domicile
 * @property integer $management_area
 * @property string $cardid
 * @property string $telephone
 * @property integer $machineoffarm_id
 * @property string $farmerpinyin
 */
class Machineapply extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%machineapply}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['farms_id', 'age', 'management_area', 'machineoffarm_id'], 'integer'],
            [['farmername', 'sex', 'domicile', 'cardid', 'telephone', 'farmerpinyin'], 'string', 'max' => 500]
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
            'farmername' => '法人姓名',
            'age' => '年龄',
            'sex' => '性别',
            'domicile' => '户籍所在地',
            'management_area' => '管理区',
            'cardid' => '法人身份证',
            'telephone' => '联系电话',
            'machineoffarm_id' => '农机',
            'farmerpinyin' => '法人拼音首字母',
        ];
    }
}
