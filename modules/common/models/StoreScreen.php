<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "store_screen".
 *
 * @property integer $store_screen_id
 * @property string $screen_key
 * @property string $store_code
 * @property integer $is_login
 * @property string $modify_time
 * @property string $create_time
 */
class StoreScreen extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store_screen';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['screen_key'], 'required'],
            [['is_login'], 'integer'],
            [['modify_time', 'create_time'], 'safe'],
            [['screen_key'], 'string', 'max' => 45],
            [['store_code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'store_screen_id' => 'Store Screen ID',
            'screen_key' => 'Screen Key',
            'store_code' => 'Store Code',
            'is_login' => 'Is Login',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
        ];
    }
}
