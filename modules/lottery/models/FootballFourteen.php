<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "football_fourteen".
 *
 * @property integer $football_fourteen_id
 * @property string $periods
 * @property string $schedule_mids
 * @property string $beginsale_time
 * @property string $endsale_time
 * @property string $schedule_results
 * @property string $first_prize
 * @property string $second_prize
 * @property integer $status
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 * @property string $nine_prize
 * @property integer $win_status
 */
class FootballFourteen extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'football_fourteen';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['beginsale_time', 'endsale_time', 'modify_time', 'create_time', 'update_time'], 'safe'],
            [['first_prize', 'second_prize', 'nine_prize'], 'number'],
            [['status', 'win_status'], 'integer'],
            [['periods'], 'string', 'max' => 15],
            [['schedule_mids', 'schedule_results'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'football_fourteen_id' => 'Football Fourteen ID',
            'periods' => 'Periods',
            'schedule_mids' => 'Schedule Mids',
            'beginsale_time' => 'Beginsale Time',
            'endsale_time' => 'Endsale Time',
            'schedule_results' => 'Schedule Results',
            'first_prize' => 'First Prize',
            'second_prize' => 'Second Prize',
            'status' => 'Status',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'nine_prize' => 'Nine Prize',
            'win_status' => 'Win Status',
        ];
    }
}
