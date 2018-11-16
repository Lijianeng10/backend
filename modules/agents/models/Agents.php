<?php

namespace app\modules\agents\models;

use app\modules\common\models\Bussiness;
use Yii;
use app\modules\agents\models\User;
class Agents extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agents';
    }
     /**
     * @inheritdoc
     */
//    public function rules()
//    {
//        return [
//            [['agents_appid','secret_key','agents_account','agents_name','upagents_code','upagents_name', 'agents_type'], 'required'],
//            [['check_time', 'create_time', 'update_time'], 'safe'],
//            ['agents_code','string','max'=>45],
//            [['agents_remark', 'review_remark','to_url'],'string', 'max' => 100],
//            [['agents_name', 'upagents_name','opt_id'],'string','max' => 100],
//        ];
//    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'agents_id' => 'Agents Id',
            'agents_appid' => 'Agents Appid',
            'secret_key' => 'Secret Key',
            'agents_account' => 'Agents Account',
            'agents_name' => 'Agents Name',
            'agents_code' => 'Agents Code',
            'upagents_code' => 'Upagents Code',
            'upagents_name' => 'Upagents Name',
            'to_url' => 'To Url',
            'agents_type' => 'Agents Type',
            'pass_status' => 'Pass Status',
            'use_status' => 'Use Status',
            'create_time' => 'Create Time',
            'check_time' => 'Check Time',
            'update_time' => 'Update Time',
            'opt_id' => 'Opt Id',
            'agents_remark' => 'Agents Remark',
            'review_remark' => 'Review Remark',
        ];
    }
    public static function getAgentsArray(){
        $form=[];
        $form['']="请选择";
        $form[0]="咕啦体育";
        //代理商
        $agents = Agents::find()->select("agents_id,agents_name")->asArray()->all();
        foreach ($agents as $k=>$v){
            $form[$v['agents_id']]=$v['agents_name'];
        }
        //推广人员
        $user = User::find()->select('user_id,user_name')->asArray()->all();
//        ->where(["<>","spread_type","0"])
        foreach ($user as $k=>$v){
            $form[$v['user_id']]=$v['user_name'];
        }
        return $form;
    }
}

