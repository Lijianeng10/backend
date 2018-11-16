<?php

namespace app\modules\member\models;

use Yii;

/**
 * This is the model class for table "gift".
 *
 * @property integer $gift_id
 * @property string $gift_code
 * @property string $gift_name
 * @property string $subtitle
 * @property integer $gift_category
 * @property integer $type
 * @property string $batch
 * @property integer $gift_level
 * @property integer $gift_glcoin
 * @property integer $gift_integral
 * @property string $gift_picture
 * @property string $gift_picture2
 * @property integer $in_stock
 * @property integer $exchange_nums
 * @property string $start_date
 * @property string $end_date
 * @property integer $status
 * @property string $agent_code
 * @property string $agent_name
 * @property string $gift_remark
 * @property integer $opt_id
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 */
class Gift extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gift';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gift_code', 'gift_category', 'start_date', 'end_date'], 'required'],
            [['gift_category', 'type', 'gift_level', 'gift_glcoin', 'gift_integral', 'in_stock', 'exchange_nums', 'status', 'opt_id'], 'integer'],
            [['start_date', 'end_date', 'modify_time', 'create_time', 'update_time'], 'safe'],
            [['gift_remark'], 'string'],
            [['gift_code'], 'string', 'max' => 25],
            [['gift_name', 'agent_code', 'agent_name'], 'string', 'max' => 50],
            [['subtitle', 'gift_picture', 'gift_picture2'], 'string', 'max' => 100],
            [['batch'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gift_id' => 'Gift ID',
            'gift_code' => 'Gift Code',
            'gift_name' => 'Gift Name',
            'subtitle' => 'Subtitle',
            'gift_category' => 'Gift Category',
            'type' => 'Type',
            'batch' => 'Batch',
            'gift_level' => 'Gift Level',
            'gift_glcoin' => 'Gift Glcoin',
            'gift_integral' => 'Gift Integral',
            'gift_picture' => 'Gift Picture',
            'gift_picture2' => 'Gift Picture2',
            'in_stock' => 'In Stock',
            'exchange_nums' => 'Exchange Nums',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'status' => 'Status',
            'agent_code' => 'Agent Code',
            'agent_name' => 'Agent Name',
            'gift_remark' => 'Gift Remark',
            'opt_id' => 'Opt ID',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
} 