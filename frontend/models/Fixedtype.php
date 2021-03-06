<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%fixedtype}}".
 *
 * @property integer $id
 * @property string $typename
 */
class Fixedtype extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fixedtype}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['typename'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'id' => 'ID',
            'typename' => '类别名称',
        ];
    }
}
