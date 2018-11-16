<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "user_growth_record".
 *
 * @property integer $user_growth_id
 * @property integer $type
 * @property string $growth_source
 * @property integer $growth_value
 * @property integer $totle_balance
 * @property string $growth_remark
 * @property integer $user_id
 * @property integer $levels
 * @property string $order_code
 * @property integer $order_source
 * @property string $create_time
 */
class UserGrowthRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_growth_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'growth_value', 'totle_balance', 'user_id', 'levels', 'order_source'], 'integer'],
            [['growth_source'], 'required'],
            [['create_time'], 'safe'],
            [['growth_source'], 'string', 'max' => 25],
            [['growth_remark'], 'string', 'max' => 255],
            [['order_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_growth_id' => 'User Growth ID',
            'type' => 'Type',
            'growth_source' => 'Growth Source',
            'growth_value' => 'Growth Value',
            'totle_balance' => 'Totle Balance',
            'growth_remark' => 'Growth Remark',
            'user_id' => 'User ID',
            'levels' => 'Levels',
            'order_code' => 'Order Code',
            'order_source' => 'Order Source',
            'create_time' => 'Create Time',
        ];
    }
}
