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

class ChannelReportController extends Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * 获取店铺报表日统计数据
     */
    public function actionGetReport() {
        $request = \Yii::$app->request;
        $post = \Yii::$app->request->post();
        $type = $request->post("type",1);
        $statusArr = [3, 4, 5];
        $field =["b.name", "b.cust_no","b.user_id", "count(distinct l.cust_no) count", "sum(l.bet_money) salemoney", "count(l.lottery_order_id) ordernum", "sum(l.win_amount) winmoney","sum(l.award_amount) award_amount",new Expression("sum(case when l.status = 3 then 1 else 0 end) as stayopen"),new Expression("sum(case when l.status = 4 and l.deal_status = 1 then 1 else 0 end) as  stayaward")];
        if($type==1){
            array_push($field,"DATE_FORMAT(l.out_time,'%Y-%m-%d') days");
        }elseif($type==2){
            array_push($field,"DATE_FORMAT(l.create_time,'%Y-%m-%d') days");
        }
        $query = (new Query())->select($field)
            ->from("lottery_order as l")
            ->leftJoin("bussiness as b", "l.user_id=b.user_id")
            ->where(["l.order_platform"=>3])
            ->andWhere(["in", "l.status", $statusArr]);
        if (isset($post["channelName"]) && !empty($post["channelName"])) {
            $query = $query->andWhere(['or',["b.cust_no" => $post["channelName"]], ["like", "b.store_name", $post["channelName"]]]);
        }
        //判断统计类型是出票时间 1 还是创建时间 2
        if (isset($post["start_date"]) && !empty($post["start_date"])) {
            if($type==1){
                $query = $query->andWhere([">=", "l.out_time", $post["start_date"] . " 00:00:00"]);
            }else{
                $query = $query->andWhere([">=", "l.create_time", $post["start_date"] . " 00:00:00"]);
            }
        }
        if (isset($post["end_date"]) && !empty($post["end_date"])) {
            if($type==1){
                $query = $query->andWhere(["<=", "l.out_time", $post["end_date"] . " 23:59:59"]);
            }else{
                $query = $query->andWhere(["<=", "l.create_time", $post["end_date"] . " 23:59:59"]);
            }
        }
        $query = $query->groupBy(["b.name", "days"])
            ->orderBy("b.name desc,days desc");
        $result = $query->all();
//        print_r($result);
//        die;
        return $this->jsonResult(100, "获取成功", $result);
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
        $field =["b.name", "b.cust_no","b.user_id", "count(distinct l.cust_no) count", "sum(l.bet_money) salemoney", "count(l.lottery_order_id) ordernum", "sum(l.win_amount) winmoney","sum(l.award_amount) award_amount",new Expression("sum(case when l.status = 3 then 1 else 0 end) as stayopen"),new Expression("sum(case when l.status = 4 and l.deal_status = 1 then 1 else 0 end) as  stayaward")];
        if($type==1){
            array_push($field,"DATE_FORMAT(l.out_time,'%Y-%m') months");
        }elseif($type==2){
            array_push($field,"DATE_FORMAT(l.create_time,'%Y-%m') months");
        }
        $query = (new Query())->select($field)
                ->from("lottery_order as l")
                ->leftJoin("bussiness as b", "l.user_id=b.user_id")
                ->where(["l.order_platform"=>3])
                ->andWhere(["in", "l.status", $statusArr]);
        if (isset($post["channelName"]) && !empty($post["channelName"])) {
            $query = $query->andWhere(['or',["b.cust_no" => $post["channelName"]], ["like", "b.store_name", $post["channelName"]]]);
        }
        if($type==1){
            $query = $query->andWhere(["DATE_FORMAT(l.out_time,'%Y-%m')" => $years . "-" . $months]);
        }else{
            $query = $query->andWhere(["DATE_FORMAT(l.create_time,'%Y-%m')" => $years . "-" . $months]);
        }
        $query = $query->groupBy(["b.name", "months"])
            ->orderBy("b.name desc,months desc");
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
        $field =["l.lottery_id","l.lottery_name","b.name", "b.cust_no","b.user_id", "count(distinct l.cust_no) count", "sum(l.bet_money) salemoney", "count(l.lottery_order_id) ordernum", "sum(l.win_amount) winmoney","sum(l.award_amount) award_amount",new Expression("sum(case when l.status = 3 then 1 else 0 end) as stayopen"),new Expression("sum(case when l.status = 4 and l.deal_status = 1 then 1 else 0 end) as  stayaward")];

        $query = (new Query())->select($field)
            ->from("lottery_order as l")
            ->leftJoin("bussiness as b", "l.user_id=b.user_id")
            ->where(["l.order_platform"=>3])
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
        $query = $query->groupBy(["b.name","l.lottery_id"]);
        $result = $query->all();
        $res =[];
        foreach ($result as $k => $v) {
            $res[$v['user_id']][] = $v;
        }
        if($lotteryCode == 0){
            foreach ($res as $key =>$val){
                foreach ($val as $k =>$v){
                    if (in_array($v["lottery_id"], $lotteryZu)) {
                        $res[$key]["3000"] ["lottery_name"] = "竞彩足球";
                        $res[$key]["3000"] ["lottery_id"] = "3000";
                        $res[$key]["3000"] ["count"] = 0;
                        $res[$key]["3000"] ["salemoney"] = 0;
                        $res[$key]["3000"]["ordernum"] = 0;
                        $res[$key]["3000"]["winmoney"] = 0;
                        $res[$key]["3000"]["award_amount"] = 0;
                        $res[$key]["3000"]["stayopen"] = 0;
                        $res[$key]["3000"]["stayaward"] = 0;
                    }
                    if (in_array($v["lottery_id"], $lotteryLan)) {
                        $res[$key]["3100"] ["lottery_name"] = "竞彩篮球";
                        $res[$key]["3100"] ["lottery_id"] = "3100";
                        $res[$key]["3100"] ["count"] = 0;
                        $res[$key]["3100"] ["salemoney"] = 0;
                        $res[$key]["3100"]["ordernum"] = 0;
                        $res[$key]["3100"]["winmoney"] = 0;
                        $res[$key]["3100"]["award_amount"] = 0;
                        $res[$key]["3100"]["stayopen"] = 0;
                        $res[$key]["3100"]["stayaward"] = 0;
                    }
                    if (in_array($v["lottery_id"], $lotteryBd)) {
                        $res[$key]["5000"] ["lottery_name"] = "北京单场";
                        $res[$key]["5000"] ["lottery_id"] = "5000";
                        $res[$key]["5000"] ["count"] = 0;
                        $res[$key]["5000"] ["salemoney"] = 0;
                        $res[$key]["5000"]["ordernum"] = 0;
                        $res[$key]["5000"]["winmoney"] = 0;
                        $res[$key]["5000"]["award_amount"] = 0;
                        $res[$key]["5000"]["stayopen"] = 0;
                        $res[$key]["5000"]["stayaward"] = 0;
                    }
                }
                foreach ($val as $k =>$v){
                    if (in_array($v["lottery_id"], $lotteryZu)) {
                        $res[$key]["3000"]["count"]+=$v["count"];
                        $res[$key]["3000"]["salemoney"]+=$v["salemoney"];
                        $res[$key]["3000"]["ordernum"]+=$v["ordernum"];
                        $res[$key]["3000"]["winmoney"]+=round($v['winmoney'],2);
                        $res[$key]["3000"]["award_amount"]+=round($v['award_amount'],2);
                        $res[$key]["3000"]["stayopen"] +=$v["stayopen"];
                        $res[$key]["3000"]["stayaward"]+=$v["stayaward"];
                        $res[$key]["3000"]["name"]=$v["name"];
                        $res[$key]["3000"]["cust_no"]=$v["cust_no"];
                        $res[$key]["3000"]["user_id"]=$v["user_id"];
                        unset($res[$key][$k]);
                    }
                    if (in_array($v["lottery_id"], $lotteryLan)) {
                        $res[$key]["3100"]["count"]+=$v["count"];
                        $res[$key]["3100"]["salemoney"]+=$v["salemoney"];
                        $res[$key]["3100"]["ordernum"]+=$v["ordernum"];
                        $res[$key]["3100"]["winmoney"]+=round($v['winmoney'],2);
                        $res[$key]["3100"]["award_amount"]+=round($v['award_amount'],2);
                        $res[$key]["3100"]["stayopen"] +=$v["stayopen"];
                        $res[$key]["3100"]["stayaward"]+=$v["stayaward"];
                        $res[$key]["3100"]["name"]=$v["name"];
                        $res[$key]["3100"]["cust_no"]=$v["cust_no"];
                        $res[$key]["3100"]["user_id"]=$v["user_id"];
                        unset($res[$key][$k]);
                    }
                    if (in_array($v["lottery_id"], $lotteryBd)) {
                        $res[$key]["5000"]["count"]+=$v["count"];
                        $res[$key]["5000"]["salemoney"]+=$v["salemoney"];
                        $res[$key]["5000"]["ordernum"]+=$v["ordernum"];
                        $res[$key]["5000"]["winmoney"]+=round($v['winmoney'],2);
                        $res[$key]["5000"]["award_amount"]+=round($v['award_amount'],2);
                        $res[$key]["5000"]["stayopen"] +=$v["stayopen"];
                        $res[$key]["5000"]["stayaward"]+=$v["stayaward"];
                        $res[$key]["5000"]["name"]=$v["name"];
                        $res[$key]["5000"]["cust_no"]=$v["cust_no"];
                        $res[$key]["5000"]["user_id"]=$v["user_id"];
                        unset($res[$key][$k]);
                    }
                }
            }
        }else if ($lotteryCode == 3000) {
            foreach ($res as $key=>$val){
                $res[$key]["3000"] ["lottery_name"] = "竞彩足球";
                $res[$key]["3000"] ["lottery_id"] = "3000";
                $res[$key]["3000"] ["count"] = 0;
                $res[$key]["3000"] ["salemoney"] = 0;
                $res[$key]["3000"]["ordernum"] = 0;
                $res[$key]["3000"]["winmoney"] = 0;
                $res[$key]["3000"]["award_amount"] = 0;
                $res[$key]["3000"]["stayopen"] = 0;
                $res[$key]["3000"]["stayaward"] = 0;
            }
            foreach ($res as $key => &$val) {
                foreach ($val as $k=>$v){
                    if (in_array($v["lottery_id"], $lotteryZu)) {
                        $res[$key]["3000"]["count"]+=$v["count"];
                        $res[$key]["3000"]["salemoney"]+=$v["salemoney"];
                        $res[$key]["3000"]["ordernum"]+=$v["ordernum"];
                        $res[$key]["3000"]["winmoney"]+=round($v['winmoney'],2);
                        $res[$key]["3000"]["award_amount"]+=round($v['award_amount'],2);
                        $res[$key]["3000"]["stayopen"] +=$v["stayopen"];
                        $res[$key]["3000"]["stayaward"]+=$v["stayaward"];
                        $res[$key]["3000"]["name"]=$v["name"];
                        $res[$key]["3000"]["cust_no"]=$v["cust_no"];
                        $res[$key]["3000"]["user_id"]=$v["user_id"];
                        unset($res[$key][$k]);
                    }
                }
            }
        } else if ($lotteryCode == 3100) {
            foreach ($res as $key=>$val){
                $res[$key]["3100"] ["lottery_name"] = "竞彩篮球";
                $res[$key]["3100"] ["lottery_id"] = "3100";
                $res[$key]["3100"] ["count"] = 0;
                $res[$key]["3100"] ["salemoney"] = 0;
                $res[$key]["3100"]["ordernum"] = 0;
                $res[$key]["3100"]["winmoney"] = 0;
                $res[$key]["3100"]["award_amount"] = 0;
                $res[$key]["3100"]["stayopen"] = 0;
                $res[$key]["3100"]["stayaward"] = 0;
            }
            foreach ($res as $key => &$val) {
                foreach ($val as $k=>$v){
                    if (in_array($v["lottery_id"], $lotteryZu)) {
                        if (in_array($v["lottery_id"], $lotteryLan)) {
                            $res[$key]["3100"]["count"]+=$v["count"];
                            $res[$key]["3100"]["salemoney"]+=$v["salemoney"];
                            $res[$key]["3100"]["ordernum"]+=$v["ordernum"];
                            $res[$key]["3100"]["winmoney"]+=round($v['winmoney'],2);
                            $res[$key]["3100"]["award_amount"]+=round($v['award_amount'],2);
                            $res[$key]["3100"]["stayopen"] +=$v["stayopen"];
                            $res[$key]["3100"]["stayaward"]+=$v["stayaward"];
                            $res[$key]["3100"]["name"]=$v["name"];
                            $res[$key]["3100"]["cust_no"]=$v["cust_no"];
                            $res[$key]["3100"]["user_id"]=$v["user_id"];
                            unset($res[$key][$k]);
                        }
                    }
                }
            }
        } elseif ($lotteryCode == 5000) {
            foreach ($res as $key=>$val){
                $res[$key]["5000"] ["lottery_name"] = "北京单场";
                $res[$key]["5000"] ["lottery_id"] = "5000";
                $res[$key]["5000"] ["count"] = 0;
                $res[$key]["5000"] ["salemoney"] = 0;
                $res[$key]["5000"]["ordernum"] = 0;
                $res[$key]["5000"]["winmoney"] = 0;
                $res[$key]["5000"]["award_amount"] = 0;
                $res[$key]["5000"]["stayopen"] = 0;
                $res[$key]["5000"]["stayaward"] = 0;
            }
            foreach ($res as $key => &$val) {
                foreach ($val as $k=>$v){
                    if (in_array($v["lottery_id"], $lotteryBd)) {
                        $res[$key]["5000"]["count"]+=$v["count"];
                        $res[$key]["5000"]["salemoney"]+=$v["salemoney"];
                        $res[$key]["5000"]["ordernum"]+=$v["ordernum"];
                        $res[$key]["5000"]["winmoney"]+=round($v['winmoney'],2);
                        $res[$key]["5000"]["award_amount"]+=round($v['award_amount'],2);
                        $res[$key]["5000"]["stayopen"] +=$v["stayopen"];
                        $res[$key]["5000"]["stayaward"]+=$v["stayaward"];
                        $res[$key]["5000"]["name"]=$v["name"];
                        $res[$key]["5000"]["cust_no"]=$v["cust_no"];
                        $res[$key]["5000"]["user_id"]=$v["user_id"];
                        unset($res[$key][$k]);
                    }
                }

            }
        }
        return $this->jsonResult(100, "获取成功", $res);
    }

    public function actionSaledetail() {
        return $this->render('saledetail');
    }

    /**
     * 订单统计门店详情
     */
    public function actionStoreDetail() {
        return $this->render('store-detail');
    }
    public function actionGetStoreDetail(){
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
                    $where = ['l.lottery_id' => $post["lottery"]];
                    break;
                case 3000:
                    $where = ['l.lottery_type' => 2];
                    break;
                case 3100:
                    $where = ['l.lottery_type' => 4];
                    break;
                case 5000:
                    $where = ['l.lottery_type' => 5];
                    break;
                case 3300:
                    $where = ['l.lottery_type' => 6];
                    break;
            }
        }

        //查询字段
        $field =["s.store_name","count(distinct l.cust_no) count", "sum(l.bet_money) salemoney", "count(l.lottery_order_id) ordernum", "sum(l.win_amount) winmoney","sum(l.award_amount) award_amount","sum(p.pay_money) paymoney",new Expression("sum(case when l.status = 3 then 1 else 0 end) as stayopen"),new Expression("sum(case when l.status = 4 and l.deal_status = 1 then 1 else 0 end) as  stayaward")];
        $query = (new Query())->select($field)
            ->from("lottery_order as l")
            ->leftJoin("store as s", "l.store_no=s.store_code  and l.store_id=s.user_id")
            ->leftJoin("pay_record p","l.lottery_order_code=p.order_code and p.pay_type=16")
            ->where(["l.order_platform"=>3])
            ->andWhere($where)
            ->andWhere(["in", "l.status", $statusArr]);
        if (isset($post["sNo"]) && !empty($post["sNo"])) {
            $query = $query->andWhere(["l.user_id" => $post["sNo"]]);
        }
        if($type==1){
            if (isset($post["timer"]) && !empty($post["timer"])) {
                $query = $query->andWhere(["between", "l.out_time", $post["timer"] . " 00:00:00", $post["timer"] . " 23:59:59"]);
            }
            if (isset($post["months"]) && !empty($post["months"])) {
                $query = $query->andWhere(["between", "l.out_time", $post["months"] . "-01" . " 00:00:00", $post["months"] . "-31" . " 23:59:59"]);
            }
            //彩种开始结束时间
            if (!empty($post["star"]) && !empty($post["end"])) {
                $query = $query->andWhere(["between", "l.out_time", $post["star"] . " 00:00:00", $post["end"] . " 23:59:59"]);
            }
        }else{
            if (isset($post["timer"]) && !empty($post["timer"])) {
                $query = $query->andWhere(["between", "l.create_time", $post["timer"] . " 00:00:00", $post["timer"] . " 23:59:59"]);
            }
            if (isset($post["months"]) && !empty($post["months"])) {
                $query = $query->andWhere(["between", "l.create_time", $post["months"] . "-01" . " 00:00:00", $post["months"] . "-31" . " 23:59:59"]);
            }
            if (!empty($post["star"]) && !empty($post["end"])) {
                $query = $query->andWhere(["between", "l.create_time", $post["star"] . " 00:00:00", $post["end"] . " 23:59:59"]);
            }
        }
        $query = $query->groupBy('s.store_name')->orderBy('s.store_name desc');
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
