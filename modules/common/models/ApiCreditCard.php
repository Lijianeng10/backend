<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "api_credit_card".
 *
 * @property integer $credit_card_id
 * @property string $card_name
 * @property string $cash_quota
 * @property string $free_periods
 * @property string $jump_url
 * @property string $pic_url
 * @property integer $status
 * @property integer $sort
 * @property string $card_activity
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class ApiCreditCard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_credit_card';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'sort'], 'integer'],
            [['card_activity'], 'string'],
            [['create_time', 'modify_time', 'update_time'], 'safe'],
            [['card_name'], 'string', 'max' => 100],
            [['cash_quota'], 'string', 'max' => 25],
            [['free_periods'], 'string', 'max' => 50],
            [['jump_url', 'pic_url'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'credit_card_id' => 'Credit Card ID',
            'card_name' => 'Card Name',
            'cash_quota' => 'Cash Quota',
            'free_periods' => 'Free Periods',
            'jump_url' => 'Jump Url',
            'pic_url' => 'Pic Url',
            'status' => 'Status',
            'sort' => 'Sort',
            'card_activity' => 'Card Activity',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
