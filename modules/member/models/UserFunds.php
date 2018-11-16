<?php

namespace app\modules\member\models;

use Yii;

/**
 * This is the model class for table "user_funds".
 *
 * @property integer $user_funds_id
 * @property integer $user_id
 * @property string $user_name
 * @property string $cust_no
 * @property string $all_funds
 * @property string $able_funds
 * @property string $ice_funds
 * @property string $no_withdraw
 * @property integer $user_integral
 * @property integer $user_glcoin
 * @property integer $user_growth
 * @property string $pay_password
 * @property integer $opt_id
 * @property integer $withdraw_status
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 */
class UserFunds extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_funds';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'user_integral', 'user_glcoin', 'user_growth', 'opt_id', 'withdraw_status'], 'integer'],
            [['cust_no'], 'required'],
            [['all_funds', 'able_funds', 'ice_funds', 'no_withdraw'], 'number'],
            [['modify_time', 'create_time', 'update_time'], 'safe'],
            [['user_name', 'pay_password'], 'string', 'max' => 100],
            [['cust_no'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_funds_id' => 'User Funds ID',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'cust_no' => 'Cust No',
            'all_funds' => 'All Funds',
            'able_funds' => 'Able Funds',
            'ice_funds' => 'Ice Funds',
            'no_withdraw' => 'No Withdraw',
            'user_integral' => 'User Integral',
            'user_glcoin' => 'User Glcoin',
            'user_growth' => 'User Growth',
            'pay_password' => 'Pay Password',
            'opt_id' => 'Opt ID',
            'withdraw_status' => 'Withdraw Status',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
