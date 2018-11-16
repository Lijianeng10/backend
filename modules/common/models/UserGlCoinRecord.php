<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "user_gl_coin_record".
 *
 * @property integer $gl_coin_record_id
 * @property string $order_code
 * @property integer $user_id
 * @property string $cust_no
 * @property integer $type
 * @property integer $transaction_type
 * @property integer $source_id
 * @property integer $exchange_type
 * @property string $coin_value
 * @property integer $integral_value
 * @property string $value_money
 * @property integer $totle_balance
 * @property string $remark
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class UserGlCoinRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_gl_coin_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'type', 'transaction_type', 'source_id', 'exchange_type', 'integral_value', 'totle_balance', 'status'], 'integer'],
            [['coin_value', 'value_money'], 'number'],
            [['create_time', 'modify_time', 'update_time'], 'safe'],
            [['order_code'], 'string', 'max' => 50],
            [['cust_no'], 'string', 'max' => 25],
            [['remark'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gl_coin_record_id' => 'Gl Coin Record ID',
            'order_code' => 'Order Code',
            'user_id' => 'User ID',
            'cust_no' => 'Cust No',
            'type' => 'Type',
            'transaction_type' => 'Transaction Type',
            'source_id' => 'Source ID',
            'exchange_type' => 'Exchange Type',
            'coin_value' => 'Coin Value',
            'integral_value' => 'Integral Value',
            'value_money' => 'Value Money',
            'totle_balance' => 'Totle Balance',
            'remark' => 'Remark',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
} 

