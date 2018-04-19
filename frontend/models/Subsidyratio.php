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
            [['farms_id', 'typeid','lease_id'], 'integer'],
            [['farmer', 'lessee'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'farms_id' => '农场',
            'lease_id' => '承租人',
            'typeid' => '补贴类型',
            'farmer' => '法人占比',
            'lessee' => '承租人占比',
        ];
    }
}
