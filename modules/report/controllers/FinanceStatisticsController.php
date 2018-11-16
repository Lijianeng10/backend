<?php

namespace app\modules\report\controllers;

use Yii;
use app\modules\common\models\PayRecord;
use yii\web\Controller;
use app\modules\common\models\Bussiness;
use yii\db\Expression;
use yii\base\Exception;
use app\modules\common\models\BussinessMonthStatistics;
use app\modules\common\models\BussinessDayStatistics;
use app\modules\common\services\ApiSysService;
use app\modules\common\helpers\Commonfun;
use app\modules\common\models\UserFunds;

class FinanceStatisticsController extends Controller {

    public function actionIndex() {
        $session = \Yii::$app->session;
//        $loginPort = $session["login_port"];
        $loginType = $session['type'];
//        $loginAdmin = $session["admin_name"];
//        $agentCode = $session['agent_code'];
        $channel = [];
        if ($loginType == 0) {
            $channel = Bussiness::find()->select(['name', 'cust_no'])->where(['status' => 1])->indexBy('cust_no')->asArray()->all();
        }
        return $this->render('index', ['loginType' => $loginType, 'channelData' => $channel]);
    }

    public function actionGetStatistic() {
        $session = \Yii::$app->session;
        $loginType = $session['type'];
        $loginAdmin = $session["admin_name"];
        $agentCode = $session['agent_code'];
        $request = \Yii::$app->request;
        $years = $request->post('years', date('Y'));
        $month = $request->post('months', date('m'));
        $tabType = $request->post('tabType', 1);
        $where = ['and'];
        $where1 = ['and'];
        if ($loginType == 1) {
            if ($agentCode != 'gl00015788') {
                $sourceNo = $agentCode;
            } else {
                $sourceNo = $loginAdmin;
            }
            $sourceId = Bussiness::find()->select(['cust_no'])->where(['cust_no' => $sourceNo])->asArray()->one();
            if (!empty($sourceId)) {
                $sourceNo = $sourceId['cust_no'];
            } else {
                return $this->jsonError(109, '无效商户！！请联系客服');
            }
        } elseif($loginType == 0) {
            $sourceNo = $request->post('sourceNo', '');
            if (empty($sourceNo)) {
                return $this->jsonResult(600, '请选择要统计渠道方', false);
            }
        } else {
            return $this->jsonError(109, '无效管理员');
        }
        $where[] = ['cust_no' => $sourceNo];
        $where1[] = ['pay_record.cust_no' => $sourceNo];
        $postDate = $years . '-' . $month;
        $monthStatis = BussinessMonthStatistics::find()->select(['begin_funds', 'end_funds', 'status', 'deal_status', 'month_tc', 'grant_tc', 'grant_time', 'grant_type'])->where(['cust_no' => $sourceNo, 'statistics_month' => $postDate])->asArray()->one();
        if (empty($monthStatis)) {
            return $this->jsonResult(600, '暂无此项统计数据', true);
        }
        $groupBy = new Expression("DATE_FORMAT(pay_time, '%Y-%m-%d')");
        $orderBy = new Expression("DATE_FORMAT(pay_time, '%Y-%m-%d')");
        $str = "DATE_FORMAT(pay_time, '%Y-%m-%d') statistics_date";
        if ($tabType == 0) {
            if ($monthStatis['status'] == 2) {
                return $this->jsonError(109, '该月份已结算');
            }
            $where[] = new Expression("DATE_FORMAT(pay_time,'%Y-%m') = '{$years}-{$month}'");

            $financeStatis = PayRecord::find()->select([$str, new Expression('sum(case when pay_type = 3 and status = 1 then pay_money else 0 end) as cz_amount'), new Expression('sum(case when pay_type = 15 and status = 1 then pay_money else 0 end) as award_amount'),
                        new Expression('sum(case when pay_type = 4 and status = 1 then pay_money else 0 end) as tx_amount'), new Expression('sum(case when pay_type = 24 and status = 1 then pay_money else 0 end) as tc_amount'),
                        new Expression('sum(case when pay_type = 15 and status = 1 then (select l.win_amount from lottery_order l where l.lottery_order_code = pay_record.order_code) else 0 end) as win_amount'),
                        new Expression('sum(case when pay_type = 20 and status = 1 then pay_money else 0 end) as order_amount')])
                    ->where($where)
                    ->groupBy($groupBy)
                    ->orderBy($orderBy)
                    ->asArray()
                    ->all();
            $tzStatis = PayRecord::find()->select([$str, new Expression('sum(case when pay_record.pay_type = 1 and pay_record.status = 1 and pay_record.pay_way = 3 then pay_record.pay_money else 0 end) as tz_amount'),
                        new Expression('sum(case when pay_record.pay_type = 1 and pay_record.status = 1 and l.status = 4 and l.deal_status = 1 then l.win_amount else 0 end) as stay_award_money'),
                        new Expression('sum(case when pay_record.pay_type = 1 and pay_record.status = 1 and l.status = 4 and l.deal_status = 3 then l.award_amount else 0 end) as already_award_money'),
                        new Expression('sum(case when pay_record.pay_type = 1 and pay_record.status = 1 and l.status = 4 then l.win_amount else 0 end) as tz_win_money')])
                    ->innerJoin('lottery_order l', 'l.lottery_order_code = pay_record.order_code')
                    ->where(['in', 'l.status', [2, 3, 4, 5, 11]])
                    ->andWhere($where1)
                    ->andWhere(new Expression("DATE_FORMAT(pay_time,'%Y-%m') = '{$years}-{$month}'"))
                    ->groupBy($groupBy)
                    ->orderBy($orderBy)
                    ->indexBy('statistics_date')
                    ->asArray()
                    ->all();
            $orderByArr = [];
            foreach ($financeStatis as $key => &$val) {
                $val['tz_amount'] = $tzStatis[$val['statistics_date']]['tz_amount'];
                $val['tz_win_money'] = $tzStatis[$val['statistics_date']]['tz_win_money'];
                $val['stay_award_money'] = $tzStatis[$val['statistics_date']]['stay_award_money'];
                $val['already_award_money'] = $tzStatis[$val['statistics_date']]['already_award_money'];
                $val['sr_amount'] = bcadd(bcadd($val['cz_amount'], $val['award_amount'], 2), $val['tc_amount'], 2);
                $val['zc_amount'] = bcadd($val['tx_amount'], $val['tz_amount'], 2);
                if ($key == 0) {
                    $val['begin_amount'] = $monthStatis['begin_funds'];
                    $val['ye_amount'] = bcsub(bcadd(bcadd(bcadd($val['cz_amount'], $val['award_amount'], 2), $val['tc_amount'], 2), $val['begin_amount'], 2), bcadd($val['tx_amount'], $val['tz_amount'], 2), 2);
                } else {
                    $val['begin_amount'] = $financeStatis[$key - 1]['ye_amount'];
                    $val['ye_amount'] = bcsub(bcadd(bcadd(bcadd($val['cz_amount'], $val['award_amount'], 2), $val['tc_amount'], 2), $val['begin_amount'], 2), bcadd($val['tx_amount'], $val['tz_amount'], 2), 2);
                }
                $orderByArr[] = $val['statistics_date'];
            }
            array_multisort($orderByArr, SORT_DESC, $financeStatis);
        } else {
            $field = ['statistics_date', 'begin_amount', 'cz_amount', 'award_amount', 'tc_amount', 'tz_amount', 'tx_amount', 'ye_amount', 'sr_amount', 'zc_amount', 'win_amount', 'order_amount'];
            $tzStatis = PayRecord::find()->select([$str, new Expression('sum(case when pay_record.pay_type = 1 and pay_record.status = 1 and l.status = 4 and l.deal_status = 1 then l.win_amount else 0 end) as stay_award_money'),
                        new Expression('sum(case when pay_record.pay_type = 1 and pay_record.status = 1 and l.status = 4 and l.deal_status = 3 then l.award_amount else 0 end) as already_award_money'),
                        new Expression('sum(case when pay_record.pay_type = 1 and pay_record.status = 1 and l.status = 4 then l.win_amount else 0 end) as tz_win_money')])
                    ->innerJoin('lottery_order l', 'l.lottery_order_code = pay_record.order_code')
                    ->where(['in', 'l.status', [2, 3, 4, 5, 11]])
                    ->andWhere($where1)
                    ->andWhere(new Expression("DATE_FORMAT(pay_time,'%Y-%m') = '{$years}-{$month}'"))
                    ->groupBy($groupBy)
                    ->orderBy($orderBy)
                    ->indexBy('statistics_date')
                    ->asArray()
                    ->all();
            $financeStatis = BussinessDayStatistics::find()->select($field)->where(['cust_no' => $sourceNo])->andWhere(new Expression("DATE_FORMAT(statistics_date,'%Y-%m') = '{$years}-{$month}'"))->orderBy('statistics_date desc')->asArray()->all();
            foreach ($financeStatis as &$value) {
                if(isset($tzStatis[$value['statistics_date']])){
                    $value['tz_win_money'] = $tzStatis[$value['statistics_date']]['tz_win_money'];
                    $value['stay_award_money'] = $tzStatis[$value['statistics_date']]['stay_award_money'];
                    $value['already_award_money'] = $tzStatis[$value['statistics_date']]['already_award_money'];
                } else {
                    $value['tz_win_money'] = 0;
                    $value['stay_award_money'] = 0;
                    $value['already_award_money'] = 0;
                }
                
            }
        }
        return $this->jsonResult(600, '获取成功', ['data' => $financeStatis, 'tcData' => $monthStatis]);
    }

    public function actionDoSettle() {
        $request = \Yii::$app->request;
        $years = $request->post('years', '');
        $month = $request->post('months', '');
        $nowDate = date('Y-m');
        $postDate = $years . '-' . $month;
        if ($postDate >= $nowDate) {
            return $this->jsonError(109, '当前月不可结算');
        }
        $session = \Yii::$app->session;
        $loginType = $session['type'];
        $where = ['and'];
        $where1 = ['and'];
        $sourceNo = $request->post('sourceNo', '');
        if (empty($sourceNo)) {
            return $this->jsonError(109, '请选择要结算渠道方');
        }
        if ($loginType == 1) {
            return $this->jsonError(109, '该账户不具备该项权限');
        }
        $where[] = ['cust_no' => $sourceNo];
        $where1[] = ['pay_record.cust_no' => $sourceNo];
        $monthStatis = BussinessMonthStatistics::findOne(['cust_no' => $sourceNo, 'statistics_month' => $postDate]);
        $agoFinance = PayRecord::find()->select([new Expression('sum(case when pay_type = 3 and status = 1 then pay_money else 0 end) as cz_amount'), new Expression('sum(case when pay_type = 15 and status = 1 then pay_money else 0 end) as award_amount'),
                    new Expression('sum(case when pay_type = 4 and status = 1 then pay_money else 0 end) as tx_amount'), new Expression('sum(case when pay_type = 24 and status = 1 then pay_money else 0 end) as tc_amount'),
                    new Expression('sum(case when pay_type = 15 and status = 1 then (select l.win_amount from lottery_order l where l.lottery_order_code = pay_record.order_code) else 0 end) as win_amount'),
                    new Expression('sum(case when pay_type = 20 and status = 1 then pay_money else 0 end) order_amount')])
                ->where($where)
                ->andWhere(new Expression("DATE_FORMAT(pay_time,'%Y-%m') < '{$postDate}'"))
                ->asArray()
                ->one();
        if (empty($monthStatis) && !empty($agoFinance)) {
            return $this->jsonError(109, '该月份暂未有期初值！！请确认之前月已结算');
        }
        $beforeStatis = BussinessMonthStatistics::find()->select(['status', 'deal_status'])->where(['cust_no' => $sourceNo])->andWhere(['<', 'statistics_month', $postDate])->asArray()->all();
        if (!empty($beforeStatis)) {
            $dealStatus = array_column($beforeStatis, 'deal_status');
            $status = array_column($beforeStatis, 'status');
            if (in_array(1, $status)) {
                return $this->jsonError(109, '暂不可结算！请确认之前月已结算');
            }
            if (in_array(1, $dealStatus)) {
                return $this->jsonError(109, '暂不可结算！之前月还有提成未发放');
            }
        }
        if (empty($monthStatis)) {
            $monthStatis = new BussinessMonthStatistics();
            $monthStatis->cust_no = $sourceNo;
            $monthStatis->statistics_month = $postDate;
            $monthStatis->begin_funds = 0;
            $monthStatis->status = 1;
            $monthStatis->create_time = date('Y-m-d');
        }

        if ($monthStatis->status == 2) {
            return $this->jsonError(109, '该月份已结算！请勿重复结算！');
        }
        $where[] = new Expression("DATE_FORMAT(pay_time,'%Y-%m') = '{$postDate}'");
        $groupBy = new Expression("DATE_FORMAT(pay_time, '%Y-%m-%d')");
        $str = "DATE_FORMAT(pay_time, '%Y-%m') statistics_date";
        $str1 = "DATE_FORMAT(pay_time, '%Y-%m-%d') statistics_date";
        $financeStatis = PayRecord::find()->select([$str, new Expression('sum(case when pay_type = 3 and status = 1 then pay_money else 0 end) as cz_amount'), new Expression('sum(case when pay_type = 15 and status = 1 then pay_money else 0 end) as award_amount'),
                    new Expression('sum(case when pay_type = 4 and status = 1 then pay_money else 0 end) as tx_amount'), new Expression('sum(case when pay_type = 24 and status = 1 then pay_money else 0 end) as tc_amount'),
                    new Expression('sum(case when pay_type = 15 and status = 1 then (select l.win_amount from lottery_order l where l.lottery_order_code = pay_record.order_code) else 0 end) as win_amount'),
                    new Expression('sum(case when pay_type = 20 and status = 1 then pay_money else 0 end) order_amount')])
                ->where($where)
                ->asArray()
                ->one();
        $tzStatis = PayRecord::find()->select([$str, new Expression('sum(case when pay_record.pay_type = 1 and pay_record.status = 1 and pay_record.pay_way = 3 then pay_record.pay_money else 0 end) as tz_amount'),
                    new Expression('sum(case when l.status = 2 or l.status = 11 then 1 else 0 end) as stay_deal')])
                ->innerJoin('lottery_order l', 'l.lottery_order_code = pay_record.order_code')
                ->where(['in', 'l.status', [2, 3, 4, 5, 11]])
                ->andWhere($where1)
                ->andWhere(new Expression("DATE_FORMAT(pay_time,'%Y-%m') = '{$postDate}'"))
                ->indexBy('statistics_date')
                ->asArray()
                ->one();
        if ($tzStatis['stay_deal'] != 0) {
            return $this->jsonError(109, '该月份还存在未清算投注单！！不可结算');
        }
        $endFunds = bcadd(bcsub(bcadd(bcadd(bcadd($financeStatis['cz_amount'], $financeStatis['award_amount'], 2), $monthStatis->begin_funds, 2), $financeStatis['tc_amount'], 2), bcadd($financeStatis['tx_amount'], $tzStatis['tz_amount'], 2), 2), $financeStatis['order_amount'], 2);
        if ($endFunds < 0) {
            return $this->jsonError(109, '该月份的财务有问题！！请清查');
        }
        $dayStatis = PayRecord::find()->select([$str1, new Expression('sum(case when pay_type = 3 and status = 1 then pay_money else 0 end) as cz_amount'), new Expression('sum(case when pay_type = 15 and status = 1 then pay_money else 0 end) as award_amount'),
                    new Expression('sum(case when pay_type = 4 and status = 1 then pay_money else 0 end) as tx_amount'), new Expression('sum(case when pay_type = 24 and status = 1 then pay_money else 0 end) as tc_amount'),
                    new Expression('sum(case when pay_type = 15 and status = 1 then (select l.win_amount from lottery_order l where l.lottery_order_code = pay_record.order_code) else 0 end) as win_amount'),
                    new Expression('sum(case when pay_type = 20 and status = 1 then pay_money else 0 end) order_amount')])
                ->where($where)
                ->groupBy($groupBy)
                ->asArray()
                ->all();
        $tzDayStatis = PayRecord::find()->select([$str1, new Expression('sum(case when pay_record.pay_type = 1 and pay_record.status = 1 and pay_record.pay_way = 3 then pay_record.pay_money else 0 end) as tz_amount')])
                ->innerJoin('lottery_order l', 'l.lottery_order_code = pay_record.order_code')
                ->where(['in', 'l.status', [2, 3, 4, 5, 11]])
                ->andWhere($where1)
                ->andWhere(new Expression("DATE_FORMAT(pay_time,'%Y-%m') = '{$years}-{$month}'"))
                ->groupBy($groupBy)
                ->indexBy('statistics_date')
                ->asArray()
                ->all();
        $bd = \Yii::$app->db;
        $trans = $bd->beginTransaction();
        try {
            $monthStatis->sum_cz_amount = $financeStatis['cz_amount'];
            $monthStatis->sum_tc_amount = $financeStatis['tc_amount'];
            $monthStatis->sum_tx_amount = $financeStatis['tx_amount'];
            $monthStatis->sum_tz_amount = $tzStatis['tz_amount'];
            $monthStatis->sum_award_amount = $financeStatis['award_amount'];
            $monthStatis->sum_win_amount = $financeStatis['win_amount'];
            $monthStatis->sum_order_amount = $financeStatis['order_amount'];
            $monthStatis->end_funds = $endFunds;
            $monthStatis->month_tc = bcmul($tzStatis['tz_amount'], 0.63, 2);
            $monthStatis->status = 2;
            $monthStatis->modify_time = date('Y-m-d H:i:s');
            if (!$monthStatis->save()) {
                throw new Exception('结算失败!');
//                return $this->jsonError(109, '结算失败!');
            }
            $nextMonth = new BussinessMonthStatistics();
            $nextMonth->cust_no = $sourceNo;
            $nextMonth->statistics_month = date('Y-m', strtotime('+1 month', strtotime($postDate)));
            $nextMonth->begin_funds = $endFunds;
            $nextMonth->create_time = date('Y-m-d H:i:s');
            if (!$nextMonth->save()) {
                throw new Exception('下个月的期初生成失败!');
//                return $this->jsonError(109, '下个月的期初生成失败');
            }
            $key = ['cust_no', 'statistics_date', 'begin_amount', 'cz_amount', 'award_amount', 'win_amount', 'tc_amount', 'sr_amount', 'tz_amount', 'tx_amount', 'zc_amount', 'ye_amount', 'create_time', 'order_amount'];
            $info = [];
            foreach ($dayStatis as $k => &$val) {
                $val['tz_amount'] = $tzDayStatis[$val['statistics_date']]['tz_amount'];
                $val['sr_amount'] = bcadd(bcadd($val['cz_amount'], $val['award_amount'], 2), $val['tc_amount'], 2);
                $val['zc_amount'] = bcadd($val['tx_amount'], $val['tz_amount'], 2);
                if ($k == 0) {
                    $val['begin_amount'] = $monthStatis['begin_funds'];
                    $val['ye_amount'] = bcadd(bcsub(bcadd(bcadd(bcadd($val['cz_amount'], $val['award_amount'], 2), $val['tc_amount'], 2), $val['begin_amount'], 2), bcadd($val['tx_amount'], $val['tz_amount'], 2), 2), $val['order_amount'], 2);
                } else {
                    $val['begin_amount'] = $dayStatis[$k - 1]['ye_amount'];
                    $val['ye_amount'] = bcadd(bcsub(bcadd(bcadd(bcadd($val['cz_amount'], $val['award_amount'], 2), $val['tc_amount'], 2), $val['begin_amount'], 2), bcadd($val['tx_amount'], $val['tz_amount'], 2), 2), $val['order_amount'], 2);
                }
                $info[] = [$sourceNo, $val['statistics_date'], $val['begin_amount'], $val['cz_amount'], $val['award_amount'], $val['win_amount'], $val['tc_amount'], $val['sr_amount'], $val['tz_amount'], $val['tx_amount'], $val['zc_amount'], $val['ye_amount'], date('Y-m-d H:i:s'), $val['order_amount']];
            }
            $ret = $bd->createCommand()->batchInsert('bussiness_day_statistics', $key, $info)->execute();
            if ($ret === false) {
                throw new Exception('日统计写入失败');
            }
            $trans->commit();
            return $this->jsonResult(600, '结算成功', true);
        } catch (Exception $ex) {
            $trans->rollBack();
            return $this->jsonError(109, $ex->getMessage());
        }
    }

    public function actionGrantTc() {
        $request = \Yii::$app->request;
        $years = $request->post('years', '');
        $month = $request->post('months', '');
        $tcAmount = $request->post('tcAmount', '');
        $grantType = $request->post('grantType', '');
        $nowDate = date('Y-m');
        $postDate = $years . '-' . $month;
        if ($postDate >= $nowDate) {
            return $this->jsonError(109, '当前月不可发放');
        }
        if (empty($grantType)) {
            return $this->jsonError(109, '请选择发放方式');
        }
        $session = \Yii::$app->session;
        $loginType = $session['type'];
        $sourceNo = $request->post('sourceNo', '');
        if (empty($sourceNo)) {
            return $this->jsonError(109, '请选择要结算渠道方');
        }
        if ($tcAmount == '') {
            return $this->jsonError(109, '请确认要发放的提成金额');
        }
        if ($loginType == 1) {
            return $this->jsonError(109, '该账户不具备该项权限');
        }
        $monthStatis = BussinessMonthStatistics::findOne(['cust_no' => $sourceNo, 'statistics_month' => $postDate]);
        if (empty($monthStatis)) {
            return $this->jsonError(109, '该月份还未结算');
        }
        if ($monthStatis->status != 2) {
            return $this->jsonError(109, '该月份还未结算');
        }
        if ($monthStatis->deal_status != 1) {
            return $this->jsonError(109, '该月份已发放过提成');
        }

        $db = \Yii::$app->db;
        $trans = $db->beginTransaction();
        try {
            $monthStatis->deal_status = 2;
            $monthStatis->grant_tc = $tcAmount;
            $monthStatis->grant_time = date('Y-m-d');
            $monthStatis->grant_type = $grantType;
            $monthStatis->opt_name = $session['admin_name'];
            $monthStatis->modify_time = date('Y-m-d H:i:s');
            if (!$monthStatis->save()) {
                throw new Exception('提成发放失败1');
            }
            if ($grantType == 1) {
                $update = ['all_funds' => new Expression('all_funds+' . $tcAmount), 'able_funds' => new Expression('able_funds+' . $tcAmount), 'modify_time' => date('Y-m-d H:i:s')];
                $where = ['cust_no' => $monthStatis->cust_no];
                $userFund = UserFunds::updateAll($update, $where);
                if ($userFund === false) {
                    throw new Exception('提交失败, 余额更新失败');
                }

                $funds = UserFunds::find()->select(['all_funds'])->where(['cust_no' => $monthStatis->cust_no])->asArray()->one();
                $payRecord = new PayRecord;
                $payRecord->order_code = Commonfun::getCode('PAY', 'L');
                $payRecord->cust_no = $monthStatis->cust_no;
                $payRecord->cust_type = 1;
                $payRecord->pay_no = Commonfun::getCode('PAY', 'L');
                $payRecord->pay_pre_money = $monthStatis->month_tc;
                $payRecord->pay_money = $tcAmount;
                $payRecord->pay_name = '余额';
                $payRecord->way_name = '余额';
                $payRecord->way_type = 'YE';
                $payRecord->pay_way = 3;
                $payRecord->pay_type_name = '渠道佣金';
                $payRecord->pay_type = 24;
                $payRecord->body = '渠道佣金';
                $payRecord->status = 1;
                $payRecord->pay_time = date('Y-m-d H:i:s');
                $payRecord->balance = $funds['all_funds'];
                $payRecord->create_time = date('Y-m-d H:i:s');
                $payRecord->modify_time = date('Y-m-d H:i:s');
                if (!$payRecord->save()) {
                    throw new Exception('提交失败,交易明细写入失败');
                }
            }
            $trans->commit();
            if ($grantType == 1) {
                ApiSysService::payRecord($payRecord->pay_record_id);
            }
            return $this->jsonResult(600, '发放成功');
        } catch (Exception $ex) {
            $trans->rollBack();
            return $this->jsonResult(109, $ex->getMessage());
        }
    }

}
