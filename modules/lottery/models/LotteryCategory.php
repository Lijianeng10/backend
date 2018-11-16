<?php

namespace app\modules\lottery\models;

use Yii;

/**
 * This is the model class for table "lottery_category".
 *
 * @property integer $lottery_category_id
 * @property string $cp_category_name
 * @property string $modify_time
 * @property string $create_time
 * @property string $update_time
 */
class LotteryCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lottery_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cp_category_name'], 'required'],
            [['modify_time', 'create_time', 'update_time'], 'safe'],
            [['cp_category_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lottery_category_id' => 'Lottery Category ID',
            'cp_category_name' => 'Cp Category Name',
            'modify_time' => 'Modify Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
    
    /**
     * 获取全部彩种类别
     * @return array
     */
    public function getCategoryList(){
        $result = $this->find()
                ->select("lottery_category_id,cp_category_name")
                ->asArray()
                ->all();
        $ret['0'] = '请选择';
        foreach ($result as $val) {
            $ret[$val["lottery_category_id"]] = $val["cp_category_name"];
        }
        return $ret;
    }
    
     /**
     * 筛选彩种类别
     */
    public function getLotteryType($cId){
        $categoryInfo=  $this->find()->select("lottery_category_id,parent_id")->where(["lottery_category_id"=>$cId])->asArray()->one();
        $newAry=[];
        if($categoryInfo["parent_id"]==0){
            array_push($newAry, $categoryInfo["lottery_category_id"]);
            $childInfo=$this->find()->select("lottery_category_id")->where(["parent_id"=>$categoryInfo["lottery_category_id"]])->asArray()->all();
            if(!empty($childInfo)){
                foreach($childInfo as $v){
                    array_push($newAry, $v["lottery_category_id"]);
                }
            }
        }else{
           array_push($newAry, $categoryInfo["lottery_category_id"]); 
        }
        return $newAry;
    }
}
