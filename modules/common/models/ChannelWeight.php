<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "channel_weight".
 *
 * @property integer $channel_weight_id
 * @property integer $channel_id
 * @property string $channel_code
 * @property string $channel_name
 * @property string $store_code
 * @property string $store_province
 * @property integer $weight
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class ChannelWeight extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel_weight';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'weight'], 'integer'],
            [['create_time', 'modify_time', 'update_time'], 'safe'],
            [['channel_code', 'channel_name', 'store_code','channel_id'], 'string', 'max' => 25],
            [['store_province'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'channel_weight_id' => 'Channel Weight ID',
            'channel_id' => 'Channel ID',
            'channel_code' => 'Channel Code',
            'channel_name' => 'Channel Name',
            'store_code' => 'Store Code',
            'store_province' => 'Store Province',
            'weight' => 'Weight',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
