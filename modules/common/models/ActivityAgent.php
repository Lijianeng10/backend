<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "activity_agent".
 *
 * @property integer $activity_agent_id
 * @property string $agent_name
 * @property string $agent_code
 * @property string $create_time
 */
class ActivityAgent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_agent';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time'], 'safe'],
            [['agent_name', 'agent_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'activity_agent_id' => 'Activity Agent ID',
            'agent_name' => 'Agent Name',
            'agent_code' => 'Agent Code',
            'create_time' => 'Create Time',
        ];
    }
}
