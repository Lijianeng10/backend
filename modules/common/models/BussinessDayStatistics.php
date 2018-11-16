<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "bussiness_day_statistics".
 *
 * @property integer $day_statistics_id
 * @property string $cust_no
 * @property string $statistics_date
 * @property string $begin_amount
 * @property string $cz_amount
 * @property string $award_amount
 * @property string $win_amount
 * @property string $tc_amount
 * @property string $sr_amount
 * @property string $tz_amount
 * @property string $tx_amount
 * @property string $zc_amount
 * @property string $ye_amount
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class BussinessDayStatistics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bussiness_day_statistics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['statistics_date', 'create_time', 'modify_time', 'update_time'], 'safe'],
            [['begin_amount', 'cz_amount', 'award_amount', 'tc_amount', 'sr_amount', 'tz_amount', 'tx_amount', 'zc_amount', 'ye_amount'], 'number'],
            [['cust_no'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'day_statistics_id' => 'Day Statistics ID',
            'cust_no' => 'Cust No',
            'statistics_date' => 'Statistics Date',
            'begin_amount' => 'Begin Amount',
            'cz_amount' => 'Cz Amount',
            'award_amount' => 'Award Amount',
            'win_amount' => 'Win Amount',
            'tc_amount' => 'Tc Amount',
            'sr_amount' => 'Sr Amount',
            'tz_amount' => 'Tz Amount',
            'tx_amount' => 'Tx Amount',
            'zc_amount' => 'Zc Amount',
            'ye_amount' => 'Ye Amount',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
