<?php

namespace app\modules\report\controllers;

use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use app\modules\lottery\models\Store;
use app\modules\lottery\helpers\Constant;
use app\modules\common\helpers\Constants;
use app\modules\agents\models\Agents;
use app\modules\common\models\Bussiness;
use app\modules\agents\models\User;
use yii\db\Expression;

class GroupMoneyController extends Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        return $this->render('index');
    }
    /**
     * 获取店铺报表月统计数据
     */
    public function actionGetMonthReport() {
        $request = \Yii::$app->request;
        $post = \Yii::$app->request->post();
        $years = $post['years'];
        $months = $post['months'];
        $type = $request->post("type",1);
        $statusArr = [3, 4, 5];
        $field =["s.province","s.city", "s.store_name", "s.store_code","sum(l.bet_money) allMoney",
            new Expression("sum(case when l.order_platform = 3 and l.cust_no = 'gl00021282' then l.bet_money else 0 end) as nmMoney"),
            new Expression("sum(case when l.order_platform = 4 then l.bet_money else 0 end) as wqMoney"),
            new Expression("sum(case when l.order_platform = 2 and l.agent_id in (19)then l.bet_money else 0 end) as hxMoney"),
            new Expression("sum(case when l.order_platform = 1 and l.agent_id =0 then l.bet_money else 0 end) as zyMoney")];
        if($type==1){
            array_push($field,"DATE_FORMAT(l.out_time,'%Y-%m') months");
        }elseif($type==2){
            array_push($field,"DATE_FORMAT(l.create_time,'%Y-%m') months");
        }
        $query = (new Query())->select($field)
                ->from("lottery_order as l")
                ->leftJoin("store as s", "l.store_no=s.store_code  and l.store_id=s.user_id")
                ->where(["<>","l.store_no",10004])
                ->andWhere(["in", "l.status", $statusArr]);
        if (isset($post["store_name"]) && !empty($post["store_name"])) {
            $query = $query->andWhere(['or', ["s.phone_num" => $post["store_name"]], ["s.store_code" => $post["store_name"]], ["like", "s.store_name", $post["store_name"]]]);
        }
        if($type==1){
            $query = $query->andWhere(["DATE_FORMAT(l.out_time,'%Y-%m')" => $years . "-" . $months]);
        }else{
            $query = $query->andWhere(["DATE_FORMAT(l.create_time,'%Y-%m')" => $years . "-" . $months]);
        }
        $query = $query->groupBy(["s.store_code", "months"])
                ->orderBy("s.store_id asc");
        $result = $query->all();
        return $this->jsonResult(100, "获取成功", $result);
    }

    /**
     * 获取彩种
     * @auther  GL ljn
     * @return type
     */
    public function actionGetSaleLottery() {
        $data = (new Query())->select("lottery_code,lottery_name")
                ->from("lottery")
                ->all();
        $football = Constant::MADE_FOOTBALL_LOTTERY;
        $basketball = Constant::MADE_BASKETBALL_LOTTERY;
        $bd = Constant::MADE_BD_LOTTERY;
        $lottery = [];
        foreach ($data as $key => &$val) {
            if (in_array($val['lottery_code'], $football)) {
                unset($data[$key]);
            }
            if (in_array($val['lottery_code'], $basketball)) {
                unset($data[$key]);
            }
            if (in_array($val['lottery_code'], $bd)) {
                unset($data[$key]);
            }
        }
        $lottery = array_values($data);
        return $this->jsonResult(600, "获取成功", $lottery);
    }

    /**
     * 获取彩种统计数据
     */
    public function actionGetLotteryReport() {
        $request = \Yii::$app->request;
        $post = \Yii::$app->request->post();
        $type = $request->post("type",1);
        $statusArr = [3, 4, 5];
        $lotteryCode = $post['lottery_code'];
        $star = $post['star'];
        $end = $post['end'];
        $lotteryZu = [3006, 3007, 3008, 3009, 3010, 3011];
        $lotteryLan = [3001, 3002, 3003, 3004, 3005];
        $lotteryBd = [5001, 5002, 5003, 5004, 5005, 5006];
        $field =["l.lottery_name", "l.lottery_id", "count(distinct l.cust_no) count", "sum(l.bet_money) salemoney", "count(l.lottery_order_id) ordernum", "sum(l.win_amount) winmoney","sum(l.award_amount) award_amount","sum(p.pay_money) paymoney",new Expression("sum(case when l.status = 3 then 1 else 0 end) as stayopen"),new Expression("sum(case when l.status = 4 and l.deal_status = 1 then 1 else 0 end) as  stayaward")];

        $query = (new Query())->select($field)
                ->from("lottery_order as l")
                ->leftJoin("pay_record as p","l.lottery_order_code=p.order_code and p.pay_type=16")
                ->andWhere(["in", "l.status", $statusArr]);
        if (!empty($star) && !empty($end)) {
            if($type==1){
                $query = $query->andWhere(["between", "l.out_time", $star . " 00:00:00", $end . " 23:59:59"]);
            }else{
                $query = $query->andWhere(["between", "l.create_time", $star . " 00:00:00", $end . " 23:59:59"]);
            }
        }
        if ($lotteryCode != 0) {
            if ($lotteryCode == 3000) {
                $query = $query->andWhere(["in", "l.lottery_id", $lotteryZu]);
            } elseif ($lotteryCode == 3100) {
                $query = $query->andWhere(["in", "l.lottery_id", $lotteryLan]);
            } elseif ($lotteryCode == 5000) {
                $query = $query->andWhere(["in", "l.lottery_id", $lotteryBd]);
            } else {
                $query = $query->andWhere(["l.lottery_id" => $lotteryCode]);
            }
        }
        $query = $query->groupBy("l.lottery_id");
        $result = $query->all();
        //数组种类重组
        if ($lotteryCode == 0) {
            foreach ($result as $k => &$v) {
                if (in_array($v["lottery_id"], $lotteryZu)) {
                    $result["3000"] ["lottery_name"] = "竞彩足球";
                    $result["3000"] ["lottery_id"] = "3000";
                    $result["3000"] ["count"] = 0;
                    $result["3000"] ["salemoney"] = 0;
                    $result["3000"]["ordernum"] = 0;
                    $result["3000"]["winmoney"] = 0;
                    $result["3000"]["award_amount"] = 0;
                    $result["3000"]["paymoney"] = 0;
                    $result["3000"]["stayopen"] = 0;
                    $result["3000"]["stayaward"] = 0;
                }
                if (in_array($v["lottery_id"], $lotteryLan)) {
                    $result["3100"] ["lottery_name"] = "竞彩篮球";
                    $result["3100"] ["lottery_id"] = "3100";
                    $result["3100"] ["count"] = 0;
                    $result["3100"] ["salemoney"] = 0;
                    $result["3100"]["ordernum"] = 0;
                    $result["3100"]["winmoney"] = 0;
                    $result["3100"]["award_amount"] = 0;
                    $result["3100"]["paymoney"] = 0;
                    $result["3100"]["stayopen"] = 0;
                    $result["3100"]["stayaward"] = 0;
                }
                if (in_array($v["lottery_id"], $lotteryBd)) {
                    $result["5000"] ["lottery_name"] = "北京单场";
                    $result["5000"] ["lottery_id"] = "5000";
                    $result["5000"] ["count"] = 0;
                    $result["5000"] ["salemoney"] = 0;
                    $result["5000"]["ordernum"] = 0;
                    $result["5000"]["winmoney"] = 0;
                    $result["5000"]["award_amount"] = 0;
                    $result["5000"]["paymoney"] = 0;
                    $result["5000"]["stayopen"] = 0;
                    $result["5000"]["stayaward"] = 0;
                }
            }
            foreach ($result as $k => &$v) {
                if (in_array($v["lottery_id"], $lotteryZu)) {
                    $result["3000"]["count"]+=$v["count"];
                    $result["3000"]["salemoney"]+=$v["salemoney"];
                    $result["3000"]["ordernum"]+=$v["ordernum"];
                    $result["3000"]["winmoney"]+=round($v["winmoney"],2);
                    $result["3000"]["award_amount"]+=round($v["award_amount"],2);
                    $result["3000"]["paymoney"] +=$v["paymoney"];
                    $result["3000"]["stayopen"] +=$v["stayopen"];
                    $result["3000"]["stayaward"]+=$v["stayaward"];
                    unset($result[$k]);
                }
                if (in_array($v["lottery_id"], $lotteryLan)) {
                    $result["3100"]["count"]+=$v["count"];
                    $result["3100"]["salemoney"]+=$v["salemoney"];
                    $result["3100"]["ordernum"]+=$v["ordernum"];
                    $result["3100"]["winmoney"]+=round($v["winmoney"],2);
                    $result["3100"]["award_amount"]+=round($v["award_amount"],2);
                    $result["3100"]["paymoney"] +=$v["paymoney"];
                    $result["3100"]["stayopen"] +=$v["stayopen"];
                    $result["3100"]["stayaward"]+=$v["stayaward"];
                    unset($result[$k]);
                }
                if (in_array($v["lottery_id"], $lotteryBd)) {
                    $result["5000"]["count"]+=$v["count"];
                    $result["5000"]["salemoney"]+=$v["salemoney"];
                    $result["5000"]["ordernum"]+=$v["ordernum"];
                    $result["5000"]["winmoney"]+=round($v["winmoney"],2);
                    $result["5000"]["award_amount"]+=round($v["award_amount"],2);
                    $result["5000"]["paymoney"] +=$v["paymoney"];
                    $result["5000"]["stayopen"] +=$v["stayopen"];
                    $result["5000"]["stayaward"]+=$v["stayaward"];
                    unset($result[$k]);
                }
            }
        } else if ($lotteryCode == 3000) {
            $result["3000"] ["lottery_name"] = "竞彩足球";
            $result["3000"] ["lottery_id"] = "3000";
            $result["3000"] ["count"] = 0;
            $result["3000"] ["salemoney"] = 0;
            $result["3000"]["ordernum"] = 0;
            $result["3000"]["winmoney"] = 0;
            $result["3000"]["award_amount"] = 0;
            $result["3000"]["paymoney"] = 0;
            $result["3000"]["stayopen"] = 0;
            $result["3000"]["stayaward"] = 0;
            foreach ($result as $k => &$v) {
                if (in_array($v["lottery_id"], $lotteryZu)) {
                    $result["3000"]["count"]+=$v["count"];
                    $result["3000"]["salemoney"]+=$v["salemoney"];
                    $result["3000"]["ordernum"]+=$v["ordernum"];
                    $result["3000"]["winmoney"]+=round($v["winmoney"],2);
                    $result["3000"]["award_amount"]+=round($v["award_amount"],2);
                    $result["3000"]["paymoney"] +=$v["paymoney"];
                    $result["3000"]["stayopen"] +=$v["stayopen"];
                    $result["3000"]["stayaward"]+=$v["stayaward"];
                    unset($result[$k]);
                }
            }
        } else if ($lotteryCode == 3100) {
            $result["3100"] ["lottery_name"] = "竞彩篮球";
            $result["3100"] ["lottery_id"] = "3100";
            $result["3100"] ["count"] = 0;
            $result["3100"] ["salemoney"] = 0;
            $result["3100"]["ordernum"] = 0;
            $result["3100"]["winmoney"] = 0;
            $result["3100"]["award_amount"] = 0;
            $result["3100"]["paymoney"] = 0;
            $result["3100"]["stayopen"] = 0;
            $result["3100"]["stayaward"] = 0;
            foreach ($result as $k => &$v) {
                if (in_array($v["lottery_id"], $lotteryLan)) {
                    $result["3100"]["count"]+=$v["count"];
                    $result["3100"]["salemoney"]+=$v["salemoney"];
                    $result["3100"]["ordernum"]+=$v["ordernum"];
                    $result["3100"]["winmoney"]+=round($v["winmoney"],2);
                    $result["3100"]["award_amount"]+=round($v["award_amount"],2);
                    $result["3100"]["paymoney"] +=$v["paymoney"];
                    $result["3100"]["stayopen"] +=$v["stayopen"];
                    $result["3100"]["stayaward"]+=$v["stayaward"];
                    unset($result[$k]);
                }
            }
        } elseif ($lotteryCode == 5000) {
            $result["5000"] ["lottery_name"] = "北京单场";
            $result["5000"] ["lottery_id"] = "5000";
            $result["5000"] ["count"] = 0;
            $result["5000"] ["salemoney"] = 0;
            $result["5000"]["ordernum"] = 0;
            $result["5000"]["winmoney"] = 0;
            $result["5000"]["award_amount"] = 0;
            $result["5000"]["paymoney"] = 0;
            $result["5000"]["stayopen"] = 0;
            $result["5000"]["stayaward"] = 0;
            foreach ($result as $k => &$v) {
                if (in_array($v["lottery_id"], $lotteryBd)) {
                    $result["5000"]["count"]+=$v["count"];
                    $result["5000"]["salemoney"]+=$v["salemoney"];
                    $result["5000"]["ordernum"]+=$v["ordernum"];
                    $result["5000"]["winmoney"]+=round($v["winmoney"],2);
                    $result["5000"]["award_amount"]+=round($v["award_amount"],2);
                    $result["5000"]["paymoney"] +=$v["paymoney"];
                    $result["5000"]["stayopen"] +=$v["stayopen"];
                    $result["5000"]["stayaward"]+=$v["stayaward"];
                    unset($result[$k]);
                }
            }
        }
        return $this->jsonResult(100, "获取成功", $result);
    }

    public function actionSaledetail() {
        return $this->render('saledetail');
    }

    /**
     * 根据统计明细跳转到详细订单信息
     */
    public function actionGetSaleDetail() {
        $post = \Yii::$app->request->post();
        $statusArr = [3, 4, 5];
        $lotteryZu = [3006, 3007, 3008, 3009, 3010, 3011];
        $lotteryLan = [3001, 3002, 3003, 3004, 3005];
        $lotteryBd = [5001, 5002, 5003, 5004, 5005, 5006];
        $query = (new Query())->select(["lottery_order.store_id", "lottery_order.lottery_order_code", "lottery_order.create_time", "lottery_order.bet_val", "lottery_order.play_name", "lottery_order.lottery_name", "lottery_order.count", "lottery_order.bet_double", "lottery_order.bet_money", "lottery_order.win_amount", "lottery_order.cust_no", "user.user_tel","lottery_order.out_time"])
                ->from("lottery_order")
                ->leftJoin("user", "lottery_order.cust_no=user.cust_no")
                ->andWhere(["in", "lottery_order.status", $statusArr]);
        if (isset($post["sNo"]) && !empty($post["sNo"])) {
            $query = $query->andWhere(["lottery_order.store_no" => $post["sNo"]]);
        }
        if (isset($post["timer"]) && !empty($post["timer"])) {
            $query = $query->andWhere(["between", "lottery_order.out_time", $post["timer"] . " 00:00:00", $post["timer"] . " 23:59:59"]);
        }
        if (isset($post["months"]) && !empty($post["months"])) {
            $query = $query->andWhere(["between", "lottery_order.out_time", $post["months"] . "-01" . " 00:00:00", $post["months"] . "-31" . " 23:59:59"]);
        }
        if (isset($post["lotteryId"]) && !empty($post["lotteryId"])) {
            if ($post["lotteryId"] == 3000) {
                $query = $query->andWhere(["in", "lottery_id", $lotteryZu]);
            } elseif ($post["lotteryId"] == 3100) {
                $query = $query->andWhere(["in", "lottery_id", $lotteryLan]);
            } elseif ($post["lotteryId"] == 5000) {
                $query = $query->andWhere(["in", "lottery_id", $lotteryBd]);
            } else {
                $query = $query->andWhere(["lottery_id" => intval($post["lotteryId"])]);
            }
        }
        if (isset($post["totaldays"]) && $post["totaldays"] != "") {
            if (intval($post["totaldays"]) == 0) {
                $query = $query->andWhere(["between", "lottery_order.out_time", date('Y-m-d') . " 00:00:00", date('Y-m-d') . " 23:59:59"]);
            } else {
                $query = $query->andWhere(["between", "lottery_order.out_time", date("Y-m-d", strtotime("-" . $post["totaldays"] . "days")) . " 00:00:00", date("Y-m-d") . " 23:59:59"]);
            }
        }
        if (!empty($post["star"]) && !empty($post["end"])) {
            $query = $query->andWhere(["between", "lottery_order.out_time", $post["star"] . " 00:00:00", $post["end"] . " 23:59:59"]);
        }
        if (isset($post["page"])) {
            $page = $post["page"];
        } else {
            $page = 1;
        }
        $size = 10;
        $offset = $size * ($page - 1);
        $data["total"] = (int) $query->count();
        $data["page"] = $page;
        $data["pages"] = ceil($data["total"] / $size);
        $query = $query->offset($offset)
                ->limit($size)
                ->orderBy("create_time desc");
        $data["list"] = $query->all();
        return $this->jsonResult(100, "获取成功", $data);
    }

    /**
     * 订单统计通道详情
     */
    public function actionAgentDetail() {
        $platFrom= Constants::ORDER_PLAT_FROM;
        return $this->render('agent-detail',["platFrom"=>$platFrom]);
    }
    public function actionGetAgentDetail(){
        $post = \Yii::$app->request->post();
        $type = $post["type"];
        $statusArr = [3, 4, 5];
        $where = "";
        //彩种code
        if (isset($post["lottery"]) && !empty($post["lottery"])) {
            switch ($post["lottery"]){
                case 1001:
                case 1002:
                case 1003:
                case 2001:
                case 2002:
                case 2003:
                case 2004:
                case 2005:
                case 2006:
                case 2007:
                case 2008:
                case 2010:
                case 4001:
                case 4002:
                    $where = ['a.lottery_id' => $post["lottery"]];
                    break;
                case 3000:
                    $where = ['a.lottery_type' => 2];
                    break;
                case 3100:
                    $where = ['a.lottery_type' => 4];
                    break;
                case 5000:
                    $where = ['a.lottery_type' => 5];
                    break;
                case 3300:
                    $where = ['a.lottery_type' => 6];
                    break;
            }
        }

        //查询字段
        $field =["a.order_platform","count(distinct a.cust_no) count", "sum(a.bet_money) salemoney", "count(a.lottery_order_id) ordernum", "sum(a.win_amount) winmoney","sum(a.award_amount) award_amount","b.agents_name","b.agents_id","sum(p.pay_money) paymoney",new Expression("sum(case when a.status = 3 then 1 else 0 end) as stayopen"),new Expression("sum(case when a.status = 4 and a.deal_status = 1 then 1 else 0 end) as  stayaward")];
        $q1 = (new Query())->select("(2) as order_platform,agents_id, agents_name")->from("agents");
        $q2 = (new Query())->select("(3) as order_platform,user_id,name")->from("bussiness");
        $q3 = (new Query())->select("(1),(0),('直营')");
        $q4 = (new Query())->select("(1),(1),('推广')");
        $q5 = (new Query())->select("(4) as order_platform,user_id,user_name")->from("user");
        $new = $q1->union($q2)->union($q3)->union($q4)->union($q5);
        $query = (new Query())->select($field)
            ->from("lottery_order as a")
            ->leftJoin(['b'=>$new],'a.order_platform = b.order_platform and b.agents_id = case a.order_platform when 2 then a.agent_id when 3 then a.user_id when 4 then a.agent_id when 1  then (case when a.agent_id=0 then 0 else 1 end) end')
            ->leftJoin("pay_record p","a.lottery_order_code=p.order_code and p.pay_type=16")
            ->where($where)
            ->andWhere(["in", "a.status", $statusArr]);
        //门店code
        if (isset($post["sNo"]) && !empty($post["sNo"])) {
            $query = $query->andWhere(["a.store_no" => $post["sNo"]]);
        }
        if($type==1){
            //门店日期
            if (isset($post["timer"]) && !empty($post["timer"])) {
                $query = $query->andWhere(["between", "a.out_time", $post["timer"] . " 00:00:00", $post["timer"] . " 23:59:59"]);
            }
            //门店月份
            if (isset($post["months"]) && !empty($post["months"])) {
                $query = $query->andWhere(["between", "a.out_time", $post["months"] . "-01" . " 00:00:00", $post["months"] . "-31" . " 23:59:59"]);
            }
            //彩种开始结束时间
            if (!empty($post["star"]) && !empty($post["end"])) {
                $query = $query->andWhere(["between", "a.out_time", $post["star"] . " 00:00:00", $post["end"] . " 23:59:59"]);
            }
        }else{
            if (isset($post["timer"]) && !empty($post["timer"])) {
                $query = $query->andWhere(["between", "a.create_time", $post["timer"] . " 00:00:00", $post["timer"] . " 23:59:59"]);
            }
            if (isset($post["months"]) && !empty($post["months"])) {
                $query = $query->andWhere(["between", "a.create_time", $post["months"] . "-01" . " 00:00:00", $post["months"] . "-31" . " 23:59:59"]);
            }
            if (!empty($post["star"]) && !empty($post["end"])) {
                $query = $query->andWhere(["between", "a.create_time", $post["star"] . " 00:00:00", $post["end"] . " 23:59:59"]);
            }
        }
        //订单来源
        if (isset($post["platFrom"]) && !empty($post["platFrom"])){
            if(isset($post["from"]) && !empty($post["from"])){
                //咕啦
                if($post["platFrom"]==1&&$post["from"]==1){
                    $query = $query->andWhere(["a.order_platform"=>$post["platFrom"],"a.agent_id"=>0]);
                }elseif($post["platFrom"]==1&&$post["from"]==2){
                    if(isset($post["from_user"]) && !empty($post["from_user"])){
                        $query = $query->andWhere(["a.order_platform"=>$post["platFrom"],"a.agent_id"=>$post["from_user"]]);
                    }else{
                        $query = $query->andWhere(["and",["a.order_platform"=>$post["platFrom"]],["<>","a.agent_id",0]]);
                    }
                }
                //代理商
                if($post["platFrom"]==2){
                    $query = $query->andWhere(["a.order_platform"=>$post["platFrom"],"a.agent_id"=>$post["from"]]);
                }
                //渠道商
                if($post["platFrom"]==3){
                    $query = $query->andWhere(["a.order_platform"=>$post["platFrom"],"a.user_id"=>$post["from"]]);
                }
                //推广
                if($post["platFrom"]==4){
                    $query = $query->andWhere(["a.order_platform"=>$post["platFrom"],"a.agent_id"=>$post["from"]]);
                }
            }else{
                $query = $query->andWhere(["a.order_platform"=>$post["platFrom"]]);
            }
        }
        $query = $query->groupBy('a.order_platform,b.agents_name,b.agents_id');
        $result = $query->all();
        return $this->jsonResult(100, "获取成功", $result);
    }
    /**
     * 根据注册平台获取具体来源
     */
    public function actionGetPlatFrom(){
        $request = \Yii::$app->request;
        $from = $request->post('from', 1);
        switch ($from) {
            case 1:
                $infoArr = ['1' => '自营', '2' => '推广'];
                break;
            case 2:
                $infoArr = Agents::find()->select(['agents_code', 'agents_name'])->where(['pass_status' => 3])->indexBy('agents_code')->asArray()->all();
                break;
            case 3:
                $infoArr = Bussiness::find()->select([ 'name','user_id'])
                    ->where(['status' => 1])
                    ->indexBy('user_id')
                    ->asArray()
                    ->all();
                break;
            case 4:
                $infoArr = User::find()->select("user_name,user_id")
                    ->where(["spread_type"=>1])
                    ->indexBy("user_id")
                    ->asArray()
                    ->all();
                break;
            default :
                $infoArr = [];
                break;
        }
        if (empty($infoArr)) {
            return $this->jsonError(109, '暂无可配置的信息');
        }
        return $this->jsonResult(600, '获取成功', $infoArr);
    }
    /**
     * 获取推广人员
     */
    public function actionGetSpreadUser(){
        $user = User::find()->select(["user_id","user_name"])->where(["<>","spread_type",0])->indexBy('user_id')->asArray()->all();
        return $this->jsonResult(600, '获取成功', $user);
    }
}
