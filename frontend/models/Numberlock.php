<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%numberlock}}".
 *
 * @property integer $id
 * @property integer $number
 * @property integer $create_at
 */
class Numberlock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%numberlock}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'create_at','farms_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => '流水号',
            'create_at' => '创建日期',
            'farms_id' => '农场ID'
        ];
    }

    public static function lock($num,$farms_id)
    {
        $lock = Numberlock::find()->where(['number'=>$num,'farms_id'=>$farms_id])->one();
        if(empty($lock)) {
            $model = new Numberlock();
            $model->number = $num;
            $model->create_at = time();
            $model->farms_id = $farms_id;
            return $model->save();
        }
        return false;
    }

    public static function unlock()
    {
        $lock = Numberlock::find()->all();
        foreach ($lock as $value) {
            $model = Numberlock::findOne($value['id']);
            $cha = time() - $value['create_at'];
            if($cha > 86400) {
                $model->delete();
            }
        }
    }


    public static function inLock($num)
    {
        $num = Numberlock::find()->where(['number'=>$num])->count();
        if($num) {
            return true;
        }
        return false;
    }

    public static function isFarm($farms_id)
    {
        $num = Numberlock::find()->where(['farms_id'=>$farms_id])->one();
        if($num) {
            return $num;
        }
        return false;
    }
    
    public static function getNumber($farms_id,$state='new')
    {
        self::unlock();
        $lock = Numberlock::find()->where(['farms_id'=>$farms_id])->one();
        if($lock) {
            return Farms::getContractnumber($farms_id,$state,$lock['number']);
        }
        return Farms::getContractnumber($farms_id,$state);
    }
    
    public static function getAutoNumber()
    {
        self::unlock();
    }
}
