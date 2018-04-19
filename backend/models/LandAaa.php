<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%aaa}}".
 *
 * @property integer $id
 * @property integer $aaa
 */
class LandAaa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%aaa}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aaa'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'id' => 'ID',
            'aaa' => 'aaa',
        ];
    }
}
