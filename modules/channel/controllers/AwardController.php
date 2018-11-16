<?php

namespace app\modules\channel\controllers;

use yii\web\Controller;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\common\models\LotteryOrder;
use app\modules\channel\helpers\DoAward;


class AwardController extends Controller {

    public function actionLists() {
        $get = \Yii::$app->request->get();
        $detail = (new Query())->select(["lottery_order.*", 's.store_name', 'u.user_tel', 'a.api_order_code', 'a.third_order_code', 'sum(d.win_amount) deal_win_amount'])
                ->from("lottery_order")
                ->innerJoin('api_order a', 'a.api_order_id = lottery_order.source_id')
                ->leftJoin('store as s', 's.store_code = lottery_order.store_no and s.user_id=lottery_order.store_id')
                ->leftJoin('user as u', 'u.cust_no = lottery_order.cust_no')
                ->leftJoin('deal_order d', 'd.order_id = lottery_order.lottery_order_id and lottery_order.status = 4')
                ->andWhere(['lottery_order.source' => 7, 'lottery_order.status' => 4]);
//        var_dump($get);die;
        if (isset($get['api_order_code'])) {
            $detail = $detail->andWhere(['a.api_order_code' => $get['api_order_code']]);
        }
        if (isset($get['third_order_code'])) {
            $detail = $detail->andWhere(['a.third_order_code' => $get['third_order_code']]);
        }
        if (isset($get['order_code'])) {
            $detail = $detail->andWhere(['lottery_order.lottery_order_code' => $get['order_code']]);
        }
        if (isset($get["user_info"])) {
            $detail = $detail->andWhere(["or", ["lottery_order.cust_no" => $get['user_info']], ["u.user_name" => $get['user_info']], ["u.user_tel" => $get['user_info']]]);
        }
        if (isset($get["store_info"])) {
            $detail = $detail->andWhere(["or", ["s.cust_no" => $get['store_info']], ["s.store_name" => $get['store_info']], ["s.phone_num" => $get['store_info']], ["sd.consignee_name" => $get['store_info']]]);
        }
        if (isset($get["lottery_order_code"])) {
            $detail = $detail->andWhere(["like", "lottery_order.lottery_order_code", "%{$get['lottery_order_code']}%", false]);
        }
        if (isset($get["lottery_code"])) {
            if ($get['lottery_code'] == '3000') {
                $codeArr = ['3006', '3007', '3008', '3009', '3010', '3011'];
            } elseif ($get['lottery_code'] == '3100') {
                $codeArr = ['3001', '3002', '3003', '3004', '3005'];
            } elseif ($get['lottery_code'] == '5000') {
                $codeArr = ['5001', '5002', '5003', '5004', '5005', '5006'];
            } else {
                $codeArr = [$get['lottery_code']];
            }
            $detail = $detail->andWhere(['in', "lottery_order.lottery_id", $codeArr]);
        }
        if (isset($get["startdate"])) {
            $detail = $detail->andWhere([">", "lottery_order.create_time", $get["startdate"] . " 00:00:00"]);
        } else {
            $detail = $detail->andWhere([">", "lottery_order.create_time", date("Y-m-d", strtotime("-3 day")) . " 00:00:00"]);
        }
        if (isset($get["enddate"])) {
            $detail = $detail->andWhere(["<", "lottery_order.create_time", $get["enddate"] . " 23:59:59"]);
        } else {
            $detail = $detail->andWhere(["<", "lottery_order.create_time", date("Y-m-d") . " 23:59:59"]);
        }

        if (isset($get["deal_status"])) {
            $detail = $detail->andWhere(["lottery_order.deal_status" => $get["deal_status"]]);
        }
        if (isset($get["auto_type"])) {
            $detail = $detail->andWhere(["lottery_order.auto_type" => $get["auto_type"]]);
        }
        $detail = $detail->groupBy('lottery_order.lottery_order_id')->orderBy("lottery_order.create_time desc");
        $orderStatus = [
            "0" => "所有",
            "1" => "未支付",
            "2" => "处理中",
            "3" => "待开奖",
            "4" => "中奖",
            "5" => "未中奖",
            "6|9|10|11" => "出票失败"
        ];
        $data = new ActiveDataProvider([
            'query' => $detail,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['lottery_id'],
            ],
        ]);
        return $this->render('lists', ['data' => $data, "orderStatus" => $orderStatus, "get" => $get]);
    }

    public function actionDeal() {
        $get = \Yii::$app->request->get();
        $detail = (new Query())->select(["lottery_order.*", 's.store_name', 'u.user_tel', 'a.api_order_code', 'a.third_order_code', 'sum(d.win_amount) deal_win_amount'])
                ->from("lottery_order")
                ->innerJoin('api_order a', 'a.api_order_id = lottery_order.source_id')
                ->leftJoin('store as s', 's.store_code = lottery_order.store_no and s.user_id=lottery_order.store_id')
                ->leftJoin('user as u', 'u.cust_no = lottery_order.cust_no')
                ->leftJoin('deal_order d', 'd.order_id = lottery_order.lottery_order_id and lottery_order.status = 4')
                ->andWhere(['lottery_order.source' => 7, 'lottery_order.status' => 4])
                ->andWhere(['in', 'lottery_order.deal_status', ['1', '2']]);
        if (isset($get['api_order_code'])) {
            $detail = $detail->andWhere(['a.api_order_code' => $get['api_order_code']]);
        }
        if (isset($get['third_order_code'])) {
            $detail = $detail->andWhere(['a.third_order_code' => $get['third_order_code']]);
        }
        if (isset($get['order_code'])) {
            $detail = $detail->andWhere(['lottery_order.lottery_order_code' => $get['order_code']]);
        }
        if (isset($get["user_info"])) {
            $detail = $detail->andWhere(["or", ["lottery_order.cust_no" => $get['user_info']], ["u.user_name" => $get['user_info']], ["u.user_tel" => $get['user_info']]]);
        }
        if (isset($get["store_info"])) {
            $detail = $detail->andWhere(["or", ["s.cust_no" => $get['store_info']], ["s.store_name" => $get['store_info']], ["s.phone_num" => $get['store_info']], ["sd.consignee_name" => $get['store_info']]]);
        }
        if (isset($get["lottery_order_code"])) {
            $detail = $detail->andWhere(["like", "lottery_order.lottery_order_code", "%{$get['lottery_order_code']}%", false]);
        }
        if (isset($get["lottery_code"])) {
            if ($get['lottery_code'] == '3000') {
                $codeArr = ['3006', '3007', '3008', '3009', '3010', '3011'];
            } elseif ($get['lottery_code'] == '3100') {
                $codeArr = ['3001', '3002', '3003', '3004', '3005'];
            } elseif ($get['lottery_code'] == '5000') {
                $codeArr = ['5001', '5002', '5003', '5004', '5005', '5006'];
            } else {
                $codeArr = [$get['lottery_code']];
            }
            $detail = $detail->andWhere(['in', "lottery_order.lottery_id", $codeArr]);
        }
        if (isset($get["startdate"])) {
            $detail = $detail->andWhere([">", "lottery_order.create_time", $get["startdate"] . " 00:00:00"]);
        } else {
            $detail = $detail->andWhere([">", "lottery_order.create_time", date("Y-m-d", strtotime("-3 day")) . " 00:00:00"]);
        }
        if (isset($get["enddate"])) {
            $detail = $detail->andWhere(["<", "lottery_order.create_time", $get["enddate"] . " 23:59:59"]);
        } else {
            $detail = $detail->andWhere(["<", "lottery_order.create_time", date("Y-m-d") . " 23:59:59"]);
        }

        if (isset($get["deal_status"])) {
            $detail = $detail->andWhere(["lottery_order.deal_status" => $get["deal_status"]]);
        }
        if (isset($get["auto_type"])) {
            $detail = $detail->andWhere(["lottery_order.auto_type" => $get["auto_type"]]);
        }
        $detail = $detail->groupBy('lottery_order.lottery_order_id')->orderBy("lottery_order.create_time desc");
        $orderStatus = [
            "0" => "所有",
            "1" => "未支付",
            "2" => "处理中",
            "3" => "待开奖",
            "4" => "中奖",
            "5" => "未中奖",
            "6|9|10|11" => "出票失败"
        ];
        $data = new ActiveDataProvider([
            'query' => $detail,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['lottery_id'],
            ],
        ]);
        return $this->render('deal', ['data' => $data, "orderStatus" => $orderStatus, "get" => $get]);
    }

    public function actionDoAward() {
        $this->layout = false;
        if (\Yii::$app->request->isGet) {
            $request = \Yii::$app->request;
            $orderId = $request->get('orderId', '');
            if (empty($orderId)) {
                echo '参数错误';
                exit();
            }
            $orderData = LotteryOrder::find()->select(['lottery_order_id', 'win_amount', 'zmf_award_money'])->where(['source' => 7, 'status' => 4, 'lottery_order_id' => $orderId])->andWhere(['in', 'deal_status', [1, 2]])->asArray()->one();
            if (empty($orderData)) {
                echo '该订单有误！！请重新操作！！';
                exit();
            }
            return $this->render('do-award', ['data' => $orderData]);
        } else {
            $request = \Yii::$app->request;
            $orderIdArr = $request->post('orderIdArr', '');
            $pAwardArr = $request->post('pAwardArr', '');
            $errorData = [];
            if (empty($orderIdArr)) {
                return $this->jsonError(109, '请选择要派奖订单');
            }
            foreach ($orderIdArr as $val) {
                $ret = DoAward::doAwardThird($val, $pAwardArr);
                $errorData[] = $ret['data'];
            }
            return $this->jsonResult(600, '操作成功', $errorData);
        }
    }
    
    public function actionSzcRead() {
        $this->layout = false;
        $request = \Yii::$app->request;
        $orderId = $request->get('orderId', '');
        if(empty($orderId)) {
            echo '参数错误！';
            exit();
        }
        $field = ['lottery_name', 'lottery_order_code', 'play_name', 'periods', 'cust_no', 'end_time', 'bet_val', 'bet_money', 'count', 'bet_double', 'is_bet_add', 'win_amount', 'out_time', 
            'lottery_order.create_time bet_time', 'p.pay_no', 'p.outer_no', 'p.pay_name', 'p.way_name', 'p.pay_money', 'p.pay_time', 'lottery_order.status bet_status', 'p.discount_money', 'lr.lottery_numbers', 
            'lr.lottery_time', 'l.lottery_pic', 'lottery_order.lottery_id'];
        $orderData = LotteryOrder::find()->select($field)
                ->leftJoin('pay_record p', 'p.order_code = lottery_order.lottery_order_code')
                ->leftJoin('lottery l', 'l.lottery_code = lottery_order.lottery_id')
                ->leftJoin('lottery_record lr', 'lr.lottery_code = lottery_order.lottery_id and lr.periods = lottery_order.periods')
                ->where(['lottery_order_od' => $orderId])
                ->asArray()
                ->one();
        return $this->render('szc-read', ['data' => $orderData]);
        
    }

}
