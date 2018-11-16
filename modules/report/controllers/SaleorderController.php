<?php

namespace app\modules\report\controllers;

use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use app\modules\lottery\models\Store;
use app\modules\lottery\helpers\Constant;

class SaleorderController extends Controller {
    public $enableCsrfValidation = false;
    public function actionIndex(){
        return $this->render('index');
    }
    /**
     * 获取彩店销售订单数据
     */
    public function actionGetSaleOrderList() {
        $post = \Yii::$app->request->post();
        $statusArr = [3, 4, 5];
        $dealStatusArr = [1, 3];
        $lotteryZu = [3006, 3007, 3008, 3009, 3010, 3011];
        $lotteryLan = [3001, 3002, 3003, 3004, 3005];
        $query = (new Query())->select(["lottery_order.lottery_order_code","store.store_name", "lottery_order.create_time", "lottery_order.bet_val", "lottery_order.award_amount", "lottery_order.play_name", "lottery_order.lottery_name", "lottery_order.count", "lottery_order.bet_double", "lottery_order.bet_money", "lottery_order.win_amount", "lottery_order.cust_no", "user.user_tel", "lottery_order.deal_status"])
                ->from("lottery_order")
                ->leftJoin("user", "lottery_order.cust_no=user.cust_no")
                ->rightJoin("store","lottery_order.store_no=store.store_code and store.user_id=lottery_order.store_id");
        if (isset($post["store_name"]) && !empty($post["store_name"])){
            $query = $query->andWhere(["store.store_name" => $post["store_name"]]);
        }
        if (isset($post["lotteryOrderCode"]) && !empty($post["lotteryOrderCode"])) {
            $query = $query->andWhere(["lottery_order.lottery_order_code" => $post["lotteryOrderCode"]]);
        }
        if (isset($post["userInfo"]) && !empty($post["userInfo"])) {
            $query = $query->andWhere(["or", ["lottery_order.cust_no" => $post['userInfo']], ["user.user_name" => $post['userInfo']], ["user.user_tel" => $post['userInfo']]]);
        }
        if ($post["lotteryId"] != 10) {
            if ($post["lotteryId"] == 3000) {
                $query = $query->andWhere(["in", "lottery_order.lottery_id", $lotteryZu]);
            } elseif ($post["lotteryId"] == 3100) {
                $query = $query->andWhere(["in", "lottery_order.lottery_id", $lotteryLan]);
            } else {
                $query = $query->andWhere(["lottery_order.lottery_id" => $post["lotteryId"]]);
            }
        }
        if (isset($post["star"]) && !empty($post["star"])) {
            $query = $query->andWhere([">=", "lottery_order.create_time", $post["star"] . " 00:00:00"]);
        }
        if (isset($post["end"]) && !empty($post["end"])) {
            $query = $query->andWhere(["<=", "lottery_order.create_time", $post["end"] . " 23:59:59"]);
        }
        if ($post["status"] == 10) {
            $query = $query->andWhere(["in", "lottery_order.status", $statusArr]);
        } else {
            $query = $query->andWhere(["lottery_order.status" => $post['status']]);
        }
        if ($post["dealStatus"] == 10) {
            $query = $query->andWhere(["in", "lottery_order.deal_status", $dealStatusArr]);
        } else {
            $query = $query->andWhere(["lottery_order.deal_status" => $post['dealStatus']]);
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
                ->orderBy("create_time desc")
                ->indexBy("lottery_order_code");
        $result = $query->all();
        $list = (new Query())->select(["pay_record.order_code","store.store_name", "pay_record.pay_money"])
                ->from("lottery_order")
                ->leftJoin("pay_record", "lottery_order.lottery_order_code=pay_record.order_code")
                ->rightJoin("store","lottery_order.store_no=store.store_code and store.user_id=lottery_order.store_id")
                ->andWhere("pay_record.pay_type=16");
        if (isset($post["store_name"]) && !empty($post["store_name"])){
            $list = $list->andWhere(["store.store_name" => $post["store_name"]]);
        }
        if (isset($post["lotteryOrderCode"]) && !empty($post["lotteryOrderCode"])) {
            $list = $list->andWhere(["lottery_order.lottery_order_code" => $post["lotteryOrderCode"]]);
        }
        if (isset($post["userInfo"]) && !empty($post["userInfo"])) {
            $list = $list->andWhere(["or", ["lottery_order.cust_no" => $post['userInfo']], ["user.user_name" => $post['userInfo']], ["user.user_tel" => $post['userInfo']]]);
        }
        if ($post["lotteryId"] != 10) {
            if ($post["lotteryId"] == 3000) {
                $list = $list->andWhere(["in", "lottery_order.lottery_id", $lotteryZu]);
            } elseif ($post["lotteryId"] == 3100) {
                $list = $list->andWhere(["in", "lottery_order.lottery_id", $lotteryLan]);
            } else {
                $list = $list->andWhere(["lottery_order.lottery_id" => $post["lotteryId"]]);
            }
        }
        if (isset($post["star"]) && !empty($post["star"])) {
            $list = $list->andWhere([">=", "lottery_order.create_time", $post["star"] . " 00:00:00"]);
        }
        if (isset($post["end"]) && !empty($post["end"])) {
            $list = $list->andWhere(["<=", "lottery_order.create_time", $post["end"] . " 23:59:59"]);
        }
        if ($post["status"] == 10) {
            $list = $list->andWhere(["in", "lottery_order.status", $statusArr]);
        } else {
            $list = $list->andWhere(["lottery_order.status" => $post['status']]);
        }
        if ($post["dealStatus"] == 10) {
            $list = $list->andWhere(["in", "lottery_order.deal_status", $dealStatusArr]);
        } else {
            $list = $list->andWhere(["lottery_order.deal_status" => $post['dealStatus']]);
        }
        $list = $list->offset($offset)
                ->limit($size)
                ->orderBy("lottery_order.create_time desc")
                ->indexBy("order_code");
        $res = $list->all();
        //数据手续费重组
        foreach ($result as $k => &$v) {
            if (isset($res[$k])) {
                $v["paymoney"] = $res[$k]["pay_money"];
            } else {
                $v["paymoney"] = "0.00";
            }
        }
        $data["result"] = $result;
        return $this->jsonResult(100, "获取成功", $data);
    }
}

