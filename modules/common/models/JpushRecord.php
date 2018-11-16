<?php

namespace app\modules\common\models;

use Yii; 

/** 
 * This is the model class for table "jpush_record". 
 * 
 * @property integer $jpush_notice_id
 * @property string $titile
 * @property string $msg
 * @property string $jump_url
 * @property string $push_time
 * @property integer $status
 * @property string $response
 * @property string $remark
 * @property integer $opt_id
 * @property string $remark_name
 * @property string $send_name
 * @property string $create_time
 * @property string $update_time
 */ 
class JpushRecord extends \yii\db\ActiveRecord
{ 
    /** 
     * @inheritdoc 
     */ 
    public static function tableName() 
    { 
        return 'jpush_record'; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function rules() 
    { 
        return [
            [['push_time', 'create_time', 'update_time'], 'safe'],
            [['status', 'opt_id'], 'integer'],
            [['create_time'], 'required'],
            [['titile'], 'string', 'max' => 30],
            [['msg', 'remark'], 'string', 'max' => 255],
            [['jump_url'], 'string', 'max' => 100],
            [['response'], 'string', 'max' => 500],
            [['remark_name', 'send_name'], 'string', 'max' => 50],
        ]; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function attributeLabels() 
    { 
        return [ 
            'jpush_notice_id' => 'Jpush Notice ID',
            'titile' => 'Titile',
            'msg' => 'Msg',
            'jump_url' => 'Jump Url',
            'push_time' => 'Push Time',
            'status' => 'Status',
            'response' => 'Response',
            'remark' => 'Remark',
            'opt_id' => 'Opt ID',
            'remark_name' => 'Remark Name',
            'send_name' => 'Send Name',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ]; 
    } 
} 
