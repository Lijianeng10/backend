<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "bussiness_month_statistics".
 *
 * @property integer $month_statistics_id
 * @property string $cust_no
 * @property string $statistics_month
 * @property string $begin_funds
 * @property string $sum_cz_amount
 * @property string $sum_win_amount
 * @property string $sum_award_amount
 * @property string $sum_tc_amount
 * @property string $sum_tz_amount
 * @property string $sum_tx_amount
 * @property string $end_funds
 * @property string $month_tc
 * @property string $grant_tc
 * @property string $grant_time
 * @property integer $status
 * @property integer $deal_status
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class BussinessMonthStatistics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bussiness_month_statistics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['begin_funds', 'sum_cz_amount', 'sum_award_amount', 'sum_tc_amount', 'sum_tz_amount', 'sum_tx_amount', 'end_funds', 'month_tc', 'grant_tc'], 'number'],
            [['grant_time', 'create_time', 'modify_time', 'update_time'], 'safe'],
            [['status', 'deal_status'], 'integer'],
            [['cust_no', 'statistics_month'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'month_statistics_id' => 'Month Statistics ID',
            'cust_no' => 'Cust No',
            'statistics_month' => 'Statistics Month',
            'begin_funds' => 'Begin Funds',
            'sum_cz_amount' => 'Sum Cz Amount',
            'sum_win_amount' => 'Sum Win Amount',
            'sum_award_amount' => 'Sum Award Amount',
            'sum_tc_amount' => 'Sum Tc Amount',
            'sum_tz_amount' => 'Sum Tz Amount',
            'sum_tx_amount' => 'Sum Tx Amount',
            'end_funds' => 'End Funds',
            'month_tc' => 'Month Tc',
            'grant_tc' => 'Grant Tc',
            'grant_time' => 'Grant Time',
            'status' => 'Status',
            'deal_status' => 'Deal Status',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
