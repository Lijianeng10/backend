<?php

namespace app\modules\promote\models;

use Yii;

/**
 * This is the model class for table "redeem_code".
 *
 * @property integer $redeem_code_id
 * @property string $redeem_code
 * @property string $value_amount
 * @property integer $status
 * @property integer $store_id
 * @property string $settle_date
 * @property integer $type
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 */
class RedeemCode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'redeem_code';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['redeem_code', 'value_amount'], 'required'],
            [['value_amount'], 'number'],
            [['status', 'store_id', 'type'], 'integer'],
            [['settle_date', 'modify_time', 'create_time', 'update_time'], 'safe'],
            [['redeem_code'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'redeem_code_id' => 'Redeem Code ID',
            'redeem_code' => 'Redeem Code',
            'value_amount' => 'Value Amount',
            'status' => 'Status',
            'store_id' => 'Store ID',
            'settle_date' => 'Settle Date',
            'type' => 'Type',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
