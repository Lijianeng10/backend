<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "callback_detail".
 *
 * @property integer $id
 * @property integer $callback_base_id
 * @property string $url
 * @property integer $exec_status
 * @property integer $callback_status
 * @property integer $exec_times
 * @property string $params
 * @property integer $c_time
 * @property integer $u_time
 */
class CallbackDetail extends \yii\db\ActiveRecord
{
    const CALL_STA=[
        ''=>'请选择',
        0=>'未回调',
        1=>'成功',
        2=>'失败'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'callback_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['callback_base_id', 'url', 'params'], 'required'],
            [['callback_base_id', 'exec_status', 'callback_status', 'exec_times', 'c_time', 'u_time'], 'integer'],
            [['url'], 'string', 'max' => 255],
            [['params'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'callback_base_id' => 'Callback Base ID',
            'url' => 'Url',
            'exec_status' => 'Exec Status',
            'callback_status' => 'Callback Status',
            'exec_times' => 'Exec Times',
            'params' => 'Params',
            'c_time' => 'C Time',
            'u_time' => 'U Time',
        ];
    }
}
