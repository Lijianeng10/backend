<?php

namespace app\modules\lottery\controllers;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\LotteryAdditional;
use app\modules\lottery\models\Lottery;
use app\modules\lottery\models\LotteryOrder;

class TraceController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $detail = (new Query())->select("lottery_additional.*,u.user_tel")
                ->from("lottery_additional")
                ->leftJoin("user as u","u.cust_no = lottery_additional.cust_no")
                ->where([">", "periods_total", 1]);
        if (isset($get["lottery_additional_code"])) {
            $detail = $detail->andWhere(["like", "lottery_additional.lottery_additional_code", "%{$get['lottery_additional_code']}%", false]);
        }
        if (isset($get["lottery_code"])) {
            $detail = $detail->andWhere(["lottery_additional.lottery_id" => $get["lottery_code"]]);
        }
        if (isset($get["startdate"])) {
            $detail = $detail->andWhere([">", "lottery_additional.create_time", $get["startdate"]]);
        }
        if (isset($get["enddate"])) {
            $detail = $detail->andWhere(["<", "lottery_additional.create_time", $get["enddate"] . " 23:59:59"]);
        }

        if (isset($get["status"])) {
            $detail = $detail->andWhere(["lottery_additional.status" => $get["status"]]);
        }
        if (isset($get["user_info"])&&!empty($get["user_info"])) {
            $detail = $detail->andWhere(["or",["lottery_additional.cust_no" => $get['user_info']], ["u.user_tel" => $get['user_info']]]);
        }
        $detail = $detail->orderBy("lottery_additional.lottery_additional_id desc");
        $orderStatus = [
            "-1" => "所有",
            "0" => "停止",
            "1" => "未追",
            "2" => "正在追",
            "3" => "完成"
        ];
        $lottery = new lottery();
        $lotteryNames = $lottery->getLotterynamelist();
        $lotteryNames[0] = "请选择";
        $pageSize = 15;
        $data = new ActiveDataProvider([
            'query' => $detail,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'attributes' => ['lottery_id'],
            ],
        ]);

        return $this->render('index', ['data' => $data, "lotteryNames" => $lotteryNames, "orderStatus" => $orderStatus, "get" => $get]);
    }

    public function actionReaddetail() {
        $get = \Yii::$app->request->get();
        $data = [];
        $data['lotAdditional'] = LotteryAdditional::find()
                ->select("lottery_additional.*,s.store_name")
                ->leftJoin("store as s","s.store_code = lottery_additional.store_no")
                ->where(["lottery_additional_id" => $get["lottery_additional_id"]])
                ->asArray()
                ->one();
        $data['lotOrders'] = LotteryOrder::find()
                ->select("lottery_order.* ,lottery_record.lottery_time")
                ->join("JOIN", "lottery_record", "lottery_record.periods=lottery_order.periods and lottery_record.lottery_code=lottery_order.lottery_id")
                ->leftJoin("lottery_additional as la","la.lottery_additional_id = lottery_order.source_id")
                ->where(["la.lottery_additional_id" => $get["lottery_additional_id"]])
                ->orderBy("lottery_order.chased_num desc")
                ->asArray()
                ->all();
        
        $data['orderStatus'] = [
            "0" => "停止",
            "1" => "未追",
            "2" => "正在追",
            "3" => "完成"
        ];
        return $this->render("readdetail", ["data" => $data]);
    }

}
