<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "lottery_levels".
 *
 * @property integer $levels_id
 * @property string $lottery_code
 * @property string $lottery_name
 * @property string $lottery_category
 * @property string $levels_name
 * @property integer $levels_code
 * @property integer $levels_sort
 * @property integer $levels_red
 * @property integer $levels_blue
 * @property string $levels_remark
 * @property string $levels_bonus_category
 * @property string $levels_bonus
 * @property string $levels_bonus_remark
 */
class LotteryLevels extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lottery_levels';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lottery_code', 'lottery_category', 'levels_name', 'levels_code', 'levels_bonus_category'], 'required'],
            [['levels_code', 'levels_sort', 'levels_red', 'levels_blue'], 'integer'],
            [['levels_bonus'], 'number'],
            [['lottery_code', 'levels_name', 'levels_bonus_category'], 'string', 'max' => 50],
            [['lottery_name', 'lottery_category', 'levels_remark', 'levels_bonus_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'levels_id' => 'Levels ID',
            'lottery_code' => 'Lottery Code',
            'lottery_name' => 'Lottery Name',
            'lottery_category' => 'Lottery Category',
            'levels_name' => 'Levels Name',
            'levels_code' => 'Levels Code',
            'levels_sort' => 'Levels Sort',
            'levels_red' => 'Levels Red',
            'levels_blue' => 'Levels Blue',
            'levels_remark' => 'Levels Remark',
            'levels_bonus_category' => 'Levels Bonus Category',
            'levels_bonus' => 'Levels Bonus',
            'levels_bonus_remark' => 'Levels Bonus Remark',
        ];
    }
}
