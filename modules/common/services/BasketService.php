<?php

namespace app\modules\common\services;

use app\modules\common\helpers\CompetConst;
use app\modules\common\models\Lottery;
use app\modules\common\models\LanSchedule;
use app\modules\common\helpers\Constants;
use Yii;
use yii\db\Query;
use app\modules\common\helpers\Commonfun;
use app\modules\common\services\OrderService;
use app\modules\common\services\PayService;
use app\modules\common\models\LotteryOrder;
use app\modules\common\models\BettingDetail;
use app\modules\orders\services\MajorService;

class BasketService {

    /**
     * 获取可投注赛程
     * @auther GL zyl
     * @param type $lotteryCode  彩种编号
     * @return type
     */
    public function getBetSchedule($lotteryCode) {
        $lanPlay = CompetConst::LAN_SCHEDULE_PLAY;
        $scheduleList = [];
        $list = [];
        $where = [];
        $lottery = Lottery::findOne(["lottery_code" => $lotteryCode]);
        if ($lottery->status == "0") {
            return ['code' => 109, 'msg' => '投注失败，此彩种已经停止投注，请选择其他彩种进行投注'];
        }
        $field = ['lan_schedule_id', 'schedule_mid', 'schedule_code', 'league_id', 'league_name', 'schedule_date', 'visit_short_name', 'home_short_name', 'start_time', 'beginsale_time', 'endsale_time',
            'schedule_sf', 'schedule_rfsf', 'schedule_dxf', 'schedule_sfc', 'high_win_status', 'hot_status'];
        if ($lotteryCode == Lottery::CODE_HHTZ) {
            $oddStr = ['odds3001', 'odds3002', 'odds3003', 'odds3004'];
        } else {
            $oddStr = ['odds' . $lotteryCode];
            $playField = $lanPlay[$lotteryCode];
            $where = ['!=', $playField, 3];
        }
        $scheDetail = LanSchedule::find()->select($field)
                ->with($oddStr)
                ->where(['schedule_status' => 1])
                ->andWhere($where)
                ->andWhere(['>', 'start_time', date('Y-m-d H:i:s', strtotime("+10 minute"))])
                ->andWhere(['<', 'beginsale_time', date('Y-m-d H:i:s')])
                ->andWhere(['>', 'endsale_time', date('Y-m-d H:i:s')])
                ->orderBy('start_time,schedule_mid')
                ->asArray()
                ->all();

        foreach ($scheDetail as $value) {
            $shcedultDate = (string) $value['schedule_date'];
            $gameDate = date('Y-m-d', strtotime($shcedultDate));
            $weekarray = array("日", "一", "二", "三", "四", "五", "六");
            if (array_key_exists($gameDate, $list)) {
                $list[$gameDate]['game'][] = $value;
                $list[$gameDate]['field_num'] += 1;
            } else {
                $list[$gameDate]['game'][] = $value;
                $list[$gameDate]['field_num'] = 1;
                $list[$gameDate]['game_date'] = date('Y-m-d', strtotime($gameDate));
                $list[$gameDate]['week'] = '周' . $weekarray[date('w', strtotime($gameDate))];
            }
        }
        foreach ($list as $val) {
            $scheduleList[] = $val;
        }
        return ['code' => 600, 'msg' => '获取可投注赛程成功', 'data' => $scheduleList];
    }

    /**
     * 篮球投注
     * @auther GL zyl
     * @param type $lotteryCode 彩种编号
     * @param type $custNo 会员编号 
     * @param type $storeId 门店ID
     * @param type $source 来源
     * @param type $sourceId 来源ID
     * @return type
     */
    public function playOrder($lotteryCode, $custNo, $userId, $storeId, $storeCode, $source, $sourceId = '') {
        $layName = Constants::MANNER;
        $lotteryType = Constants::LOTTERY_ABBREVI;
        $lotteryName = Constants::LOTTERY;
        $request = Yii::$app->request;
        $post = $request->post();
        $orderData = json_decode($post["order_data"], JSON_UNESCAPED_UNICODE);
        $betNums = $orderData['contents']; // 投注内容
        $total = $orderData['total']; //总价
        $countBet = $orderData['count_bet']; // 注数
        $maxTime = $orderData['max_time'];
        if (isset($orderData['end_time'])) {
            $endTime = date('Y-m-d H:i:s', $orderData['end_time'] / 1000);
        } else {
            $endTime = '';
        }
        $count = 0;
        $playName = [];
        $mCN = CompetConst::M_CHUAN_N;
        $buildPlay = '';
        $buildName = '';
        if (array_key_exists($betNums['play'], $mCN)) {
            $buildPlay = $betNums['play'];
            $buildName = $layName[$buildPlay];
            $betNums['play'] = $mCN[$buildPlay];
        }
        $arr = explode(",", $betNums["play"]);
        if (is_array($arr)) {
            foreach ($arr as $val) {
                $playName[] = $layName[$val];
            }
        } else {
            return ["code" => 2, "msg" => "投注内容注数不对应！"
            ];
        }
        $playCode = $betNums["play"];
        $playNum = $betNums["nums"];

        $ret = $this->calculationCount($lotteryCode, $betNums);
        if ($ret["code"] == 0) {
            $count = $ret["result"];
            $odds = json_encode($ret["odds"], 256);
        } else {
            return $ret;
        }
        $multiple = 1;
        $price = Constants::PRICE;
        $subCount = 0;
        if (array_key_exists('major_type', $orderData) && array_key_exists('major_data', $orderData)) {
            $betMoney = 0;
            foreach ($orderData['major_data'] as $vm) {
                $betMoney += $price * $vm['mul'];
                $subCount += 1;
            }
            $majorType = $orderData['major_type'];
            $majorData = json_encode($orderData['major_data']);
        } else {
            $multiple = $orderData['multiple']; // 倍数
            $subCount = $count;
            $betMoney = $price * $count;
            if ($multiple >= 1) {
                $betMoney *= $multiple;
            } else {
                return ["code" => 2, "msg" => "投注加倍参数错误！"];
            }
            $majorType = 0;
        }
        if ($countBet != $count || $countBet != $subCount || $count != $subCount) {
            return ["code" => 2, "msg" => "投注内容注数不对应！"];
        }
        $totalMoney = $betMoney;
        if ($total != $totalMoney) {
            return [ "code" => 2, "msg" => "投注总金额错误！"];
        }
        $period = strtotime('+3 hour', (int) $maxTime);
        $periods = (string) $period;
        $ret = OrderService::insertOrder([
                    "lottery_type" => $lotteryType[$lotteryCode],
                    "play_code" => $playCode,
                    "play_name" => implode(',', $playName),
                    "lottery_id" => $lotteryCode,
                    "lottery_name" => $lotteryName[$lotteryCode],
                    "periods" => $periods,
                    "cust_no" => $custNo,
                    "store_id" => $storeId,
                    "source_id" => $sourceId,
                    "agent_id" => "0",
                    "bet_val" => $playNum . "^",
                    "bet_double" => $multiple,
                    "is_bet_add" => 0,
                    "bet_money" => $betMoney,
                    "source" => $source,
                    "count" => $count,
                    "periods_total" => 1,
                    "is_random" => 0,
                    "win_limit" => 0,
                    "is_limit" => 0,
                    "odds" => $odds,
                    "end_time" => $endTime,
                    'user_id' => $userId,
                    'store_no' => $storeCode,
                    'build_code' => $buildPlay,
                    'build_name' => $buildName,
                    'major_type' => $majorType
                        ], false);
        if ($ret["error"] === true) {
            if ($source != 6) {
                $paySer = new PayService();
                $paySer->productPayRecord($custNo, $ret["orderCode"], 1, 1, $betMoney, 1);
            }
            if ($majorType != 0) {
                $majorOrder = new MajorService();
                $majorOrder->createMajor($ret['orderId'], $majorData, $majorType);
            }
            return ["code" => 600, "msg" => "下注成功！", "result" => ["lottery_order_code" => $ret["orderCode"]]];
        } elseif ($ret == false) {
            return ["code" => 2, "msg" => "下注失败！"];
        } else {
            return ["code" => 2, "msg" => "下注失败！", "result" => $ret];
        }
    }

    /**
     * 计算注数
     * @auther GL zyl
     * @param string $lotteryCode 彩种编号
     * @param array $contents 投注内容
     * @return array
     */
    public function calculationCount($lotteryCode, $contents, $isLimit = true) {
        $odds = [];
        $mids = [];
        $lottery = Lottery::findOne(["lottery_code" => $lotteryCode]);
        if ($lottery->status == "0") {
            return ["code" => 109, "msg" => "投注失败，此彩种已经停止投注，请选择其他彩种进行投注"];
        }
        if ($lotteryCode != "3005") {
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $scheduleMids = [];
        $betNums = explode("|", $contents["nums"]);
        $playCodes = explode(",", $contents['play']);
        $count = 0;
        if (!is_array($playCodes)) {
            return ["code" => 2, "msg" => "玩法格式出错"];
        }
        if (!is_array($betNums)) {
            return ["code" => 2, "msg" => "投注格式出错"];
        }

        $betCounts = [];
        $isMost3 = false; //是否最高只能4串1
        if ($lotteryCode == 3003) {
            $isMost3 = true;
        }
        foreach ($betNums as $v) {
            preg_match($pattern, $v, $result);
            if (is_array($result) && count($result) > 0 && isset($result[2])) {
                if ($lotteryCode != '3005') {
                    if ($isLimit == true) {
                        if (!array_key_exists($lotteryCode, $scheduleMids)) {
                            $scheduleMids[$lotteryCode] = $this->getSchedule($lotteryCode, $playCodes);
                        }
                        if (!in_array($result[1], $scheduleMids[$lotteryCode])) {
                            return ["code" => 415, "msg" => "已选择的场次含有已停售场次，请重新选择"];
                        }
                    }
                    $mids[] = $result[1];
                    $arr = explode(",", $result[2]);
                    $betCounts[] = count($arr);
                    if (!array_key_exists($lotteryCode, $odds)) {
                        $odds[$lotteryCode] = [];
                    }
                    $odds[$lotteryCode][$result[1]] = $this->getOdds($lotteryCode, $result[1], $arr);
                } else {
                    $resultBalls = trim($result[2], "*");
                    $resultBalls = explode("*", $resultBalls);
                    $thisCount = 0;
                    foreach ($resultBalls as $str) {
                        preg_match('/^([0-9]+)\((([0-9]|,)+)\)$/', $str, $r);
                        if ($isLimit == true) {
                            if (!array_key_exists($r[1], $scheduleMids)) {
                                $scheduleMids[$r[1]] = $this->getSchedule($r[1], $playCodes);
                            }
                            if (!in_array($result[1], $scheduleMids[$r[1]])) {
                                return ["code" => 415, "msg" => "已选择的场次含有已停售场次，请重新选择"];
                            }
                        }
                        if ($isMost3 == false && $lotteryCode == 3003) {
                            $isMost3 = true;
                        }
                        $mids[] = $result[1];
                        $arr = explode(",", $r[2]);
                        $thisCount+= count($arr);
                        if (!isset($odds[$r[1]])) {
                            $odds[$r[1]] = [];
                        }
                        $odds[$r[1]][$result[1]] = $this->getOdds($r[1], $result[1], $arr);
                    }
                    $betCounts[] = $thisCount;
                }
            } else {
                return ["code" => 2, "msg" => "投注格式出错"];
            }
        }
        foreach ($playCodes as $playCode) {
            $needBall = 0;
            if ($isMost3 == true && $playCode > 6) {
                return ["code" => 2, "msg" => "投注格式出错,非胜平负，最高只能四串一"];
            }
            switch ($playCode) {
                case "1":
                    $needBall = 1;
                    break;
                case "2":
                    $needBall = 2;
                    break;
                case "3":
                    $needBall = 3;
                    break;
                case "6":
                    $needBall = 4;
                    break;
                case "11":
                    $needBall = 5;
                    break;
                case "18":
                    $needBall = 6;
                    break;
                case "28":
                    $needBall = 7;
                    break;
                case "35":
                    $needBall = 8;
                    break;
                default :
                    return ["code" => 2, "msg" => "玩法未开放"];
            }

            if (is_array($betNums) && count($betNums) >= $needBall) {
                $ret = Commonfun::getCombination_array($betCounts, $needBall);
                if (!is_array($ret)) {
                    return ["code" => 2, "msg" => "投注格式出错"];
                }
                foreach ($ret as $nums) {
                    $theCount = 1;
                    foreach ($nums as $v) {
                        $theCount = $theCount * $v;
                    }
                    $count +=$theCount;
                }
            } else {
                return ["code" => 2, "msg" => "投注格式出错"];
            }
        }
        $scheduleEndTime = LanSchedule::find()->select(['min(endsale_time) as limit_time'])->where(['schedule_mid' => $mids])->asArray()->one();
        return ["code" => 0, "msg" => "获取成功", "result" => $count, "odds" => $odds, "limit_time" => $scheduleEndTime["limit_time"]];
    }

    /**
     * 获取对应彩种与对应玩法的可下注赛程
     * @auther  GL zyl
     * @param string $lotteryCode
     * @param string $play
     * @return array
     */
    public function getSchedule($lotteryCode, $play) {
        $format = 'Y-m-d H:i:s';
        $col = CompetConst::LAN_SCHEDULE_PLAY;
        if ((is_array($play) && in_array(1, $play)) || $play == 1) {
            $whereStr = " {$col[$lotteryCode]}=2 ";
        } else {
            $whereStr = " ({$col[$lotteryCode]}=2 or {$col[$lotteryCode]}=1) ";
        }
        $data = LanSchedule::find()->select(['schedule_mid'])
                ->where([">", "start_time", date($format, strtotime("+10 minute"))])
                ->andWhere(["<", "beginsale_time", date($format)])
                ->andWhere([">", "endsale_time", date($format)])
                ->andWhere($whereStr)
                ->andWhere(["schedule_status" => 1])
                ->indexBy("schedule_mid")
                ->asArray()
                ->all();
        foreach ($data as $key => $val) {
            $data[$key] = $val['schedule_mid'];
        }
        return $data;
    }

    /**
     * 获取下注时的赔率
     * @auther GL zyl
     * @param type $lotteryCode 彩种编号
     * @param type $scheduleMid 赛程编号集
     * @param type $vals 投注内容
     * @return type
     */
    public function getOdds($lotteryCode, $scheduleMid, $vals) {
        $select = [];
        switch ($lotteryCode) {
            case "3001":
                $arr = [
                    "wins_3001" => "3",
                    "lose_3001" => "0"
                ];
                foreach ($vals as $val) {
                    $key = array_search($val, $arr);
                    $select[$key . ' as ' . $val] = $val;
                }
                break;
            case "3002":
                $arr = [
                    "wins_3002" => "3",
                    "lose_3002" => "0"
                ];
                foreach ($vals as $val) {
                    $key = array_search($val, $arr);
                    $select[$key . ' as ' . $val] = $val;
                }
                $select['rf_nums'] = 0;
                break;
            case "3003":
                $arr = [
                    'cha_01' => '01',
                    'cha_02' => '02',
                    'cha_03' => '03',
                    'cha_04' => '04',
                    'cha_05' => '05',
                    'cha_06' => '06',
                    'cha_11' => '11',
                    'cha_12' => '12',
                    'cha_13' => '13',
                    'cha_14' => '14',
                    'cha_15' => '15',
                    'cha_16' => '16'
                ];
                foreach ($vals as $val) {
                    $key = array_search($val, $arr);
                    $select[$key . ' as ' . $val] = $val;
                }
                break;
            case "3004":
                $arr = [
                    "da_3004" => "1",
                    "xiao_3004" => "2"
                ];
                foreach ($vals as $val) {
                    $key = array_search($val, $arr);
                    $select[$key . ' as ' . $val] = $val;
                }
                $select['fen_cutoff'] = 0;
                break;
        }
        $field = array_keys($select);
        $data = (new Query())->select($field)->from("odds_" . $lotteryCode)->where(["schedule_mid" => $scheduleMid])->orderBy("update_nums desc")->one();
        return $data;
    }

    /**
     * 篮球投注验证
     * @auther  GL zyl
     * @param string $lotteryCode 彩种
     * @return array
     */
    public function playVerification($lotteryCode) {
        $layName = Constants::MANNER;
        $lotteryName = Constants::LOTTERY;
        $request = Yii::$app->request;
        $post = $request->post();
        $betNums = $post['contents']; // 投注内容
        $total = $post['total']; //总价
        $multiple = $post['multiple']; // 倍数
        $countBet = $post['count_bet']; // 注数
        $count = 0;
        $mCN = CompetConst::M_CHUAN_N;
        $flag = 0;
        if (array_key_exists($betNums['play'], $mCN)) {
            $mCNPlay = $betNums['play'];
            $betNums['play'] = $mCN[$betNums['play']];
            $arr = explode(',', $betNums['play']);
            $flag = 1;
        } else {
            $arr = explode(",", $betNums["play"]);
        }
        $playName = [];
        if (is_array($arr)) {
            if ($flag == 0) {
                foreach ($arr as $val) {
                    $playName[] = $layName[$val];
                }
                $playCode = $betNums["play"];
            } else {
                $playName[] = $layName[$mCNPlay];
                $playCode = $mCNPlay;
            }
        } else {
            return ["code" => 2, "msg" => "投注内容注数不对应！"];
        }
        $playNum = $betNums["nums"];
        $limit_time = "";
        $ret = $this->calculationCount($lotteryCode, $betNums);
        if ($ret["code"] == 0) {
            $count = $ret["result"];
            $limit_time = $ret["limit_time"];
        } else {
            return $ret;
        }
        $price = Constants::PRICE;
        $subCount = 0;
        if (array_key_exists('major_type', $post) && array_key_exists('major_data', $post)) {
            $betMoney = 0;
            foreach ($post['major_data'] as $vm) {
                $betMoney += $price * $vm['mul'];
                $subCount += 1;
            }
        } else {
            $subCount = $count;
            $betMoney = $price * $count;
            if ($multiple >= 1) {
                $betMoney *= $multiple;
            } else {
                return ["code" => 2, "msg" => "投注加倍参数错误！"];
            }
        }
        if ($countBet != $count || $countBet != $subCount || $count != $subCount) {
            return ["code" => 2, "msg" => "投注内容注数不对应！"];
        }
        $totalMoney = $betMoney;
        if ($total != $totalMoney) {
            return [ "code" => 2, "msg" => "投注总金额错误！"];
        }
        return ["code" => 0, "msg" => "投注信息正确！", "data" => ["lottery_name" => $lotteryName[$lotteryCode], "play_name" => (implode(',', $playName)), "play_code" => $playCode, "bet_val" => ($playNum . "^"), "limit_time" => $limit_time]];
    }

    /**
     * 生成子单
     * @auther GL zyl
     * @param model $model
     * @return array
     */
    public function productSuborder($model) {
        $infos = [];
        $infos["agent_id"] = $model->agent_id;
        $infos["bet_double"] = $model->bet_double;
        $infos["bet_money"] = $model->bet_money;
        $infos["is_bet_add"] = $model->is_bet_add;
        $infos["lottery_id"] = $model->lottery_id;
        $infos["lottery_name"] = $model->lottery_name;
        $infos["lottery_order_code"] = $model->lottery_order_code;
        $infos["lottery_order_id"] = $model->lottery_order_id;
        $infos["opt_id"] = $model->opt_id;
        $infos["periods"] = $model->periods;
        $infos["status"] = $model->status;
        $infos["cust_no"] = $model->cust_no;
        $infos["count"] = $model->count;

        $odds = json_decode($model->odds, true);
        $contents = trim($model->bet_val, "^");
        $lotteryCode = $model->lottery_id;

        $isMix = ($lotteryCode == "3005");
        if ($isMix == false) {
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $betNums = explode("|", $contents);
        $playCodes = explode(",", $model->play_code);
        $playNames = explode(",", $model->play_name);
        $order = [];
        $n = 0;
        if (!is_array($playCodes)) {
            return [
                "code" => 2,
                "msg" => "玩法格式出错"
            ];
        }
        foreach ($playCodes as $key => $playCode) {
            $needBall = 0;
            switch ($playCode) {
                case "1":
                    $needBall = 1;
                    break;
                case "2":
                    $needBall = 2;
                    break;
                case "3":
                    $needBall = 3;
                    break;
                case "6":
                    $needBall = 4;
                    break;
                case "11":
                    $needBall = 5;
                    break;
                case "18":
                    $needBall = 6;
                    break;
                case "28":
                    $needBall = 7;
                    break;
                case "35":
                    $needBall = 8;
                    break;
                default :
                    return [
                        "code" => 2,
                        "msg" => "玩法未开放"
                    ];
            }

            if (is_array($betNums) && count($betNums) >= $needBall) {
                $ret = Commonfun::getCombination_array($betNums, $needBall);
                if (!is_array($ret)) {
                    return [
                        "code" => 2,
                        "msg" => "投注格式出错"
                    ];
                }
                foreach ($ret as $nums) {
                    $combData = [];
                    $crossData = [];
                    $crossNum = 0;
                    foreach ($nums as $v) {
                        preg_match($pattern, $v, $result);
                        if (is_array($result) && count($result) > 0 && isset($result[2])) {
                            if ($isMix == false) {
                                $arr = explode(",", $result[2]);
                                $crossData[$crossNum] = [];
                                foreach ($arr as $a) {
                                    $crossData[$crossNum][] = $result[1] . "({$a})";
                                }
                                $crossNum++;
                            } else {
                                $resultBalls = trim($result[2], "*");
                                $resultBalls = explode("*", $resultBalls);
                                $crossData[$crossNum] = [];
                                foreach ($resultBalls as $str) {
                                    preg_match('/^([0-9]+)\((([0-9]|,)+)\)$/', $str, $r);
                                    $arr = explode(",", $r[2]);
                                    foreach ($arr as $a) {
                                        $crossData[$crossNum][] = $result[1] . "*" . $r[1] . "({$a})";
                                    }
                                }
                                $crossNum++;
                            }
                        } else {
                            return [
                                "code" => 2,
                                "msg" => "投注格式出错"
                            ];
                        }
                    }
                    if ($needBall != 1) {
                        $str = '';
                        for ($i = 0; $i < $needBall; $i++) {
                            $str.=',$crossData[' . $i . ']';
                        }
                        eval('$combData[] = \app\modules\common\helpers\Commonfun::proCross_string("|"' . $str . ');');
                    } else {
                        foreach ($crossData as $crossVal) {
                            $combData[] = $crossVal;
                        }
                    }
                    foreach ($combData as $v1) {
                        foreach ($v1 as $v2) {
                            $order[$n]["bet_val"] = $v2;
                            $order[$n]["play_code"] = $playCode;
                            $order[$n]["play_name"] = $playNames[$key];
                            $oddsAmount = 1;
                            $fenData = [];
                            $item = explode('|', $v2);
                            foreach ($item as $vi) {
                                preg_match($pattern, $vi, $res);
                                if ($isMix == false) {
                                    $oddsAmount *= $odds[$lotteryCode][$res[1]][$res[2]];
                                    if ($lotteryCode == 3002) {
                                        $fenData[$res[1]] = $odds[$lotteryCode][$res[1]]['rf_nums'];
                                    } elseif ($lotteryCode == 3004) {
                                        $fenData[$res[1]] = $odds[$lotteryCode][$res[1]]['fen_cutoff'];
                                    }
                                } else {
                                    $str = explode('*', $res[2]);
                                    preg_match('/^([0-9]+)\((([0-9]|,)+)\)$/', $str[1], $r);
                                    $oddsAmount *= $odds[$r[1]][$res[1]][$r[2]];
                                    if ($r[1] == 3002) {
                                        $fenData[$res[1]] = $odds[$r[1]][$res[1]]['rf_nums'];
                                    } elseif ($r[1] == 3004) {
                                        $fenData[$res[1]] = $odds[$r[1]][$res[1]]['fen_cutoff'];
                                    }
                                }
                            }
                            $order[$n]['odds'] = $oddsAmount;
                            $order[$n]['fen_json'] = json_encode($fenData);
                            $n++;
                        }
                    }
                }
            } else {
                return [
                    "code" => 2,
                    "msg" => "投注格式出错"
                ];
            }
        }
        $infos["content"] = $order;
        $result = OrderService::insertDetail($infos);
        if ($result["error"] === true) {
            return [
                "code" => 0,
                "msg" => "操作成功"
            ];
        } else {
            return [
                "code" => 2,
                "msg" => "操作失败",
                "err" => $result
            ];
        }
    }

    /**
     * 获取篮球投注单
     * @auther  GL zyl
     * @param type $lotteryOrderCode  订单编号
     * @param type $cust_no  会员编号
     * @param type $orderId  订单ID
     * @return type
     */
    public function getOrder($lotteryOrderCode, $cust_no = '', $orderId = '') {
        $where['lottery_order.lottery_order_code'] = $lotteryOrderCode;
        if (!empty($cust_no)) {
            $where['lottery_order.cust_no'] = $cust_no;
        }
        if (!empty($orderId)) {
            $where['lottery_order.lottery_order_id'] = $orderId;
        }
        $status = Constants::ORDER_STATUS;
        $sfcArr = CompetConst::SFC_BETWEEN_ARR;
        $majorArr = CompetConst::MAJOR_ARR;
        $lotOrder = LotteryOrder::find()
                ->select("bet_val,odds,lottery_id,lottery_name,bet_money,refuse_reason,lottery_order_code,lottery_order.create_time,lottery_order_id,lottery_order.status,win_amount,play_code,play_name,bet_double,count,periods,s.store_name,s.store_code,s.telephone as phone_num,major_type,build_code,build_name")
                ->leftJoin('store as s', 's.store_code = lottery_order.store_no and s.status = 1')
                ->where($where)
                ->asArray()
                ->one();
        if ($lotOrder == null) {
            return ["code" => 2, "msg" => "查询结果不存在"];
        }
        $odds = [];
        if (!empty($lotOrder["odds"])) {
            $odds = json_decode($lotOrder["odds"], 256);
        }
        $lotOrder["status_name"] = $status[$lotOrder["status"]];
        $lotOrder['award_time'] = date('Y-m-d H:i:s', strtotime('+2 hours', $lotOrder['periods'] / 1000));
        $lotOrder['major_name'] = $majorArr[$lotOrder['major_type']];
        if(empty($lotOrder['build_code'])) {
            $lotOrder['build_code'] = '';
            $lotOrder['build_name'] = '';
        }
        $data = $lotOrder;
        $betVal = trim($lotOrder["bet_val"], "^");
        if ($lotOrder["lottery_id"] != '3005') {
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $betNums = explode("|", $betVal);
        $mids = [];
        $bets = [];
        foreach ($betNums as $key => $ball) {
            $bets[$key] = [];
            preg_match($pattern, $ball, $result);
            $n = 0;
            if ($lotOrder["lottery_id"] != '3005') {
                $bets[$key]["mid"] = $result[1];
                $mids[] = $result[1];
                $arr = explode(",", $result[2]);
                foreach ($arr as $v) {
                    $bets[$key]["lottery"][$n] = [];
                    if ($lotOrder['lottery_id'] == 3003) {
                        $bets[$key]["lottery"][$n]["bet_name"] = $sfcArr[$v];
                    }
                    $bets[$key]["lottery"][$n]["bet"] = $v;
                    $bets[$key]["lottery"][$n]["play"] = $lotOrder["lottery_id"];
                    $bets[$key]["lottery"][$n]["odds"] = isset($odds[$lotOrder["lottery_id"]][$result[1]][$v]) ? $odds[$lotOrder["lottery_id"]][$result[1]][$v] : "赔率"; //赔率
                    if ($lotOrder['lottery_id'] == 3002) {
                        $bets[$key]['rf_nums'] = isset($odds[$lotOrder["lottery_id"]][$result[1]]['rf_nums']) ? $odds[$lotOrder["lottery_id"]][$result[1]]['rf_nums'] : "";
                    } elseif ($lotOrder['lottery_id'] == 3004) {
                        $bets[$key]['fen_cutoff'] = isset($odds[$lotOrder["lottery_id"]][$result[1]]['fen_cutoff']) ? $odds[$lotOrder["lottery_id"]][$result[1]]['fen_cutoff'] : "";
                    }
                    $n++;
                }
            } else {
                $mids[] = $result[1];
                $bets[$key]["mid"] = $result[1];
                $result[2] = trim($result[2], "*");
                $strs = explode("*", $result[2]);
                foreach ($strs as $str) {
                    preg_match('/^([0-9]+)\((([0-9]|,)+)\)$/', $str, $r);
                    $arr = explode(",", $r[2]);
                    foreach ($arr as $v) {
                        $bets[$key]["lottery"][$n] = [];
                        if ($r[1] == 3003) {
                            $bets[$key]["lottery"][$n]["bet_name"] = $sfcArr[$v];
                        }
                        $bets[$key]["lottery"][$n]["bet"] = $v;
                        $bets[$key]["lottery"][$n]["play"] = $r[1];
                        $bets[$key]["lottery"][$n]["odds"] = isset($odds[$r[1]][$result[1]][$v]) ? $odds[$r[1]][$result[1]][$v] : "赔率"; //赔率
                        if ($r[1] == 3002) {
                            $bets[$key]['rf_nums'] = isset($odds[$r[1]][$result[1]]['rf_nums']) ? $odds[$r[1]][$result[1]]['rf_nums'] : "";
                        } elseif ($r[1] == 3004) {
                            $bets[$key]['fen_cutoff'] = isset($odds[$r[1]][$result[1]]['fen_cutoff']) ? $odds[$r[1]][$result[1]]['fen_cutoff'] : "";
                        }
                        $n++;
                    }
                }
            }
        }
        $field = ['lan_schedule.schedule_code', 'lan_schedule.schedule_mid', 'lan_schedule.visit_short_name', 'lan_schedule.home_short_name', 'sr.result_3001', 'sr.result_3003', 'sr.result_status', 'sr.schedule_zf', 'sr.result_qcbf', 'sr.schedule_fc'];
        $schedules = LanSchedule::find()->select($field)
                ->join("left join", "lan_schedule_result sr", "lan_schedule.schedule_mid=sr.schedule_mid")
                ->where(["in", "lan_schedule.schedule_mid", $mids])
                ->indexBy("schedule_mid")
                ->asArray()
                ->all();
        $plays = Constants::LOTTERY;
        foreach ($bets as &$val) {
            $schedule = $schedules[$val["mid"]];
            foreach ($val["lottery"] as $key => $v) {
                $val["lottery"][$key]["play_name"] = $plays[$v["play"]];
                if ($v['play'] == 3001) {
                    if ($schedule["result_status"] == 2 && !empty($schedule['result_qcbf'])) {
                        $bfArr = explode(':', $schedule['result_qcbf']);
                        if ((int) $bfArr[1] > (int) $bfArr[0]) {
                            $val['result_3001'] = '3';
                        } else {
                            $val['result_3001'] = '0';
                        }
                    }
                } elseif ($v['play'] == 3002) {
                    if (array_key_exists('rf_nums', $val)) {
                        if (!empty($schedule['result_qcbf']) && $schedule['result_status'] == 2) {
                            $bfArr = explode(':', $schedule['result_qcbf']);
                            if ((int) $bfArr[1] + (float) $val['rf_nums'] > (int) $bfArr[0]) {
                                $val['result_3002'] = '3';
                            } else {
                                $val['result_3002'] = '0';
                            }
                        }
                    }
                } elseif ($v['play'] == 3003) {
                    $val["result_sfc"] = ($schedule["result_status"] != 2) ? "" : $sfcArr[$schedule["result_3003"]];
                    $val["result_3003"] = ($schedule["result_status"] != 2) ? "" : $schedule["result_3003"];
                } elseif ($v['play'] == 3004) {
                    if (array_key_exists('fen_cutoff', $val) && $schedule['result_status'] == 2) {
                        if ($val['fen_cutoff'] > $schedule['schedule_zf']) {
                            $val['result_3004'] = '2';
                        } else {
                            $val['result_3004'] = '1';
                        }
                    }
                    $val['schedule_zf'] = ($schedule["result_status"] != 2) ? "" : $schedule["schedule_zf"];
                }
            }
            $val["schedule_code"] = $schedule["schedule_code"];
            $val["home_team_name"] = $schedule["home_short_name"];
            $val["visit_team_name"] = $schedule["visit_short_name"];

            $val['schedule_fc'] = ($schedule["result_status"] != 2) ? "" : $schedule["schedule_fc"];
            $val['result_qcbf'] = ($schedule["result_status"] != 2) ? "" : $schedule["result_qcbf"];
        }
        $data["contents"] = $bets;
        return ["code" => 600, "msg" => "获取成功", "result" => $data];
    }

    /**
     * 出票更新赔率
     * @auther GL zyl
     * @param type $lotteryCode 彩种编号
     * @param type $orderId 订单ID
     * @param type $bet 投注内容
     * @return type
     */
    public function updateOdds($lotteryCode, $orderId, $bet) {
        $odds = [];
        $mids = [];
        if ($lotteryCode != "3005") {
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $betNums = explode("|", trim($bet, '^'));
        foreach ($betNums as $v) {
            preg_match($pattern, $v, $result);
            if ($lotteryCode != '3005') {
                $mids[] = $result[1];
                $arr = explode(",", $result[2]);
                if (!array_key_exists($lotteryCode, $odds)) {
                    $odds[$lotteryCode] = [];
                }
                $odds[$lotteryCode][$result[1]] = $this->getOdds($lotteryCode, $result[1], $arr);
            } else {
                $resultBalls = trim($result[2], "*");
                $resultBalls = explode("*", $resultBalls);
                $thisCount = 0;
                foreach ($resultBalls as $str) {
                    preg_match('/^([0-9]+)\((([0-9]|,)+)\)$/', $str, $r);
                    $mids[] = $result[1];
                    $arr = explode(",", $r[2]);
                    $thisCount+= count($arr);
                    if (!isset($odds[$r[1]])) {
                        $odds[$r[1]] = [];
                    }
                    $odds[$r[1]][$result[1]] = $this->getOdds($r[1], $result[1], $arr);
                }
            }
        }
        $order = LotteryOrder::findOne(['lottery_order_id' => $orderId]);
        $order->odds = json_encode($odds);
        $order->save();
        $detail = BettingDetail::find()->select(['betting_detail_id', 'bet_val'])->where(['lottery_order_id' => $orderId])->asArray()->all();
        $updetail = '';
        foreach ($detail as $val) {
            $betArr = explode('|', $val['bet_val']);
            $oddsAmount = 1;
            $fenData = [];
            foreach ($betArr as $it) {
                preg_match($pattern, $it, $res);
                if ($lotteryCode != 3005) {
                    $oddsAmount *= $odds[$lotteryCode][$res[1]][$res[2]];
                    if ($lotteryCode == 3002) {
                        $fenData[$res[1]] = $odds[$lotteryCode][$res[1]]['rf_nums'];
                    } elseif ($lotteryCode == 3004) {
                        $fenData[$res[1]] = $odds[$lotteryCode][$res[1]]['fen_cutoff'];
                    }
                } else {
                    $str = explode('*', $res[2]);
                    preg_match('/^([0-9]+)\((([0-9]|,)+)\)$/', $str[1], $r);
                    $oddsAmount *= $odds[$r[1]][$res[1]][$r[2]];
                    if ($r[1] == 3002) {
                        $fenData[$res[1]] = $odds[$r[1]][$res[1]]['rf_nums'];
                    } elseif ($r[1] == 3004) {
                        $fenData[$res[1]] = $odds[$r[1]][$res[1]]['fen_cutoff'];
                    }
                }
            }
            $updetail .= "update betting_detail set odds = {$oddsAmount}, fen_json = '" . json_encode($fenData) . "' where betting_detail_id = {$val['betting_detail_id']} and lottery_order_id = {$orderId};";
        }
        $db = \Yii::$app->db;
        $updateId = $db->createCommand($updetail)->execute();
        if ($updateId == false) {
            return ['code' => 109, 'msg' => '详情表修改失败'];
        }
        return ['code' => 600, 'msg' => '赔率修改成功'];
    }

    /**
     * 获取订单明细
     * @auther  GL zyl
     * @param type $lotteryOrderCode  订单编号
     * @param type $page
     * @param type $size
     * @return type
     */
    public function getDetail($lotteryOrderCode, $page, $size) {
        $status = Constants::ORDER_STATUS;
        $sfcArr = CompetConst::SFC_BETWEEN_ARR;
        $lotOrder = LotteryOrder::find()->select("lottery_order_id,odds,bet_val,lottery_id")->where(["lottery_order_code" => $lotteryOrderCode])->asArray()->one();
        if (empty($lotOrder)) {
            return ["code" => 2, "msg" => "查询结果不存在"];
        }
        $odds = [];
        if (!empty($lotOrder["odds"])) {
            $odds = json_decode($lotOrder["odds"], 256);
        }
        $total = BettingDetail::find()->where(['lottery_order_id' => $lotOrder['lottery_order_id']])->count();
        $pages = ceil($total / $size);
        $offset = ($page - 1) * $size;
        $bettingDetails = (new Query())
                ->select("betting_detail_id,lottery_order_id,lottery_order_code,lottery_id,lottery_name,play_name,play_code,bet_double,win_amount,bet_val,status,win_level,bet_money")
                ->from("betting_detail")
                ->where(["lottery_order_id" => $lotOrder["lottery_order_id"]])
                ->limit($size)
                ->offset($offset)
                ->all();

        $betVal = trim($lotOrder["bet_val"], "^");
        if ($lotOrder["lottery_id"] != '3005') {
            $patternDetail = '/^([0-9]+)\((([0-9]|,)+)\)$/';
            $pattern = '/^([0-9]+)\((([0-9]|,)+)\)$/';
        } else {
            $patternDetail = '/^([0-9]+\*[0-9]+)\((([0-9]|,)+)\)$/';
            $pattern = '/^([0-9]+)((\*[0-9]+\(([0-9]|,)+\))+)$/';
        }
        $betNums = explode("|", $betVal);
        foreach ($betNums as $ball) {
            preg_match($pattern, $ball, $result);
            if ($lotOrder["lottery_id"] != '3005') {
                $mids[] = $result[1];
            } else {
                $strs = explode("*", $result[1]);
                $mids[] = $strs[0];
            }
        }
        $field = ['lan_schedule.schedule_code', 'lan_schedule.schedule_mid', 'lan_schedule.home_short_name', 'lan_schedule.visit_short_name', 'sr.result_3003', 'sr.schedule_zf', 'sr.result_qcbf', 'sr.result_status'];
        $schedules = LanSchedule::find()->select($field)
                ->join("left join", "lan_schedule_result sr", "lan_schedule.schedule_mid = sr.schedule_mid")
                ->where(["in", "lan_schedule.schedule_mid", $mids])
                ->indexBy("schedule_mid")
                ->asArray()
                ->all();
        $betPlay = [];
        foreach ($bettingDetails as &$val) {
            $betNums = explode("|", $val["bet_val"]);
            $val["bet"] = [];
            foreach ($betNums as $key => $ball) {
                preg_match($patternDetail, $ball, $res);
                if ($lotOrder["lottery_id"] != '3005') {
                    $mid = $res[1];
                    $theOdds = isset($odds[$lotOrder["lottery_id"]][$mid][$res[2]]) ? $odds[$lotOrder["lottery_id"]][$mid][$res[2]] : "赔率";
                    $val["content"][$key] = [];
                    $val["content"][$key]["schedule_code"] = $schedules[$mid]["schedule_code"];
                    $val["content"][$key]["lottery_code"] = $lotOrder["lottery_id"];
                    $val['content'][$key]['bet_code'] = $res[2];
                    $val["content"][$key]["bet_odds"] = $theOdds; //赔率
//                    $val['content'][$key]['visit_team_name'] = $schedules[$mid]['visit_team_name'];
//                    $val['content'][$key]['home_team_name'] = $schedules[$mid]['home_team_name'];
                    $val['content'][$key]['visit_team_name'] = $schedules[$mid]['visit_short_name'];
                    $val['content'][$key]['home_team_name'] = $schedules[$mid]['home_short_name'];
                    if ($lotOrder['lottery_id'] == 3001) {
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '胜' : '负';
                    } elseif ($lotOrder["lottery_id"] == 3002) {
                        $val['content'][$key]['rf_nums'] = $odds[$lotOrder['lottery_id']][$mid]['rf_nums'];
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '胜' : '负';
                    } elseif ($lotOrder["lottery_id"] == 3003) {
                        $val["content"][$key]["bet_play"] = $sfcArr[$res[2]];
                    } elseif ($lotOrder['lottery_id'] == 3004) {
                        $val['content'][$key]['fen_cutoff'] = $odds[$lotOrder['lottery_id']][$mid]['fen_cutoff'];
                        $val["content"][$key]["bet_play"] = $res[2] == 1 ? '大分' : '小分';
                    }
                    if ($schedules[$mid]['result_status'] == 2) {
                        $bfArr = explode(':', $schedules[$mid]['result_qcbf']);
                        if ($lotOrder['lottery_id'] == 3001) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($lotOrder['lottery_id'] == 3002) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] + floatval($odds[$lotOrder['lottery_id']][$mid]['rf_nums']) > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($lotOrder['lottery_id'] == 3003) {
                            $val['content'][$key]['result'] = $schedules[$mid]['result_3003'];
                        } elseif ($lotOrder['lottery_id'] == 3004) {
                            $val['content'][$key]['result'] = (int) $schedules[$mid]['schedule_zf'] > floatval($odds[$lotOrder['lottery_id']][$mid]['fen_cutoff']) ? 1 : 2;
                        }
                    }
                } else {
                    $strs = explode("*", $res[1]);
                    $mid = $strs[0];
                    $theOdds = isset($odds[$strs[1]][$mid][$res[2]]) ? $odds[$strs[1]][$mid][$res[2]] : "赔率";
                    $val["content"][$key]["schedule_code"] = $schedules[$mid]["schedule_code"];
                    $val["content"][$key]["lottery_code"] = $strs[1];
                    $val['content'][$key]['bet_code'] = $res[2];
                    $val["content"][$key]["bet_odds"] = $theOdds; //赔率
                    $val['content'][$key]['visit_team_name'] = $schedules[$mid]['visit_short_name'];
                    $val['content'][$key]['home_team_name'] = $schedules[$mid]['home_short_name'];
                    if ($strs[1] == 3001) {
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '胜' : '负';
                    } elseif ($strs[1] == 3002) {
                        $val['content'][$key]['rf_nums'] = $odds[$strs[1]][$mid]['rf_nums'];
                        $val["content"][$key]["bet_play"] = $res[2] == 3 ? '胜' : '负';
                    } elseif ($strs[1] == 3003) {
                        $val["content"][$key]["bet_play"] = $sfcArr[$res[2]];
                    } elseif ($strs[1] == 3004) {
                        $val['content'][$key]['fen_cutoff'] = $odds[$strs[1]][$mid]['fen_cutoff'];
                        $val["content"][$key]["bet_play"] = $res[2] == 1 ? '大分' : '小分';
                    }
                    if ($schedules[$mid]['result_status'] == 2) {
                        $bfArr = explode(':', $schedules[$mid]['result_qcbf']);
                        if ($strs[1] == 3001) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($strs[1] == 3002) {
                            $val['content'][$key]['result'] = (int) $bfArr[1] + floatval($odds[$strs[1]][$mid]['rf_nums']) > (int) $bfArr[0] ? 3 : 0;
                        } elseif ($strs[1] == 3003) {
                            $val['content'][$key]['result'] = $schedules[$mid]['result_3003'];
                        } elseif ($strs[1] == 3004) {
                            $val['content'][$key]['result'] = (int) $schedules[$mid]['schedule_zf'] > floatval($odds[$strs[1]][$mid]['fen_cutoff']) ? 1 : 2;
                        }
                    }
                }
            }
            $val['status_name'] = $status[$val['status']];
            $val["bet"] = implode("x", $val["bet"]);
        }
        return [
            "code" => 600,
            "msg" => "获取成功",
            "result" => ['page' => $page, 'pages' => $pages, 'size' => count($bettingDetails), 'total' => $total, 'data' => $bettingDetails]
        ];
    }

}
