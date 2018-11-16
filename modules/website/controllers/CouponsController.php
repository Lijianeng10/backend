<?php

namespace app\modules\website\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\db\Exception;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use app\modules\member\models\Gift;
use app\modules\common\models\Coupons;
use app\modules\common\models\CouponsDetail;
use app\modules\member\models\User;
use app\modules\member\models\UserLevels;
use app\modules\member\helpers\Constants;
use app\modules\lottery\models\LotteryCategory;
use app\modules\common\models\ActivityAgent;

class CouponsController extends Controller {

    public function actionIndex() {
        $application_type = Constants::APPLICATION_TYPE;
        $use_range = Constants::USE_RANGE;
        $is_gift = Constants::IS_GIFT;
        $get = \Yii::$app->request->get();
        $couponsList = (new Query())->select("coupons.*")
                ->from("coupons");
        if (isset($get["batch"]) && !empty($get["batch"])) {
            $couponsList = $couponsList->andWhere(["coupons.batch" => $get["batch"]]);
        }
        if (isset($get["application_type"]) && !empty($get["application_type"])) {
            $couponsList = $couponsList->andWhere(["coupons.application_type" => $get["application_type"]]);
        }
        if (isset($get["use_range"]) && !empty($get["use_range"]) && $get["use_range"] != "") {
            $lottery = new LotteryCategory;
            $Ary = $lottery->getLotteryType($get["use_range"]);
            $couponsList = $couponsList->andWhere(["in", "coupons.use_range", $Ary]);
        }
        if (isset($get["coupons_name"]) && !empty($get["coupons_name"])) {
            $couponsList = $couponsList->andWhere(["coupons.coupons_name" => $get["coupons_name"]]);
        }
        if (isset($get["is_gift"]) && !empty($get["is_gift"])) {
            $couponsList = $couponsList->andWhere(["coupons.is_gift" => $get["is_gift"]]);
        }
        if (isset($get["use_agents"]) && !empty($get["use_agents"])) {
            $couponsList = $couponsList->andWhere(["use_agents" => $get["use_agents"]]);
        }
        $couponsList = $couponsList->orderBy("coupons.create_time desc");

        //彩种分类
        $type = LotteryCategory::find()->orderBy('lottery_category_id')->asArray()->all();
        $tree = array();
        $tree[""] = "请选择";
        $tree[0] = "--全部彩种";
        $this->childtree($type, $tree, 0, "");
        $tree[100] = "--购买文章";
        $tree[101] = "--全场通用";
        $data = new ActiveDataProvider([
            'query' => $couponsList,
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
        return $this->render('index', ["data" => $data, "application_type" => $application_type, "use_range" => $tree, "is_gift" => $is_gift, "get" => $get, "proplayform" => $agent,]);
    }

    /*
     * 新增优惠券
     */

    public function actionAddview() {
        if (Yii::$app->request->isGet) {
            $yh_type = Constants::YH_TYPE;
            $is_gift = Constants::IS_GIFT;
            $application_type = Constants::APPLICATION_TYPE;
            $agent = [];
            $agent[0] = "请选择";
            $proplayform = ActivityAgent::find()->select(["agent_name", "agent_code"])->asArray()->all();
            foreach ($proplayform as $k => $v) {
                $agent[$v["agent_code"]] = $v["agent_name"];
            }
            $data = LotteryCategory::find()->orderBy('lottery_category_id')->asArray()->all();
            $tree = array();
            $tree[""] = "请选择";
            $tree[0] = "--全部彩种";
            $this->childtree($data, $tree, 0, "");
            $tree[100] = "--购买文章";
            $tree[101] = "--全场通用";
            return $this->render('addcoupons', ["yh_type" => $yh_type, "use_range" => $tree, "is_gift" => $is_gift, "application_type" => $application_type, "proplayform" => $agent]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $type = $request->post("type", 1);
            $use_range = $request->post("use_range", "");
            $coupons_name = $request->post("coupons_name", "");
            $numbers = $request->post("numbers", '');
            $reduce_money = $request->post("reduce_money", '');
            $days_num = $request->post("days_num", "");
            $less_money = $request->post("less_money", '');
            $send_content = $request->post("send_content", "");
            $start_date = $request->post("start_date", "");
            $end_date = $request->post("end_date", "");
            $is_gift = $request->post("is_gift", 2);
            $stack_use = $request->post("stack_use", 2);
            $application_type = $request->post("application_type", 0);
            $use_agents = $request->post("use_agents", "");
            $is_sure_date = $request->post('is_sure_date', 0);
            $sure_time = $request->post('sure_time', '');
            $is_limit_less = $request->post('is_limit_less', 0);
//            if($type==2){
//                if(empty($discount)){
//                    return $this->jsonResult("109", "折扣比例未填写");
//                }
//            }elseif($type==1){
//                 if(empty($reduce_money)){
//                    return $this->jsonResult("109", "优惠金额未填写");
//                }
//            }
            if (empty($type) || $use_range == "" || empty($reduce_money) || empty($coupons_name) || $numbers == "" || $days_num == "") {
                return $this->jsonResult("109", "参数缺失，请将表单填写完整");
            } elseif ($is_sure_date == 1 && (empty($start_date) || empty($end_date))) {
                return $this->jsonError(109, '参数缺失，请填写有效期区间');
            } elseif ($is_sure_date == 0 && empty($sure_time)) {
                return $this->jsonError(109, '参数缺失，请填写有效天数！！');
            } elseif ($is_limit_less == 1 && empty($less_money)) {
                return $this->jsonError(109, '参数缺失，请填写最低消费金额');
            }
            //生成批次号
            $batchInfo = Coupons::find()->select("max(coupons_id) Mid")->asArray()->one();
            if (empty($batchInfo["Mid"])) {
                $batchInfo["Mid"] = 1;
            } else {
                $batchInfo["Mid"] = $batchInfo["Mid"] + 1;
            }
            $No = str_pad($batchInfo["Mid"], 6, "0", STR_PAD_LEFT);
            $batch = "GL" . $No;
            $coupons = new Coupons();
            $coupons->batch = $batch;
            $coupons->coupons_name = $coupons_name;
            $coupons->type = $type;
            $coupons->application_type = $application_type;
            $coupons->is_gift = $is_gift;
//            if($type==1){
//                $coupons->reduce_money=$reduce_money;
//            }elseif($type==2){
//                $coupons->discount=$discount;
//            }
            $coupons->numbers = $numbers;
            $coupons->use_range = $use_range;
            $coupons->use_agents = $use_agents;
            $coupons->less_consumption = $less_money;
            $coupons->reduce_money = $reduce_money;
            $coupons->days_num = $days_num;
            $coupons->stack_use = $stack_use;
            $coupons->is_sure_date = $is_sure_date;
            $coupons->is_limit_less = $is_limit_less;
            if ($is_sure_date == 1) {
                $coupons->start_date = $start_date . " 00:00:00";
                $coupons->end_date = $end_date . " 23:59:59";
            } else {
                $coupons->sure_time = $sure_time;
            }

            if (!empty($send_content)) {
                $coupons->send_content = $send_content;
            }

            $coupons->opt_id = $session["admin_id"];
            $coupons->create_time = date('Y-m-d H:i:s');
            //保存优惠主表信息记录
            if ($coupons->validate()) {
                $res = $coupons->save();
                if ($res == false) {
                    return $this->jsonResult("109", "优惠券新增失败");
                }
            } else {
                return $this->jsonResult("109", "优惠券表单验证失败", $coupons->getErrors());
            }
            if ($application_type == 1) {
                return $this->jsonResult("600", "优惠券发行成功");
            } else {
                $db = Yii::$app->db;
                $trans = $db->beginTransaction();
                try {
                    //保存优惠券明细表记录
                    $times = date('Y-m-d H:i:s');
                    $detail = "insert into coupons_detail(coupons_no,conversion_code,coupons_batch,create_time) values";
                    for ($i = 1; $i <= $numbers; $i++) {
                        $num = str_pad($i, 6, "0", STR_PAD_LEFT);
                        $coupons_no = $batch . date("Ymdhis") . $num;
                        $conversionCode = $this->getCouponsMark();
                        if ($i == $numbers) {
                            $detail .= "('" . $coupons_no . "','" . $conversionCode . "','" . $batch . "','" . $times . "')";
                        } else {
                            $detail .= "('" . $coupons_no . "','" . $conversionCode . "','" . $batch . "','" . $times . "'),";
                        }
                    }
                    $addDetail = $db->createCommand($detail)->execute();
                    if (!$addDetail) {
                        throw new Exception('失败，优惠券详情表新增失败');
                    }
                    $trans->commit();
                    return $this->jsonResult(600, '优惠券发行成功');
                } catch (Exception $e) {
                    $trans->rollBack();
                    return $this->jsonResult(109, $e->getMessage());
                }
            }
        }
    }

    /*
     * 根据批次查看优惠券详情
     */

    public function actionViewDetail() {
        $send_status = [
            "" => "请选择",
            "1" => "未发送",
            "2" => "已发送",
        ];
        $use_status = [
            "" => "请选择",
            "0" => "未领取",
            "1" => "未使用",
            "2" => "已使用",
        ];
        $status = [
            "" => "请选择",
            "1" => "激活",
            "2" => "锁定",
        ];
        $get = \Yii::$app->request->get();
        $couponsList = (new Query())->select("*")
                ->from("coupons_detail")
                ->where(["coupons_batch" => $get["batch"]]);
        if (isset($get["coupons_no"]) && !empty($get["coupons_no"])) {
            $couponsList = $couponsList->andWhere(["or", ["coupons_no" => $get["coupons_no"]], ["conversion_code" => $get["coupons_no"]]]);
        }
        if (isset($get["send_user"]) && !empty($get["send_user"])) {
            $couponsList = $couponsList->andWhere(["send_user" => $get["send_user"]]);
        }
        if (isset($get["send_status"]) && !empty($get["send_status"])) {
            $couponsList = $couponsList->andWhere(["send_status" => $get["send_status"]]);
        }
        if (isset($get["use_status"]) && $get["use_status"] != "") {
            $couponsList = $couponsList->andWhere(["use_status" => $get["use_status"]]);
        }
        if (isset($get["status"]) && !empty($get["status"])) {
            $couponsList = $couponsList->andWhere(["status" => $get["status"]]);
        }
        if (!empty($get["start_date"])) {
            $couponsList = $couponsList->andWhere([">", "send_time", $get["start_date"] . " 00:00:00"]);
        }
        if (!empty($get["end_date"])) {
            $couponsList = $couponsList->andWhere(["<", "send_time", $get["end_date"] . " 23:59:59"]);
        }
        if (!empty($get["start"])) {
            $couponsList = $couponsList->andWhere([">", "use_time", $get["start"] . " 00:00:00"]);
        }
        if (!empty($get["end"])) {
            $couponsList = $couponsList->andWhere(["<", "use_time", $get["end"] . " 23:59:59"]);
        }
        $data = new ActiveDataProvider([
            'query' => $couponsList,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        //查找当前批次的优惠券信息
        $coupons = Coupons::find()->where(["batch" => $get["batch"]])->asArray()->one();
        return $this->render('viewdetail', ["data" => $data, "send_status" => $send_status, "use_status" => $use_status, "status" => $status, "get" => $get, "coupons" => $coupons]);
    }

    /*
     * 修改优惠券状态
     */

    public function actionEditstatus() {
        $post = \Yii::$app->request->post();
        if (!isset($post["id"]) || empty($post["id"])) {
            return $this->jsonResult(109, "参数缺失");
        }
        $updateRes = CouponsDetail::updateAll(["status" => $post["status"]], ["coupons_no" => $post["id"]]);
        if ($updateRes) {
            return $this->jsonResult(600, "状态修改成功");
        } else {
            return $this->jsonResult(109, "状态修改失败");
        }
    }

    /*
     * 查看优惠券详情
     */

    public function actionReadDetail() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        if (empty($get["coupons_no"])) {
            return $this->jsonResult(109, "参数缺失");
        }
        $detailRes = CouponsDetail::find()->where(["coupons_no" => $get["coupons_no"]])->asArray()->one();
        $detailRes["coupons"] = Coupons::find()->where(["batch" => $detailRes["coupons_batch"]])->asArray()->one();
        return $this->render('readdetail', ["data" => $detailRes]);
    }

    /**
     * 生成15位优惠券兑换码
     */
    public function getCouponsMark() {
        $str = md5(uniqid(microtime(true), true));
        $mark = strtoupper(substr($str, 0, 15));
        while (CouponsDetail::findOne(["conversion_code" => $mark])) {
            $str = md5(uniqid(microtime(true), true));
            $mark = strtoupper(substr($str, 0, 15));
        }
        return $mark;
    }

    /**
     * 生成彩种分类子集效果
     */
    public function childtree($info, &$tree, $pid = 0, $str) {
        $str .= "--";
        if (!empty($info)) {
            foreach ($info as $k => &$v) {
                if ($v['parent_id'] == $pid) {
                    $tree[$v["lottery_category_id"]] = $str . $v["cp_category_name"];
//                    $this->childtree($info, $tree, $v["lottery_category_id"], $str);
                    unset($info[$k]);
                }
            }
        }
    }

    /*
     * 发送优惠券
     */

    public function actionSendCoupons() {
        if (Yii::$app->request->isGet) {
            $get = \Yii::$app->request->get();
            if (empty($get["batch"])) {
                return $this->jsonResult(109, "参数缺失");
            }
            //用户等级
            $userLevel = UserLevels::find()->select("user_level_id,level_name")->indexBy('user_level_id')->asArray()->all();
            $levelAry = array();
            $levelAry[""] = "请选择";
            $levelAry[0] = "所有用户";
            $levelAry[100] = "按会员号发送";
            foreach ($userLevel as $k => $v) {
                $levelAry[$k] = $v["level_name"];
            }
            return $this->render('sendcoupons', ["batch" => $get["batch"], "levelAry" => $levelAry]);
        } elseif (Yii::$app->request->isAjax) {
            $post = \Yii::$app->request->post();
            $batch = $post["batch"];
            $userAry = $post["userAry"];
            if ($batch == "" || $userAry == "") {
                return $this->jsonResult(109, "参数缺失");
            }
            $couponsData = Coupons::find()->select(['is_sure_date', 'start_date', 'end_date', 'sure_time'])->where(['batch' => $batch])->asArray()->one();
            if ($couponsData['is_sure_date'] == 0) {
                $startTime = date('Y-m-d H:i:s');
                $endTime = date('Y-m-d H:i:s', strtotime("+{$couponsData['sure_time']} day"));
            } else {
                $startTime = $couponsData['start_date'];
                $endTime = $couponsData['end_date'];
            }
            $db = Yii::$app->db;
            $trans = $db->beginTransaction();
            try {
                //保存优惠券明细表记录
                $times = date('Y-m-d H:i:s');
                $detail = "insert into coupons_detail(coupons_no,conversion_code,coupons_batch,send_user,send_status,use_status,send_time,start_date,end_date,create_time) values";
                foreach ($userAry as $k => $v) {
                    $num = str_pad($k + 1, 6, "0", STR_PAD_LEFT);
                    $coupons_no = $batch . date("Ymdhis") . $num;
                    $conversionCode = $this->getCouponsMark();
                    if ($k == count($userAry) - 1) {
                        $detail .= "('" . $coupons_no . "','" . $conversionCode . "','" . $batch . "','" . $v . "',2,1,'" . $times ."','" . $startTime . "','" . $endTime .  "','" . $times . "')";
                    } else {
                        $detail .= "('" . $coupons_no . "','" . $conversionCode . "','" . $batch . "','" . $v . "',2,1,'" . $times ."','" . $startTime . "','" . $endTime .  "','" . $times . "'),";
                    }
                }
                $addDetail = $db->createCommand($detail)->execute();
                if (!$addDetail) {
                    throw new Exception('失败，优惠券详情表新增失败');
                }
                //更新优惠券主表数据
                $updateCoupons = \Yii::$app->db->createCommand()->update('coupons', ['numbers' => new Expression('numbers+' . count($userAry)), 'send_num' => new Expression('send_num+' . count($userAry))], ["batch" => $batch])->execute();
                if (!$updateCoupons) {
                    throw new Exception('优惠券表单更新失败');
                }
                $trans->commit();
                return $this->jsonResult(600, '优惠券发行成功');
            } catch (Exception $e) {
                $trans->rollBack();
                return $this->jsonResult(109, $e->getMessage());
            }
        }
    }

    /*
     * 获取选择用户编号
     */

    public function actionGetUserInfo() {
        $post = \Yii::$app->request->post();
        if ($post["type"] == "") {
            return $this->jsonResult(109, "参数缺失");
        } elseif ($post["type"] == 0) {
            //用户信息
            $user = User::find()->select("cust_no")->where(["status" => 1])->asArray()->all();
            return $this->jsonResult(600, "", $user);
        } else {
            //用户信息
            $user = User::find()->select("cust_no")->where(["level_id" => $post["type"], "status" => 1])->asArray()->all();
            return $this->jsonResult(600, "", $user);
        }
    }

    /*
     * 判断该批次优惠券是否可编辑
     * 已存在发放优惠券和是礼品的批次不可编辑
     */

    public function actionEditCoupons() {
        $post = \Yii::$app->request->post();
        $batch = $post["batch"];
        if (empty($batch)) {
            return $this->jsonResult(109, "优惠券批次参数缺失", "");
        }
        $detail = CouponsDetail::find()->select("coupons_detail_id")->where(["coupons_batch" => $batch, "send_status" => 2])->asArray()->one();
        if (!empty($detail)) {
            return $this->jsonResult(109, "该批次优惠券已发放给用户，不允许修改", "");
        }
        $gift = Gift::find()->select("gift_id")->where(["batch" => $batch])->asArray()->one();
        if (!empty($gift)) {
            return $this->jsonResult(109, "该批次优惠券已是在线礼品，不允许修改", "");
        }
        return $this->jsonResult(600, "", "");
    }

    /**
     * 编辑优惠券
     */
    public function actionEditCouponsDetail() {
//        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $get = \Yii::$app->request->get();
            $yh_type = Constants::YH_TYPE;
            $is_gift = Constants::IS_GIFT;
            $application_type = Constants::APPLICATION_TYPE;
            $data = LotteryCategory::find()->orderBy('lottery_category_id')->asArray()->all();
            $tree = array();
            $tree[""] = "请选择";
            $tree[0] = "--全部彩种";
            $this->childtree($data, $tree, 0, "");
            $tree[100] = "--购买文章";
            $tree[101] = "--全场通用";
            $batch = $get["batch"];
            if (empty($batch)) {
                return $this->jsonResult(109, "优惠券批次参数缺失", "");
            }
            $agent = [];
            $agent[0] = "请选择";
            $proplayform = ActivityAgent::find()->select(["agent_name", "agent_code"])->asArray()->all();
            foreach ($proplayform as $k => $v) {
                $agent[$v["agent_code"]] = $v["agent_name"];
            }
            $couponsDetail = Coupons::find()->where(["batch" => $batch])->asArray()->one();
            return $this->render('editcoupons', ["data" => $couponsDetail, "yh_type" => $yh_type, "use_range" => $tree, "is_gift" => $is_gift, "application_type" => $application_type, "proplayform" => $agent]);
        } elseif (Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            $session = Yii::$app->session;
            $batch = $request->post("batch", 1);
            $type = $request->post("type", 1);
            $use_range = $request->post("use_range", "");
            $coupons_name = $request->post("coupons_name", "");
            $numbers = $request->post("numbers", '');
            $reduce_money = $request->post("reduce_money", '');
            $days_num = $request->post("days_num", "");
            $less_money = $request->post("less_money", '');
            $send_content = $request->post("send_content", "");
            $start_date = $request->post("start_date", "");
            $end_date = $request->post("end_date", "");
            $is_gift = $request->post("is_gift", 2);
            $stack_use = $request->post("stack_use", 2);
            $application_type = $request->post("application_type", 0);
            $use_agents = $request->post("use_agents", "");
            $is_sure_date = $request->post('is_sure_date', '');
            $sure_time = $request->post('sure_time', '');
            $is_limit_less = $request->post('is_limit_less', 0);
            if (empty($batch) || empty($type) || $use_range == "" || empty($reduce_money) || empty($coupons_name) || $numbers == "" || $days_num == "") {
                return $this->jsonResult("109", "参数缺失，请将表单填写完整");
            } elseif ($is_sure_date == 1 && (empty($start_date) || empty($end_date))) {
                return $this->jsonError(109, '参数缺失，请填写有效期区间');
            } elseif ($is_sure_date == 0 && empty($sure_time)) {
                return $this->jsonError(109, '参数缺失，请填写有效天数！！');
            } elseif ($is_limit_less == 1 && empty($less_money)) {
                return $this->jsonError(109, '参数缺失，请填写最低消费金额');
            }
            $coupons = Coupons::find()->where(["batch" => $batch])->one();
            $coupons->coupons_name = $coupons_name;
            $coupons->type = $type;
            $coupons->application_type = $application_type;
            $coupons->is_gift = $is_gift;
            $coupons->numbers = $numbers;
            $coupons->use_range = $use_range;
            $coupons->use_agents = $use_agents;
            $coupons->less_consumption = $less_money;
            $coupons->reduce_money = $reduce_money;
            $coupons->days_num = $days_num;
            $coupons->stack_use = $stack_use;
            $coupons->is_sure_date = $is_sure_date;
            $coupons->is_limit_less = $is_limit_less;
            if ($is_sure_date == 1) {
                $coupons->start_date = $start_date . " 00:00:00";
                $coupons->end_date = $end_date . " 23:59:59";
            } else {
                $coupons->sure_time = $sure_time;
            }
            if (!empty($send_content)) {
                $coupons->send_content = $send_content;
            }
            $coupons->opt_id = $session["admin_id"];
            if ($coupons->validate()) {
                $res = $coupons->save();
                if ($res == false) {
                    return $this->jsonResult("109", "优惠券修改失败");
                } else {
                    return $this->jsonResult("600", "优惠券修改成功");
                }
            } else {
                return $this->jsonResult("109", "优惠券表单验证失败", $coupons->getErrors());
            }
        }
    }

    /**
     * 发送优惠券验证输入是否是已注册咕啦会员
     */
    public function actionGetCustno() {
        $post = Yii::$app->request->post();
        $user = User::find()->select("cust_no")->where(["or", ["cust_no" => $post["custNo"]], ["user_tel" => $post["custNo"]]])->asArray()->one();
        if (!empty($user)) {
            return $this->jsonResult("600", "获取成功", $user);
        } else {
            return $this->jsonResult("109", "你输入的不是合法的星星会员", "");
        }
    }

    public function actionEditSta() {
        $request = \Yii::$app->request;
        $batch = $request->post('batch', '');
        if (empty($batch)) {
            return $this->jsonError(109, '参数缺失');
        }
        $coupons = Coupons::findOne(['batch' => $batch]);
        if (empty($coupons)) {
            return $this->jsonError(109, '该批次电子优惠券不存在，请重新加载');
        }
        $coupons->status = 0;
        $coupons->opt_id = \Yii::$app->session['admin_id'];
        if (!$coupons->save()) {
            return $this->jsonError(109, '保存失败！请重新操作');
        }
        return $this->jsonResult(600, '保存成功', true);
    }

}
