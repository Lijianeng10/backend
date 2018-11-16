<?php

namespace app\modules\common\models; 

/** 
 * This is the model class for table "coupons_detail". 
 * 
 * @property integer $coupons_detail_id
 * @property string $coupons_no
 * @property string $conversion_code
 * @property string $coupons_batch
 * @property integer $send_status
 * @property string $send_user
 * @property string $send_time
 * @property integer $use_status
 * @property string $use_order_code
 * @property integer $use_order_source
 * @property string $use_time
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */ 
class CouponsDetail extends \yii\db\ActiveRecord
{ 
    /** 
     * @inheritdoc 
     */ 
    public static function tableName() 
    { 
        return 'coupons_detail'; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function rules() 
    { 
        return [
            [['coupons_no', 'coupons_batch'], 'required'],
            [['send_status', 'use_status', 'use_order_source', 'status'], 'integer'],
            [['send_time', 'use_time', 'create_time', 'update_time'], 'safe'],
            [['coupons_no', 'send_user'], 'string', 'max' => 30],
            [['conversion_code'], 'string', 'max' => 15],
            [['coupons_batch'], 'string', 'max' => 10],
            [['use_order_code'], 'string', 'max' => 50],
        ]; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function attributeLabels() 
    { 
        return [ 
            'coupons_detail_id' => 'Coupons Detail ID',
            'coupons_no' => 'Coupons No',
            'conversion_code' => 'Conversion Code',
            'coupons_batch' => 'Coupons Batch',
            'send_status' => 'Send Status',
            'send_user' => 'Send User',
            'send_time' => 'Send Time',
            'use_status' => 'Use Status',
            'use_order_code' => 'Use Order Code',
            'use_order_source' => 'Use Order Source',
            'use_time' => 'Use Time',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ]; 
    } 
} 

