<?php

namespace app\modules\member\models;

use Yii;

/**
 * This is the model class for table "exchange_record".
 *
 * @property integer $exchange_record_id
 * @property string $exch_code
 * @property integer $platform
 * @property string $transaction_code
 * @property string $cust_no
 * @property integer $pay_type
 * @property integer $exch_nums
 * @property integer $exch_value
 * @property integer $exch_type
 * @property integer $order_status
 * @property string $agent_code
 * @property integer $opt_id
 * @property integer $review_status
 * @property string $review_remark
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class ExchangeRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'exchange_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exch_code', 'cust_no'], 'required'],
            [['platform', 'pay_type', 'exch_nums', 'exch_value', 'exch_type', 'order_status', 'opt_id', 'review_status'], 'integer'],
            [['create_time', 'modify_time', 'update_time'], 'safe'],
            [['exch_code', 'transaction_code', 'cust_no'], 'string', 'max' => 50],
            [['agent_code'], 'string', 'max' => 100],
            [['review_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'exchange_record_id' => 'Exchange Record ID',
            'exch_code' => 'Exch Code',
            'platform' => 'Platform',
            'transaction_code' => 'Transaction Code',
            'cust_no' => 'Cust No',
            'pay_type' => 'Pay Type',
            'exch_nums' => 'Exch Nums',
            'exch_value' => 'Exch Value',
            'exch_type' => 'Exch Type',
            'order_status' => 'Order Status',
            'agent_code' => 'Agent Code',
            'opt_id' => 'Opt ID',
            'review_status' => 'Review Status',
            'review_remark' => 'Review Remark',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
} 