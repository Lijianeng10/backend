<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "callback_base".
 *
 * @property integer $id
 * @property string $url
 * @property string $code
 * @property integer $times
 * @property string $name
 * @property integer $agent_id
 * @property string $remark
 * @property integer $c_time
 */
class CallbackBase extends \yii\db\ActiveRecord
{
	const THIRD_TYPE=[1=>'分销商',2=>'流量商'];
	const TYPE=[1=>'出票回调'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'callback_base';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'code', 'name', 'agent_id', 'remark','third_type','type'], 'required'],
            [['times', 'agent_id', 'c_time','third_type','type'], 'integer'],
            [['url', 'code', 'remark'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 50],
            [['url'], 'unique'],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => 'Url',
            'code' => 'Code',
            'times' => 'Times',
            'name' => 'Name',
            'agent_id' => 'Agent ID',
            'remark' => 'Remark',
            'c_time' => 'C Time',
        ];
    }
}
