<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%subsidytypetofarm}}".
 *
 * @property integer $id
 * @property string $typename
 */
class Subsidytypetofarm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subsidytypetofarm}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['typename','mark'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'typename' => '补贴类型',
            'mark' => '标识'
        ];
    }
}
