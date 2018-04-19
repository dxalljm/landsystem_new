<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%insurancetype}}".
 *
 * @property integer $id
 * @property integer $plant_id
 */
class Insurancetype extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%insurancetype}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['plant_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'id' => 'ID',
            'plant_id' => '种植结构',
        ];
    }
}
