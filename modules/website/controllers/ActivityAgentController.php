<?php

namespace app\modules\website\controllers;

use app\modules\common\models\ActivityType;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\db\Exception;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use app\modules\member\helpers\Constants;
use app\modules\common\models\ActivityAgent;

class ActivityAgentController extends Controller {

    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $agentList = (new Query())->select("*")
                ->from("activity_agent");
        $agentList = $agentList->orderBy("create_time desc");
        $data = new ActiveDataProvider([
            'query' => $agentList,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
        return $this->render('index', ["data" => $data,"get" => $get]);
    }

    /*
     * 配置活动信息：新增优惠券
     */

    public function actionAddAgent() {
        if (Yii::$app->request->isGet) {
            $this->layout=false;
            return $this->render('add-agent');
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $agent_name = $request->post("agent_name","");
            $agent_code = $request->post("agent_code", "");
            if (empty($agent_name) ||empty($agent_code)) {
                return $this->jsonResult("109", "参数缺失，请将表单填写完整");
            }
            //保证code唯一性
            $res = ActivityAgent::findOne(["agent_code"=>$agent_code]);
            if(!empty($res)){
                return $this->jsonResult("109", "代理商Code重复，请检查");
            }
            //保存活动数据
            $agent = new ActivityAgent();
            $agent->agent_name = $agent_name;
            $agent->agent_code = $agent_code;
            $agent->create_time = date("Y-m-d H:i:s");
            if($agent->validate()){
                $res = $agent->save();
                if ($res == false) {
                    return $this->jsonResult(109, "新增失败");
                }else{
                    return $this->jsonResult(600, "新增成功");
                }
            }else{
                return $this->jsonResult(109, "表单验证失败",$agent->getFirstErrors());
            }
        }
    }
    /*
     * 修改优惠券状态
     */

    public function actionDelAgent() {
        $post = \Yii::$app->request->post();
        if (!isset($post["id"]) || empty($post["id"])) {
            return $this->jsonResult(109, "参数缺失");
        }
        $updateRes = ActivityAgent::deleteAll(["activity_agent_id" => $post["id"]]);
        if ($updateRes) {
            return $this->jsonResult(600, "删除成功");
        } else {
            return $this->jsonResult(109, "删除失败");
        }
    }
    /*
    * 编辑优惠券
    */

    public function actionEditAgent() {
        if (Yii::$app->request->isGet) {
            $this->layout=false;
            $get=Yii::$app->request->get();
            $agentId=$get["agent_id"];
            if(empty($agentId)){
                return $this->jsonResult(109, '参数有误', '');
            }
            $agent = ActivityAgent::find()->where(["activity_agent_id"=>$agentId])->asArray()->one();
            return $this->render('edit-agent',["data"=>$agent]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $activity_agent_id = $request->post("activity_agent_id","");
            $agent_name = $request->post("agent_name","");
            $agent_code = $request->post("agent_code", "");
            if (empty($activity_agent_id)||empty($agent_name) ||empty($agent_code)) {
                return $this->jsonResult(109, "参数缺失，请将表单填写完整");
            }
            //保存活动数据
            $agentInfo = ActivityAgent::find()->where(["activity_agent_id"=>$activity_agent_id])->one();
            if($agent_name==$agentInfo->agent_name&&$agent_code==$agentInfo->agent_code){
                return $this->jsonResult(109, "数据相同，无需更新");
            }
            //保证code的唯一性
            $res = ActivityAgent::find()->where(["agent_code" => $agent_code])->andWhere(["<>","activity_agent_id",$activity_agent_id])->one();
            if(!empty($res)){
                return $this->jsonResult(109, "代理商Code重复，请检查");
            }
            $agentInfo->agent_name= $agent_name;
            $agentInfo->agent_code = $agent_code;
            $res = $agentInfo->save();
            if ($res == false) {
                return $this->jsonResult(109, "编辑失败");
            }else{
                return $this->jsonResult(600, "编辑成功");
            }
        }
    }


}
