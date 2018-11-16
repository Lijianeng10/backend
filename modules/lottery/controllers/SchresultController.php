<?php

namespace app\modules\lottery\controllers;

use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\ScheduleResult;
use app\modules\common\models\BettingDetail;
use app\modules\lottery\models\CheckLotteryResultRecord;
use app\modules\common\models\LanScheduleResult;
use app\modules\common\models\LotteryOrder;
use app\modules\common\helpers\Constants;

class SchresultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        if (isset($get["code"])) {
            $code = $get["code"];
        } else {
            $code = 1;
        }
        if($code == 1) {
            $query = (new Query())->select("schedule_result.*,schedule.*")
                    ->from("schedule_result")
                    ->leftJoin("schedule", "schedule.schedule_mid=schedule_result.schedule_mid")
                    ->where("schedule_result.status!=0");
            if (isset($get["schedule_mid"]) && !empty($get["schedule_mid"])) {
                $query = $query->andWhere(["or",["schedule_result.schedule_mid" => $get["schedule_mid"]],["like","schedule.schedule_code",$get["schedule_mid"]]]);
            }
            if (isset($get["schedule_date"]) && !empty($get["schedule_date"])) {
                $time = date('Ymd', strtotime($get["schedule_date"]));
                $query = $query->andWhere(["schedule_result.schedule_date" => $time]);
            }
            if (isset($get["result_status"]) && !empty($get["result_status"])) {
                $query = $query->andWhere(["schedule_result.status" => $get["result_status"]]);
            }
            $query = $query->orderBy("(schedule_result.schedule_mid+0) desc");
            $data = new ActiveDataProvider([
                'query' => $query,
            ]);
        } elseif($code == 2) {
            $query = (new Query())->select("lan_schedule_result.*,lan_schedule.*,lan_schedule_history.rf_nums")
                    ->from("lan_schedule_result")
                    ->leftJoin("lan_schedule", "lan_schedule.schedule_mid=lan_schedule_result.schedule_mid")
                    ->rightJoin("lan_schedule_history", "lan_schedule_history.schedule_mid=lan_schedule_result.schedule_mid")
                    ->where("lan_schedule_result.result_status!=0");
            if (isset($get["schedule_mid"]) && !empty($get["schedule_mid"])) {
                $query = $query->andWhere(["or",["lan_schedule_result.schedule_mid" => $get["schedule_mid"]],["like","lan_schedule.schedule_code",$get["schedule_mid"]]]);
            }
            if (isset($get["schedule_date"]) && !empty($get["schedule_date"])) {
                $time = date('Ymd', strtotime($get["schedule_date"]));
                $query = $query->andWhere(["lan_schedule_result.schedule_date" => $time]);
            }
            if (isset($get["result_status"]) && !empty($get["result_status"])) {
                $query = $query->andWhere(["lan_schedule_result.result_status" => $get["result_status"]]);
            }
            $query = $query->orderBy("(lan_schedule_result.schedule_mid+0) desc");
            $data = new ActiveDataProvider([
                'query' => $query,
            ]);
        }else{
            $info = ["r.open_mid","r.status","r.result_5001","r.result_5002","r.result_5003","r.result_5004","r.result_5005","r.result_5006","s.league_name"
                ,"s.home_name","s.visit_name","s.schedule_date"];
            $query = (new Query())->select($info)
                    ->from("bd_schedule_result as r")
                    ->leftJoin("bd_schedule as s", "s.open_mid=r.open_mid")
                    ->where("r.status!=0");
            if (isset($get["schedule_mid"]) && !empty($get["schedule_mid"])) {
                $query = $query->andWhere(["r.open_mid" => $get["schedule_mid"]]);
            }
            if (isset($get["schedule_date"]) && !empty($get["schedule_date"])) {
                $time = date('Y-m-d', strtotime($get["schedule_date"]));
                $query = $query->andWhere(["s.schedule_date" => $time]);
            }
            if (isset($get["result_status"]) && !empty($get["result_status"])) {
                $query = $query->andWhere(["r.status" => $get["result_status"]]);
            }
            $query = $query->orderBy("(r.open_mid+0) desc");
            $data = new ActiveDataProvider([
                'query' => $query,
            ]);
        }
        return $this->render('index', ["data" => $data, "code" => $code, "get" => $get]);
    }

    public function actionDealDelayOrder() {
        $request = \Yii::$app->request;
        $scheduleMid = $request->post('mid', '');
        $code = $request->post('code', '');
        if (empty($scheduleMid) || empty($code)) {
            return $this->jsonResult(100, '参数缺失');
        }
        $db = \Yii::$app->db;
        if ($code == 3000) {
            $delaySchedule = ScheduleResult::find()->where(['schedule_mid' => $scheduleMid, 'status' => 3])->one();
        } elseif ($code == 3100) {
            $delaySchedule = LanScheduleResult::find()->where(['schedule_mid' => $scheduleMid, 'result_status' => 3])->one();
        }

        if (empty($delaySchedule)) {
            return $this->jsonResult(109, $scheduleMid . ':无该场次或者该场次赛果并非推迟状态，请检查赛果表！');
        }
        $field = ['betting_detail_id', 'betting_detail.lottery_order_id', 'betting_detail.lottery_id', 'betting_detail.bet_val', 'o.odds'];
        $betDetial = BettingDetail::find()->select($field)
                ->innerJoin('lottery_order o', 'o.lottery_order_id = betting_detail.lottery_order_id')
                ->where(['betting_detail.status' => 3])
                ->andWhere(['like', 'betting_detail.bet_val', $scheduleMid])
                ->asArray()
                ->all();
        $updetail = '';
        $mids = [];
        $res = [];
        foreach ($betDetial as $val) {
            if ($val["lottery_id"] != '3011' && $val['lottery_id'] != '3005') {
                $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
            } else {
                $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
            }
            $betNums = explode('|', $val['bet_val']);
            foreach ($betNums as $ball) {
                preg_match($pattern, $ball, $res);
                if (!in_array($res[1], $mids)) {
                    $mids[] = $res[1];
                }
            }
        }
        if ($code == 3000) {
            $midStatus = ScheduleResult::find()->select(['schedule_mid', 'status'])->where(['in', 'schedule_mid', $mids])->indexBy('schedule_mid')->asArray()->all();
        } elseif ($code == 3100) {
            $midStatus = LanScheduleResult::find()->select(['schedule_mid', 'result_status status'])->where(['in', 'schedule_mid', $mids])->indexBy('schedule_mid')->asArray()->all();
        }

        foreach ($betDetial as $val) {
            if ($val["lottery_id"] != '3011' && $val['lottery_id'] != '3005') {
                $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
            } else {
                $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
            }
            $odds = json_decode($val['odds'], true);
            $betNums = explode('|', $val['bet_val']);
            $lotteryCode = $val['lottery_id'];
            $result = [];
            $r = [];
            $oddsAmount = 1;
            foreach ($betNums as $ball) {
                preg_match($pattern, $ball, $result);
                if ($lotteryCode != 3011 && $lotteryCode != 3005) {
                    if ($result[1] == $scheduleMid) {
                        $oddsAmount *= 1;
                    } else {
                        if ($midStatus[$result[1]]['status'] == 3) {
                            $oddsAmount *= 1;
                        } else {
                            $oddsAmount *= $odds[$lotteryCode][$result[1]][$result[2]];
                        }
                    }
                } else {
                    $str = explode('*', $result[2]);
                    preg_match('/^([0-9]+)\((([0-9]|,)+)\)$/', $str[1], $r);
                    if ($result[1] == $scheduleMid) {
                        $oddsAmount *= 1;
                    } else {
                        if ($midStatus[$result[1]]['status'] == 1) {
                            $oddsAmount *= 1;
                        } else {
                            $oddsAmount *= $odds[$r[1]][$result[1]][$r[2]];
                        }
                    }
                }
            }
            $updetail .= "update betting_detail set odds = {$oddsAmount} where betting_detail_id = {$val['betting_detail_id']} and lottery_order_id = {$val['lottery_order_id']};";
//            BettingDetail::addQueUpdate(['betting_detail_id' => $val['betting_detail_id'], 'lottery_order_id' => $val['lottery_order_id']]);
        }
        $ret = $db->createCommand($updetail)->execute();
        if ($ret === false) {
            return $this->jsonResult(109, '修改失败!');
        }
        return $this->jsonResult(600, '成功修改' . count($betDetial) . '条');
    }

    /**
     * 取消赛程对奖
     * @param type $scheduleMid 赛程MID
     * @return type
     * @throws \app\modules\orders\helpers\Exception
     */
    public function actionDealDelayAward() {
        $request = \Yii::$app->request;
        $scheduleMid = $request->post('mid', '');
        $code = $request->post('code', '');
        if (empty($scheduleMid) || empty($code)) {
            return $this->jsonResult(100, '参数缺失');
        }
        $sql = "call CheckZQLQ_Cancel('{$scheduleMid}'); ";
        $str = '';
        if ($code == 3000) {
            $str = '足球';
        } elseif ($code == 3100) {
            $str = '篮球';
        }
        $connection = \Yii::$app->db;
        try {
            $ret = $connection->createCommand($sql)->execute(1);
            $remark = $str . " - 兑奖完成!成功执行:{$ret['Update_Row_Count']}条";
            $data = [
                'lottery_code' => 4001,
                'periods' => $scheduleMid,
                'open_num' => '',
                'remark' => $remark,
            ];
            CheckLotteryResultRecord::tosave($data);
        } catch (Exception $e) {
            throw $e;
        }
        return $this->jsonResult(600, $remark);
    }

    public function actionDealErrorAward() {
        set_time_limit(0);
        $requst = \Yii::$app->request;
        $scheduleMid = $requst->post('mid', '');
        $code = $requst->post('code', '');
        if (empty($scheduleMid) || empty($code)) {
            return $this->jsonResult(100, '参数缺失');
        }
        if ($code == 3000) {
            $mid = ScheduleResult::find()->select(['schedule_mid', 'status'])->where(['schedule_mid' => $scheduleMid, 'status' => 2])->asArray()->one();
        } elseif ($code == 3100) {
            $mid = LanScheduleResult::find()->select(['schedule_mid', 'result_status status'])->where(['schedule_mid' => $scheduleMid, 'result_status' => 2])->asArray()->one();
        }
        if (empty($mid)) {
            return $this->jsonResult(109, '该场次还未出赛果');
        }
        $where = ['and', ['in', 'status', [3, 4, 5]], ['like', 'bet_val', $scheduleMid]];
        $orderAll = LotteryOrder::find()->select(['lottery_order_id', 'lottery_id', 'bet_val'])->where($where)->asArray()->all();
        if (empty($orderAll)) {
            return $this->jsonResult(600, '无相关订单');
        }
        $mids = [];
        $orderIds = [];
        foreach ($orderAll as $val) {
            if ($val["lottery_id"] != '3011' && $val['lottery_id'] != '3005') {
                $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
            } else {
                $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
            }
            $orderIds[] = $val['lottery_order_id'];
            $betNums = explode('|', trim($val['bet_val'], '^'));
            foreach ($betNums as $v) {
                preg_match($pattern, $v, $res);
                if (!in_array($res[1], $mids)) {
                    $mids[] = $res[1];
                }
            }
        }
        if ($code == 3000) {
            $midStatus = ScheduleResult::find()->select(['schedule_mid', 'schedule_result_3006', 'schedule_result_3007', 'schedule_result_3008', 'schedule_result_3009', 'schedule_result_3010', 'status'])->where(['and', ['status' => 2], ['in', 'schedule_mid', $mids]])->indexBy('schedule_mid')->asArray()->all();
        } elseif ($code == 3100) {
            $midStatus = LanScheduleResult::find()->select(['schedule_mid', 'result_qcbf', 'result_status status'])->where(['and', ['result_status' => 2], ['in', 'schedule_mid', $mids]])->indexBy('schedule_mid')->asArray()->all();
        }
        $update = '';
        $update .= "update lottery_order SET `status` = 3,deal_status = 0,win_amount = 0 WHERE bet_val like '%" . $scheduleMid . "%' and status in (3,4,5);";
        $update .= "update betting_detail SET `status` = 3,deal_status = 0,win_amount = 0,deal_nums = 0, deal_schedule = 'gl' WHERE bet_val like '%" . $scheduleMid . "%' and status in (3,4,5);";
        $db = \Yii::$app->db;
        $ret = $db->createCommand($update)->execute();
        if ($ret === false) {
            return $this->jsonResult(109, '订单还原成功!');
        }
        $bifen = Constants::BIFEN_ARR;
        foreach ($midStatus as $item) {
            if ($item['status'] != 2) {
                continue;
            }
            if ($code == 3000) {
                $result3007 = str_replace(':', '', $item['schedule_result_3007']);
                if ($item['schedule_result_3010'] == 0) {
                    if (!in_array($result3007, $bifen[0])) {
                        $result3007 = '09';
                    }
                } elseif ($item['schedule_result_3010'] == 1) {
                    if (!in_array($result3007, $bifen[1])) {
                        $result3007 = '99';
                    }
                } elseif ($item['schedule_result_3010'] == 3) {
                    if (!in_array($result3007, $bifen[3])) {
                        $result3007 = '90';
                    }
                }
                if ($item['schedule_result_3008'] > 7) {
                    $result3008 = 7;
                } else {
                    $result3008 = $item['schedule_result_3008'];
                }
                $sql = "call CheckZQ('{$item['schedule_mid']}', '{$item['schedule_result_3006']}', {$result3007}, {$result3008}, {$item['schedule_result_3009']}, {$item['schedule_result_3010']});";
                $str = '足球';
                $result = $item['schedule_result_3007'];
            } elseif ($code == 3100) {
                $bf = explode(':', $item['result_qcbf']);
                $sql = "call CheckBskBall('{$item['schedule_mid']}', $bf[0], $bf[1]);";
                $str = '篮球';
                $result = $item['result_qcbf'];
            }
            $ret = $db->createCommand($sql)->execute(1);
            $remark = $str . " - 兑奖完成!成功执行:{$ret['Update_Row_Count']}条";
            $data = [
                'lottery_code' => 4001,
                'periods' => $mid,
                'open_num' => $result,
                'remark' => $remark,
            ];
            CheckLotteryResultRecord::tosave($data);
        }
        return $this->jsonResult(600, '成功修改' . count($orderAll) . '条');
    }

}
