<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "lottery_record".
 *
 * @property integer $lottery_record_id
 * @property string $lottery_code
 * @property string $lottery_name
 * @property string $periods
 * @property string $lottery_time
 * @property string $week
 * @property string $lottery_numbers
 * @property integer $status
 * @property string $total_sales
 * @property string $pool
 * @property string $create_time
 * @property string $update_time
 * @property string $parity_ratio
 * @property string $size_ratio
 */
class LotteryRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lottery_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lottery_code', 'lottery_name', 'periods', 'lottery_time'], 'required'],
            [['lottery_time', 'create_time', 'update_time'], 'safe'],
            [['status'], 'integer'],
            [['total_sales', 'pool'], 'number'],
            [['lottery_code', 'parity_ratio', 'size_ratio'], 'string', 'max' => 50],
            [['lottery_name'], 'string', 'max' => 20],
            [['periods'], 'string', 'max' => 15],
            [['week'], 'string', 'max' => 5],
            [['lottery_numbers'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lottery_record_id' => 'Lottery Record ID',
            'lottery_code' => 'Lottery Code',
            'lottery_name' => 'Lottery Name',
            'periods' => 'Periods',
            'lottery_time' => 'Lottery Time',
            'week' => 'Week',
            'lottery_numbers' => 'Lottery Numbers',
            'status' => 'Status',
            'total_sales' => 'Total Sales',
            'pool' => 'Pool',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'parity_ratio' => 'Parity Ratio',
            'size_ratio' => 'Size Ratio',
        ];
    }
}
