<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "lottery_order".
 *
 * @property string $lottery_order_id
 * @property integer $lottery_additional_id
 * @property string $lottery_name
 * @property string $lottery_order_code
 * @property string $play_name
 * @property string $play_code
 * @property integer $lottery_id
 * @property integer $lottery_type
 * @property string $periods
 * @property string $cust_no
 * @property integer $cust_type
 * @property integer $user_id
 * @property integer $store_id
 * @property string $store_no
 * @property string $agent_id
 * @property integer $user_plan_id
 * @property string $end_time
 * @property string $programme_code
 * @property string $bet_val
 * @property integer $additional_periods
 * @property integer $chased_num
 * @property integer $bet_double
 * @property integer $is_bet_add
 * @property string $bet_money
 * @property string $odds
 * @property integer $count
 * @property string $win_amount
 * @property string $award_amount
 * @property integer $deal_status
 * @property integer $status
 * @property string $refuse_reason
 * @property integer $record_type
 * @property integer $source
 * @property integer $suborder_status
 * @property string $opt_id
 * @property string $remark
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 * @property string $award_time
 * @property integer $source_id
 * @property integer $send_status
 * @property string $build_code
 * @property string $build_name
 * @property integer $major_type
 */
class LotteryOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lottery_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lottery_additional_id', 'lottery_id', 'lottery_type', 'cust_type', 'user_id', 'store_id', 'user_plan_id', 'additional_periods', 'chased_num', 'bet_double', 'is_bet_add', 'count', 'deal_status', 'status', 'record_type', 'source',  'suborder_status', 'source_id', 'send_status', 'major_type'], 'integer'],
            [['end_time', 'modify_time', 'create_time', 'update_time', 'award_time'], 'safe'],
            [['lottery_id'], 'required'],
            [['bet_money', 'win_amount', 'award_amount'], 'number'],
            [['lottery_name', 'lottery_order_code', 'agent_id', 'build_name'], 'string', 'max' => 50],
            [['play_name'], 'string', 'max' => 1500],
            [['play_code'], 'string', 'max' => 700],
            [['periods', 'opt_id', 'build_code'], 'string', 'max' => 25],
            [['cust_no', 'store_no'], 'string', 'max' => 15],
            [['programme_code', 'remark'], 'string', 'max' => 100],
            [['bet_val', 'odds'], 'string', 'max' => 1000],
            [['refuse_reason'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lottery_order_id' => 'Lottery Order ID',
            'lottery_additional_id' => 'Lottery Additional ID',
            'lottery_name' => 'Lottery Name',
            'lottery_order_code' => 'Lottery Order Code',
            'play_name' => 'Play Name',
            'play_code' => 'Play Code',
            'lottery_id' => 'Lottery ID',
            'lottery_type' => 'Lottery Type',
            'periods' => 'Periods',
            'cust_no' => 'Cust No',
            'cust_type' => 'Cust Type',
            'user_id' => 'User ID',
            'store_id' => 'Store ID',
            'store_no' => 'Store No',
            'agent_id' => 'Agent ID',
            'user_plan_id' => 'User Plan ID',
            'end_time' => 'End Time',
            'programme_code' => 'Programme Code',
            'bet_val' => 'Bet Val',
            'additional_periods' => 'Additional Periods',
            'chased_num' => 'Chased Num',
            'bet_double' => 'Bet Double',
            'is_bet_add' => 'Is Bet Add',
            'bet_money' => 'Bet Money',
            'odds' => 'Odds',
            'count' => 'Count',
            'win_amount' => 'Win Amount',
            'award_amount' => 'Award Amount',
            'deal_status' => 'Deal Status',
            'status' => 'Status',
            'refuse_reason' => 'Refuse Reason',
            'record_type' => 'Record Type',
            'source' => 'Source',
            'suborder_status' => 'Suborder Status',
            'opt_id' => 'Opt ID',
            'remark' => 'Remark',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'award_time' => 'Award Time',
            'source_id' => 'Source ID',
            'send_status' => 'Send Status',
            'build_code' => 'Build Code',
            'build_name' => 'Build Name',
            'major_type' => 'Major Type',
        ];
    }
}
