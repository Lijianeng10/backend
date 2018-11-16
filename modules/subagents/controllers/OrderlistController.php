<?php

namespace app\modules\subagents\controllers;

use yii\web\Controller;
use app\modules\lottery\models\LotteryOrder;
use app\modules\lottery\models\Lottery;
use Yii;
use yii\db\Query;
use app\modules\lottery\models\Schedule;
use app\modules\lottery\helpers\Constant;
use app\modules\lottery\models\LotteryAdditional;
use app\modules\member\models\User;
use app\modules\lottery\models\FootballFourteen;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\LanSchedule;
use app\modules\lottery\models\OptionalSchedule;
use app\modules\lottery\models\BettingDetail;
use app\modules\lottery\models\LotteryRecord;
use app\modules\lottery\helpers\Winning;
use app\modules\agents\models\Agents;
use yii\data\ArrayDataProvider;
use app\modules\common\helpers\Excel;


class OrderlistController extends Controller {

    /**
     * 投注列表
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $session = Yii::$app->session;
        if (isset($get["lottery_category_id"])) {
            $lottery_category_ids = explode(",", $get["lottery_category_id"]);
        } else {
            $lottery_category_ids = [4,5,6];
        }
        $query = Lottery::find()->select("lottery_code")->where(["in", "lottery_category_id", $lottery_category_ids]);
        //判断当前登录用户是代理商还是咕啦内部用户,代理商是本账号还是所属操作账号
        $where = [];
        if($session["type"]==1){
            if($session["agent_code"]=="gl00015788"){
                $agentsId = Agents::find()->select("agents_id")->where(["agents_account"=>$session["admin_name"]])->asArray()->one();
                $where["lottery_order.agent_id"]=$agentsId["agents_id"]; 
            }else{
                $agentsId = Agents::find()->select("agents_id")->where(["agents_account"=>$session["agent_code"]])->asArray()->one();
                $where["lottery_order.agent_id"]=$agentsId["agents_id"]; 
            }
        }elseif($session["type"]==0){
            $where["lottery_order.agent_id"]=0;
        }
        $detail = (new Query())->select(["lottery_order.*", 's.store_name', 's.phone_num', 'sd.consignee_name', 'u.user_tel', 'u.user_remark',"pr.pay_pre_money"])
                ->from("lottery_order")
                ->leftJoin('store as s', 's.store_code = lottery_order.store_no and s.user_id=lottery_order.store_id')
                ->leftJoin('store_detail as sd', 'sd.store_id = s.store_id ')
                ->leftJoin('user as u', 'u.cust_no = lottery_order.cust_no')
                ->leftJoin("pay_record as pr","pr.order_code=lottery_order.lottery_order_code and pr.pay_type=16")
                ->where(["in", "lottery_order.lottery_id", $query])
                ->andWhere($where);
        
        if (isset($get["user_info"])){
            $detail = $detail->andWhere(["or", ["lottery_order.cust_no" => $get['user_info']], ["u.user_name" => $get['user_info']], ["u.user_tel" => $get['user_info']]]);
        }
        if (isset($get["store_info"])) {
            $detail = $detail->andWhere(["or", ["s.cust_no" => $get['store_info']], ["s.store_name" => $get['store_info']], ["s.phone_num" => $get['store_info']], ["sd.consignee_name" => $get['store_info']]]);
        }
        if (isset($get["lottery_order_code"])) {
            $detail = $detail->andWhere(["like", "lottery_order.lottery_order_code", "%{$get['lottery_order_code']}%", false]);
        }
        if (isset($get["lottery_code"])) {
            $detail = $detail->andWhere(["lottery_order.lottery_id" => $get["lottery_code"]]);
        }
        if (isset($get["startdate"])) {
            $detail = $detail->andWhere([">", "lottery_order.create_time", $get["startdate"]. " 00:00:00"]);
        }else{
            $detail = $detail->andWhere([">", "lottery_order.create_time", date("Y-m-d",strtotime("-3 day"))." 00:00:00"]);
        }
        if (isset($get["enddate"])) {
            $detail = $detail->andWhere(["<", "lottery_order.create_time", $get["enddate"] . " 23:59:59"]);
        }else{
            $detail = $detail->andWhere(["<", "lottery_order.create_time",date("Y-m-d") . " 23:59:59"]);
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
        $detail = $detail->orderBy("lottery_order.create_time desc");
       
        $orderStatus = Constant::ORDER_STATUS;
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
        return $this->render('index', ['data' => $data, "lotteryNames" => $lotteryNames, "orderStatus" => $orderStatus, "get" => $get]);
    }
     /**
     * 
     * 数字彩投注单详情
     */
    public function actionReaddetail12() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        $data = LotteryOrder::find()->select("lottery_order.*,lottery_record.lottery_time,lottery_record.lottery_time,lottery_record.lottery_numbers,store.store_name,store.phone_num")
                ->join("JOIN", "lottery_record", "lottery_record.periods=lottery_order.periods and lottery_record.lottery_code=lottery_order.lottery_id")
                ->join("JOIN", "store", "store.user_id=lottery_order.store_id and store.store_code=lottery_order.store_no")
                ->where(["lottery_order.lottery_order_id" => $get["lottery_order_id"]])
                ->asArray()
                ->one();
        $successStatus = [1,2,3,4,5];
        $errorStatus = [6,9,10];
        $where="";
        if(in_array($data["status"],$successStatus)){
            $where="create_time asc";
        }else {
            $where="create_time desc"; 
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
        if ($optInfo == null){
            $data["optInfo"] = "";
        } else {
            $data["optInfo"] = $optInfo["user_name"] . " ( {$optInfo['user_tel']} ) ";
        }
        return $this->render("readdetail", ["data" => $data, "payRecord" => $payRecord]);
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
                ->select("lottery_order.*,store.store_name,store.phone_num")
                ->join("JOIN", "store", "store.user_id=lottery_order.store_id and store.store_code=lottery_order.store_no")
                ->where(["lottery_order.lottery_order_id" => $get['lottery_order_id']])
                ->asArray()
                ->one();
        if ($lotOrder["lottery_id"] == "4001" || $lotOrder["lottery_id"] == "4002") {
            $data = $this->readDetail5($lotOrder);
            return $this->render("readdetailsf", $data);
        }
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
        $successStatus = [1, 2, 3, 4, 5,11];
        $errorStatus = [6,9,10];
        $order="";
        if(in_array($lotOrder["status"],$successStatus)){
            $order="create_time asc";
        }else {
            $order="create_time desc"; 
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
        if($data["lotOrder"]["play_code"]==1){
            if($data["lotOrder"]["lottery_id"]==3011){
                $data["lotOrder"]["lottery_name"]="混合单关";
            }else{
                $data["lotOrder"]["lottery_name"].="(单)"; 
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
        return $this->render("readdetail3", ["data" => $data, "payRecord" => $payRecord, "detailCount" => $detailCount]);
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
        $successStatus = [1, 2, 3, 4, 5,11];
        $errorStatus = [6,9,10];
        $order="";
        if(in_array($res["status"],$successStatus)){
            $order="create_time asc";
        }else {
            $order="create_time desc"; 
        }
        $payRecord = (new Query())->select("*")
                ->from("pay_record")
                ->where(["order_code" => $lotteryOrderCode])
                ->orderBy($order)
                ->one();
        $detail = $this->getLanOrderDetail($lotteryOrderCode);
        $data = ["res" => $res, "payInfo" => $payRecord, "content" => $detail];
        return $this->jsonResult("600", "获取成功", $data);
    }

    /**
     * 获取篮球投注单
     * @auther  GL zyl
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
                ->select("lottery_order.build_name,lottery_order.out_time,lottery_order.award_time,lottery_order.major_type,lottery_order.opt_id,lottery_order.store_id,lottery_order.cust_no,bet_val,odds,lottery_id,lottery_name,bet_money,lottery_order_code,lottery_order.create_time,lottery_order_id,lottery_order.status,win_amount,play_code,play_name,bet_double,count,periods,s.store_name,s.store_code,s.phone_num")
                ->leftJoin('store as s', 's.user_id = lottery_order.store_id and s.store_code=lottery_order.store_no')
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
        if($lotOrder["play_code"]==1){
            if($lotOrder["lottery_id"]==3005){
                $lotOrder["lottery_name"]="混合单关";
            }else{
               $lotOrder["lottery_name"].="(单)"; 
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
                if($fourteen['schedule_results'] =="") {
                    $scheResult = '';
                }  else {
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
        $successStatus = [1, 2, 3, 4, 5,11];
        $errorStatus = [6,9,10];
        $order="";
        if(in_array($data["lotOrder"]["status"],$successStatus)){
            $order="create_time asc";
        }else {
            $order="create_time desc"; 
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
        return [ 'total' => $total, 'data' => $bettingDetails];
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
        $result=[ 'total' => $total, 'data' => $bettingDetails];
        return $this->jsonResult(600, "订单详情", $result);
    }
    
    public function actionDoAward() {
        $request = Yii::$app->request;
        $orderId = $request->post('order_id', '');
        if(empty($orderId)) {
            return $this->jsonResult(109, '参数缺失');
        }
        $order = LotteryOrder::find()->select(['lottery_id', 'lottery_type', 'periods', 'bet_val'])->where(['lottery_order_id' => $orderId, 'status' => 3])->asArray()->one();
        if(empty($order)) {
            return $this->jsonResult(109, '该场次无需对奖');
        }
        $winning = new Winning();
        if($order['lottery_type'] == 1) {
            $ret = $winning->szcWinning($order['lottery_id'], $order['periods']);
        }elseif ($order['lottery_type'] == 2 || $order['lottery_type'] == 4) {
            $ret = $winning->competWinning($order['bet_val'], $order['lottery_type'], $order['lottery_id']);
        }  elseif($order['lottery_type'] == 3) {
            $ret = $winning->optionalWinning($order['periods']);
        }
        return $this->jsonResult($ret['code'], $ret['msg']);
    }

    //打印报表
    public function actionPrintReport(){
        $get = \Yii::$app->request->get();
        $session = Yii::$app->session;
        if (!empty($get["lottery_category_id"])) {
            $lottery_category_ids = explode(",", $get["lottery_category_id"]);
        } else {
            $lottery_category_ids = [4,5,6];
        }
        $query = Lottery::find()->select("lottery_code")->where(["in", "lottery_category_id", $lottery_category_ids]);
        //判断当前登录用户是代理商还是咕啦内部用户,代理商是本账号还是所属操作账号
        $where = [];
        if($session["type"]==1){
            if($session["agent_code"]=="gl00015788"){
                $agentsId = Agents::find()->select("agents_id")->where(["agents_account"=>$session["admin_name"]])->asArray()->one();
                $where["lottery_order.agent_id"]=$agentsId["agents_id"];
            }else{
                $agentsId = Agents::find()->select("agents_id")->where(["agents_account"=>$session["agent_code"]])->asArray()->one();
                $where["lottery_order.agent_id"]=$agentsId["agents_id"];
            }
        }else{
            $where["lottery_order.agent_id"]=0;
        }
        $detail = (new Query())->select(["lottery_order.*", 's.store_name', 's.phone_num', 'sd.consignee_name', 'u.user_tel',"pr.pay_pre_money","u.user_remark"])
            ->from("lottery_order")
            ->leftJoin('store as s', 's.store_code = lottery_order.store_no and s.user_id=lottery_order.store_id')
            ->leftJoin('store_detail as sd', 'sd.store_id = s.store_id ')
            ->leftJoin('user as u', 'u.cust_no = lottery_order.cust_no')
            ->leftJoin("pay_record as pr","pr.order_code=lottery_order.lottery_order_code and pr.pay_type=16")
            ->where(["in", "lottery_order.lottery_id", $query])
            ->andWhere($where);
        if (!empty($get["user_info"])){
            $detail = $detail->andWhere(["or", ["lottery_order.cust_no" => $get['user_info']], ["u.user_name" => $get['user_info']], ["u.user_tel" => $get['user_info']]]);
        }
        if (!empty($get["store_info"])) {
            $detail = $detail->andWhere(["or", ["s.cust_no" => $get['store_info']], ["s.store_name" => $get['store_info']], ["s.phone_num" => $get['store_info']], ["sd.consignee_name" => $get['store_info']]]);
        }
        if (!empty($get["lottery_order_code"])) {
            $detail = $detail->andWhere(["like", "lottery_order.lottery_order_code", "%{$get['lottery_order_code']}%", false]);
        }
        if (!empty($get["lottery_code"])) {
            $detail = $detail->andWhere(["lottery_order.lottery_id" => $get["lottery_code"]]);
        }
        if (!empty($get["startdate"])) {
            $detail = $detail->andWhere([">", "lottery_order.create_time", $get["startdate"]. " 00:00:00"]);
        }else{
            $detail = $detail->andWhere([">", "lottery_order.create_time", date("Y-m-d",strtotime("-3 day"))." 00:00:00"]);
        }
        if (!empty($get["enddate"])) {
            $detail = $detail->andWhere(["<", "lottery_order.create_time", $get["enddate"] . " 23:59:59"]);
        }else{
            $detail = $detail->andWhere(["<", "lottery_order.create_time",date("Y-m-d") . " 23:59:59"]);
        }
        if (!empty($get["end_time_start"])) {
            $detail = $detail->andWhere([">", "lottery_order.end_time", $get["end_time_start"]]);
        }
        if (!empty($get["end_time_end"])) {
            $detail = $detail->andWhere(["<", "lottery_order.end_time", $get["end_time_end"] . " 23:59:59"]);
        }
        if (!empty($get["status"])) {
            $detail = $detail->andWhere(["in", "lottery_order.status", explode("|", $get["status"])]);
        } else {
            if (isset($get["choose"])&&$get["choose"]!="") {
                $detail = $detail->andWhere(["=", "lottery_order.status", $get["choose"]]);
            } else {
                $detail = $detail->andWhere(["!=", "lottery_order.status", "1"]);
            }
        }

        if (!empty($get["deal_status"])) {
            $detail = $detail->andWhere(["lottery_order.deal_status" => $get["deal_status"]]);
        }
        $detail = $detail->orderBy("lottery_order.create_time desc")->all();
        $counts = count($detail);
        if($counts==0){
            echo '<script type="text/javascript">alert("当前条件下未找到数据，请检查");window.history.go(-1);</script>';
            exit();
        }
        //判断是数字彩打印还是竞彩
        if($lottery_category_ids==[4,5,6]){
            Excel::printSzcOrder($detail,$counts);
            echo '<script type="text/javascript">alert("打印成功");window.history.go(-1);</script>';
            exit();
        }else{
            Excel::printJcOrder($detail,$counts);
            echo '<script type="text/javascript">alert("打印成功");window.history.go(-1);</script>';
            exit();
        }
    }
}

