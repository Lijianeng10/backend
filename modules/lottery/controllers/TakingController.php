<?php

namespace app\modules\lottery\controllers;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\Lottery;
use app\modules\lottery\models\LotteryOrder;
use app\modules\lottery\models\Store;
use app\modules\common\models\OrderTaking;
use app\modules\common\helpers\Constants;

class TakingController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $orderStatus = Constants::ORDER_TAKING_STATUS;
        $detail = (new Query())->select("o.*,s.store_name,s.phone_num")
                ->from("order_taking as o")
                ->leftJoin("store as s","s.store_code = o.store_code and s.status = 1");
        if (isset($get["order_code"])&&!empty($get["order_code"])) {
            $detail = $detail->andWhere(["like", "o.order_code", "%{$get['order_code']}%", false]);
        }
        if (isset($get["store_info"])&&!empty($get["store_info"])) {
            $detail = $detail->andWhere(["or",["like", "o.store_code", "%{$get['store_info']}%",false],["like", "s.phone_num", "%{$get['store_info']}%",false]]);
        }
//        if (isset($get["startdate"])) {
//            $detail = $detail->andWhere([">", "lottery_additional.create_time", $get["startdate"]]);
//        }
//        if (isset($get["enddate"])) {
//            $detail = $detail->andWhere(["<", "lottery_additional.create_time", $get["enddate"] . " 23:59:59"]);
//        }
//
        if (isset($get["status"])&&$get["status"]!="") {
            $detail = $detail->andWhere(["o.status" => $get["status"]]);
        }
        $detail = $detail->orderBy("o.order_taking_id desc");
        $data = new ActiveDataProvider([
            'query' => $detail,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $this->render('index', ['data' => $data,"orderStatus" => $orderStatus, "get" => $get]);
    }


}
