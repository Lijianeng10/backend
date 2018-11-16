<?php

namespace app\modules\trading\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use yii\data\ActiveDataProvider;

class RefundController extends Controller {

    public function actionIndex() {
        $this->enableCsrfValidation = false;
        $get = \Yii::$app->request->get();
        $query = new Query();
        $query = $query->select("pay_record.*,user.user_tel")
                ->from("pay_record")
                ->join("left join", "user", "user.cust_no=pay_record.cust_no")
                ->where(["pay_record.status" => 3]);
        if (isset($get["order_code"]) && !empty($get["order_code"])) {
            $query = $query->andWhere(["pay_record.order_code" => $get["order_code"]]);
        }
        if (isset($get["cust_no"]) && !empty($get["cust_no"])) {
            $query = $query->andWhere(["or",["user.user_tel" => $get["cust_no"]],["pay_record.cust_no" => $get["cust_no"]]]);
        }
        if (isset($get["pay_no"]) && !empty($get["pay_no"])) {
            $query = $query->andWhere(["pay_record.pay_no" => $get["pay_no"]]);
        }
        if (isset($get["outer_no"]) && !empty($get["outer_no"])) {
            $query = $query->andWhere(["pay_record.outer_no" => $get["outer_no"]]);
        }
        if (isset($get["way_type"]) && !empty($get["way_type"])) {
            $strs = explode("_", $get["way_type"]);
            $query = $query->andWhere(["pay_record.pay_way" => $strs[0]])
                    ->andWhere(["pay_record.way_type" => $strs[1]]);
        }
        
        if (isset($get["pay_money_min"]) && !empty($get["pay_money_min"])) {
            $query = $query->andWhere([">=", "pay_record.pay_money", $get["pay_money_min"]]);
        }
        if (isset($get["pay_money_max"]) && !empty($get["pay_money_max"])) {
            $query = $query->andWhere(["<=", "pay_record.pay_money", $get["pay_money_max"]]);
        }
        if (isset($get["startdate"]) && !empty($get["startdate"])) {
            $query = $query->andWhere([">", "pay_record.create_time", $get["startdate"] . " 00:00:00"]);
        }else{
            $query = $query->andWhere([">", "pay_record.create_time", date("Y-m-d",strtotime("-1 weeks"))." 00:00:00"]);
        }
        if (isset($get["enddate"]) && !empty($get["enddate"])) {
            $query = $query->andWhere(["<", "pay_record.create_time", $get["enddate"] . " 23:59:59"]);
        }else{
            $query = $query->andWhere(["<", "pay_record.create_time",date("Y-m-d") . " 23:59:59"]);
        }
        $data = $query->orderBy("pay_record.pay_record_id desc");
        $data = new ActiveDataProvider([
            'query' => $data,
        ]);
        return $this->render("index", ["data" => $data]);
    }

}
