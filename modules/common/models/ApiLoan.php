<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "api_loan".
 *
 * @property integer $loan_id
 * @property string $title
 * @property string $sub_title
 * @property string $quota
 * @property string $profit
 * @property string $profit_remark
 * @property string $loan_periods
 * @property string $pic_url
 * @property string $jump_url
 * @property integer $status
 * @property integer $sort
 * @property string $create_time
 * @property string $modify_time
 * @property string $update_time
 */
class ApiLoan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_loan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['profit'], 'number'],
            [['status', 'sort'], 'integer'],
            [['create_time', 'modify_time', 'update_time'], 'safe'],
            [['title', 'sub_title', 'quota', 'loan_periods'], 'string', 'max' => 50],
            [['profit_remark'], 'string', 'max' => 100],
            [['pic_url', 'jump_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'loan_id' => 'Loan ID',
            'title' => 'Title',
            'sub_title' => 'Sub Title',
            'quota' => 'Quota',
            'profit' => 'Profit',
            'profit_remark' => 'Profit Remark',
            'loan_periods' => 'Loan Periods',
            'pic_url' => 'Pic Url',
            'jump_url' => 'Jump Url',
            'status' => 'Status',
            'sort' => 'Sort',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
            'update_time' => 'Update Time',
        ];
    }
}
