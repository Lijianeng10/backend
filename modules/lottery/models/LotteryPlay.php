<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "lottery_play".
 *
 * @property integer $lottery_play_id
 * @property string $lottery_play_code
 * @property string $lottery_play_name
 * @property string $lottery_code
 * @property string $lottery_name
 * @property string $category_name
 * @property string $example
 * @property string $number_count
 * @property string $format_remark
 * @property string $opt_id
 * @property string $create_time
 * @property string $update_time
 */
class LotteryPlay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lottery_play';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lottery_play_code', 'lottery_play_name', 'lottery_code', 'lottery_name', 'category_name'], 'required'],
            [['create_time', 'update_time'], 'safe'],
            [['lottery_play_code', 'lottery_code'], 'string', 'max' => 10],
            [['lottery_play_name', 'lottery_name', 'category_name'], 'string', 'max' => 20],
            [['example'], 'string', 'max' => 255],
            [['number_count'], 'string', 'max' => 50],
            [['format_remark'], 'string', 'max' => 200],
            [['opt_id'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lottery_play_id' => 'Lottery Play ID',
            'lottery_play_code' => 'Lottery Play Code',
            'lottery_play_name' => 'Lottery Play Name',
            'lottery_code' => 'Lottery Code',
            'lottery_name' => 'Lottery Name',
            'category_name' => 'Category Name',
            'example' => 'Example',
            'number_count' => 'Number Count',
            'format_remark' => 'Format Remark',
            'opt_id' => 'Opt ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
