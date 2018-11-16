<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "single_bonus".
 *
 * @property integer $single_bonus_id
 * @property string $single_bonus_mid
 * @property integer $single_bonus_spf
 * @property string $single_bonus_rqspf
 * @property string $single_bonus_bf
 * @property integer $single_bonus_zjqs
 * @property string $single_bonus_bqcspf
 * @property integer $schedule_id
 * @property string $schedule_mid
 */
class SingleBonus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'single_bonus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['single_bonus_mid', 'schedule_id', 'schedule_mid'], 'required'],
            [['single_bonus_spf', 'single_bonus_zjqs', 'schedule_id'], 'integer'],
            [['single_bonus_mid', 'single_bonus_rqspf', 'single_bonus_bf', 'single_bonus_bqcspf', 'schedule_mid'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'single_bonus_id' => 'Single Bonus ID',
            'single_bonus_mid' => 'Single Bonus Mid',
            'single_bonus_spf' => 'Single Bonus Spf',
            'single_bonus_rqspf' => 'Single Bonus Rqspf',
            'single_bonus_bf' => 'Single Bonus Bf',
            'single_bonus_zjqs' => 'Single Bonus Zjqs',
            'single_bonus_bqcspf' => 'Single Bonus Bqcspf',
            'schedule_id' => 'Schedule ID',
            'schedule_mid' => 'Schedule Mid',
        ];
    }
}
