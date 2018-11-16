<?php

namespace app\modules\report\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use app\modules\lottery\models\Store;
use app\modules\lottery\helpers\Constant;
use app\modules\common\services\ApiSysService;
use app\modules\agents\models\User;
use app\modules\common\models\PayRecord;
use yii\db\Exception;
use app\modules\common\models\SendRebateRecord;
use app\modules\member\models\UserFunds;
use app\modules\common\helpers\Excel;

class SpreadReportController extends Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * 获取推广统计数据
     */
    public function actionGetSpreadReport() {
        $request = \Yii::$app->request;
        $type =$request->post("type","un_settle_list");
        $user_info =$request->post("user_info","");
        $agent_info =$request->post("agent_info","");
        $years =$request->post("years","");
        $month =$request->post("month","");
        $data = json_encode(["type"=>$type,"settle_month" => $years.'-'.$month,"user_info_filter"=>$user_info,"agent_info_filter"=>$agent_info]);
        $SettledDetail = ApiSysService::getCustSettleInfo($data);
        $res=[];
        if(!empty($SettledDetail)){
            switch ($type){
                case 'un_settle_list':
                    foreach ($SettledDetail["data"] as $k => $v){
                        $res[]=[
                            'settleid' => '',
                            'start_time' => $v["start_time"],
                            'end_time' => $v['end_time'],
                            'settle_month' => date('Y-m'),
                            'cust_no' => $v['cust_no'],
                            'total_amount' => $v['total_amount'],
                            'user_tel' => $v['user_tel'],
                            'amount' => $v['amount'],
                            'agent_code' => $v['agent_code'],
                            'agent_tel' => $v['agent_tel'],
                            'IsTC' => 0,
                            'rate' => $v['rate'],
                        ];
                    }
                    break;
                case 'settle_list':
                    foreach ($SettledDetail["data"] as $k => $v){
                        $res[]=[
                            'settleid' => $v["settleid"],
                            'start_time' => $v["start_time"],
                            'end_time' => $v['end_time'],
                            'settle_month' => $v['settle_month'],
                            'cust_no' => $v['cust_no'],
                            'total_amount' => $v['total_amount'],
                            'user_tel' => $v['user_tel'],
                            'amount' => $v['amount'],
                            'agent_code' => $v['agent_code'],
                            'agent_tel' => $v['agent_tel'],
                            'IsTC' => $v['IsTC'],
                            'rate' => $v['rate'],
                        ];
                    }
                    break;
            }

        }else{
            $res=[];
        }
        return $this->jsonResult(600, "获取成功", $res);
    }
    /**
     * 发放提成
     */
    public function actionAwardAmount(){
        $this->layout =false;
        $request = \Yii::$app->request;
        $session = \Yii::$app->session;
        if (Yii::$app->request->isGet) {
            $settleid =$request->get("settleid","");
            $tel=$request->get("tel","");
            $money=$request->get("money","");
            return $this->render("award-amount", ["settleid" => $settleid,"tel"=>$tel,"money"=>$money]);
        }elseif (Yii::$app->request->isPost) {
            $settleid =$request->post("settleid","");
            $userTel =$request->post("userTel","");
            $money =$request->post("money","");
            if(empty($money)){
                return $this->jsonError(109,"请输入金额");
            }
            $user = User::findOne(["user_tel" => $userTel]);
            if (empty($user)) {
                return $this->jsonError(109, "未找到该用户！");
            }
            $db = \Yii::$app->db;
            $tran = $db->beginTransaction();
            try {
                $userFunds = UserFunds::findOne(["user_id" => $user->user_id]);
                $userFunds->all_funds = $userFunds->all_funds + $money;
                $userFunds->able_funds = $userFunds->able_funds + $money;
//                $userFunds->no_withdraw = $userFunds->no_withdraw + $money;
                $userFunds->modify_time = date("Y-m-d H:i:s");
                if ($userFunds->validate()) {
                    $ret = $userFunds->save();
                    if ($ret == false) {
                        throw new Exception('资金表更新失败,推广金额发放失败');
                    }
                    $order_code = "GLREBA" . date("YmdHis") . "O" . (sprintf("%06d", rand(1, 999999)));
                    $retRecord = $db->createCommand()->insert("pay_record", [
                        "order_code" => $order_code,
                        "cust_no" => $user->cust_no,
                        "cust_type" => 1,
                        "pay_no" => "GLREBA" . date("YmdHis") . "R" . (sprintf("%06d", rand(1, 999999))),
                        "pay_name" => "余额",
                        "way_name" => "余额",
                        "way_type" => "YE",
                        "pay_way" => 3,
                        "pay_money" => $money,
                        "pay_pre_money" => $money,
                        "balance" => $userFunds->all_funds,
                        "pay_type_name" => "推广提成发放",
                        "pay_type" => 23,
                        "body" => "推广提成发放",
                        "status" => 1,
                        "pay_time" => date("Y-m-d H:i:s"),
                        "modify_time" => date("Y-m-d H:i:s"),
                        "create_time" => date("Y-m-d H:i:s")
                    ])->execute();
                    if ($retRecord == false) {
                        throw new Exception('交易记录插入失败,推广金额发放失败');
                    }
                    //获取刚插入数据ID
                    $nowId = $db->getLastInsertID();
                    $sendRebateRecord = new SendRebateRecord();
                    $sendRebateRecord->cust_no =  $user->cust_no;
                    $sendRebateRecord->send_money =  $money;
                    $sendRebateRecord->send_time =  date("Y-m-d H:i:s");
                    $sendRebateRecord->opt_id =  $session['admin_id'];
                    $sendRebateRecord->create_time =  date("Y-m-d H:i:s");
//                    $sendRebateRecord = $db->createCommand()->insert("send_rebate_record", [
//                        "cust_no" => $user->cust_no,
//                        "send_money" => $money,
//                        "send_time" => date("Y-m-d H:i:s"),
//                        "opt_id" => $session['admin_id'],
//                        "create_time" => date("Y-m-d H:i:s")
//                    ])->execute();
                    if (!$sendRebateRecord->save()) {
                        throw new Exception('提成发放记录表新增失败,推广金额发放失败');
                    }
                    //同步发放状态到小郑
                    $data = json_encode(["type"=>"settle_tc","settleid" => $settleid,"amount"=>$money]);
                    $result = ApiSysService::getCustSettleInfo($data);
                    if($result["code"]!=600){
                        throw new Exception($result["msg"]);
                    }
                }
                $tran->commit();
                //同步数据到小郑
                ApiSysService::payRecord($nowId);
                //微信通知
//                $this->sendCampaignBonusMsg($order_code);
                return $this->jsonResult(600, "发放成功！", "");
            } catch (Exception $e) {
                $tran->rollBack();
                return $this->jsonResult(109,$e->getMessage());
            }
        }
    }
    /**
     * 查看详情
     * 未结算：un_settle_detail 已结算：settle_detail
     */
    public function actionDetail(){
        return $this->render('settle_detail');
    }
    /**
     * 获取未结算、已结算分润明细
     */
    public function actionGetSettleDetail(){
        $cust_no = \Yii::$app->request->post('cust_no','');
        $settleid = \Yii::$app->request->post('settleid','');
        $page = \Yii::$app->request->post('page',1);
        $size = \Yii::$app->request->post('size',15);
        if(!empty($cust_no)){
            $data = json_encode(["type"=>"un_settle_detail","cust_no" => $cust_no,"page"=>$page,"size"=>$size]);
        }elseif(!empty($settleid)){
            $data = json_encode(["type"=>"settle_detail","settleid" => $settleid,"page"=>$page,"size"=>$size]);
        }
        $res = [];
        $SettleDetail = ApiSysService::getCustSettleInfo($data);
        $res["list"] = $SettleDetail["data"];
        $res["pages"] = ceil($SettleDetail["total_count"]/$size);
        return $this->jsonResult(600,'succ',$res);
    }
    /**
     * 发送推广提成发放微信推送
     * @param type $order_code
     */
    public function sendCampaignBonusMsg($order_code) {
        @file_get_contents(\Yii::$app->params["userDomain"] . "/api/cron/time/send-campaign-bonus-msg?order_code=" . $order_code);
    }
    /**
     * 导出明细
     */
    public function actionPrintReport(){
        $cust_no = \Yii::$app->request->get('cust_no','');
        $settleid = \Yii::$app->request->get('settleid','');
        $page = \Yii::$app->request->get('page',1);
        $size = \Yii::$app->request->get('size',10000);
        if(!empty($cust_no)){
            $data = json_encode(["type"=>"un_settle_detail","cust_no" => $cust_no,"page"=>$page,"size"=>$size]);
        }elseif(!empty($settleid)){
            $data = json_encode(["type"=>"settle_detail","settleid" => $settleid,"page"=>$page,"size"=>$size]);
        }
        $res = [];
        $SettleDetail = ApiSysService::getCustSettleInfo($data);
        Excel::printSpreadDetail($SettleDetail["data"],$SettleDetail["total_count"]);
        echo '<script type="text/javascript">alert("打印成功");window.history.go(-1);</script>';
        exit();
    }
    /**
     * 获取已结算分润门店详情
     */
    public function actionStoreDetail(){
        return $this->render('store_detail');
    }
    public function actionGetStoreDetail(){
        $cust_no = \Yii::$app->request->post('cust_no','');
        $settleid = \Yii::$app->request->post('settleid','');
        $page = \Yii::$app->request->post('page',1);
        $size = \Yii::$app->request->post('size',10);
        if(!empty($cust_no)){
            $data = json_encode(["type"=>"un_settle_detail","cust_no" => $cust_no,"page"=>$page,"size"=>$size]);
        }elseif(!empty($settleid)){
            $data = json_encode(["type"=>"settle_store_list","settleid" => $settleid,"page"=>$page,"size"=>$size]);
        }
        $res = [];
        $SettleDetail = ApiSysService::getCustSettleInfo($data);
        return $this->jsonResult(600,'succ',$SettleDetail);
    }
    /**
     * 获取已结算分润用户每天购彩详情
     */
    public function actionDayDetail(){
        if(\Yii::$app->request->isGet){
            return $this->render('day_detail');
        }elseif(\Yii::$app->request->isPost){
            $cust_no = \Yii::$app->request->post('cust_no','');
            $settleid = \Yii::$app->request->post('settleid','');
            $page = \Yii::$app->request->post('page',1);
            $size = \Yii::$app->request->post('size',15);
            if(!empty($cust_no)){
                $data = json_encode(["type"=>"un_settle_detail","cust_no" => $cust_no,"page"=>$page,"size"=>$size]);
            }elseif(!empty($settleid)){
                $data = json_encode(["type"=>"settle_detail_day","settleid" => $settleid,"page"=>$page,"size"=>$size]);
            }
            $res = [];
            $SettleDetail = ApiSysService::getCustSettleInfo($data);
            $res["list"] = $SettleDetail["data"];
            $res["pages"] = ceil($SettleDetail["total_count"]/$size);
            return $this->jsonResult(600,'succ',$res);
        }
    }
    /**
     * 导出已结算分润用户每天购彩详情
     */
    public function actionPrintDayCustReport(){
        $cust_no = \Yii::$app->request->get('cust_no','');
        $settleid = \Yii::$app->request->get('settleid','');
        $page = \Yii::$app->request->get('page',1);
        $size = \Yii::$app->request->get('size',100000);
        if(!empty($cust_no)){
            $data = json_encode(["type"=>"un_settle_detail","cust_no" => $cust_no,"page"=>$page,"size"=>$size]);
        }elseif(!empty($settleid)){
            $data = json_encode(["type"=>"settle_detail_day","settleid" => $settleid,"page"=>$page,"size"=>$size]);
        }
        $res = [];
        $SettleDetail = ApiSysService::getCustSettleInfo($data);
        Excel::printSpreadDayDetail($SettleDetail["data"],$SettleDetail["total_count"]);
        echo '<script type="text/javascript">alert("打印成功");window.history.go(-1);</script>';
        exit();
    }
}
