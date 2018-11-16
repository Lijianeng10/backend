<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "schedule_result".
 *
 * @property integer $schedule_result_id
 * @property integer $schedule_id
 * @property string $schedule_mid
 * @property integer $schedule_result_3010
 * @property string $schedule_result_3006
 * @property string $schedule_result_3007
 * @property integer $schedule_result_3008
 * @property string $schedule_result_3009
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 * @property integer $opt_id
 */
class ScheduleResult extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'schedule_result';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['schedule_id', 'schedule_mid'], 'required'],
            [['schedule_id', 'schedule_result_3010', 'schedule_result_3008', 'opt_id'], 'integer'],
            [['schedule_mid', 'schedule_result_3006', 'schedule_result_3007', 'schedule_result_3009'], 'string', 'max' => 25],
            [['modify_time', 'create_time', 'update_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'schedule_result_id' => 'Schedule Result ID',
            'schedule_id' => 'Schedule ID',
            'schedule_mid' => 'Schedule Mid',
            'schedule_result_3010' => 'Schedule Result 3010',
            'schedule_result_3006' => 'Schedule Result 3006',
            'schedule_result_3007' => 'Schedule Result 3007',
            'schedule_result_3008' => 'Schedule Result 3008',
            'schedule_result_3009' => 'Schedule Result 3009',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'opt_id' => 'Opt ID',
        ];
    }

}
