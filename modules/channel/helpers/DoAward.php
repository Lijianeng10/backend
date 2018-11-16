<?php

namespace app\modules\channel\helpers;

use Yii;
use yii\base\Exception;
use yii\db\Expression;
use app\modules\common\models\UserFunds;
use app\modules\common\models\PayRecord;
use app\modules\common\services\ApiSysService;
use app\modules\common\helpers\Commonfun;
use app\modules\common\models\LotteryOrder;

class DoAward {

    public static function doAwardThird($orderId, $pAwardArr) {
        $field = ['lottery_order.lottery_order_id', 'lottery_order.lottery_order_code', 'sum(d.win_amount) deal_win_amount', 'lottery_order.win_amount', 'lottery_order.zmf_award_money',
            'lottery_order.deal_status', 'lottery_order.status', 'lottery_order.cust_no', 'lottery_order.lottery_type', 'lottery_order.lottery_id', 'lottery_order.user_id'];
        $orderData = LotteryOrder::find()->select($field)
                ->innerJoin('api_order a', 'a.api_order_id = lottery_order.source_id')
                ->leftJoin('deal_order d', 'd.order_id = lottery_order.lottery_order_id and lottery_order.status = 4')
                ->andWhere(['lottery_order.source' => 7, 'lottery_order.status' => 4, 'lottery_order.lottery_order_id' => $orderId])
                ->andWhere(['in', 'lottery_order.deal_status', [1, 2]])
                ->groupBy('lottery_order.lottery_order_id')
                ->asArray()
                ->one();
        $db = \Yii::$app->db;
//            $errorArr = [];
//            foreach ($orderData as $orderData) {
        $trans = $db->beginTransaction();
        try {
            $thirdWinAmount = ApiSysService::getWinAamount($orderData['lottery_order_code']);
            if ($thirdWinAmount['code'] != 600) {
                throw new Exception('获取第三方中奖金额失败, 请稍后再试！！');
            }
            if (bccomp($thirdWinAmount['win_amount'], $orderData['win_amount'], 2) != 0) {
                throw new Exception('订单:' . $orderData['lottery_order_code'] . '中奖金额与第三方不匹配<br/>');
            }
            $winAmount = 0;
            if(empty($orderData['zmf_award_money'])) {
                throw new Exception('获取出票方中奖金额失败, 请稍后再试');
            }
            if (!empty($pAwardArr)) {
                $winAmount = $pAwardArr;
            } else {
                $winAmount = $orderData['zmf_award_money'];
            }
            
            if ($orderData['lottery_type'] == 1 && $winAmount > 10000) {
                throw new Exception('订单:' . $orderData['lottery_order_code'] . '中奖金额大于10000,请核对再派奖<br/>');
            }
            $orderUpdate = ['award_amount' => $winAmount, 'deal_status' => 3, 'award_time' => date('Y-m-d H:i:s'), 'modify_time' => date('Y-m-d H:i:s')];
            $orderWhere = ['lottery_order_id' => $orderData['lottery_order_id'], 'deal_status' => $orderData['deal_status']];
            $order = LotteryOrder::updateAll($orderUpdate, $orderWhere);
            if ($order === false) {
                throw new Exception('订单:' . $orderData['lottery_order_code'] . '派奖失败, 订单状态更新失败<br/>');
            }
            $fundUpdate = ['all_funds' => new Expression('all_funds+' . $winAmount), 'able_funds' => new Expression('able_funds+' . $winAmount), 'modify_time' => date('Y-m-d H:i:s')];
            $fundWhere = ['user_id' => $orderData['user_id'], 'cust_no' => $orderData['cust_no']];
            $userFund = UserFunds::updateAll($fundUpdate, $fundWhere);
            if ($userFund === false) {
                throw new Exception('订单:' . $orderData['lottery_order_code'] . '派奖失败, 余额更新失败<br/>');
            }
            $funds = UserFunds::find()->select(['all_funds'])->where(['cust_no' => $orderData['cust_no']])->asArray()->one();
            $userPay = new PayRecord();
            $userPay->order_code = $orderData['lottery_order_code'];
            $userPay->cust_no = $orderData['cust_no'];
            $userPay->cust_type = 1;
            $userPay->pay_no = Commonfun::getCode('PAY', 'L');
            $userPay->pay_pre_money = $winAmount;
            $userPay->pay_money = $winAmount;
            $userPay->pay_name = '余额';
            $userPay->way_name = '余额';
            $userPay->way_type = 'YE';
            $userPay->pay_way = 3;
            $userPay->pay_type_name = '奖金';
            $userPay->pay_type = 15;
            $userPay->body = '奖金';
            $userPay->status = 1;
            $userPay->balance = $funds['all_funds'];
            $userPay->pay_time = date('Y-m-d H:i:s');
            $userPay->create_time = date('Y-m-d H:i:s');
            $userPay->modify_time = date('Y-m-d H:i:s');
            if (!$userPay->save()) {
                throw new Exception('订单:' . $orderData['lottery_order_code'] . '派奖失败, 交易处理明细写入失败<br/>');
            }
            $trans->commit();
            ApiSysService::payRecord($userPay->pay_record_id);
            return ['code' => 600, 'msg' => '成功', 'data' => '订单:' . $orderData['lottery_order_code'] . '派奖成功<br/>'];
//                    $errorArr[] = ;
        } catch (Exception $ex) {
            $trans->rollBack();
//                    $errorArr[] = $ex->getMessage();
            return ['code' => 109, 'msg' => '失败', 'data' => $ex->getMessage()];
        }
//            }
//            return $this->jsonResult(600, '操作成功', $errorArr);
    }

}
