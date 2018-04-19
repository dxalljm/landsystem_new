<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "land_exceltotable".
 *
 * @property integer $id
 * @property string $tablename
 * @property string $Ctablename
 */
class Exceltotable33 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'land_exceltotable';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tablename', 'Ctablename'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tablename' => 'Tablename',
            'Ctablename' => 'Ctablename',
        ];
    }
}
