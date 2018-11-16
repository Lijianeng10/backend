<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "lottery_order".
 *
 * @property integer $lottery_order_id
 * @property integer $lottery_additional_id
 * @property string $lottery_name
 * @property string $lottery_order_code
 * @property string $play_name
 * @property string $play_code
 * @property string $pay_time
 * @property integer $lottery_id
 * @property string $periods
 * @property string $cust_no
 * @property string $agent_id
 * @property string $bet_val
 * @property integer $additional_periods
 * @property integer $chased_num
 * @property integer $bet_double
 * @property integer $is_bet_add
 * @property string $bet_money
 * @property integer $is_win
 * @property string $win_amount
 * @property integer $count
 * @property integer $status
 * @property integer $source
 * @property integer $is_generate_child
 * @property string $opt_id
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
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
            [['lottery_additional_id', 'lottery_id', 'additional_periods', 'chased_num', 'bet_double', 'is_bet_add', 'count', 'status', 'source'], 'integer'],
            [['lottery_order_code', 'play_name', 'play_code', 'lottery_id', 'periods', 'cust_no', 'agent_id', 'bet_val', 'bet_money'], 'required'],
            [['modify_time', 'create_time', 'update_time'], 'safe'],
            [['bet_money', 'win_amount'], 'number'],
            [['lottery_name', 'play_name', 'agent_id'], 'string', 'max' => 50],
            [['lottery_order_code', 'periods', 'opt_id'], 'string', 'max' => 25],
            [['play_code'], 'string', 'max' => 255],
            [['cust_no'], 'string', 'max' => 15],
            [['bet_val'], 'string', 'max' => 256],
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
            'periods' => 'Periods',
            'cust_no' => 'Cust No',
            'agent_id' => 'Agent ID',
            'bet_val' => 'Bet Val',
            'additional_periods' => 'Additional Periods',
            'chased_num' => 'Chased Num',
            'bet_double' => 'Bet Double',
            'is_bet_add' => 'Is Bet Add',
            'bet_money' => 'Bet Money',
            'win_amount' => 'Win Amount',
            'count' => 'Count',
            'status' => 'Status',
            'source' => 'Source',
            'opt_id' => 'Opt ID',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
