<?php

namespace app\modules\subchannel\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\common\models\ApiOrder;
use app\modules\common\helpers\Constants;
use app\modules\lottery\models\Lottery;
class ApiorderController extends \yii\web\Controller {
    /**
     * 合作商投注订单列表
     */
    public function actionIndex(){
        $session = Yii::$app->session;
        $get = \Yii::$app->request->get();
        $status = Constants::THIRD_STATUS;
        $lottery = new lottery();
        $lotteryNames = $lottery->getLotterynamelist();
        $lotteryNames[0] = "请选择";
        $query = (new Query())->select("a.*,u.user_name,u.user_tel,l.lottery_order_code,l.create_time as jtime")
                ->from("api_order as a")
                ->leftJoin("user as u","u.user_id = a.user_id")
                ->leftJoin("lottery_order as l","l.source = 7 and l.source_id = a.api_order_id");
         //判断当前登录用户是咕啦内部用户还是合作渠道商户,合作渠道商是本账号还是所属操作账号
        if($session["type"]==1){
            if($session["agent_code"]=="gl00015788"){
                $query = $query->andWhere(['u.cust_no' => $session["admin_name"]]);
            }else{
                $query = $query->andWhere(['u.cust_no' => $session["agent_code"]]);
            }
        }
        if (isset($get["user_info"]) && !empty($get["user_info"])){
            $query = $query->andWhere(["or",["like","u.user_name", $get["user_info"]],["u.user_tel" => $get["user_info"]],["u.cust_no" => $get["user_info"]]]);
        }
        if (isset($get["api_order_code"]) && !empty($get["api_order_code"])) {
            $query = $query->andWhere(["a.api_order_code" => $get["api_order_code"]]);
        }
        if (isset($get["third_order_code"]) && !empty($get["third_order_code"])) {
            $query = $query->andWhere(["a.third_order_code" => $get["third_order_code"]]);
        }
        if (isset($get["lottery_code"])&& !empty($get["lottery_code"])) {
            $query = $query->andWhere(["a.lottery_code" => $get["lottery_code"]]);
        }
        if (isset($get["startdate"])) {
            $query = $query->andWhere([">", "a.create_time", $get["startdate"] . " 00:00:00"]);
        } else {
            $query = $query->andWhere([">", "a.create_time", date("Y-m-d", strtotime("-3 day")) . " 00:00:00"]);
        }
        if (isset($get["enddate"])) {
            $query = $query->andWhere(["<", "a.create_time", $get["enddate"] . " 23:59:59"]);
        } else {
            $query = $query->andWhere(["<", "a.create_time", date("Y-m-d") . " 23:59:59"]);
        }
        if (isset($get["status"]) && !empty($get["status"])) {
            $query = $query->andWhere(["a.status" => $get["status"]]);
        }
        $query = $query->orderBy("a.api_order_id desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
       return $this->render("index",["data" => $data,"get"=>$get,"status"=>$status,"lotteryNames" => $lotteryNames,]); 
    }
}
