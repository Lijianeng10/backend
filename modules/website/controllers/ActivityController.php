<?php

namespace app\modules\website\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\db\Exception;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Coupons;
use app\modules\member\helpers\Constants;
use app\modules\common\models\CouponsActivity;
use app\modules\common\models\ActivityAgent;
use app\modules\common\models\ActivityType;
use app\modules\common\models\Activity;

class ActivityController extends Controller {

    public function actionIndex() {
        $request = Yii::$app->request;
        $get = \Yii::$app->request->get();
        $use_agengts = $request->get('use_agengts', '');
        $type_id = $request->get('type_id', '');
        $status = $request->get('status', '');
        $acStatus = Constants::ACTIVITY_STATUS;
        $activityList = (new Query())->select("*")
                ->from("activity");
        if (!empty($use_agengts)) {
            $activityList = $activityList->andWhere(["use_agengts" => $use_agengts]);
        }
        if (isset($type_id) && !empty($type_id)) {
            $activityList = $activityList->andWhere(["type_id" => $type_id]);
        }
        if (isset($status) && $status != "") {
            $activityList = $activityList->andWhere(["status" => $status]);
        }
        $activityList = $activityList->orderBy("activity_id desc");
        $data = new ActiveDataProvider([
            'query' => $activityList,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
        $agent = [];
        $agent[0] = "请选择";
        $proplayform = ActivityAgent::find()->select(["agent_name", "agent_code"])->asArray()->all();
        foreach ($proplayform as $k => $v) {
            $agent[$v["agent_code"]] = $v["agent_name"];
        }
        $type = [];
        $type[0] = "请选择";
        $ac_type = ActivityType::find()->select(["type_name", "activity_type_id"])->asArray()->all();
        foreach ($ac_type as $k => $v) {
            $type[$v["activity_type_id"]] = $v["type_name"];
        }
        return $this->render('index', ["data" => $data, "get" => $get, "proplayform" => $agent, "ac_type" => $type, "acStatus" => $acStatus]);
    }

    /*
     * 配置活动信息：新增优惠券
     */

    public function actionAddActivity() {
        if (Yii::$app->request->isGet) {
            $this->layout = false;
            $agent = [];
            $agent[0] = "请选择";
            $proplayform = ActivityAgent::find()->select(["agent_name", "agent_code"])->asArray()->all();
            foreach ($proplayform as $k => $v) {
                $agent[$v["agent_code"]] = $v["agent_name"];
            }
            $type = [];
            $type[0] = "请选择";
            $ac_type = ActivityType::find()->select(["type_name", "activity_type_id"])->asArray()->all();
            foreach ($ac_type as $k => $v) {
                $type[$v["activity_type_id"]] = $v["type_name"];
            }
            return $this->render('add-activity', ["proplayform" => $agent, "ac_type" => $type]);
        } elseif (Yii::$app->request->isAjax) {
            $now = date("Y-m-d H:i:s");
            $request = Yii::$app->request;
            $activity_name = $request->post("activity_name", "");
            $use_agents = $request->post("use_agents", "");
            $type = $request->post("type", "");
            $start_date = $request->post("start_date", '');
            $end_date = $request->post("end_date", '');
            $activityAry = $request->post("activityAry", "");
            if (empty($use_agents) || empty($activityAry) || empty($start_date) || empty($end_date) || empty($type)) {
                return $this->jsonResult("109", "参数缺失，请将表单填写完整");
            }
            //判断是否有同代理同类型的活动存在
            $data = Activity::find()->where(["use_agents" => $use_agents, "type_id" => $type, "status" => 1])->one();
            if (!empty($data)) {
                return $this->jsonResult("109", "存在同代理商同类型活动处于生效中，请先禁用");
            }
            //保存活动主表数据
            $activity = new Activity();
            $activity->activity_name = $activity_name;
            $activity->use_agents = $use_agents;
            $activity->type_id = $type;
            $activity->start_date = $start_date;
            $activity->end_date = $end_date;
            $activity->create_time = date("Y-m-d H:i:s");
            if ($activity->validate()) {
                $res = $activity->save();
                if ($res == false) {
                    return $this->jsonResult(109, "新增失败");
                } else {
                    //取出新插入ID
                    $activity_id = $activity->attributes['activity_id'];
                    foreach ($activityAry as $v) {
                        $newAry[] = [$activity_id, $v["batch"], $v["send_num"], $now];
                    }
                    Yii::$app->db->createCommand()->batchInsert('coupons_activity', ['activity_id', 'batch', 'send_num', 'create_time'], $newAry)->execute();
                    return $this->jsonResult("600", "新增成功");
                }
            } else {
                return $this->jsonResult(109, "表单验证失败", $agent->getFirstErrors());
            }
        }
    }

    /*
     * 修改优惠券状态
     */

    public function actionEditStatus() {
        $post = \Yii::$app->request->post();
        if (!isset($post["id"]) || empty($post["id"])) {
            return $this->jsonResult(109, "参数缺失");
        }
        $updateRes = CouponsActivity::updateAll(["status" => $post["status"]], ["coupons_activity_id" => $post["id"]]);
        if ($updateRes) {
            return $this->jsonResult(600, "状态修改成功");
        } else {
            return $this->jsonResult(109, "状态修改失败");
        }
    }

    /*
     * 获取选择代理所属优惠券
     */

    public function actionGetBatch() {
        $now = date('Y-m-d H:i:s');
        $post = \Yii::$app->request->post();
        if ($post["use_agents"] == "") {
            return $this->jsonResult(109, "参数缺失");
        }
//        ['and',["<=","start_date",$now],]
        $batch = Coupons::find()->select(["batch", "coupons_name", "less_consumption", "reduce_money"])
                ->where(["use_agents" => $post["use_agents"]])
                ->andWhere(["status" => 1])
                ->asArray()
                ->all();
        return $this->jsonResult(600, "获取成功", $batch);
    }

    /*
     * 根据代理商和活动类型查看数据
     */

    public function actionViewCoupons() {
        $get = \Yii::$app->request->get();
        $couponsList = (new Query())->select("c.*,cp.coupons_name,cp.less_consumption,cp.reduce_money,cp.days_num,cp.stack_use")
                ->from("coupons_activity as c")
                ->leftJoin("coupons as cp", "cp.batch = c.batch")
                ->where(["c.activity_id" => $get["id"]]);
        $data = new ActiveDataProvider([
            'query' => $couponsList,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
        return $this->render('view-coupons', ["data" => $data, "get" => $get,]);
    }

    /*
     * 修改活动状态，同时会修改优惠券子活动状态
     */

    public function actionEditActivityStatus() {
        $post = \Yii::$app->request->post();
        if (!isset($post["id"]) || empty($post["id"])) {
            return $this->jsonResult(109, "参数缺失");
        }
        $res = Activity::updateAll(["status" => $post["status"]], ["activity_id" => $post["id"]]);
        $updateRes = CouponsActivity::updateAll(["status" => $post["status"]], ["activity_id" => $post["id"]]);
        if ($updateRes) {
            return $this->jsonResult(600, "状态修改成功");
        } else {
            return $this->jsonResult(109, "状态修改失败");
        }
    }

    /*
     * 删除优惠券
     */

    public function actionDelete() {
        $post = \Yii::$app->request->post();
        if (!isset($post["id"]) || empty($post["id"])) {
            return $this->jsonResult(109, "参数缺失");
        }
        $delActivity = Activity::deleteAll(['activity_id' => $post['id']]);
        if (!$delActivity) {
            return $this->jsonResult(109, "删除失败");
        }
        $updateRes = CouponsActivity::deleteAll(["activity_id" => $post["id"]]);
        if ($updateRes) {
            return $this->jsonResult(600, "删除成功");
        } else {
            return $this->jsonResult(109, "删除失败");
        }
    }

}
