<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "expert_source".
 *
 * @property integer $source_id
 * @property integer $user_id
 * @property string $source_name
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class ExpertSource extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'expert_source';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'integer'],
            [['create_time', 'modify_time', 'update_time'], 'safe'],
            [['source_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'source_id' => 'Source ID',
            'user_id' => 'User ID',
            'source_name' => 'Source Name',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
