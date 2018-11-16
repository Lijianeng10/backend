<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "activity_type".
 *
 * @property integer $activity_type_id
 * @property string $type_name
 * @property string $create_time
 */
class ActivityType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time'], 'safe'],
            [['type_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'activity_type_id' => 'Activity Type ID',
            'type_name' => 'Type Name',
            'create_time' => 'Create Time',
        ];
    }
}
