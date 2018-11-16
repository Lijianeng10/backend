<?php

namespace app\modules\trading\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use yii\data\ActiveDataProvider;

class OrderController extends Controller {

    public function actionIndex() {
        $this->enableCsrfValidation = false;
        $get = \Yii::$app->request->get();
        $query = new Query();
        $query = $query->select(["pay_record.*","user.user_tel"])
                ->from("pay_record")
                ->join("left join", "user", "user.cust_no=pay_record.cust_no")
//                ,"store.store_code","store.phone_num"
//                ->leftJoin("lottery_order","lottery_order.lottery_order_code = pay_record.order_code")
//                ->leftJoin("store","store.store_code = lottery_order.store_no and store.status = 1")
                ->where("1=1");
        if (isset($get["order_code"]) && !empty($get["order_code"])) {
            $query = $query->andWhere(["pay_record.order_code" => $get["order_code"]]);
        }
        if (isset($get["cust_no"]) && !empty($get["cust_no"])) {
            $query = $query->andWhere(["or", ["pay_record.cust_no" =>trim($get["cust_no"])], ["user.user_tel" => trim($get["cust_no"])]]);
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
        if (isset($get["pay_type"]) && !empty($get["pay_type"])) {
            if ($get["pay_type"] == "6") {
                $query = $query->andWhere(["pay_record.status" => 3]);
            } else {
                $query = $query->andWhere(["in", "pay_record.pay_type", explode("|", $get["pay_type"])]);
            }
        }
        if (isset($get["status"]) && $get["status"] >= "0") {
            $query = $query->andWhere(["pay_record.status" => $get["status"]]);
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
        if (isset($get["flow"]) && !empty($get["flow"])) {
            if ($get["flow"] == "1") {
                $query = $query->andWhere(["or", ["not in", "pay_record.pay_type", [1, 4, 5, 7, 10, 11, 13, 16, 17,21]], ["pay_record.status" => 3]]);
            } else {
                $query = $query->andWhere(["in", "pay_record.pay_type", [1,4,5, 7, 10, 11, 13, 16, 17,21]])->andWhere(["!=", "pay_record.status", 3]);
            }
        }
//        if (!empty($post["order_code"])) {
//            $query = $query->andWhere(["order_code" => $post["order_code"]]);
//        }
        $data = $query->orderBy("pay_record.pay_record_id desc");
               
        $data = new ActiveDataProvider([
            'query' => $data,
        ]);
        return $this->render("index", ["data" => $data]);
    }

}
