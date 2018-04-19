<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%subsidyratio}}".
 *
 * @property integer $id
 * @property integer $farms_id
 * @property integer $typeid
 * @property double $farmer
 * @property double $lessee
 */
class Subsidyratio extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subsidyratio}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['farms_id', 'typeid'], 'integer'],
            [['farmer', 'lessee'], 'number'],
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
            'typeid' => 'Typeid',
            'farmer' => 'Farmer',
            'lessee' => 'Lessee',
        ];
    }
}
