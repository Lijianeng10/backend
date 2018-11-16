<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "coupons".
 *
 * @property integer $coupons_id
 * @property string $batch
 * @property string $coupons_name
 * @property integer $type
 * @property integer $application_type
 * @property integer $is_gift
 * @property integer $is_sure_date
 * @property integer $discount
 * @property integer $numbers
 * @property string $use_agents
 * @property integer $use_range
 * @property integer $is_limit_less
 * @property integer $less_consumption
 * @property integer $reduce_money
 * @property integer $days_num
 * @property integer $stack_use
 * @property integer $sure_time
 * @property string $start_date
 * @property string $end_date
 * @property string $send_content
 * @property integer $send_num
 * @property integer $use_num
 * @property integer $opt_id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class Coupons extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coupons';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'application_type', 'is_gift', 'is_sure_date', 'discount', 'numbers', 'use_range', 'is_limit_less', 'less_consumption', 'reduce_money', 'days_num', 'stack_use', 'sure_time', 'send_num', 'use_num', 'opt_id', 'status'], 'integer'],
            [['start_date', 'end_date', 'create_time', 'update_time'], 'safe'],
            [['batch'], 'string', 'max' => 20],
            [['coupons_name', 'use_agents'], 'string', 'max' => 50],
            [['send_content'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'coupons_id' => 'Coupons ID',
            'batch' => 'Batch',
            'coupons_name' => 'Coupons Name',
            'type' => 'Type',
            'application_type' => 'Application Type',
            'is_gift' => 'Is Gift',
            'is_sure_date' => 'Is Sure Date',
            'discount' => 'Discount',
            'numbers' => 'Numbers',
            'use_agents' => 'Use Agents',
            'use_range' => 'Use Range',
            'is_limit_less' => 'Is Limit Less',
            'less_consumption' => 'Less Consumption',
            'reduce_money' => 'Reduce Money',
            'days_num' => 'Days Num',
            'stack_use' => 'Stack Use',
            'sure_time' => 'Sure Time',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'send_content' => 'Send Content',
            'send_num' => 'Send Num',
            'use_num' => 'Use Num',
            'opt_id' => 'Opt ID',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
