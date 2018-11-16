<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "user_rate_record".
 *
 * @property integer $user_rate_record_id
 * @property string $cust_no
 * @property double $rate_value
 * @property string $start_time
 * @property string $end_time
 * @property string $create_time
 */
class UserRateRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_rate_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cust_no'], 'required'],
            [['rate_value'], 'number'],
            [['start_time', 'end_time', 'create_time'], 'safe'],
            [['cust_no'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_rate_record_id' => 'User Rate Record ID',
            'cust_no' => 'Cust No',
            'rate_value' => 'Rate Value',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'create_time' => 'Create Time',
        ];
    }
}
