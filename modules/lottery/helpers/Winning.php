<?php

namespace app\modules\lottery\helpers;

use Yii;
use yii\db\Exception;
use app\modules\lottery\models\CheckLotteryResultRecord;
use app\modules\common\models\LanScheduleResult;
use app\modules\common\models\ScheduleResult;
use app\modules\common\helpers\Constants;
use app\modules\common\models\LotteryRecord;
use app\modules\common\models\FootballFourteen;
use app\modules\common\models\BdScheduleResult;

class Winning {

    /**
     * 数字彩对奖
     * @auther GL zyl
     * @param type $lotteryCode  彩种编号
     * @param type $periods 期数 
     * @return type
     * @throws Exception
     */
    public function szcWinning($lotteryCode, $periods) {
        $lotteryRecord = LotteryRecord::find()->select(['lottery_numbers'])->where(['lottery_code' => $lotteryCode, 'periods' => $periods, 'status' => 2])->asArray()->one();
        if (empty($lotteryRecord)) {
            return ['code' => 109, 'msg' => '此订单相关期数还未开奖'];
        }
        $openNums = $lotteryRecord['lottery_numbers'];
        if ($lotteryCode == 1001) {
            $openNums = str_replace('|', ',', $openNums);
            $sql = "call CheckSSQ('{$openNums}',4000000,3000000,'{$periods}'); ";
            $remark = "双色球";
        } elseif ($lotteryCode == 1002) {
            $sql = "call Check3D('{$openNums}','{$periods}'); ";
            $remark = '福彩3D';
        } elseif ($lotteryCode == 1003) {
            $sql = "call CheckQLC('{$openNums}',4000000,3000000,2000000,'{$periods}'); ";
            $remark = "七乐彩";
        } elseif ($lotteryCode == 2001) {
            $openNums = str_replace('|', ',', $openNums);
            $sql = "call CheckDLT('{$openNums}',4000000,3000000,2000000,'{$periods}'); ";
            $remark = "大乐透";
        } elseif ($lotteryCode == 2002) {
            $openNums = str_replace('|', ',', $openNums);
            $sql = "call CheckPL3 ('{$openNums}', '{$periods}');";
            $remark = "排列三";
        } elseif ($lotteryCode == 2003) {
            $sql = "call CheckPL5 ('{$openNums}','{$periods}');";
            $remark = "排列五";
        } elseif ($lotteryCode == 2004) {
            $sql = "call CheckQXC ('{$openNums}','{$periods}');";
            $remark = "七星彩";
        } elseif ($lotteryCode == 2005) {
            $sql = "call CheckGD11X5('{$openNums}', '{$periods}'); ";
            $remark = "广东11X5";
        } elseif ($lotteryCode == 2006) {
            $sql = "call CheckJX11X5('{$openNums}', '{$periods}'); ";
            $remark = "江西11X5";
        } elseif ($lotteryCode == 2007) {
            $sql = "call CheckYDJ11X5('{$openNums}', '{$periods}'); ";
            $remark = "山东11X5";
        } elseif ($lotteryCode == 2010) {
            $sql = "call CheckHB11X5('{$openNums}', '{$periods}'); ";
            $remark = "湖北11X5";
        } elseif ($lotteryCode == 2011) {
            $sql = "call CheckFJ11X5('{$openNums}', '{$periods}'); ";
            $remark = "福建11X5";
        } elseif ($lotteryCode == 2012) {
            $sql = "call CheckTJ11X5('{$openNums}', '{$periods}'); ";
            $remark = "天津11X5";
        } else {
            return ['code' => 109, 'msg' => '无效彩种'];
        }
        $connection = \Yii::$app->db;
        try {
            $ret = $connection->createCommand($sql)->execute(1); // 1:返回条数 日志所需
            $remark .= " - 兑奖完成!成功执行:{$ret['UpdateRowCount']}条";
            $data = [
                'lottery_code' => $lotteryCode,
                'periods' => $periods,
                'open_num' => $openNums,
                'remark' => $remark,
            ];
            CheckLotteryResultRecord::tosave($data);
            $postData = ['periods' => $periods, 'zj' => $openNums];
            $this->sendAward($lotteryCode, $postData);
        } catch (Exception $e) {
            throw $e;
        }
        return ['code' => 600, 'msg' => "对奖完成!成功执行:{$ret['UpdateRowCount']}条"];
    }

    /**
     * 竞篮竞足对奖
     * @auther GL zyl
     * @param type $betval  投注内容
     * @param type $lotteryType 订单类型
     * @param type $lotteryCode 彩种编号
     * @return type
     */
    public function competWinning($betval, $lotteryType, $lotteryCode) {
        $betVal = trim($betval, "^");
        if ($lotteryCode != '3011' && $lotteryCode != '3005') {
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $result = [];
        $betNums = explode("|", $betVal);
        $mids = [];
        foreach ($betNums as $ball) {
            preg_match($pattern, $ball, $result);
            if ($lotteryCode != '3011' && $lotteryCode != '3005') {
                $mids[] = $result[1];
            } else {
                $strs = explode("*", $result[1]);
                $mids[] = $strs[0];
            }
        }
        $UpdateRowCount = 0;
        if ($lotteryType == 2) {
            $data = ScheduleResult::find()->select(['schedule_mid', 'schedule_result_3006', 'schedule_result_3007', 'schedule_result_3008', 'schedule_result_3009', 'schedule_result_3010'])->where(['status' => 2])->andWhere(['in', 'schedule_mid', $mids])->asArray()->all();
            if (empty($data)) {
                return ['code' => 109, 'msg' => '此订单相关赛程还未完赛'];
            }
            $bifen = Constants::BIFEN_ARR;
            foreach ($data as $val) {
                $result3007 = str_replace(':', '', $val['schedule_result_3007']);
                if ($val['schedule_result_3010'] == 0) {
                    if (!in_array($result3007, $bifen[0])) {
                        $result3007 = '09';
                    }
                } elseif ($val['schedule_result_3010'] == 1) {
                    if (!in_array($result3007, $bifen[1])) {
                        $result3007 = '99';
                    }
                } elseif ($val['schedule_result_3010'] == 3) {
                    if (!in_array($result3007, $bifen[3])) {
                        $result3007 = '90';
                    }
                }
                if ($val['schedule_result_3008'] > 7) {
                    $result3008 = 7;
                } else {
                    $result3008 = $val['schedule_result_3008'];
                }
                $ret = $this->footballLevel($val['schedule_mid'], $val['schedule_result_3006'], $result3007, $result3008, $val['schedule_result_3009'], $val['schedule_result_3010']);
                $UpdateRowCount += $ret['Update_Row_Count'];
            }
        } elseif ($lotteryType == 4) {
            $data = LanScheduleResult::find()->select(['schedule_mid', 'result_qcbf'])->where(['result_status' => 2])->andWhere(['in', 'schedule_mid', $mids])->asArray()->all();
            if (empty($data)) {
                return ['code' => 109, 'msg' => '此订单相关赛程还未完赛'];
            }
            foreach ($data as $val) {
                $bifen = explode(':', $val['result_qcbf']);
                $ret = $this->basketballLevel($val['schedule_mid'], $bifen[0], $bifen[1]);
                $UpdateRowCount += $ret['Update_Row_Count'];
            }
        } elseif ($lotteryType == 5) {
            if ($lotteryCode != 5006) {
                $playType = 1;
            } else {
                $playType = 2;
            }
            $field = ['open_mid', 'result_5001', 'odds_5001', 'result_5002', 'odds_5002', 'result_5003', 'odds_5003', 'result_5004', 'odds_5004', 'result_5005', 'odds_5005', 'result_5006', 'odds_5006'];
            $bdResult = BdScheduleResult::find()->select($field)->where(['play_type' => $playType, 'status' => 2])->andWhere(['in', 'schedule_mid', $mids])->asArray()->one();
            if (empty($bdResult)) {
                return ['code' => 109, 'msg' => '此订单相关赛程还未完赛'];
            }
            foreach ($bdResult as $val) {
                $r5001 = $val['result_5001'];
                $r5002 = $val['result_5002'];
                $r5003 = $val['result_5003'];
                $r5004 = $bdResult['result_5004'];
                $r5005 = str_replace(':', '', $val['result_5005']);
                $r5006 = $val['result_5006'];
                $odds5001 = $val['odds_5001'];
                $odds5002 = $val['odds_5002'];
                $odds5003 = $val['odds_5003'];
                $odds5004 = $val['odds_5004'];
                $odds5005 = $val['odds_5005'];
                $odds5006 = $val['odds_5006'];
                $ret = $this->bdWinning($val['open_mid'], $r5001, $r5002, $r5003, $r5004, $r5005, $r5006, $odds5001, $odds5002, $odds5003, $odds5004, $odds5005, $odds5006);
                $UpdateRowCount += $ret['Update_Row_Count'];
            }
        } else {
            return ['code' => 109, 'msg' => '无效彩种'];
        }
        return ['code' => 600, 'msg' => "对奖完成!成功执行:{$UpdateRowCount}条"];
    }

    /**
     * 篮球对奖
     * @param type $mid 赛程MID
     * @param type $visitFen 客队得分
     * @param type $homeFen 主队得分
     * @return type
     * @throws Exception
     */
    public function basketballLevel($mid, $visitFen, $homeFen) {
        $sql = "call CheckBskBall('{$mid}', $visitFen, $homeFen); call CheckLQ_Deal('{$mid}', $visitFen, $homeFen);";
        $postData = ['periods' => $mid, 'FKe' => $visitFen, 'FZhu' => $homeFen];
        $this->sendAward(3100, $postData);
        $connection = \Yii::$app->db;
        try {
            $ret = $connection->createCommand($sql)->execute(1);
            $remark = "篮球 - 兑奖完成!成功执行:{$ret['Update_Row_Count']}条";
            $data = [
                'lottery_code' => 3100,
                'periods' => $mid,
                'open_num' => $visitFen . ':' . $homeFen,
                'remark' => $remark,
            ];
            CheckLotteryResultRecord::tosave($data);
        } catch (Exception $e) {
            throw $e;
        }
        return $ret;
    }

    /**
     * 足球对奖
     * @param type $mid 赛程MID
     * @param type $res3006 让球胜平负结果
     * @param type $res3007 全场比分结果
     * @param type $res3008 总进球数
     * @param type $res3009 半全场胜平负结果
     * @param type $res3010 胜平负结果
     * @return type
     * @throws Exception
     */
    public function footballLevel($mid, $res3006, $res3007, $res3008, $res3009, $res3010) {
        $sql = "call CheckZQ('{$mid}', '{$res3006}', '{$res3007}', '{$res3008}', '{$res3009}', '{$res3010}'); ";
//        $sql .= "call CheckZQ_Deal('{$mid}', '{$res3006}', '{$res3007}', '{$res3008}', '{$res3009}', '{$res3010}'); ";
        $connection = \Yii::$app->db;
        $postData = ['periods' => $mid, 'f3006' => $res3006, 'f3007' => $res3007, 'f3008' => $res3008, 'f3009' => $res3009, 'f3010' => $res3010];
        $this->sendAward(3000, $postData);
        try {
            $ret = $connection->createCommand($sql)->execute(1);
            $remark = "足球 - 兑奖完成!成功执行:{$ret['Update_Row_Count']}条";
            $data = [
                'lottery_code' => 4001,
                'periods' => $mid,
                'open_num' => $res3007,
                'remark' => $remark,
            ];
            CheckLotteryResultRecord::tosave($data);
        } catch (Exception $e) {
            throw $e;
        }
        return $ret;
    }

    /**
     * 胜负彩（任14，任9） 对奖
     * @param type $periods 期数
     * @return type
     */
    public function optionalWinning($periods) {
        $retData = FootballFourteen::find()->select(['schedule_results', 'first_prize', 'second_prize', 'nine_prize'])->where(["periods" => $periods, 'status' => 2])->asArray()->one();
        if (empty($retData)) {
            return ['code' => 109, 'msg' => '此订单相关期数还未开奖'];
        }
        $sql = "call CheckZQ_SFC('{$retData['schedule_results']}', '{$periods}', {$retData['first_prize']}, {$retData['second_prize']}, {$retData['nine_prize']}); ";
        $postData = ['periods' => $periods, 'zj' => $retData['schedule_results'], 'jj1' => $retData['first_prize'], 'jj2' => $retData['second_prize'], 'jj3' => $retData['nine_prize']];
        $this->sendAward(4000, $postData);
        $connection = \Yii::$app->db;
        try {
            $ret = $connection->createCommand($sql)->execute(1);
            $remark = "足球任选 - 兑奖完成!成功执行:{$ret['UpdateRowCount']}条";
            $data = [
                'lottery_code' => 4001,
                'periods' => $periods,
                'open_num' => $retData['schedule_results'],
                'remark' => $remark,
            ];
            CheckLotteryResultRecord::tosave($data);
        } catch (Exception $e) {
            throw $e;
        }
        return ['code' => 600, 'msg' => "对奖完成!成功执行:{$ret['UpdateRowCount']}条"];
    }

    /**
     * 北单赛程对奖
     * @param type $lotteryCode
     * @param type $periods
     * @return type
     * @throws Exception
     */
    public function bdWinning($mid, $r5001, $r5002, $r5003, $r5004, $r5005, $r5006, $odds5001, $odds5002, $odds5003, $odds5004, $odds5005, $odds5006) {
        $sql = "call CheckBd('{$mid}', '{$r5001}', '{$r5002}', '{$r5003}', '{$r5004}', '{$r5005}', '{$r5006}', '{$odds5001}', '{$odds5002}', '{$odds5003}', '{$odds5004}', '{$odds5005}', '{$odds5006}'); ";
        $connection = \Yii::$app->db;
        $postData = ['periods' => $mid, 'f5001' => $r5001, 'f5002' => $r5002, 'f5003' => $r5003, 'f5004' => $r5004, 'f5005' => $r5005, 'f5006' => $r5006, 'pl5001' => $odds5001, 'pl5002' => $odds5002,
            'pl5003' => $odds5003, 'pl5004' => $odds5004, 'pl5005' => $odds5005, 'pl5006' => $odds5006];
        $this->sendAward(5000, $postData);
        try {
            $ret = $connection->createCommand($sql)->execute(1);
            $remark = "北单 - 兑奖完成!成功执行:{$ret['Update_Row_Count']}条";
            $data = [
                'lottery_code' => 5000,
                'periods' => $mid,
                'open_num' => $r5005,
                'remark' => $remark,
            ];
            CheckLotteryResultRecord::tosave($data);
        } catch (Exception $e) {
            throw $e;
        }
        return $ret;
    }

    public function sendAward($lotteryCode, $retData) {
        $data['lotteryCode'] = $lotteryCode;
        $data['resultData'] = json_encode($retData);
        $surl = \Yii::$app->params['userDomain'] . '/api/cron/cron/delay-out-award';
        \Yii::sendCurlPost($surl, $data);
    }

}
