<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%lockstate}}".
 *
 * @property integer $id
 * @property integer $systemstate
 * @property string $systemstatedate
 * @property string $platestate
 * @property integer $loanconfig
 * @property string $loanconfigdate
 * @property integer $transferconfig
 * @property string $transferconfigdate
 */
class Lockstate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lockstate}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['systemstate', 'loanconfig', 'transferconfig'], 'integer'],
            [['systemstatedate', 'platestate', 'loanconfigdate', 'transferconfigdate'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'id' => 'ID',
            'systemstate' => '系统状态',
            'systemstatedate' => '系统锁定时间',
            'platestate' => '板块锁定状态',
            'loanconfig' => '贷款配置项',
            'loanconfigdate' => '贷款配置冻结日期',
            'transferconfig' => '过户配置项',
            'transferconfigdate' => '过户冻结日期',
        ];
    }
}
