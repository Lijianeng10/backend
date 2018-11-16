<?php

namespace app\modules\report\controllers;

use yii\web\Controller;
use app\modules\agents\models\Agents;
use app\modules\common\models\LotteryOrder;
use yii\db\Expression;
use app\modules\common\models\Lottery;

class OrderStatisticsController extends Controller {

    public function actionIndex() {
        $session = \Yii::$app->session;
//        $loginPort = $session["login_port"];
        $loginType = $session['type'];
        $loginAdmin = $session["admin_name"];
        $agentCode = $session['agent_code'];
        $request = \Yii::$app->request;
        $startDate = date('Y-m-d', strtotime($request->post('start_date', date('Y-m-d'))));
        $endDate = date('Y-m-d', strtotime($request->post('end_date', date('Y-m-d'))));
        $years = $request->post('years', date('Y'));
//        $month = $request->post('months', date('m'));
        $StatisticsType = $request->post('infoType', 1);
        $lotteryCode = $request->post('lotteryCode', 0);
        $tabType = $request->post('tabType', 1);
        $where = ['and', ['in', 'status', [3, 4, 5]]];
        $str2 = '';
        if ($tabType == 1) {
            if ($StatisticsType == 1) {
                $where[] = new Expression("DATE_FORMAT(out_time,'%Y-%m-%d') >= '{$startDate}'");
                $where[] = new Expression("DATE_FORMAT(out_time, '%Y-%m-%d') <= '{$endDate}'");
                $groupBy = new Expression("DATE_FORMAT(out_time, '%Y-%m-%d')");
                $orderBy = new Expression("DATE_FORMAT(out_time, '%Y-%m-%d') desc");
                $str = "DATE_FORMAT(out_time, '%Y-%m-%d') statistics_date";
            } else {
                $where[] = new Expression("DATE_FORMAT(create_time,'%Y-%m-%d') >= '{$startDate}'");
                $where[] = new Expression("DATE_FORMAT(create_time, '%Y-%m-%d') <= '{$endDate}'");
                $groupBy = new Expression("DATE_FORMAT(create_time, '%Y-%m-%d')");
                $orderBy = new Expression("DATE_FORMAT(create_time, '%Y-%m-%d') desc");
                $str = "DATE_FORMAT(create_time, '%Y-%m-%d') statistics_date";
            }
        } elseif ($tabType == 2) {
            if ($StatisticsType == 1) {
                $where[] = new Expression("DATE_FORMAT(out_time,'%Y') = '{$years}'");
//                $where[] = new Expression("DATE_FORMAT(out_time, '%Y') <= '{$years}'");
                $groupBy = new Expression("DATE_FORMAT(out_time, '%Y-%m')");
                $orderBy = new Expression("DATE_FORMAT(out_time, '%Y-%m') desc");
                $str = "DATE_FORMAT(out_time, '%Y-%m') statistics_date";
            } else {
                $where[] = new Expression("DATE_FORMAT(create_time,'%Y') = '{$years}'");
//                $where[] = new Expression("DATE_FORMAT(create_time, '%Y') <= '{$years}'");
                $groupBy = new Expression("DATE_FORMAT(create_time, '%Y-%m')");
                $orderBy = new Expression("DATE_FORMAT(create_time, '%Y-%m')  desc");
                $str = "DATE_FORMAT(create_time, '%Y-%m') statistics_date";
            }
        } elseif ($tabType == 3) {
            if (!empty($lotteryCode)) {
                switch ($lotteryCode) {
                    case 1001:
                    case 1002:
                    case 1003:
                    case 2001:
                    case 2002:
                    case 2003:
                    case 2004:
                    case 2005:
                    case 2006:
                    case 2007:
                    case 2008:
                    case 2010:
                    case 4001:
                    case 4002:
                        $where[] = ['lottery_id' => $lotteryCode];
                        break;
                    case 3000:
                        $where[] = ['lottery_type' => 2];
                        break;
                    case 3100:
                        $where[] = ['lottery_type' => 4];
                        break;
                    case 5000:
                        $where[] = ['lottery_type' => 5];
                        break;
                    case 3300:
                        $where[] = ['lottery_type' => 6];
                        break;
                    default :
                        return $this->jsonError(109, '该彩种暂不支持！');
                }
                if ($StatisticsType == 1) {
                    $where[] = new Expression("DATE_FORMAT(out_time,'%Y-%m-%d') >= '{$startDate}'");
                    $where[] = new Expression("DATE_FORMAT(out_time, '%Y-%m-%d') <= '{$endDate}'");
                    $groupBy = new Expression("DATE_FORMAT(out_time, '%Y-%m-%d')");
                    $orderBy = new Expression("DATE_FORMAT(out_time, '%Y-%m-%d')");
                    $str = "DATE_FORMAT(out_time, '%Y-%m-%d') statistics_date";
                } else {
                    $where[] = new Expression("DATE_FORMAT(create_time,'%Y-%m-%d') >= '{$startDate}'");
                    $where[] = new Expression("DATE_FORMAT(create_time, '%Y-%m-%d') <= '{$endDate}'");
                    $groupBy = new Expression("DATE_FORMAT(create_time, '%Y-%m-%d')");
                    $orderBy = new Expression("DATE_FORMAT(create_time, '%Y-%m-%d') desc");
                    $str = "DATE_FORMAT(create_time, '%Y-%m-%d') statistics_date";
                }
            } else {
                if ($StatisticsType == 1) {
                    $where[] = new Expression("DATE_FORMAT(out_time,'%Y-%m-%d') >= '{$startDate}'");
                    $where[] = new Expression("DATE_FORMAT(out_time, '%Y-%m-%d') <= '{$endDate}'");
                } else {
                    $where[] = new Expression("DATE_FORMAT(create_time,'%Y-%m-%d') >= '{$startDate}'");
                    $where[] = new Expression("DATE_FORMAT(create_time, '%Y-%m-%d') <= '{$endDate}'");
                }
                $groupBy = 'lottery_id';
                $orderBy = 'lottery_id';
                $str = "lottery_name statistics_date";
            }
        }

        if ($loginType == 1) {
            if ($agentCode != 'gl00015788') {
                $sourceNo = $agentCode;
            } else {
                $sourceNo = $loginAdmin;
            }
            $sourceId = Agents::find()->select(['agents_id'])->where(['agents_account' => $sourceNo])->asArray()->one();
            if (!empty($sourceId)) {
                $sourceNo = $sourceId['agents_id'];
            }
            $where[] = ['or', ['order_platform' => 2, 'agent_id' => $sourceNo], ['order_platform' => 3, 'cust_no' => $sourceNo]];
        } else {
            $where[] = ['order_platform' => 1];
        }
        $orderStatis = LotteryOrder::find()->select([$str, 'lottery_type', 'count(lottery_order_id) order_count', 'sum(bet_money) sum_money', new Expression('sum(case when status = 3 then 1 else 0 end) as stay_open'),
                    new Expression('sum(case when status = 4 and deal_status = 1 then 1 else 0 end) as stay_award'), new Expression('IFNULL(sum(win_amount),0) as sum_win_amount'), new Expression('IFNULL(sum(award_amount),0) as sum_win'),
                    new Expression('sum(case when status = 2 or status = 11 then 1 else 0 end) stay_deal')])
                ->where($where)
                ->groupBy($groupBy)
                ->orderBy($orderBy)
//                ->createCommand()->getRawSql();
                ->asArray()
                ->all();
//        print_r($orderStatis);die;
        if (empty($lotteryCode) && $tabType == 3) {
            $lotteryType = array_unique(array_column($orderStatis, 'lottery_type'));
            if (in_array(2, $lotteryType)) {
                $footStatis = ['statistics_date' => '竞彩足球', 'order_count' => 0, 'sum_money' => 0, 'stay_open' => 0, 'stay_award' => 0, 'sum_win_amount' => 0, 'sum_win' => 0, 'stay_deal' => 0];
            }
            if (in_array(4, $lotteryType)) {
                $basketStatis = ['statistics_date' => '竞彩篮球', 'order_count' => 0, 'sum_money' => 0, 'stay_open' => 0, 'stay_award' => 0, 'sum_win_amount' => 0, 'sum_win' => 0, 'stay_deal' => 0];
            }
            if (in_array(5, $lotteryType)) {
                $bdStatis = ['statistics_date' => '北京单关', 'order_count' => 0, 'sum_money' => 0, 'stay_open' => 0, 'stay_award' => 0, 'sum_win_amount' => 0, 'sum_win' => 0, 'stay_deal' => 0];
            } 
            if (in_array(6, $lotteryType)) {
                $wcupStatis = ['statistics_date' => '冠亚军竞猜', 'order_count' => 0, 'sum_money' => 0, 'stay_open' => 0, 'stay_award' => 0, 'sum_win_amount' => 0, 'sum_win' => 0, 'stay_deal' => 0];
            }
            foreach ($orderStatis as $key => $val) {
                if ($val['lottery_type'] == 2) {
                    $footStatis['order_count'] += $val['order_count'];
                    $footStatis['sum_money'] += $val['sum_money'];
                    $footStatis['stay_open'] += $val['stay_open'];
                    $footStatis['stay_award'] += $val['stay_award'];
                    $footStatis['sum_win_amount'] += round($val['sum_win_amount'], 2);
                    $footStatis['sum_win'] += round($val['sum_win'], 2);
                    $footStatis['stay_deal'] += $val['stay_deal'];
                } elseif ($val['lottery_type'] == 4) {
                    $basketStatis['order_count'] += $val['order_count'];
                    $basketStatis['sum_money'] += $val['sum_money'];
                    $basketStatis['stay_open'] += $val['stay_open'];
                    $basketStatis['stay_award'] += $val['stay_award'];
                    $basketStatis['sum_win_amount'] += round($val['sum_win_amount'], 2);
                    $basketStatis['sum_win'] += round($val['sum_win'], 2);
                    $basketStatis['stay_deal'] += $val['stay_deal'];
                } elseif ($val['lottery_type'] == 5) {
                    $bdStatis['order_count'] += $val['order_count'];
                    $bdStatis['sum_money'] += $val['sum_money'];
                    $bdStatis['stay_open'] += $val['stay_open'];
                    $bdStatis['stay_award'] += $val['stay_award'];
                    $bdStatis['sum_win_amount'] += round($val['sum_win_amount'], 2);
                    $bdStatis['sum_win'] += round($val['sum_win'], 2);
                    $bdStatis['stay_deal'] += $val['stay_deal'];
                } elseif ($val['lottery_type'] == 6) {
                    $wcupStatis['order_count'] += $val['order_count'];
                    $wcupStatis['sum_money'] += $val['sum_money'];
                    $wcupStatis['stay_open'] += $val['stay_open'];
                    $wcupStatis['stay_award'] += $val['stay_award'];
                    $wcupStatis['sum_win_amount'] += round($val['sum_win_amount'], 2);
                    $wcupStatis['sum_win'] += round($val['sum_win'], 2);
                    $wcupStatis['stay_deal'] += $val['stay_deal'];
                } else {
                    continue;
                }
                unset($orderStatis[$key]);
            }
            if (in_array(2, $lotteryType)) {
                $footStatis['sum_win_amount'] = sprintf("%.2f", $footStatis['sum_win_amount']);
                $footStatis['sum_win'] = sprintf("%.2f", $footStatis['sum_win']);
                 $orderStatis[] = $footStatis;
            } elseif (in_array(4, $lotteryType)) {
                $basketStatis['sum_win_amount'] = sprintf("%.2f", $basketStatis['sum_win_amount']);
                $basketStatis['sum_win'] = sprintf("%.2f", $basketStatis['sum_win']);
                $orderStatis[] = $basketStatis;
            } elseif (in_array(5, $lotteryType)) {
                $bdStatis['sum_win_amount'] = sprintf("%.2f", $bdStatis['sum_win_amount']);
                $bdStatis['sum_win'] = sprintf("%.2f", $bdStatis['sum_win']);
                $orderStatis[] = $bdStatis;
            } elseif (in_array(6, $lotteryType)) {
                $wcupStatis['sum_win_amount'] = sprintf("%.2f", $wcupStatis['sum_win_amount']);
                $wcupStatis['sum_win'] = sprintf("%.2f", $wcupStatis['sum_win']);
                $orderStatis[] = $wcupStatis;
            }
            array_values($orderStatis);
        }

        if (\Yii::$app->request->isAjax) {
            return $this->jsonResult(600, '获取成功', $orderStatis);
        }
        return $this->render('index', ['statisData' => $orderStatis]);
    }

    public function actionGetLottery() {
        $lotteryData = Lottery::find()->select(['lottery_code', 'lottery_name'])->where(['result_status' => 1])->indexBy('lottery_code')->asArray()->all();
        return $this->jsonResult(600, '获取成功', $lotteryData);
    }

}
