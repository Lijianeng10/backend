<?php

namespace app\modules\member\models;

use Yii; 

/** 
 * This is the model class for table "user_growth". 
 * 
 * @property integer $user_growth_id
 * @property string $growth_source
 * @property string $growth_type
 * @property integer $growth_value
 * @property string $growth_remark
 * @property integer $opt_id
 * @property string $create_time
 * @property string $update_time
 */ 
class UserGrowth extends \yii\db\ActiveRecord
{ 
    /** 
     * @inheritdoc 
     */ 
    public static function tableName() 
    { 
        return 'user_growth'; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function rules() 
    { 
        return [
            [['growth_value'], 'required'],
            [['growth_value', 'opt_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['growth_source', 'growth_type'], 'string', 'max' => 50],
            [['growth_remark'], 'string', 'max' => 200],
        ]; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function attributeLabels() 
    { 
        return [ 
            'user_growth_id' => 'User Growth ID',
            'growth_source' => 'Growth Source',
            'growth_type' => 'Growth Type',
            'growth_value' => 'Growth Value',
            'growth_remark' => 'Growth Remark',
            'opt_id' => 'Opt ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ]; 
    } 
} 