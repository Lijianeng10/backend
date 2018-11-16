<?php

namespace app\modules\lottery\controllers;

use app\modules\common\models\Bussiness;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\LotteryOrder;
use app\modules\lottery\models\Lottery;
use app\modules\lottery\models\Schedule;
use app\modules\lottery\helpers\Constant;
use app\modules\common\helpers\Constants;
use app\modules\lottery\models\LotteryAdditional;
use app\modules\member\models\User;
use app\modules\lottery\models\FootballFourteen;
use app\modules\lottery\models\LanSchedule;
use app\modules\lottery\models\OptionalSchedule;
use app\modules\lottery\models\BettingDetail;
use app\modules\lottery\models\LotteryRecord;
use app\modules\lottery\helpers\Winning;
use app\modules\common\models\BdSchedule;
use app\modules\common\models\WorldcupChp;
use app\modules\common\models\WorldcupFnl;
use app\modules\common\models\OrderTaking;
use app\modules\agents\models\Agents;

class BettingController extends Controller {

    /**
     * 投注列表
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        if (isset($get["lottery_category_id"])) {
            $lottery_category_ids = explode(",", $get["lottery_category_id"]);
        } else {
            $lottery_category_ids = [4, 5, 6];
        }
        $query = Lottery::find()->select("lottery_code")->where(["in", "lottery_category_id", $lottery_category_ids]);
        $detail = (new Query())->select(["lottery_order.*", 's.store_name', 's.phone_num', 'sd.consignee_name', 'u.user_tel', "pr.pay_pre_money"])
                ->from("lottery_order")
                ->leftJoin('store as s', 's.store_code = lottery_order.store_no and s.user_id=lottery_order.store_id')
                ->leftJoin('store_detail as sd', 'sd.store_id = s.store_id ')
                ->leftJoin('user as u', 'u.cust_no = lottery_order.cust_no')
                ->leftJoin("pay_record as pr", "pr.order_code=lottery_order.lottery_order_code and pr.pay_type=16")
                ->where(["in", "lottery_order.lottery_id", $query]);
//                ->andWhere("lottery_order.source!=4");

        if (isset($get["user_info"])) {
            $detail = $detail->andWhere(["or", ["lottery_order.cust_no" => $get['user_info']], ["u.user_name" => $get['user_info']], ["u.user_tel" => $get['user_info']]]);
        }
        if (isset($get["store_info"])) {
            $detail = $detail->andWhere(["or", ["s.cust_no" => $get['store_info']], ["s.store_name" => $get['store_info']], ["s.phone_num" => $get['store_info']], ["sd.consignee_name" => $get['store_info']]]);
        }
        if (isset($get["lottery_order_code"])) {
            $detail = $detail->andWhere(["or", ["like", "lottery_order.lottery_order_code", "%{$get['lottery_order_code']}%", false], ["taking_code" => $get["lottery_order_code"]]]);
        }
        if (isset($get["lottery_code"])) {
            $detail = $detail->andWhere(["lottery_order.lottery_id" => $get["lottery_code"]]);
        }
        if (isset($get["startdate"])) {
            $detail = $detail->andWhere([">", "lottery_order.create_time", $get["startdate"] . " 00:00:00"]);
        } else {
            $detail = $detail->andWhere([">", "lottery_order.create_time", date("Y-m-d", strtotime("-3 day")) . " 00:00:00"]);
        }
        if (isset($get["enddate"])) {
            $detail = $detail->andWhere(["<", "lottery_order.create_time", $get["enddate"] . " 23:59:59"]);
        } else {
            $detail = $detail->andWhere(["<", "lottery_order.create_time", date("Y-m-d") . " 23:59:59"]);
        }
        if (isset($get["end_time_start"])) {
            $detail = $detail->andWhere([">=", "lottery_order.end_time", $get["end_time_start"]]);
        }
        if (isset($get["end_time_end"])) {
            $detail = $detail->andWhere(["<=", "lottery_order.end_time", $get["end_time_end"]]);
        }
        if (isset($get["status"])) {
            $detail = $detail->andWhere(["in", "lottery_order.status", explode("|", $get["status"])]);
        } else {
            if (isset($get["choose"])) {
                $detail = $detail->andWhere(["=", "lottery_order.status", $get["choose"]]);
            } else {
                $detail = $detail->andWhere(["!=", "lottery_order.status", "1"]);
            }
        }

        if (isset($get["deal_status"])) {
            $detail = $detail->andWhere(["lottery_order.deal_status" => $get["deal_status"]]);
        }
        if (isset($get["auto_type"])) {
            $detail = $detail->andWhere(["lottery_order.auto_type" => $get["auto_type"]]);
        }
        if (isset($get["source"]) && $get["source"] != '') {
            $detail = $detail->andWhere(["lottery_order.source" => $get["source"]]);
        }
        if (isset($get["out_start"])) {
            $detail = $detail->andWhere([">", "lottery_order.out_time", $get["out_start"]]);
        }
        if (isset($get["out_end"])) {
            $detail = $detail->andWhere(["<", "lottery_order.out_time", $get["out_end"] . " 23:59:59"]);
        }
        if (isset($get["plat"]) && $get["plat"] != '') {
            switch ($get["plat"]){
                case 1:
                    $detail = $detail->andWhere(["lottery_order.order_platform" => $get["plat"],"lottery_order.agent_id"=>0]);
                    break;
                case 2:
                case 4:
                    if (isset($get["from"]) && $get["from"] != '') {
                        $detail = $detail->andWhere(["lottery_order.order_platform" => $get["plat"],"lottery_order.agent_id" => $get["from"]]);
                    }
                    break;
                case 3:
                    if (isset($get["from"]) && $get["from"] != '') {
                        $detail = $detail->andWhere(["lottery_order.order_platform" => $get["plat"],"lottery_order.user_id" => $get["from"]]);
                    }
                    break;

            }
            //订单来源
            $orderForm = $this->getOrderFromAry($get["plat"]);
        }else{
            $orderForm = [];
            $orderForm[''] = "请选择";
        }
        $detail = $detail->orderBy("lottery_order.create_time desc");
        $orderStatus = Constant::ORDER_STATUS;
        $platform =  Constant::ORDER_PLAT_FORM;
        $lottery = new lottery();
        $lotteryNames = $lottery->getLotterynamelist(["in", "lottery_category_id", $lottery_category_ids]);
        $lotteryNames[0] = "请选择";
        $data = new ActiveDataProvider([
            'query' => $detail,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['lottery_id'],
            ],
        ]);
        $orderSource = Constants::ORDER_SOURCE;
        return $this->render('index', ['data' => $data, "lotteryNames" => $lotteryNames, "orderStatus" => $orderStatus, "get" => $get, "orderFrom" => $orderForm, "orderSource" => $orderSource,"platform"=>$platform]);
    }

    /**
     * 
     * 数字彩投注单详情
     */
    public function actionReaddetail12() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        $data = LotteryOrder::find()->select("lottery_order.*,lottery_record.lottery_time,lottery_record.lottery_time,lottery_record.lottery_numbers,store.store_name,store.phone_num,pr.pay_pre_money")
                ->join("JOIN", "lottery_record", "lottery_record.periods=lottery_order.periods and lottery_record.lottery_code=lottery_order.lottery_id")
                ->join("JOIN", "store", "store.user_id=lottery_order.store_id and store.store_code=lottery_order.store_no")
                ->leftJoin("pay_record as pr", "pr.order_code=lottery_order.lottery_order_code and pr.pay_type=16")
                ->where(["lottery_order.lottery_order_id" => $get["lottery_order_id"]])
                ->asArray()
                ->one();
        $successStatus = [1, 2, 3, 4, 5, 11];
        $errorStatus = [6, 9, 10, 12];
        $where = "";
        if (in_array($data["status"], $successStatus)) {
            $where = "create_time asc";
        } else {
            $where = "create_time desc";
        }
        $payRecord = (new Query())->select("*")
                ->from("pay_record")
                ->where(["order_code" => $data["lottery_order_code"]])
                ->orderBy($where)
                ->one();
        $data["pic"] = (new Query())->select("order_img1,order_img2,order_img3,order_img4")
                ->from("out_order_pic")
                ->where(["order_id" => $data["lottery_order_id"]])
                ->one();
        if ($data["opt_id"] == 0) {
            $data["opt_id"] = $data["store_id"];
        }
        $optInfo = User::find()->select("user_name,user_tel")->where(["user_id" => $data["opt_id"]])->asArray()->one();
        if ($optInfo == null) {
            $data["optInfo"] = "";
        } else {
            $data["optInfo"] = $optInfo["user_name"] . " ( {$optInfo['user_tel']} ) ";
        }
        //跳单记录
        $record = (new Query())->select("order_taking.*,s.store_name")
                ->from("order_taking")
                ->leftJoin("store as s", "s.store_code = order_taking.store_code and s.status = 1")
                ->where(["order_taking.order_code" => $data["lottery_order_code"]]);
        $takingRecord = new ActiveDataProvider([
            'query' => $record,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $this->render("readdetail", ["data" => $data, "payRecord" => $payRecord, "takingRecord" => $takingRecord]);
    }

    /**
     * 竞彩投注单详情
     * @return type
     */
    public function actionReaddetail3() {
        $this->layout = false;
        $statusNames = Constant::ORDER_STATUS;
        $get = \Yii::$app->request->get();
        $lotOrder = LotteryOrder::find()
                ->select("lottery_order.*,store.store_name,store.phone_num,pr.pay_pre_money")
                ->join("JOIN", "store", "store.user_id=lottery_order.store_id and store.store_code=lottery_order.store_no")
                ->leftJoin("pay_record as pr", "pr.order_code=lottery_order.lottery_order_code and pr.pay_type=16")
                ->where(["lottery_order.lottery_order_id" => $get['lottery_order_id']])
                ->asArray()
                ->one();
        //任14胜负彩
        if ($lotOrder["lottery_id"] == "4001" || $lotOrder["lottery_id"] == "4002") {
            $data = $this->readDetail5($lotOrder);
            //跳单记录
            $record = (new Query())->select("order_taking.*,s.store_name")
                    ->from("order_taking")
                    ->leftJoin("store as s", "s.store_code = order_taking.store_code and s.status = 1")
                    ->where(["order_taking.order_code" => $lotOrder["lottery_order_code"]]);
            $takingRecord = new ActiveDataProvider([
                'query' => $record,
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]);
            $data["takingRecord"] = $takingRecord;
            return $this->render("readdetailsf", $data);
        }
        //冠亚军竞彩
        if ($lotOrder["lottery_id"] == "301201" || $lotOrder["lottery_id"] == "301301") {
            $data = $this->getCupDetial($lotOrder);
            //跳单记录
            $record = (new Query())->select("order_taking.*,s.store_name")
                    ->from("order_taking")
                    ->leftJoin("store as s", "s.store_code = order_taking.store_code and s.status = 1")
                    ->where(["order_taking.order_code" => $lotOrder["lottery_order_code"]]);
            $takingRecord = new ActiveDataProvider([
                'query' => $record,
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]);
            $data["takingRecord"] = $takingRecord;
            return $this->render("readworldcup", $data);
        }
        //其他
        $odds = [];
        if (!empty($lotOrder["odds"])) {
            $odds = json_decode($lotOrder["odds"], 256);
        }
        $data["lotOrder"] = $lotOrder;
        $betVal = trim($lotOrder["bet_val"], "^");

        if ($lotOrder["lottery_id"] != '3011') {
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            //$pattern = '/^([0-9]+\*[0-9]+)\((([0-9]|,)+)\)$/';
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $betNums = explode("|", $betVal);
        $mids = [];
        $bets = [];
        foreach ($betNums as $key => $ball) {
            $bets[$key] = [];
            preg_match($pattern, $ball, $result);
            $bets[$key]["play"] = [];
            $bets[$key]["bet"] = [];
            $n = 0;
            $bets[$key]["lottery"] = [];
            if ($lotOrder["lottery_id"] != '3011') {
                $bets[$key]["odds"][$lotOrder["lottery_id"]] = isset($odds[$lotOrder["lottery_id"]]) && isset($odds[$lotOrder["lottery_id"]][$result[1]]) ? $odds[$lotOrder["lottery_id"]][$result[1]] : null;
                $bets[$key]["lottery"][$n] = [];
                $bets[$key]["lottery"][$n]["play"] = $lotOrder["lottery_id"];
                $bets[$key]["mid"] = $result[1];
                $mids[] = $result[1];
                $bets[$key]["lottery"][$n]["bet"] = explode(",", $result[2]);
                $n++;
            } else {
                $mids[] = $result[1];
                $bets[$key]["mid"] = $result[1];
                $result[2] = trim($result[2], "*");
                $strs = explode("*", $result[2]);
                foreach ($strs as $str) {
                    $bets[$key]["lottery"][$n] = [];
                    preg_match('/^([0-9]+)\((([0-9]|,)+)\)$/', $str, $r);
                    $bets[$key]["odds"][$r[1]] = isset($odds[$r[1]][$result[1]]) ? $odds[$r[1]][$result[1]] : null;
                    $bets[$key]["lottery"][$n]["bet"] = explode(",", $r[2]);
                    $bets[$key]["lottery"][$n]["play"] = $r[1];
                    $n++;
                }
            }
        }
        $schedules = Schedule::find()
                ->select("schedule.*,sr.schedule_result_3006,sr.schedule_result_3007,sr.schedule_result_3008,sr.schedule_result_3009,sr.schedule_result_3010,sr.status")
                ->join("left join", "schedule_result sr", "schedule.schedule_mid=sr.schedule_mid")
                ->where(["in", "schedule.schedule_mid", $mids])
                ->indexBy("schedule_mid")
                ->asArray()
                ->all();
        $plays = Constant::LOTTERY;
        foreach ($bets as &$val) {
            foreach ($val["lottery"] as $key => $v) {
                $val["lottery"][$key]["play_name"] = $plays[$v["play"]];
            }
            $val["schedule_code"] = $schedules[$val["mid"]]["schedule_code"];
            $val["home_team_name"] = $schedules[$val["mid"]]["home_short_name"];
            $val["visit_team_name"] = $schedules[$val["mid"]]["visit_short_name"];
            $val["schedule_result_3006"] = $schedules[$val["mid"]]["schedule_result_3006"];
            $val["schedule_result_3007"] = $schedules[$val["mid"]]["schedule_result_3007"];
            $val["schedule_result_3008"] = $schedules[$val["mid"]]["schedule_result_3008"];
            $val["schedule_result_3009"] = $schedules[$val["mid"]]["schedule_result_3009"];
            $val["schedule_result_3010"] = $schedules[$val["mid"]]["schedule_result_3010"];
            $val["rq_nums"] = $schedules[$val["mid"]]["rq_nums"];
            $val["status"] = $schedules[$val["mid"]]["status"];
        }

        $pageSize = count($bets);
        $data['schedules'] = new ArrayDataProvider([
            'allModels' => $bets,
            'pagination' => [
                'pageSize' => $pageSize,
            ]
        ]);

//        $data["lotOrder"]["contents"] = $bets;
        $bettingDetails = (new Query())
                ->select("*")
                ->from("betting_detail")
                ->where(["lottery_order_id" => $get['lottery_order_id']])
                ->limit(10)
                ->all();

        $detailCount = (new Query())
                ->select("*")
                ->from("betting_detail")
                ->where(["lottery_order_id" => $get['lottery_order_id']])
                ->count();
        $betPlay = [];
        if ($lotOrder["lottery_id"] != '3011') {
            $patternDetail = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $patternDetail = '/^([0-9]+\*[0-9]+)\((([0-9]|,)+)\)$/';
        }
        foreach ($bettingDetails as &$val) {
            $betNums = explode("|", $val["bet_val"]);
            $val["bet"] = [];
            foreach ($betNums as $ball) {
                preg_match($patternDetail, $ball, $result);
                if ($lotOrder["lottery_id"] != '3011') {
                    $mid = $result[1];
                    if (!isset($betPlay[$lotOrder["lottery_id"]])) {
                        $str = "COMPETING_BET_" . $lotOrder["lottery_id"];
                        eval('$betPlay[$lotOrder["lottery_id"]] = \app\modules\lottery\helpers\Constant::' . $str . ';');
                    }
                    $val["bet"][] = $schedules[$mid]["schedule_code"] . ($lotOrder["lottery_id"] == '3006' ? '[' . $schedules[$mid]["rq_nums"] . ']' : '') . '(' . $betPlay[$lotOrder["lottery_id"]][$result[2]] . '|' . $odds[$lotOrder["lottery_id"]][$mid][$result[2]] . ')';
                } else {
                    $strs = explode("*", $result[1]);
                    $mid = $strs[0];
                    if (!isset($betPlay[$strs[1]])) {
                        $str = "COMPETING_BET_" . $strs[1];
                        eval('$betPlay[$strs[1]] = \app\modules\lottery\helpers\Constant::' . $str . ';');
                    }

                    $val["bet"][] = $schedules[$mid]["schedule_code"] . ($strs[1] == '3006' ? '[' . $schedules[$mid]["rq_nums"] . ']' : '') . '(' . $betPlay[$strs[1]][$result[2]] . '|' . ($odds[$strs[1]][$mid][$result[2]]) . ')';
                }
            }
            $val["bet"] = implode("x", $val["bet"]);
            $val["statusName"] = $statusNames[$val["status"]];
            if ($val["status"] != "4") {
                if ($val["status"] == "5") {
                    $val["win_amount"] = "0";
                } else {
                    $val["win_amount"] = "--";
                }
            }
        }
        $pageSize = count($bettingDetails);
        $successStatus = [1, 2, 3, 4, 5, 11];
        $errorStatus = [6, 9, 10, 12];
        $order = "";
        if (in_array($lotOrder["status"], $successStatus)) {
            $order = "create_time asc";
        } else {
            $order = "create_time desc";
        }
        $payRecord = (new Query())->select("*")
                ->from("pay_record")
                ->where(["order_code" => $lotOrder["lottery_order_code"]])
                ->orderBy($order)
                ->one();
        $data['betting_detail'] = new ArrayDataProvider([
            'allModels' => $bettingDetails,
            'pagination' => [
                'pageSize' => $pageSize,
            ]
        ]);

        $data["lotOrder"]["pic"] = (new Query())->select("order_img1,order_img2,order_img3,order_img4")
                ->from("out_order_pic")
                ->where(["order_id" => $lotOrder["lottery_order_id"]])
                ->one();
        //显示投注单信息单关还是混合
        if ($data["lotOrder"]["play_code"] == 1) {
            if ($data["lotOrder"]["lottery_id"] == 3011) {
                $data["lotOrder"]["lottery_name"] = "混合单关";
            } else {
                $data["lotOrder"]["lottery_name"] .= "(单)";
            }
        }
        if ($lotOrder["opt_id"] == 0) {
            $lotOrder["opt_id"] = $lotOrder["store_id"];
        }
        $optInfo = User::find()->select("user_name,user_tel")->where(["user_id" => $lotOrder["opt_id"]])->asArray()->one();
        if ($optInfo == null) {
            $data["optInfo"] = "";
        } else {
            $data["optInfo"] = $optInfo["user_name"] . " ( {$optInfo['user_tel']} ) ";
        }
        //跳单记录
        $record = (new Query())->select("order_taking.*,s.store_name")
                ->from("order_taking")
                ->leftJoin("store as s", "s.store_code = order_taking.store_code and s.status = 1")
                ->where(["order_taking.order_code" => $lotOrder["lottery_order_code"]]);
        $takingRecord = new ActiveDataProvider([
            'query' => $record,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $this->render("readdetail3", ["data" => $data, "payRecord" => $payRecord, "detailCount" => $detailCount, "takingRecord" => $takingRecord]);
    }

    /**
     * 竞彩篮球
     * @param type $lotOrder
     * @return type
     */
    public function actionReaddetail4() {
        $this->layout = false;
        return $this->render("readdetail4");
    }

    public function actionLanDetail() {
//        $this->layout = false; 
        $post = \Yii::$app->request->post();
        $lotteryOrderCode = trim($post["lotteryOrderCode"]);
        $res = $this->getOrder($lotteryOrderCode);
        $successStatus = [1, 2, 3, 4, 5, 11];
        $errorStatus = [6, 9, 10, 12];
        $order = "";
        if (in_array($res["status"], $successStatus)) {
            $order = "create_time asc";
        } else {
            $order = "create_time desc";
        }
        $payRecord = (new Query())->select("*")
                ->from("pay_record")
                ->where(["order_code" => $lotteryOrderCode])
                ->orderBy($order)
                ->one();
        $detail = $this->getLanOrderDetail($lotteryOrderCode);
        //跳单记录
        $record = (new Query())->select("order_taking.*,s.store_name")
                ->from("order_taking")
                ->leftJoin("store as s", "s.store_code = order_taking.store_code and s.status = 1")
                ->where(["order_taking.order_code" => $lotteryOrderCode])
                ->all();
        $data = ["res" => $res, "payInfo" => $payRecord, "content" => $detail, "takingRecord" => $record];
        return $this->jsonResult("600", "获取成功", $data);
    }

    /**
     * 获取篮球投注单
     * @auther  GL Ljn
     * @param type $lotteryOrderCode  订单编号
     * @param type $cust_no  会员编号
     * @param type $orderId  订单ID
     * @return type
     */
    public function getOrder($lotteryOrderCode, $cust_no = '', $orderId = '') {
        $where['lottery_order.lottery_order_code'] = $lotteryOrderCode;
        if (!empty($cust_no)) {
            $where['lottery_order.cust_no'] = $cust_no;
        }
        if (!empty($orderId)) {
            $where['lottery_order.lottery_order_id'] = $orderId;
        }
        $status = Constant::ORDER_STATUS;
        $sfcArr = Constant::SFC_BETWEEN_ARR;
        $lotOrder = LotteryOrder::find()
                ->select("lottery_order.build_name,lottery_order.out_time,lottery_order.award_time,lottery_order.major_type,lottery_order.opt_id,lottery_order.store_id,lottery_order.cust_no,bet_val,odds,lottery_id,lottery_name,bet_money,lottery_order_code,lottery_order.create_time,lottery_order_id,lottery_order.status,win_amount,play_code,play_name,bet_double,count,periods,s.store_name,s.store_code,s.phone_num,pr.pay_pre_money,lottery_order.refuse_reason,")
                ->leftJoin('store as s', 's.user_id = lottery_order.store_id and s.store_code=lottery_order.store_no')
                ->leftJoin("pay_record as pr", "pr.order_code=lottery_order.lottery_order_code and pr.pay_type=16")
                ->where($where)
                ->asArray()
                ->one();
        if ($lotOrder == null) {
            return ["code" => 2, "msg" => "查询结果不存在"];
        }
        if ($lotOrder["opt_id"] == 0) {
            $lotOrder["opt_id"] = $lotOrder["store_id"];
        }
        $optInfo = (new Query())->select("user_name,user_tel")->from("user")->where(["user_id" => $lotOrder["opt_id"]])->one();
        if ($optInfo == null) {
            $lotOrder["optInfo"] = "";
        } else {
            $lotOrder["optInfo"] = $optInfo["user_name"] . " ( {$optInfo['user_tel']} ) ";
        }
        $lotOrder["pic"] = (new Query())->select("order_img1,order_img2,order_img3,order_img4")
                ->from("out_order_pic")
                ->where(["order_id" => $lotOrder["lottery_order_id"]])
                ->one();
        //篮球方案名称
        if ($lotOrder["play_code"] == 1) {
            if ($lotOrder["lottery_id"] == 3005) {
                $lotOrder["lottery_name"] = "混合单关";
            } else {
                $lotOrder["lottery_name"] .= "(单)";
            }
        }
        $odds = [];
        if (!empty($lotOrder["odds"])) {
            $odds = json_decode($lotOrder["odds"], 256);
        }
        $lotOrder["status_name"] = $status[$lotOrder["status"]];
//        $lotOrder['award_time'] = date('Y-m-d H:i:s', strtotime('+2 hours', $lotOrder['periods'] / 1000));
        $data = $lotOrder;
        $betVal = trim($lotOrder["bet_val"], "^");
        if ($lotOrder["lottery_id"] != '3005') {
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $betNums = explode("|", $betVal);
        $mids = [];
        $bets = [];
        foreach ($betNums as $key => $ball) {
            $bets[$key] = [];
            preg_match($pattern, $ball, $result);
            $n = 0;
            if ($lotOrder["lottery_id"] != '3005') {
                $bets[$key]["mid"] = $result[1];
                $mids[] = $result[1];
                $arr = explode(",", $result[2]);
                foreach ($arr as $v) {
                    $bets[$key]["lottery"][$n] = [];
                    if ($lotOrder['lottery_id'] == 3003) {
                        $bets[$key]["lottery"][$n]["bet_name"] = $sfcArr[$v];
                    }
                    $bets[$key]["lottery"][$n]["bet"] = $v;
                    $bets[$key]["lottery"][$n]["play"] = $lotOrder["lottery_id"];
                    $bets[$key]["lottery"][$n]["odds"] = isset($odds[$lotOrder["lottery_id"]][$result[1]][$v]) ? $odds[$lotOrder["lottery_id"]][$result[1]][$v] : "赔率"; //赔率
                    if ($lotOrder['lottery_id'] == 3002) {
                        $bets[$key]['rf_nums'] = isset($odds[$lotOrder["lottery_id"]][$result[1]]['rf_nums']) ? $odds[$lotOrder["lottery_id"]][$result[1]]['rf_nums'] : "";
                    } elseif ($lotOrder['lottery_id'] == 3004) {
                        $bets[$key]['fen_cutoff'] = isset($odds[$lotOrder["lottery_id"]][$result[1]]['fen_cutoff']) ? $odds[$lotOrder["lottery_id"]][$result[1]]['fen_cutoff'] : "";
                    }
                    $n++;
                }
            } else {
                $mids[] = $result[1];
                $bets[$key]["mid"] = $result[1];
                $result[2] = trim($result[2], "*");
                $strs = explode("*", $result[2]);
                foreach ($strs as $str) {
                    preg_match('/^([0-9]+)\((([0-9]|,)+)\)$/', $str, $r);
                    $arr = explode(",", $r[2]);
                    foreach ($arr as $v) {
                        $bets[$key]["lottery"][$n] = [];
                        if ($r[1] == 3003) {
                            $bets[$key]["lottery"][$n]["bet_name"] = $sfcArr[$v];
                        }
                        $bets[$key]["lottery"][$n]["bet"] = $v;
                        $bets[$key]["lottery"][$n]["play"] = $r[1];
                        $bets[$key]["lottery"][$n]["odds"] = isset($odds[$r[1]][$result[1]][$v]) ? $odds[$r[1]][$result[1]][$v] : "赔率"; //赔率
                        if ($r[1] == 3002) {
                            $bets[$key]['rf_nums'] = isset($odds[$r[1]][$result[1]]['rf_nums']) ? $odds[$r[1]][$result[1]]['rf_nums'] : "";
                        } elseif ($r[1] == 3004) {
                            $bets[$key]['fen_cutoff'] = isset($odds[$r[1]][$result[1]]['fen_cutoff']) ? $odds[$r[1]][$result[1]]['fen_cutoff'] : "";
                        }
                        $n++;
                    }
                }
            }
        }
        $field = ['lan_schedule.schedule_code', 'lan_schedule.schedule_mid', 'lan_schedule.visit_short_name', 'lan_schedule.home_short_name', 'sr.result_3001', 'sr.result_3003', 'sr.result_status', 'sr.schedule_zf', 'sr.result_qcbf', 'sr.schedule_fc'];
        $schedules = LanSchedule::find()->select($field)
                ->join("left join", "lan_schedule_result sr", "lan_schedule.schedule_mid=sr.schedule_mid")
                ->where(["in", "lan_schedule.schedule_mid", $mids])
                ->indexBy("schedule_mid")
                ->asArray()
                ->all();
        $plays = Constant::LOTTERY;
        foreach ($bets as &$val) {
            $schedule = $schedules[$val["mid"]];
            foreach ($val["lottery"] as $key => $v) {
                $val["lottery"][$key]["play_name"] = $plays[$v["play"]];
                if ($v['play'] == 3001) {
                    if ($schedule["result_status"] == 2 && !empty($schedule['result_qcbf'])) {
                        $bfArr = explode(':', $schedule['result_qcbf']);
                        if ((int) $bfArr[1] > (int) $bfArr[0]) {
                            $val['result_3001'] = '3';
                        } else {
                            $val['result_3001'] = '0';
                        }
                    }
                } elseif ($v['play'] == 3002) {
                    if (array_key_exists('rf_nums', $val)) {
                        if (!empty($schedule['result_qcbf']) && $schedule['result_status'] == 2) {
                            $bfArr = explode(':', $schedule['result_qcbf']);
                            if ((int) $bfArr[1] + (float) $val['rf_nums'] > (int) $bfArr[0]) {
                                $val['result_3002'] = '3';
                            } else {
                                $val['result_3002'] = '0';
                            }
                        }
                    }
                } elseif ($v['play'] == 3003) {
                    $val["result_sfc"] = ($schedule["result_status"] != 2) ? "" : $sfcArr[$schedule["result_3003"]];
                    $val["result_3003"] = ($schedule["result_status"] != 2) ? "" : $schedule["result_3003"];
                } elseif ($v['play'] == 3004) {
                    if (array_key_exists('fen_cutoff', $val) && $schedule['result_status'] == 2) {
                        if ($val['fen_cutoff'] > $schedule['schedule_zf']) {
                            $val['result_3004'] = '2';
                        } else {
                            $val['result_3004'] = '1';
                        }
                    }
                    $val['schedule_zf'] = ($schedule["result_status"] != 2) ? "" : $schedule["schedule_zf"];
                }
            }
            $val["schedule_code"] = $schedule["schedule_code"];
            $val["home_team_name"] = $schedule["home_short_name"];
            $val["visit_team_name"] = $schedule["visit_short_name"];

            $val['schedule_fc'] = ($schedule["result_status"] != 2) ? "" : $schedule["schedule_fc"];
            $val['result_qcbf'] = ($schedule["result_status"] != 2) ? "" : $schedule["result_qcbf"];
        }
        $data["contents"] = $bets;
        return $data;
    }

    public function readDetail5($lotOrder) {
        if (empty($lotOrder)) {
            return ['code' => 109, 'data' => '该订单有误，请重新查看'];
        }
        $data["lotOrder"] = $lotOrder;
        $fourteen = FootballFourteen::find()->select(['periods', 'beginsale_time', 'endsale_time', 'schedule_mids', 'schedule_results'])->where(['periods' => $lotOrder['periods']])->asArray()->one();
        $bets = [];
        $scheduleData = (new Query())->select(['sorting_code', 'league_name', 'schedule_mid', 'start_time', 'home_short_name', 'visit_short_name'])
                ->from("optional_schedule")
                ->where(['periods' => $lotOrder['periods']])
                ->all();
        if (!empty($scheduleData)) {
            foreach ($scheduleData as $val) {
                $betval = explode(',', trim($lotOrder['bet_val'], '^'));
                $res = explode(',', trim($fourteen['schedule_results']));
                if ($fourteen['schedule_results'] == "") {
                    $scheResult = '';
                } else {
                    $scheResult = $res[$val['sorting_code'] - 1];
                }
                $bets[] = ['sid' => $val['sorting_code'], 'home_team' => $val['home_short_name'], 'visit_team' => $val['visit_short_name'], 'bet_val' => $betval[$val['sorting_code'] - 1], 'result' => $scheResult];
            }
//            foreach ($scheduleData as $val) {
//                if (!empty($lotOrder['bet_val']) && !empty($fourteen['schedule_results'])) {
//                    $betval = explode(',', trim($lotOrder['bet_val'], '^'));
//                    $res = explode(',', trim($fourteen['schedule_results']));
//                    $bets[] = ['sid' => $val['sorting_code'], 'home_team' => $val['home_short_name'], 'visit_team' => $val['visit_short_name'], 'bet_val' => $betval[$val['sorting_code'] - 1], 'result' => $res[$val['sorting_code'] - 1]];
//                }
//            }
        }

        $pageSize = count($bets);
        $data['schedules'] = new ArrayDataProvider([
            'allModels' => $bets,
            'pagination' => [
                'pageSize' => $pageSize,
            ]
        ]);
        $successStatus = [1, 2, 3, 4, 5, 11];
        $errorStatus = [6, 9, 10];
        $order = "";
        if (in_array($data["lotOrder"]["status"], $successStatus)) {
            $order = "create_time asc";
        } else {
            $order = "create_time desc";
        }
        $payRecord = (new Query())->select("*")
                ->from("pay_record")
                ->where(["order_code" => $lotOrder["lottery_order_code"]])
                ->orderBy($order)
                ->one();
        $data["lotOrder"]["pic"] = (new Query())->select("order_img1,order_img2,order_img3,order_img4")
                ->from("out_order_pic")
                ->where(["order_id" => $lotOrder["lottery_order_id"]])
                ->one();

        if ($lotOrder["opt_id"] == 0) {
            $lotOrder["opt_id"] = $lotOrder["store_id"];
        }
        $optInfo = User::find()->select("user_name,user_tel")->where(["user_id" => $lotOrder["opt_id"]])->asArray()->one();
        if ($optInfo == null) {
            $data["optInfo"] = "";
        } else {
            $data["optInfo"] = $optInfo["user_name"] . " ( {$optInfo['user_tel']} ) ";
        }
        $result = ["data" => $data, "payRecord" => $payRecord];
        return $result;
    }

    /**
     * 详情单获取
     * @return type
     */
    public function actionGetDeatailList() {
        $this->layout = false;
        $post = \Yii::$app->request->post();
        $lotOrder = LotteryOrder::find()
                ->select("lottery_id,odds,bet_val")
                ->where(["lottery_order_id" => $post['lottery_order_id']])
                ->asArray()
                ->one();
        $odds = [];
        if (!empty($lotOrder["odds"])) {
            $odds = json_decode($lotOrder["odds"], 256);
        }
        $betVal = trim($lotOrder["bet_val"], "^");

        if ($lotOrder["lottery_id"] != '3011') {
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            //$pattern = '/^([0-9]+\*[0-9]+)\((([0-9]|,)+)\)$/';
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $betNums = explode("|", $betVal);
        $mids = [];
        foreach ($betNums as $key => $ball) {
            preg_match($pattern, $ball, $result);
            $mids[] = $result[1];
        }
        $schedules = Schedule::find()
                ->select("schedule.*,sr.schedule_result_3006,sr.schedule_result_3007,sr.schedule_result_3008,sr.schedule_result_3009,sr.schedule_result_3010")
                ->join("left join", "schedule_result sr", "schedule.schedule_mid=sr.schedule_mid")
                ->where(["in", "schedule.schedule_mid", $mids])
                ->indexBy("schedule_mid")
                ->asArray()
                ->all();

        $bettingDetails = (new Query())
                ->select("*")
                ->from("betting_detail")
                ->where(["lottery_order_id" => $post['lottery_order_id']])
                ->offset($post['offset'])
                ->limit(10)
                ->all();
        $betPlay = [];
        if ($lotOrder["lottery_id"] != '3011') {
            $patternDetail = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $patternDetail = '/^([0-9]+\*[0-9]+)\((([0-9]|,)+)\)$/';
        }

        $statusNames = Constant::ORDER_STATUS;
        foreach ($bettingDetails as &$val) {
            $betNums = explode("|", $val["bet_val"]);
            $val["bet"] = [];
            $odds = json_decode($val["odds"], true);
            foreach ($betNums as $ball) {
                preg_match($patternDetail, $ball, $result);
                if ($val["lottery_id"] != '3011') {
                    $mid = $result[1];
                    if (!isset($betPlay[$val["lottery_id"]])) {
                        $str = "COMPETING_BET_" . $val["lottery_id"];
                        eval('$betPlay[$lotOrder["lottery_id"]] = \app\modules\lottery\helpers\Constant::' . $str . ';');
                    }
                    $val["bet"][] = $schedules[$mid]["schedule_code"] . ($val["lottery_id"] == '3006' ? '[' . $schedules[$mid]["rq_nums"] . ']' : '') . '(' . $betPlay[$val["lottery_id"]][$result[2]] . '|' . $odds[$val["lottery_id"]][$mid][$result[2]] . ')';
                } else {
                    $strs = explode("*", $result[1]);
                    $mid = $strs[0];
                    if (!isset($betPlay[$strs[1]])) {
                        $str = "COMPETING_BET_" . $strs[1];
                        eval('$betPlay[$strs[1]] = \app\modules\lottery\helpers\Constant::' . $str . ';');
                    }

                    $val["bet"][] = $schedules[$mid]["schedule_code"] . ($strs[1] == '3006' ? '[' . $schedules[$mid]["rq_nums"] . ']' : '') . '(' . $betPlay[$strs[1]][$result[2]] . '|' . ($odds[$strs[1]][$mid][$result[2]]) . ')';
                }
            }
            $val["bet"] = implode("x", $val["bet"]);
            $val["statusName"] = $statusNames[$val["status"]];
            if ($val["status"] != "4") {
                if ($val["status"] == "5") {
                    $val["win_amount"] = "0";
                } else {
                    $val["win_amount"] = "--";
                }
            }
        }
        return $this->jsonResult(600, "订单详情", $bettingDetails);
    }

    /**
     * 获取篮球订单处理明细
     * @param type $lotteryOrderCode  订单编号
     * @param type $page
     * @param type $size
     * @return type
     */
    public function getLanOrderDetail($lotteryOrderCode) {
        $status = Constant::ORDER_STATUS;
        $sfcArr = Constant::SFC_BETWEEN_ARR;
        $lotOrder = LotteryOrder::find()->select("lottery_order_id,odds,bet_val,lottery_id")->where(["lottery_order_code" => $lotteryOrderCode])->asArray()->one();
        if (empty($lotOrder)) {
            return ["code" => 2, "msg" => "查询结果不存在"];
        }
        $odds = [];
        if (!empty($lotOrder["odds"])) {
            $odds = json_decode($lotOrder["odds"], 256);
        }
        $total = BettingDetail::find()->where(['lottery_order_id' => $lotOrder['lottery_order_id']])->count();
        $bettingDetails = (new Query())
                ->select("betting_detail_id,lottery_order_id,lottery_order_code,lottery_id,lottery_name,play_name,play_code,bet_double,win_amount,bet_val,status,win_level,bet_money")
                ->from("betting_detail")
                ->where(["lottery_order_id" => $lotOrder["lottery_order_id"]])
                ->limit(10)
                ->all();



        $betVal = trim($lotOrder["bet_val"], "^");
        if ($lotOrder["lottery_id"] != '3005') {
            $patternDetail = '/^([0-9]+)\((([0-9]|,)+)\)$/';
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $patternDetail = '/^([0-9]+\*[0-9]+)\((([0-9]|,)+)\)$/';
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $betNums = explode("|", $betVal);
        foreach ($betNums as $ball) {
            preg_match($pattern, $ball, $result);
            if ($lotOrder["lottery_id"] != '3005') {
                $mids[] = $result[1];
            } else {
                $strs = explode("*", $result[1]);
                $mids[] = $strs[0];
            }
        }
        $field = ['lan_schedule.schedule_code', 'lan_schedule.schedule_mid', 'lan_schedule.home_short_name', 'lan_schedule.visit_short_name', 'sr.result_3003', 'sr.schedule_zf', 'sr.result_qcbf', 'sr.result_status'];
        $schedules = LanSchedule::find()->select($field)
                ->join("left join", "lan_schedule_result sr", "lan_schedule.schedule_mid = sr.schedule_mid")
                ->where(["in", "lan_schedule.schedule_mid", $mids])
                ->indexBy("schedule_mid")
                ->asArray()
                ->all();
        $betPlay = [];
        foreach ($bettingDetails as &$val) {
            $betNums = explode("|", $val["bet_val"]);
            $val["bet"] = [];
            foreach ($betNums as $key => $ball) {
                preg_match($patternDetail, $ball, $res);
                if ($lotOrder["lottery_id"] != '3005') {
                    $mid = $res[1];
                    $theOdds = isset($odds[$lotOrder["lottery_id"]][$mid][$res[2]]) ? $odds[$lotOrder["lottery_id"]][$mid][$res[2]] : "赔率";
                    $val["content"][$key] = [];
                    $val["content"][$key]["schedule_code"] = $schedules[$mid]["schedule_code"];
                    $val["content"][$key]["lottery_code"] = $lotOrder["lottery_id"];
                    $val['content'][$key]['bet_code'] = $res[2];
                    $val["content"][$key]["bet_odds"] = $theOdds; //赔率
//                    $val['content'][$key]['visit_team_name'] = $schedules[$mid]['visit_team_name'];
                    $val['content'][$key]['result_status'] = $schedules[$mid]['result_status'];
                    $val['content'][$key]['visit_short_name'] = $schedules[$mid]['visit_short_name'];
                    $val['content'][$key]['home_short_name'] = $schedules[$mid]['home_short_name'];
                    if ($lotOrder['lottery_id'] == 3001) {
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '胜' : '负';
                    } elseif ($lotOrder["lottery_id"] == 3002) {
                        $val['content'][$key]['rf_nums'] = $odds[$lotOrder['lottery_id']][$mid]['rf_nums'];
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '让胜' : '让负';
                    } elseif ($lotOrder["lottery_id"] == 3003) {
                        $val["content"][$key]["bet_play"] = $sfcArr[$res[2]];
                    } elseif ($lotOrder['lottery_id'] == 3004) {
                        $val['content'][$key]['fen_cutoff'] = $odds[$lotOrder['lottery_id']][$mid]['fen_cutoff'];
                    }
                    if ($schedules[$mid]['result_status'] == 2) {
                        $bfArr = explode(':', $schedules[$mid]['result_qcbf']);
                        if ($lotOrder['lottery_id'] == 3001) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($lotOrder['lottery_id'] == 3002) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] + floatval($odds[$lotOrder['lottery_id']][$mid]['rf_nums']) > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($lotOrder['lottery_id'] == 3003) {
                            $val['content'][$key]['result'] = $schedules[$mid]['result_3003'];
                        } elseif ($lotOrder['lottery_id'] == 3004) {
                            $val['content'][$key]['result'] = (int) $schedules[$mid]['schedule_zf'] > floatval($odds[$lotOrder['lottery_id']][$mid]['fen_cutoff']) ? 1 : 2;
                        }
                    }
                } else {
                    $strs = explode("*", $res[1]);
                    $mid = $strs[0];
                    $theOdds = isset($odds[$strs[1]][$mid][$res[2]]) ? $odds[$strs[1]][$mid][$res[2]] : "赔率";
                    $val["content"][$key]["schedule_code"] = $schedules[$mid]["schedule_code"];
                    $val["content"][$key]["lottery_code"] = $strs[1];
                    $val['content'][$key]['bet_code'] = $res[2];
                    $val["content"][$key]["bet_odds"] = $theOdds; //赔率
                    $val['content'][$key]['visit_team_name'] = $schedules[$mid]['visit_short_name'];
                    $val['content'][$key]['home_team_name'] = $schedules[$mid]['home_short_name'];
                    $val['content'][$key]['result_status'] = $schedules[$mid]['result_status'];
                    if ($strs[1] == 3001) {
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '胜' : '负';
                    } elseif ($strs[1] == 3002) {
                        $val['content'][$key]['rf_nums'] = $odds[$strs[1]][$mid]['rf_nums'];
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '让胜' : '让负';
                    } elseif ($strs[1] == 3003) {
                        $val["content"][$key]["bet_play"] = $sfcArr[$res[2]];
                    } elseif ($strs[1] == 3004) {
                        $val['content'][$key]['fen_cutoff'] = $odds[$strs[1]][$mid]['fen_cutoff'];
                    }
                    if ($schedules[$mid]['result_status'] == 2) {
                        $bfArr = explode(':', $schedules[$mid]['result_qcbf']);
                        if ($strs[1] == 3001) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($strs[1] == 3002) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] + floatval($odds[$strs[1]][$mid]['rf_nums']) > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($strs[1] == 3003) {
                            $val['content'][$key]['result'] = $schedules[$mid]['result_3003'];
                        } elseif ($strs[1] == 3004) {
                            $val['content'][$key]['result'] = (int) $schedules[$mid]['schedule_zf'] > floatval($odds[$strs[1]][$mid]['fen_cutoff']) ? 1 : 2;
                        }
                    }
                }
            }
            $val['status_name'] = $status[$val['status']];
            $val["bet"] = implode("x", $val["bet"]);
        }
        return ['total' => $total, 'data' => $bettingDetails];
    }

    /**
     * 获取更多明细
     * @param type $lotteryOrderCode  订单编号
     * @param type $offset
     * @return type
     */
    public function actionGetMoreDetail() {
        $post = \Yii::$app->request->post();
        $lotteryOrderCode = trim($post["lotteryOrderCode"]);
        $offset = $post["offset"];
        $status = Constant::ORDER_STATUS;
        $sfcArr = Constant::SFC_BETWEEN_ARR;
        $lotOrder = LotteryOrder::find()->select("lottery_order_id,odds,bet_val,lottery_id")->where(["lottery_order_code" => $lotteryOrderCode])->asArray()->one();
        if (empty($lotOrder)) {
            return ["code" => 2, "msg" => "查询结果不存在"];
        }
        $odds = [];
        if (!empty($lotOrder["odds"])) {
            $odds = json_decode($lotOrder["odds"], 256);
        }
        $total = BettingDetail::find()->where(['lottery_order_id' => $lotOrder['lottery_order_id']])->count();
        $bettingDetails = (new Query())
                ->select("betting_detail_id,lottery_order_id,lottery_order_code,lottery_id,lottery_name,play_name,play_code,bet_double,win_amount,bet_val,status,win_level,bet_money")
                ->from("betting_detail")
                ->where(["lottery_order_id" => $lotOrder["lottery_order_id"]])
                ->offset($offset)
                ->limit(10)
                ->all();
        $betVal = trim($lotOrder["bet_val"], "^");
        if ($lotOrder["lottery_id"] != '3005') {
            $patternDetail = '/^([0-9]+)\((([0-9]|,)+)\)$/';
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $patternDetail = '/^([0-9]+\*[0-9]+)\((([0-9]|,)+)\)$/';
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $betNums = explode("|", $betVal);
        foreach ($betNums as $ball) {
            preg_match($pattern, $ball, $result);
            if ($lotOrder["lottery_id"] != '3005') {
                $mids[] = $result[1];
            } else {
                $strs = explode("*", $result[1]);
                $mids[] = $strs[0];
            }
        }
        $field = ['lan_schedule.schedule_code', 'lan_schedule.schedule_mid', 'lan_schedule.home_short_name', 'lan_schedule.visit_short_name', 'sr.result_3003', 'sr.schedule_zf', 'sr.result_qcbf', 'sr.result_status'];
        $schedules = LanSchedule::find()->select($field)
                ->join("left join", "lan_schedule_result sr", "lan_schedule.schedule_mid = sr.schedule_mid")
                ->where(["in", "lan_schedule.schedule_mid", $mids])
                ->indexBy("schedule_mid")
                ->asArray()
                ->all();
        $betPlay = [];
        foreach ($bettingDetails as &$val) {
            $betNums = explode("|", $val["bet_val"]);
            $val["bet"] = [];
            foreach ($betNums as $key => $ball) {
                preg_match($patternDetail, $ball, $res);
                if ($lotOrder["lottery_id"] != '3005') {
                    $mid = $res[1];
                    $theOdds = isset($odds[$lotOrder["lottery_id"]][$mid][$res[2]]) ? $odds[$lotOrder["lottery_id"]][$mid][$res[2]] : "赔率";
                    $val["content"][$key] = [];
                    $val["content"][$key]["schedule_code"] = $schedules[$mid]["schedule_code"];
                    $val["content"][$key]["lottery_code"] = $lotOrder["lottery_id"];
                    $val['content'][$key]['bet_code'] = $res[2];
                    $val["content"][$key]["bet_odds"] = $theOdds; //赔率
//                    $val['content'][$key]['visit_team_name'] = $schedules[$mid]['visit_team_name'];
//                    $val['content'][$key]['home_team_name'] = $schedules[$mid]['home_team_name'];
                    $val['content'][$key]['visit_short_name'] = $schedules[$mid]['visit_short_name'];
                    $val['content'][$key]['home_short_name'] = $schedules[$mid]['home_short_name'];
                    if ($lotOrder['lottery_id'] == 3001) {
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '胜' : '负';
                    } elseif ($lotOrder["lottery_id"] == 3002) {
                        $val['content'][$key]['rf_nums'] = $odds[$lotOrder['lottery_id']][$mid]['rf_nums'];
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '让胜' : '让负';
                    } elseif ($lotOrder["lottery_id"] == 3003) {
                        $val["content"][$key]["bet_play"] = $sfcArr[$res[2]];
                    } elseif ($lotOrder['lottery_id'] == 3004) {
                        $val['content'][$key]['fen_cutoff'] = $odds[$lotOrder['lottery_id']][$mid]['fen_cutoff'];
                    }
                    if ($schedules[$mid]['result_status'] == 2) {
                        $bfArr = explode(':', $schedules[$mid]['result_qcbf']);
                        if ($lotOrder['lottery_id'] == 3001) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($lotOrder['lottery_id'] == 3002) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] + floatval($odds[$lotOrder['lottery_id']][$mid]['rf_nums']) > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($lotOrder['lottery_id'] == 3003) {
                            $val['content'][$key]['result'] = $schedules[$mid]['result_3003'];
                        } elseif ($lotOrder['lottery_id'] == 3004) {
                            $val['content'][$key]['result'] = (int) $schedules[$mid]['schedule_zf'] > floatval($odds[$lotOrder['lottery_id']][$mid]['fen_cutoff']) ? 1 : 2;
                        }
                    }
                } else {
                    $strs = explode("*", $res[1]);
                    $mid = $strs[0];
                    $theOdds = isset($odds[$strs[1]][$mid][$res[2]]) ? $odds[$strs[1]][$mid][$res[2]] : "赔率";
                    $val["content"][$key]["schedule_code"] = $schedules[$mid]["schedule_code"];
                    $val["content"][$key]["lottery_code"] = $strs[1];
                    $val['content'][$key]['bet_code'] = $res[2];
                    $val["content"][$key]["bet_odds"] = $theOdds; //赔率
                    $val['content'][$key]['visit_team_name'] = $schedules[$mid]['visit_short_name'];
                    $val['content'][$key]['home_team_name'] = $schedules[$mid]['home_short_name'];
                    if ($strs[1] == 3001) {
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '胜' : '负';
                    } elseif ($strs[1] == 3002) {
                        $val['content'][$key]['rf_nums'] = $odds[$strs[1]][$mid]['rf_nums'];
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '让胜' : '让负';
                    } elseif ($strs[1] == 3003) {
                        $val["content"][$key]["bet_play"] = $sfcArr[$res[2]];
                    } elseif ($strs[1] == 3004) {
                        $val['content'][$key]['fen_cutoff'] = $odds[$strs[1]][$mid]['fen_cutoff'];
                    }
                    if ($schedules[$mid]['result_status'] == 2) {
                        $bfArr = explode(':', $schedules[$mid]['result_qcbf']);
                        if ($strs[1] == 3001) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($strs[1] == 3002) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] + floatval($odds[$strs[1]][$mid]['rf_nums']) > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($strs[1] == 3003) {
                            $val['content'][$key]['result'] = $schedules[$mid]['result_3003'];
                        } elseif ($strs[1] == 3004) {
                            $val['content'][$key]['result'] = (int) $schedules[$mid]['schedule_zf'] > floatval($odds[$strs[1]][$mid]['fen_cutoff']) ? 1 : 2;
                        }
                    }
                }
            }
            $val['status_name'] = $status[$val['status']];
            $val["bet"] = implode("x", $val["bet"]);
        }
        $result = ['total' => $total, 'data' => $bettingDetails];
        return $this->jsonResult(600, "订单详情", $result);
    }

    public function actionDoAward() {
        set_time_limit(0);
        $request = Yii::$app->request;
        $orderId = $request->post('order_id', '');
        if (empty($orderId)) {
            return $this->jsonResult(109, '参数缺失');
        }
        $order = LotteryOrder::find()->select(['lottery_id', 'lottery_type', 'periods', 'bet_val'])->where(['lottery_order_id' => $orderId, 'status' => 3])->asArray()->one();
        if (empty($order)) {
            return $this->jsonResult(109, '该场次无需对奖');
        }
        $winning = new Winning();
        if ($order['lottery_type'] == 1) {
            $ret = $winning->szcWinning($order['lottery_id'], $order['periods']);
        } elseif ($order['lottery_type'] == 2 || $order['lottery_type'] == 4 || $order['lottery_type'] == 5) {
            $ret = $winning->competWinning($order['bet_val'], $order['lottery_type'], $order['lottery_id']);
        } elseif ($order['lottery_type'] == 3) {
            $ret = $winning->optionalWinning($order['periods']);
        }
        return $this->jsonResult($ret['code'], $ret['msg']);
    }

    /**
     * 竞彩篮球
     * @param type $lotOrder
     * @return type
     */
    public function actionReadBdDetail() {
        $this->layout = false;
        return $this->render("bddetail");
    }

    public function actionBdDetail() {
        $post = \Yii::$app->request->post();
        $lotteryOrderCode = trim($post["lotteryOrderCode"]);
        $res = $this->getBdOrder($lotteryOrderCode);

        $successStatus = [1, 2, 3, 4, 5, 11];
        $errorStatus = [6, 9, 10];
        $order = "";
        if (in_array($res["status"], $successStatus)) {
            $order = "create_time asc";
        } else {
            $order = "create_time desc";
        }
        $payRecord = (new Query())->select("*")
                ->from("pay_record")
                ->where(["order_code" => $lotteryOrderCode])
                ->orderBy($order)
                ->one();
        //跳单记录
        $record = (new Query())->select("order_taking.*,s.store_name")
                ->from("order_taking")
                ->leftJoin("store as s", "s.store_code = order_taking.store_code and s.status = 1")
                ->where(["order_taking.order_code" => $lotteryOrderCode])
                ->all();
        $detail = $this->getBdDetail($lotteryOrderCode);
        $data = ["res" => $res, "payInfo" => $payRecord, "content" => $detail, "takingRecord" => $record];
        return $this->jsonResult("600", "获取成功", $data);
    }

    /**
     * 获取北单投注单
     * @auther  GL zyl
     * @param type $lotteryOrderCode  订单编号
     * @param type $cust_no  会员编号
     * @param type $orderId  订单ID
     * @return type
     */
    public function getBdOrder($lotteryOrderCode) {
        $where['lottery_order.lottery_order_code'] = $lotteryOrderCode;
        $status = Constant::ORDER_STATUS;
        $majorArr = Constants::MAJOR_ARR;
        $lotOrder = LotteryOrder::find()
                ->select("lottery_order.store_id,lottery_order.out_time,lottery_order.opt_id,lottery_order.lottery_order_id,bet_val,odds,lottery_order.lottery_id,lottery_order.lottery_name,refuse_reason,bet_money,lottery_order_code,lottery_order.create_time,lottery_order_id,lottery_order.status,win_amount,play_code,play_name,bet_double,count,periods,s.store_name,s.store_code,s.telephone as phone_num,major_type,build_code,build_name,l.lottery_pic,pr.pay_pre_money,lottery_order.refuse_reason,")
                ->leftJoin('store as s', 's.store_code = lottery_order.store_no and s.status = 1')
                ->leftJoin('lottery l', 'l.lottery_code = lottery_order.lottery_id')
                ->leftJoin("pay_record as pr", "pr.order_code=lottery_order.lottery_order_code and pr.pay_type=16")
                ->where($where)
                ->asArray()
                ->one();
        if ($lotOrder == null) {
            return ["code" => 2, "msg" => "查询结果不存在"];
        }
        //出票人信息
        if ($lotOrder["opt_id"] == 0) {
            $lotOrder["opt_id"] = $lotOrder["store_id"];
        }
        $optInfo = (new Query())->select("user_name,user_tel")->from("user")->where(["user_id" => $lotOrder["opt_id"]])->one();
        if ($optInfo == null) {
            $lotOrder["optInfo"] = "";
        } else {
            $lotOrder["optInfo"] = $optInfo["user_name"] . " ( {$optInfo['user_tel']} ) ";
        }
        $lotOrder["pic"] = (new Query())->select("order_img1,order_img2,order_img3,order_img4")
                ->from("out_order_pic")
                ->where(["order_id" => $lotOrder["lottery_order_id"]])
                ->one();

        $odds = [];
        if (!empty($lotOrder["odds"])) {
            $odds = json_decode($lotOrder["odds"], 256);
        }

        $lotOrder["status_name"] = $status[$lotOrder["status"]];
        $lotOrder['award_time'] = date('Y-m-d H:i:s', $lotOrder['periods']);
        $lotOrder['major_name'] = $majorArr[$lotOrder['major_type']];
        if (empty($lotOrder['build_code'])) {
            $lotOrder['build_code'] = '';
            $lotOrder['build_name'] = '';
        }
        $data = $lotOrder;
        $betVal = trim($lotOrder["bet_val"], "^");
        $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        $result = [];
        $betNums = explode("|", $betVal);
        $mids = [];
        $bets = [];
        foreach ($betNums as $key => $ball) {
            $bets[$key] = [];
            preg_match($pattern, $ball, $result);
            $n = 0;
            $bets[$key]["mid"] = $result[1];
            $mids[] = $result[1];
            $arr = explode(",", $result[2]);
            foreach ($arr as $v) {
                $bets[$key]["lottery"][$n] = [];
                $bets[$key]["lottery"][$n]["bet"] = $v;
                $bets[$key]["lottery"][$n]["play"] = $lotOrder["lottery_id"];
//                $bets[$key]["lottery"][$n]["odds"] = isset($odds[$lotOrder["lottery_id"]][$result[1]][$v]) ? $odds[$lotOrder["lottery_id"]][$result[1]][$v] : "赔率"; //赔率
                $n++;
            }
        }
        $field = ['bd_schedule.periods', 'bd_schedule.open_mid', 'bd_schedule.schedule_mid', 'bd_schedule.home_name', 'bd_schedule.visit_name', 'sr.result_5001', 'sr.result_5002', 'sr.result_5003', 'sr.result_5004',
            'sr.result_5005', 'sr.result_5006', 'sr.status', 'sr.odds_5001', 'sr.odds_5002', 'sr.odds_5003', 'sr.odds_5004', 'sr.odds_5005', 'sr.odds_5006', 'bd_schedule.spf_rq_nums', 'bd_schedule.sfgg_rf_nums'];
        $schedules = BdSchedule::find()->select($field)
                ->join("left join", "bd_schedule_result sr", "bd_schedule.open_mid = sr.open_mid")
                ->where(["in", "bd_schedule.open_mid", $mids])
                ->indexBy("open_mid")
                ->asArray()
                ->all();
        $plays = Constants::LOTTERY;
        $bf = Constants::COMPETING_3007_RESULT;
        $palyCode = [];
        foreach ($bets as &$val) {
            $schedule = $schedules[$val["mid"]];
            foreach ($val["lottery"] as $key => $v) {
                $val["lottery"][$key]["play_name"] = $plays[$v["play"]];
                if ($schedule['status'] == 2 && $v['bet'] == $schedule['result_' . $lotOrder['lottery_id']]) {
                    $val["lottery"][$key]["odds"] = $schedule['odds_' . $lotOrder['lottery_id']];
                } else {
                    $val["lottery"][$key]["odds"] = $odds[$lotOrder["lottery_id"]][$schedule['open_mid']][$v['bet']];
                }
            }
//            foreach ($val["lottery"] as $key => $v) {
//                $val["lottery"][$key]["play_name"] = $plays[$v["play"]];
//                array_push($palyCode, $v["play"]);
//            }
//            $schedule = $schedules[$val["mid"]];
            $val['visit_short_name'] = $schedule['visit_name'];
            $val['home_short_name'] = $schedule['home_name'];
            $val["schedule_result_5001"] = ($schedule["status"] != 2) ? "" : $schedule["result_5001"];
            $val["schedule_result_bf"] = ($schedule["status"] != 2) ? "" : $schedule["result_5005"];
            $val['status'] = $schedule['status'];
            if (!empty($val["schedule_result_bf"])) {
                if (!isset($bf[$val["schedule_result_bf"]])) {
                    $val["schedule_result_bf"] = str_replace(" ", "", $val["schedule_result_bf"]);
                    if (isset($bf[$val["schedule_result_bf"]])) {
                        $val["schedule_result_5005"] = $bf[$val["schedule_result_bf"]];
                    } else {
                        $bfBalls = explode(":", $val["schedule_result_bf"]);
                        if ($bfBalls[0] > $bfBalls[1]) {
                            $val["schedule_result_5005"] = "90";
                        } else if ($bfBalls[0] == $bfBalls[1]) {
                            $val["schedule_result_5005"] = "99";
                        } else {
                            $val["schedule_result_5005"] = "09";
                        }
                    }
                } else {
                    $val["schedule_result_5005"] = $bf[$val["schedule_result_bf"]];
                }
            } else {
                $val["schedule_result_5005"] = "";
            }
            $val["schedule_result_5002"] = ($schedule["status"] != 2) ? "" : $schedule["result_5002"];
            $val["schedule_result_5003"] = ($schedule["status"] != 2) ? "" : $schedule["result_5003"];
            $val["schedule_result_5004"] = ($schedule["status"] != 2) ? "" : $schedule["result_5004"];
            $val["schedule_result_5006"] = ($schedule["status"] != 2) ? "" : $schedule["result_5006"];
            $val["rq_nums"] = $schedule["spf_rq_nums"];
            $val['rf_nums'] = $schedule['sfgg_rf_nums'];
        }
        $data["contents"] = $bets;
        return $data;
    }

    /**
     * 获取北单处理明细
     * @param type $lotteryOrderCode
     * @return type
     */
    public function getBdDetail($lotteryOrderCode) {
        $status = Constant::ORDER_STATUS;
        $lottery = Constants::LOTTERY;
        $schedulePlay = Constants::BDSCHEDULE_PLAY;
        $lotOrder = LotteryOrder::find()
                ->select(["lottery_order_id", "odds", "bet_val", "lottery_id", "lottery_name", "build_code", "build_name", "play_name", "play_code", "bet_money", "lottery_name"])
                ->where(["lottery_order_code" => $lotteryOrderCode])
                ->asArray()
                ->one();
        if ($lotOrder == null) {
            return \Yii::jsonError(109, '查询结果不存在');
        }
        $odds = [];
        if (!empty($lotOrder["odds"])) {
            $odds = json_decode($lotOrder["odds"], 256);
        }
        $total = BettingDetail::find()->where(['lottery_order_id' => $lotOrder['lottery_order_id']])->count();
        $bettingDetails = (new Query())
                ->select("betting_detail_id,lottery_order_id,lottery_order_code,lottery_id,lottery_name,play_name,play_code,bet_double,win_amount,bet_val,status,win_level,bet_money")
                ->from("betting_detail")
                ->where(["lottery_order_id" => $lotOrder["lottery_order_id"]])
                ->limit(10)
                ->all();
        $betVal = trim($lotOrder["bet_val"], "^");
        $patternDetail = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';

        $betNums = explode("|", $betVal);
        foreach ($betNums as $ball) {
            preg_match($pattern, $ball, $result);
            $mids[] = $result[1];
        }
        if ($lotOrder['lottery_id'] != 5006) {
            $playType = 1;
        } else {
            $playType = 2;
        }
        $field = ["bd_schedule.open_mid", "bd_schedule.bd_sort", "bd_schedule.visit_name", "bd_schedule.home_name", "bd_schedule.spf_rq_nums", "bd_schedule.sfgg_rf_nums", "sr.result_5005 result_qcbf",
            "sr.result_bcbf", "sr.result_" . $lotOrder['lottery_id'], "sr.odds_" . $lotOrder['lottery_id'], 'sr.status'];
        $schedules = BdSchedule::find()->select($field)
                ->leftJoin('bd_schedule_result sr', "sr.open_mid = bd_schedule.open_mid and sr.play_type = {$playType}")
                ->where(["in", "bd_schedule.open_mid", $mids])
                ->andWhere(['bd_schedule.play_type' => $playType])
                ->indexBy("open_mid")
                ->asArray()
                ->all();
        $betPlay = [];
        foreach ($bettingDetails as &$val) {
            $betNums = explode("|", $val["bet_val"]);
            $val["bet"] = [];
            foreach ($betNums as $key => $ball) {
                preg_match($patternDetail, $ball, $result);
                $mid = $result[1];
                $theOdds = isset($odds[$lotOrder["lottery_id"]][$mid][$result[2]]) ? $odds[$lotOrder["lottery_id"]][$mid][$result[2]] : "赔率";
                $betPlay[$lotOrder["lottery_id"]][$result[2]] = $schedulePlay[$lotOrder['lottery_id']][$result[2]];
                $val["bet"][] = $schedules[$mid]["bd_sort"] . ($lotOrder["lottery_id"] == '5001' ? '[' . $schedules[$mid]["spf_rq_nums"] . ']' : '') . '(' . $betPlay[$lotOrder["lottery_id"]][$result[2]] . '|' . $theOdds . ')';
                $val["content"][$key] = [];
                $val["content"][$key]["bd_sort"] = $schedules[$mid]["bd_sort"];
                $val["content"][$key]["lottery_code"] = $lotOrder["lottery_id"];
                $val["content"][$key]["rq_nums"] = $schedules[$mid]["spf_rq_nums"];
//                $val["content"][$key]["rf_nums"] = $schedules[$mid]["sfgg_rq_nums"];
                $val["content"][$key]["bet_play"] = $betPlay[$lotOrder["lottery_id"]][$result[2]];
                $val['content'][$key]['bet_code'] = $result[2];
                $val["content"][$key]["bet_odds"] = $theOdds; //赔率
                $val['content'][$key]['visit_team_name'] = $schedules[$mid]['visit_name'];
                $val['content'][$key]['home_team_name'] = $schedules[$mid]['home_name'];
                $srOpen = 'result_' . $lotOrder["lottery_id"];
                $val['content'][$key]['result'] = $schedules[$mid][$srOpen];
                $val["content"][$key]["bet_play"] = $schedulePlay[$lotOrder['lottery_id']][$result[2]];
            }
            $val['status_name'] = $status[$val['status']];
            $val["bet"] = implode("x", $val["bet"]);
        }
        if (!empty($lotOrder['build_code'])) {
            $playStr = $lotOrder['build_name'];
        } else {
            $playStr = $lotOrder['play_name'];
        }
        $betAbb = $lotOrder['lottery_name'];
        return ["result" => ['total' => $total, 'data' => $bettingDetails, 'count_sche' => count($mids), 'play_str' => $playStr, 'order_money' => $lotOrder['bet_money'], 'bet_abb' => $betAbb]];
    }

    /**
     * 获取北单更多处理明细
     * @param type $lotteryOrderCode
     * @return type
     */
    public function actionGetBdMoreDetail() {
        $post = \Yii::$app->request->post();
        $lotteryOrderCode = trim($post["lotteryOrderCode"]);
        $offset = $post["offset"];
        $status = Constant::ORDER_STATUS;
        $lottery = Constants::LOTTERY;
        $schedulePlay = Constants::BDSCHEDULE_PLAY;
        $lotOrder = LotteryOrder::find()
                ->select(["lottery_order_id", "odds", "bet_val", "lottery_id", "lottery_name", "build_code", "build_name", "play_name", "play_code", "bet_money", "lottery_name"])
                ->where(["lottery_order_code" => $lotteryOrderCode])
                ->asArray()
                ->one();
        if ($lotOrder == null) {
            return \Yii::jsonError(109, '查询结果不存在');
        }
        $odds = [];
        if (!empty($lotOrder["odds"])) {
            $odds = json_decode($lotOrder["odds"], 256);
        }
        $total = BettingDetail::find()->where(['lottery_order_id' => $lotOrder['lottery_order_id']])->count();
        $bettingDetails = (new Query())
                ->select("betting_detail_id,lottery_order_id,lottery_order_code,lottery_id,lottery_name,play_name,play_code,bet_double,win_amount,bet_val,status,win_level,bet_money")
                ->from("betting_detail")
                ->where(["lottery_order_id" => $lotOrder["lottery_order_id"]])
                ->offset($offset)
                ->limit(10)
                ->all();
        $betVal = trim($lotOrder["bet_val"], "^");
        $patternDetail = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';

        $betNums = explode("|", $betVal);
        foreach ($betNums as $ball) {
            preg_match($pattern, $ball, $result);
            $mids[] = $result[1];
        }
        if ($lotOrder['lottery_id'] != 5006) {
            $playType = 1;
        } else {
            $playType = 2;
        }
        $field = ["bd_schedule.open_mid", "bd_schedule.bd_sort", "bd_schedule.visit_name", "bd_schedule.home_name", "bd_schedule.spf_rq_nums", "bd_schedule.sfgg_rf_nums", "sr.result_5005 result_qcbf",
            "sr.result_bcbf", "sr.result_" . $lotOrder['lottery_id'], "sr.odds_" . $lotOrder['lottery_id'], 'sr.status'];
        $schedules = BdSchedule::find()->select($field)
                ->leftJoin('bd_schedule_result sr', "sr.open_mid = bd_schedule.open_mid and sr.play_type = {$playType}")
                ->where(["in", "bd_schedule.open_mid", $mids])
                ->andWhere(['bd_schedule.play_type' => $playType])
                ->indexBy("open_mid")
                ->asArray()
                ->all();
        $betPlay = [];
        foreach ($bettingDetails as &$val) {
            $betNums = explode("|", $val["bet_val"]);
            $val["bet"] = [];
            foreach ($betNums as $key => $ball) {
                preg_match($patternDetail, $ball, $result);
                $mid = $result[1];
                $theOdds = isset($odds[$lotOrder["lottery_id"]][$mid][$result[2]]) ? $odds[$lotOrder["lottery_id"]][$mid][$result[2]] : "赔率";
                $betPlay[$lotOrder["lottery_id"]][$result[2]] = $schedulePlay[$lotOrder['lottery_id']][$result[2]];
                $val["bet"][] = $schedules[$mid]["bd_sort"] . ($lotOrder["lottery_id"] == '5001' ? '[' . $schedules[$mid]["spf_rq_nums"] . ']' : '') . '(' . $betPlay[$lotOrder["lottery_id"]][$result[2]] . '|' . $theOdds . ')';
                $val["content"][$key] = [];
                $val["content"][$key]["bd_sort"] = $schedules[$mid]["bd_sort"];
                $val["content"][$key]["lottery_code"] = $lotOrder["lottery_id"];
                $val["content"][$key]["rq_nums"] = $schedules[$mid]["spf_rq_nums"];
//                $val["content"][$key]["rf_nums"] = $schedules[$mid]["sfgg_rq_nums"];
                $val["content"][$key]["bet_play"] = $betPlay[$lotOrder["lottery_id"]][$result[2]];
                $val['content'][$key]['bet_code'] = $result[2];
                $val["content"][$key]["bet_odds"] = $theOdds; //赔率
                $val['content'][$key]['visit_team_name'] = $schedules[$mid]['visit_name'];
                $val['content'][$key]['home_team_name'] = $schedules[$mid]['home_name'];
                $srOpen = 'result_' . $lotOrder["lottery_id"];
                $val['content'][$key]['result'] = $schedules[$mid][$srOpen];
                $val["content"][$key]["bet_play"] = $schedulePlay[$lotOrder['lottery_id']][$result[2]];
            }
            $val['status_name'] = $status[$val['status']];
            $val["bet"] = implode("x", $val["bet"]);
        }
        if (!empty($lotOrder['build_code'])) {
            $playStr = $lotOrder['build_name'];
        } else {
            $playStr = $lotOrder['play_name'];
        }
        $betAbb = $lotOrder['lottery_name'];
        $result = ['total' => $total, 'data' => $bettingDetails];
        return $this->jsonResult(600, "订单详情", $result);
    }

    /**
     * 获取冠亚军竞猜订单详情
     * @param type $order
     * @param type $custNo
     * @param type $orderId
     * @return type
     */
    public static function getCupDetial($order) {
        $where = [];
        $status = Constant::ORDER_STATUS;
        $where['lottery_order.lottery_order_code'] = $order["lottery_order_code"];
        $field = ['lottery_order.cust_no', 'lottery_order.award_time', 'lottery_order.out_time', 'lottery_order.bet_val', 'lottery_order.lottery_id', 'lottery_order.lottery_name', 'bet_money', 'lottery_order_code', 'lottery_order.create_time', 'lottery_order_id', 'lottery_order.status', 'win_amount', 'play_code', 'play_name', 'bet_double', 'count', 'lottery_order.periods', 's.store_name', 's.store_code', 's.telephone phone_num', 'odds', 'l.lottery_pic', 'pr.pay_pre_money', 'lottery_order.refuse_reason',];
        $data = LotteryOrder::find()->select($field)
                ->leftJoin('store as s', 's.store_code = lottery_order.store_no and s.status = 1')
                ->leftJoin('lottery as l', 'l.lottery_code = lottery_order.lottery_id')
                ->leftJoin("pay_record as pr", "pr.order_code=lottery_order.lottery_order_code and pr.pay_type=16")
                ->where($where)
                ->asArray()
                ->one();
        if (empty($data)) {
            return ['code' => 109, 'data' => '该订单有误，请重新查看'];
        }
        $betArr = explode(',', trim($data['bet_val'], '^'));
        if ($data['lottery_id'] == '301201') {
            $field2 = ['open_mid', 'team_name', 'status'];
            $teamData = WorldcupChp::find()->select($field2)->where(['in', 'open_mid', $betArr])->asArray()->all();
        } elseif ($data['lottery_id'] == '301301') {
            $str = "CONCAT(home_name, '-' , visit_name) team_name";
            $field2 = ['open_mid', $str, 'status'];
            $teamData = WorldcupFnl::find()->select($field2)->where(['in', 'open_mid', $betArr])->asArray()->all();
        }
        $oddsArr = json_decode($data['odds'], true);
        if (!empty($teamData)) {
            foreach ($teamData as $val) {
                if ($data['status'] == 0 || $data['status'] == 1) {
                    $scheResult = '';
                } else {
                    $scheResult = $val['status'];
                }
                $data['betval_arr'][] = ['open_mid' => $val['open_mid'], 'team_name' => $val['team_name'], 'result' => $val['status'], 'odds' => $oddsArr[$val['open_mid']]];
            }
        } else {
            $data['betval_arr'] = [];
        }
        $data['status_name'] = $status[$data['status']];
        $data['betval_arr'] = new ArrayDataProvider([
            'allModels' => $data["betval_arr"],
        ]);
        //其他信息
        $payRecord = (new Query())->select("*")
                ->from("pay_record")
                ->where(["order_code" => $order["lottery_order_code"]])
                ->one();
        $data["out_pic"] = (new Query())->select("order_img1,order_img2,order_img3,order_img4")
                ->from("out_order_pic")
                ->where(["order_id" => $order["lottery_order_id"]])
                ->one();

        if ($order["opt_id"] == 0) {
            $order["opt_id"] = $order["store_id"];
        }
        $optInfo = User::find()->select("user_name,user_tel")->where(["user_id" => $order["opt_id"]])->asArray()->one();
        if ($optInfo == null) {
            $data["optInfo"] = "";
        } else {
            $data["optInfo"] = $optInfo["user_name"] . " ( {$optInfo['user_tel']} ) ";
        }

        $result = ["data" => $data, "payRecord" => $payRecord];
        return $result;
    }

    public function actionGetTakingDetail() {
        $request = \Yii::$app->request;
        $orderId = $request->get('orderId');
        $orderCode = LotteryOrder::find()->select(['lottery_order_code', 'lottery_id'])->where(['lottery_order_id' => $orderId])->asArray()->one();
        $postData = ['lottery_order_code' => $orderCode['lottery_order_code'], 'lottery_code' => $orderCode['lottery_id']];
        $surl = \Yii::$app->params['userDomain'] . '/api/store/store/get-competing-detail';
        $ret = \Yii::sendCurlPost($surl, $postData);
        $takingData = [];
        if ($ret['code'] != 600) {
            echo $ret['msg'];
            exit();
        } 
        $takingData = $ret['result'];
        return $this->render('taking-detail', ['takingDetail' => $takingData]);
    }
    /**
     * 获取订单来源
     */
    public function actionGetOrderFrom()
    {
        $post = \Yii::$app->request->post();
        $plat = $post["plat"];
        $fromAry = [];
        switch ($plat) {
            case 1:
                $fromAry = [
                    0 => [
                        "id" => "0",
                        "name" => "直营"
                    ],
                ];
                break;
            case  2:
                $agents = Agents::find()->select("agents_id,agents_name")->where(["pass_status" => 3])->asArray()->all();
                foreach ($agents as $k => $v) {
                    $fromAry[$k]["id"] = $v['agents_id'];
                    $fromAry[$k]["name"] = $v['agents_name'];
                }
                break;
            case 3:
                $bussiness = (new Query())->select("u.user_id,b.name")
                    ->from("user as u")
                    ->leftJoin("bussiness as b","b.cust_no = u.cust_no")
                    ->where(["b.status" => 1])
                    ->all();
                foreach ($bussiness as $k => $v) {
                    $fromAry[$k]["id"] = $v['user_id'];
                    $fromAry[$k]["name"] = $v['name'];
                }
                break;
            case 4:
                $user = User::find()->select("user_id,user_name")->where(["<>", "spread_type", 0])->asArray()->all();
                foreach ($user as $k => $v) {
                    $fromAry[$k]["id"] = $v['user_id'];
                    $fromAry[$k]["name"] = $v['user_name'];
                }
                break;
        }
        return $this->jsonResult(600, '获取成功', $fromAry);
    }
    public function getOrderFromAry($from)
    {
        $fromAry = [];
        $fromAry[''] = "请选择";
        switch ($from) {
            case 1:
                $fromAry = [
                    "0" => "直营",
                ];
                break;
            case  2:
                $agents = Agents::find()->select("agents_id,agents_name")->where(["pass_status" => 3])->asArray()->all();
                foreach ($agents as $k => $v) {
                    $fromAry[$v["agents_id"]] = $v['agents_name'];
                }
                break;
            case 3:
                $bussiness = (new Query())->select("u.user_id,b.name")
                    ->from("user as u")
                    ->leftJoin("bussiness as b","b.cust_no = u.cust_no")
                    ->where(["b.status" => 1])
                    ->all();
                foreach ($bussiness as $k => $v) {
                    $fromAry[$v["user_id"]] = $v['name'];
                }
                break;
            case 4:
                $user = User::find()->select("user_id,user_name")->where(["<>", "spread_type", 0])->asArray()->all();
                foreach ($user as $k => $v) {
                    $fromAry[$v["user_id"]] = $v['user_name'];
                }
                break;
        }
        return $fromAry;
    }

}
