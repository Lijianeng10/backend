<?php

namespace app\modules\member\models;

use Yii; 

/** 
 * This is the model class for table "user_levels". 
 * 
 * @property integer $user_level_id
 * @property string $level_name
 * @property integer $level_growth
 * @property integer $cz_integral
 * @property string $glcz_discount
 * @property double $multiple
 * @property integer $raffle_num
 * @property string $agent_code
 * @property integer $up_status
 * @property integer $opt_id
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 */ 
class UserLevels extends \yii\db\ActiveRecord
{ 
    /** 
     * @inheritdoc 
     */ 
    public static function tableName() 
    { 
        return 'user_levels'; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function rules() 
    { 
        return [
            [['level_name', 'up_status'], 'required'],
            [['level_growth', 'cz_integral', 'raffle_num', 'up_status', 'opt_id'], 'integer'],
            [['glcz_discount', 'multiple'], 'number'],
            [['modify_time', 'create_time', 'update_time'], 'safe'],
            [['level_name'], 'string', 'max' => 25],
            [['agent_code'], 'string', 'max' => 100],
        ]; 
    } 

    /** 
     * @inheritdoc 
     */ 
    public function attributeLabels() 
    { 
        return [ 
            'user_level_id' => 'User Level ID',
            'level_name' => 'Level Name',
            'level_growth' => 'Level Growth',
            'cz_integral' => 'Cz Integral',
            'glcz_discount' => 'Glcz Discount',
            'multiple' => 'Multiple',
            'raffle_num' => 'Raffle Num',
            'agent_code' => 'Agent Code',
            'up_status' => 'Up Status',
            'opt_id' => 'Opt ID',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ]; 
    } 
    
    public function getLevelsList(){
        $data = $this->find()->asArray()->all();
        $list = [];
        $list[0] = '请选择';
        foreach ($data as $val){
            $list[$val['user_level_id']] = $val['level_name'];
        }
        return $list;
    }
}
