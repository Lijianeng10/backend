<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "send_rebate_record".
 *
 * @property integer $send_rebate_record_id
 * @property string $cust_no
 * @property string $send_money
 * @property string $send_time
 * @property integer $opt_id
 * @property string $create_time
 */
class SendRebateRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'send_rebate_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['send_money'], 'number'],
            [['send_time', 'create_time'], 'safe'],
            [['opt_id'], 'integer'],
            [['cust_no'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'send_rebate_record_id' => 'Send Rebate Record ID',
            'cust_no' => 'Cust No',
            'send_money' => 'Send Money',
            'send_time' => 'Send Time',
            'opt_id' => 'Opt ID',
            'create_time' => 'Create Time',
        ];
    }
}
