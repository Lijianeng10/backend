<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "polling_conf".
 *
 * @property integer $polling_conf_id
 * @property string $source_code
 * @property string $source_name
 * @property integer $source_type
 * @property integer $polling_type
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class PollingConf extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'polling_conf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source_type', 'polling_type'], 'integer'],
            [['create_time', 'modify_time', 'update_time'], 'safe'],
            [['source_code'], 'string', 'max' => 50],
            [['source_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'polling_conf_id' => 'Polling Conf ID',
            'source_code' => 'Source Code',
            'source_name' => 'Source Name',
            'source_type' => 'Source Type',
            'polling_type' => 'Polling Type',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
