<?php

namespace app\modules\promote\models;

use Yii;

/** 
 * This is the model class for table "store_user". 
 * 
 * @property integer $id
 * @property integer $user_id
 * @property string $user_tel
 * @property string $store_name
 * @property string $store_code
 * @property string $qr_url
 * @property integer $status
 * @property string $create_time
 */ 
class StoreUser extends \yii\db\ActiveRecord
{ 
    /** 
     * @inheritdoc 
     */ 
    public static function tableName() 
    { 
        return 'store_user'; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function rules() 
    { 
        return [
            [['user_id'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['create_time'], 'safe'],
            [['user_tel', 'store_name', 'store_code'], 'string', 'max' => 45],
            [['qr_url'], 'string', 'max' => 100],
        ]; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function attributeLabels() 
    { 
        return [ 
            'id' => 'ID',
            'user_id' => 'User ID',
            'user_tel' => 'User Tel',
            'store_name' => 'Store Name',
            'store_code' => 'Store Code',
            'qr_url' => 'Qr Url',
            'status' => 'Status',
            'create_time' => 'Create Time',
        ]; 
    } 
} 
