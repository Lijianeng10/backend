<?php

namespace app\modules\index\controllers;

use Yii;
use app\modules\tools\helpers\Des;
use yii\db\Query;
use yii\web\Controller;
use app\modules\common\helpers\WechatTool;
use app\modules\common\models\User;

/**
 * Default controller for the `index` module
 */
class IndexController extends Controller
{
    private $key = '354344304D2B2C442B3A492A'; //密钥
    private $iv = '20180117';//IV 向量

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
//        $this->layout = false;
        $this->enableCsrfValidation = true;
        return $this->render('index');
    }

    public function actionTest(){
        $request = \Yii::$app->request;
        $param =  $request->post();
        $json_data = json_encode($param);
        $commond = isset($param['commond']) ? $param['commond'] : 1000;
        unset($param['commond']);
        $des = new Des($this->key, $this->iv);
        $des_data = $des->encrypt($json_data);

        $request_data = [
            'message' => [
                'head' => [
                    'command' => $commond,
                    'venderId' => 'GLe9ba658b9efdc1',
                    'messageId' => 1100,
                    'md' => md5($des_data),
                ],
                'body' => $des_data,
            ],
        ];
        $json_data = json_encode($request_data);
        $url = 'http://php.javaframework.cn/api/openapi/thirdapi/transfer';
        //curl
        $data = $this->jsonPost($url, $json_data);
        $data = json_decode($data, true);
        //解密
        $data = $this -> checkDate($data);
        $this->jsonResult(600,'succ', $data);
    }

    public function jsonPost($url, $data){
        $header[] = "Content-type: application/json";//定义conten t-type为xml
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        if(curl_errno($ch))
        {
            \Yii::info(var_export(curl_error($ch),true), 'backuporder_log');
        }
        curl_close($ch);
        return $response;
    }

    public function checkDate($data){
        $body = $data['message']['body'];
        //解密
        $des = new Des($this->key, $this->iv);
        $de_body = $des ->decrypt($body);
        $data['message']['body'] = $body;
        return json_decode($de_body, true);
    }

    /**
     * 测试微信推送
     */
    public function actionTestWx(){
        $wechatTool = new WechatTool();
        $errorInfo ="测试数据";
        $val = 'otEbv0RVq41n4aFfpDOOxUuON3Hc';
        $time = date("Y-m-d H:i:s");
        $remark ="请知悉！";
        $wechatTool = new WechatTool();
        $wechatTool->sendTemplateMsgSysAlert('通知',$val, '王琪推广通知', $errorInfo,"测试服务", $time, "请知悉", $remark);
    }
    public function actionTestLog(){
        \Yii::testAddLog();
        echo "成功";
    }
    /**
     * with关联查询
     */
    public function actionTestWith(){
        $name = ['userFunds','userFollow'];
        $res = User::find()->select("cust_no")->with($name)->where(["in","cust_no",["gl00017532","gl00046015"]])->asArray()->all();
        print_r($res);
        die;
    }
    public function actionTestTime(){
        $a = "2018-09-14 17:00:00";
        $b = date("Y-m-d H:i:s",strtotime('10 mins',strtotime($a)));
        print_r($b);
        $time = date("Y-m-d H:i:s",mktime(0,0,0,date("m")-1,1,date("Y")-1));
        print_r($time);die;
        $time = date("Y-m-d H:i:s");
        print_r($time);
    }
    //订单统计
    public function actionGetOrderReport(){
        $request= Yii::$app->request;
        $type = $request->post("type",1);
        $data = [];
        switch ($type){
            case 1:
                //及时订单
                $star = date("Y-m-d 00:00:00");
                $end = date("Y-m-d 23:59:59");
                $report= $this->getOrderInfo($star,$end);
                $data["timely"] = $report;
                break;
            case 2:
                //昨天订单
                $star = date("Y-m-d",strtotime("-1 day"))." 00:00:00";
                $end = date("Y-m-d",strtotime("-1 day"))." 23:59:59";
                break;
            case 3:
                //本月订单
                $star = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),1,date("Y")));
                $end = date ("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("t"),date("Y")));
                break;
            case 4:
                //上月订单
                $star = date("Y-m-d H:i:s",mktime(0,0,0,date("m")-1,1,date("Y")));
                $end = date("Y-m-d H:i:s",mktime(23,59,59,date("m"),0,date("Y")));
                break;
        }
        $out= $this->getFinishOrder($star,$end);
        $refuse= $this->getRefuseOrder($star,$end);
        $data["out"] = $out;
        $data["refuse"] = $refuse;
        return $this->jsonResult(600,"获取成功",$data);
    }
    /**
     * 查看订单详情
     * @param  order_type type为1需传 1 待接单 2 待出票 3 待兑奖
     */
    public function actionGetOrderDetail(){
        if(\Yii::$app->request->isGet){
            $get= Yii::$app->request->get();
            return $this->render("view-detail",["order_type"=>$get["order_type"]]);
        }elseif(\Yii::$app->request->isPost){
            $request= Yii::$app->request;
            $orderType = $request->post("order_type",1);
            $star = date("Y-m-d 00:00:00");
            $end = date("Y-m-d 23:59:59");
            $field = ['s.store_name',"s.store_code",'count(l.lottery_order_id) as nums','sum(l.bet_money) as moneys'];
            $query = (new Query())->select($field)
                ->from("lottery_order as l")
                ->leftJoin("store as s","s.store_code = l.store_no AND s.user_id=l.store_id")
                ->where(['<>','l.store_no',10004])
                ->andWhere(['between','l.create_time',$star,$end]);
            switch ($orderType){
                case 1:
                    $query = $query->andWhere(["l.status"=>2]);
                    break;
                case 2:
                    $query = $query->andWhere(["l.status"=>11]);
                    break;
                case 3:
                    $query = $query->andWhere(["l.status"=>4,"l.deal_status"=>1]);
                    break;
            }
            $query = $query->groupBy(["s.store_code"])->orderBy("s.store_id asc")->all();
            return $this->jsonResult(600,"获取成功",$query);
        }

    }
    /**
     * 查看拒绝订单详情
     * @param type 时间类型 1 当天 2 昨天 3 本月 4 上月
     */

    public function actionGetRefuseOrderDetail(){
        if(\Yii::$app->request->isGet){
            $get= Yii::$app->request->get();
            return $this->render("refuse-detail",["type"=>$get["type"]]);
        }elseif(\Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            $page = $request->post("page", 1);
            $size = $request->post("size", 15);
            $type = $request->post("type", 1);
            switch ($type) {
                case 1:
                    //及时订单
                    $star = date("Y-m-d 00:00:00");
                    $end = date("Y-m-d 23:59:59");
                    break;
                case 2:
                    //昨天订单
                    $star = date("Y-m-d", strtotime("-1 day")) . " 00:00:00";
                    $end = date("Y-m-d", strtotime("-1 day")) . " 23:59:59";
                    break;
                case 3:
                    //本月订单
                    $star = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y")));
                    $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("t"), date("Y")));
                    break;
                case 4:
                    //上月订单
                    $star = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
                    $end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), 0, date("Y")));
                    break;
            }
            $query = (new Query())->select("l.lottery_order_code,l.refuse_reason,s.store_name,s.province,s.city")
                ->from("lottery_order as l")
                ->leftJoin("store as s", "s.store_code = l.store_no AND s.user_id=l.store_id")
                ->where(['<>', 'l.store_no', 10004])
                ->andWhere(['between', 'l.out_time', $star, $end])
                ->andWhere(["l.status" => 10]);
            $data["total"] = $query->count();
            $data["pages"] = ceil($data["total"] / $size);
            $offset = ($page - 1) * $size;
            $data["list"] = $query
                ->limit($size)
                ->offset($offset)
                ->orderBy("l.create_time desc")
                ->all();
            return $this->jsonResult(600, "获取成功", $data);
        }
    }

    /**
     * 获取待接单、待出票、待兑奖订单
     * @param $star
     * @param $end
     * @return array|
     */
    public function  getOrderInfo($star,$end){
        $field = ['sum(case when l.status = 2 then 1 else 0 end) as waitOrderNum','sum(case when l.status = 2 then l.bet_money else 0 end) as waitOrderMoney','sum(case when l.status = 11 then 1 else 0 end) as waitOutNum','sum(case when l.status = 11 then l.bet_money else 0 end) as waitOutMoney','sum(case when l.status = 4 and l.deal_status = 1 then 1 else 0 end) as waitAwardNum','sum(case when l.status = 4 and l.deal_status = 1 then l.win_amount else 0 end) as waitAwardMoney'];
        $report = (new Query())->select($field)
            ->from('lottery_order as l')
            ->where(['<>','l.store_no',10004])
            ->andWhere(['between','l.create_time',$star,$end])
            ->one();
        if($report["waitOrderNum"]==null){
            $report["waitOrderNum"] = 0;
            $report["waitOrderMoney"] = 0;
        }
        if($report["waitOutNum"]==null){
            $report["waitOutNum"] = 0;
            $report["waitOutMoney"] = 0;
        }
        if($report["waitAwardNum"]==null){
            $report["waitAwardNum"] = 0;
            $report["waitAwardMoney"] = 0;
        }
        return $report;
    }

    /**
     * 获取已出票订单信息
     * @param $star
     * @param $end
     * @return mixed
     */
    public function getFinishOrder($star,$end){
        //已出票订单分组统计
//        'b.agents_name',
        $field = ['l.order_platform','sum(case when l.status in (3,4,5) then 1 else 0 end) as outNum','sum(case when l.status in (3,4,5) then l.bet_money else 0 end) as outMoney'];
//        $q1 = (new Query())->select("(2) as order_platform,agents_id, agents_name")->from("agents");
//        $q2 = (new Query())->select("(3) as order_platform,user_id,name")->from("bussiness");
//        $q3 = (new Query())->select("(1),(0),('直营')");
//        $q4 = (new Query())->select("(1),(1),('推广')");
//        union($q4)->
//        $q5 = (new Query())->select("(4) as order_platform,user_id,user_name")->from("user");
//        $new = $q1->union($q2)->union($q3)->union($q5);
        $order = (new Query())->select($field)
            ->from('lottery_order as l')
//            ->leftJoin(['b'=>$new],'l.order_platform = b.order_platform and b.agents_id = case l.order_platform when 2 then l.agent_id when 3 then l.user_id when 4 then l.agent_id when 1  then 0  end')
            ->where(['and',['<>','l.store_no',10004],['<>','l.order_platform',4]])
            ->andWhere(['between','l.out_time',$star,$end])
            ->groupBy('l.order_platform')
            ->all();
//        $custAry = (new Query())->select(["a.cust_no"])
//            ->from("user a")
//            ->leftJoin("user b","ifnull(b.is_profit,0)=1 and a.p_tree like concat('%',b.cust_no,'%')")
//            ->where("a.cust_no != b.cust_no");
        $userAry = (new Query())->select(["user_id"])->from("user")->where(["is_profit"=>1]);
        $tgOrder = (new Query())->select($field)
            ->from('lottery_order as l')
            ->where(['and',['<>','l.store_no',10004],['l.order_platform'=>4],["in","l.agent_id",$userAry]])
            ->andWhere(['between','l.out_time',$star,$end])
            ->all();
        if(empty($tgOrder[0]["order_platform"])){
            $tgOrder=[];
        }
        $list["outOrder"] = array_merge($order,$tgOrder);
        $outNum = 0;
        $outMoney =0;
        if(!empty($list["outOrder"])){
            foreach ($list["outOrder"] as $v){
                $outNum += $v["outNum"];
                $outMoney+=$v["outMoney"];
            }
        }
        $list["allOutNum"] = $outNum;
        $list["allOutMoney"] = $outMoney;
        return $list;
    }
    /**
     * 获取拒绝订单信息
     * @param $star
     * @param $end
     * @return array|bool
     */
    public function getRefuseOrder($star,$end){
        $field = ['sum(case when l.status =10 then 1 else 0 end) as sgRefuseNum','sum(case when l.status =10 then l.bet_money else 0 end) as sgRefuseMoney','sum(case when l.status =12 then 1 else 0 end) as zdRefuseNum','sum(case when l.status =12 then l.bet_money else 0 end) as zdRefuseMoney'];
        $refuseList = (new Query())->select($field)
            ->from('lottery_order as l')
            ->where(['<>','l.store_no',10004])
            ->andWhere(['between','l.out_time',$star,$end])
            ->one();
        if($refuseList["sgRefuseNum"]==null){
            $refuseList["sgRefuseNum"] = 0;
            $refuseList["sgRefuseMoney"] = 0;
        }
        if($refuseList["zdRefuseNum"]==null){
            $refuseList["zdRefuseNum"] = 0;
            $refuseList["zdRefuseMoney"] = 0;
        }
        $refuseNum = $refuseList["sgRefuseNum"]+$refuseList["zdRefuseNum"];
        $refuseMoney =$refuseList["sgRefuseMoney"]+$refuseList["zdRefuseMoney"];
        $refuseList["allRefuseNum"] = $refuseNum;
        $refuseList["allRefuseMoney"] = $refuseMoney;
        return $refuseList;
    }
}