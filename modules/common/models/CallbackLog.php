<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "callback_log".
 *
 * @property integer $id
 * @property integer $callback_base_id
 * @property string $url
 * @property string $params
 * @property string $return
 * @property integer $c_time
 */
class CallbackLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'callback_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['callback_base_id', 'c_time'], 'integer'],
            [['url', 'params', 'return'], 'required'],
            [['url'], 'string', 'max' => 255],
            [['params', 'return'], 'string', 'max' => 1000],
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
            'params' => 'Params',
            'return' => 'Return',
            'c_time' => 'C Time',
        ];
    }
}
