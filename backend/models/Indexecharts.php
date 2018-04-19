<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%indexecharts}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $onem
 * @property integer $oner
 * @property integer $twol
 * @property integer $twom
 * @property integer $twor
 */
class Indexecharts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%indexecharts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'onem', 'oner', 'twol', 'twom', 'twor'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'id' => 'ID',
            'user_id' => '用户ID',
            'onem' => '第一行中间',
            'oner' => '第一行右侧',
            'twol' => '第二行左侧',
            'twom' => '第二行中间',
            'twor' => '第二行右侧',
        ];
    }
}
