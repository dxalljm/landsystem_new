<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%verisonlist}}".
 *
 * @property integer $id
 * @property string $ver
 * @property string $update
 */
class Verisonlist extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%verisonlist}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['update'], 'string'],
            [['ver'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ver' => 'Ver',
            'update' => 'Update',
        ];
    }
}
