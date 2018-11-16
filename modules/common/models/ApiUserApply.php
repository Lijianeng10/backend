<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "api_user_apply".
 *
 * @property integer $api_user_apply_id
 * @property string $apply_code
 * @property integer $user_id
 * @property string $cust_no
 * @property integer $type
 * @property string $money
 * @property string $voucher_pic
 * @property integer $status
 * @property integer $api_user_bank_id
 * @property string $remark
 * @property string $refuse_reson
 * @property integer $opt_id
 * @property string $create_time
 * @property string $modify_time
 */
class ApiUserApply extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_user_apply';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'cust_no', 'type', 'money'], 'required'],
            [['user_id', 'type', 'status', 'api_user_bank_id', 'opt_id'], 'integer'],
            [['money'], 'number'],
            [['create_time', 'modify_time'], 'safe'],
            [['apply_code', 'cust_no'], 'string', 'max' => 45],
            [['voucher_pic', 'remark', 'refuse_reson'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'api_user_apply_id' => 'Api User Apply ID',
            'apply_code' => 'Apply Code',
            'user_id' => 'User ID',
            'cust_no' => 'Cust No',
            'type' => 'Type',
            'money' => 'Money',
            'voucher_pic' => 'Voucher Pic',
            'status' => 'Status',
            'api_user_bank_id' => 'Api User Bank ID',
            'remark' => 'Remark',
            'refuse_reson' => 'Refuse Reson',
            'opt_id' => 'Opt ID',
            'create_time' => 'Create Time',
            'modify_time' => 'Modify Time',
        ];
    }
}
