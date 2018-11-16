<?php

namespace app\modules\lottery\controllers;

use Yii;
use yii\web\Controller;
use app\modules\lottery\models\LotteryOrder;
use app\modules\lottery\models\Lottery;
use app\modules\lottery\models\LotteryPlay;
use app\modules\lottery\helpers\Constant;
use app\modules\lottery\models\LotteryRecord;
use app\modules\lottery\models\LotteryAdditional;
use app\modules\lottery\helpers\CalculationDetail;

class RandomController extends Controller {

    public function actionIndex() {
        $lottery = Constant::LOTTERY;
        // $lottery = array_unshift($lottery, ['0' => '请选择']);
        $play = LotteryPlay::find()->select('lottery_code,lottery_play_code,lottery_play_name')->orderBy('lottery_code')->asArray()->all();
        $data['lottery'] = $lottery;
        $data['play'] = $play;
        return $this->render('index', ['data' => $data]);
    }

    /**
     * 加入投注表或追号表
     * @return array
     */
    public function actionAddorder() {
        if (!Yii::$app->request->isAjax) {
            return $this->jsonResult(2, '非法操作', '');
        }
        $request = Yii::$app->request;
        $bet = [];
        $post = $request->post();
        $abbArr = Constant::LOTTERY_ABBREVI;
        $playCode = rtrim($post['p_code'], ',');
        $playName = rtrim($post['p_name'], ',');
        $periods = LotteryRecord::find()->select('periods')->where(['lottery_code' => $post['l_code'], 'status' => 1])->orderBy('periods desc')->asArray()->one();
        $insert = ['lottery_type' => $abbArr[$post['l_code']], 'lottery_name' => $post['l_name'], 'lottery_id' => $post['l_code'], 'play_code' => $playCode, 'play_name' => $playName, 'periods' => $periods['periods'],
            'cust_no' => 'gl00004273', 'bet_val' => $post['bet_nums'], 'agent_id' => '0', 'periods_total' => $post['trace'], 'bet_double' => 1, 'bet_money' => $post['bet_total'], 'count' => $post['bet_count'],
            'is_bet_add' => 0, 'is_random' => 0, 'win_limit' => '', 'is_limit' => 0];
        $db = Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            $betResult = self::insertOrder($insert);
            if ($betResult['error'] == true) {
                $betId = $betResult['orderId'];
            } else {
                $tran->rollBack();
                return $this->jsonResult(2, $betResult['data'], '');
            }
            $infos = [];
            $model = LotteryOrder::findOne(["lottery_order_id" => $betId]);
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
            $content = trim($model->bet_val, "^");
            $noteNums = explode("^", $content);
            $playCodes = explode(",", $model->play_code);
            $playNames = explode(",", $model->play_name);

            if ($post['l_code'] == '1001') {
                $detail = $this->ssqDetail($noteNums, $playCodes, $playNames, $model->lottery_id);
            } elseif ($post['l_code'] == '1002') {
                $detail = $this->fctdDetail($noteNums, $playCodes, $playNames);
            } elseif ($post['l_code'] == '1003') {
                $detail = $this->qlcDetail($noteNums, $playCodes, $playNames, $model->lottery_id);
            } elseif ($post['l_code'] == '2001') {
                $detail = $this->dltDetail($noteNums, $playCodes, $playNames);
            } elseif ($post['l_code'] == '2002') {
                $detail = $this->pltDetail($noteNums, $playCodes, $playNames);
            } elseif ($post['l_code'] == '2003') {
                $detail = $this->plfDetail($noteNums, $playCodes, $playNames);
            } elseif ($post['l_code'] == '2004') {
                $detail = $this->qxcDetail($noteNums, $playCodes, $playNames);
            }
            if (empty($detail)) {
                return $this->jsonResult(2, '详情订单失败', '');
               
            }
            $infos["content"] = $detail;
            $result = $this->insertDetail($infos);
            if ($result["error"] != true) {
                $tran->rollBack();
                return $this->jsonResult(2, $result['data'], '');
                
            }
            $tran->commit();
            return $this->jsonResult(1, '投注成功', '');
        } catch (\yii\db\Exception $e) {
            $tran->rollBack();
            return $this->jsonResult(2, '失败失败', $e);
        }
    }

    public static function insertOrder($info) {

        $order = new LotteryOrder();
        $order->lottery_order_code = self::getCode($info["lottery_type"], "T");
        $order->play_code = $info["play_code"];
        $order->play_name = $info["play_name"];
        $order->lottery_id = $info["lottery_id"];
        $order->lottery_name = $info["lottery_name"];
        $order->periods = $info["periods"];
        $order->cust_no = $info["cust_no"];
        $order->agent_id = $info["agent_id"];
        $order->bet_val = $info["bet_val"];
        $order->chased_num = 1;
        $order->bet_double = $info["bet_double"];
        $order->is_bet_add = $info["is_bet_add"];
        $order->bet_money = $info["bet_money"];
        $order->status = 3;
        $order->source = 1;
        $order->count = $info["count"] ;
        $order->modify_time = date('Y-m-d H:i:s');
        $order->create_time = date('Y-m-d H:i:s');

        if ($order->validate()) {
            $orderId = $order->save();
            if ($orderId > 0) {
                return [
                    "error" => true,
                    "orderId" => $order->lottery_order_id
                ];
            } else {
                return [
                    "error" => false,
                    "msg" => "投注表插入失败！",
                ];
            }
        } else {
            return [
                "error" => false,
                "msg" => "投注表验证失败！",
                "data" => $order->getFirstErrors()
            ];
        }
    }

    public function ssqDetail($noteNums, $playCodes, $playNames, $lcode) {
        $order = [];
        $n = 0;
        foreach ($noteNums as $key => $val) {
            $numsArr = CalculationDetail::noteNums($val);
            $fun = "ssqNote_" . $lcode;
            $ret = CalculationDetail::$fun($numsArr, $playCodes[$key]);
            foreach ($ret as $k => $v) {
                $order[$n]["bet_val"] = $v;
                $order[$n]["play_code"] = $playCodes[$key];
                $order[$n]["play_name"] = $playNames[$key];
                $n++;
            }
        }
        return $order;
    }

    public function fctdDetail($noteNums, $playCodes, $playNames) {
        $order = [];
        $n = 0;
        foreach ($noteNums as $key => $val) {
            $numsArr = CalculationDetail::noteNums($val);
            $fun = "tdNote_" . $playCodes[$key];
            $ret = CalculationDetail::$fun($numsArr);
            foreach ($ret as $k => $v) {
                $order[$n]["bet_val"] = $v;
                $order[$n]["play_code"] = $playCodes[$key];
                $order[$n]["play_name"] = $playNames[$key];
                $n++;
            }
        }
        return $order;
    }

    public function qlcDetail($noteNums, $playCodes, $playNames, $lcode) {
        $order = [];
        $n = 0;
        foreach ($noteNums as $key => $val) {
            $numsArr = CalculationDetail::noteNums($val);
            $fun = "qlcNote_" . $lcode;
            $ret = CalculationDetail::$fun($numsArr);
            foreach ($ret as $k => $v) {
                $order[$n]["bet_val"] = $v;
                $order[$n]["play_code"] = $playCodes[$key];
                $order[$n]["play_name"] = $playNames[$key];
                $n++;
            }
        }
        return $order;
    }

    public function dltDetail($noteNums, $playCodes, $playNames) {
        $order = [];
        $n = 0;
        foreach ($noteNums as $key => $nums) {
            $fun = "dltNote_" . $playCodes[$key];
            $areas = CalculationDetail::noteNums($nums);
            $ret = CalculationDetail::$fun($areas);
            foreach ($ret as $k => $v) {
                $order[$n]["bet_val"] = $v;
                $order[$n]["play_code"] = $playCodes[$key];
                $order[$n]["play_name"] = $playNames[$key];
                $n++;
            }
        }
        return $order;
    }

    public function pltDetail($noteNums, $playCodes, $playNames) {
        $order = [];
        $n = 0;
        foreach ($noteNums as $key => $nums) {
            $fun = "plsNote_" . $playCodes[$key];
            $areas = CalculationDetail::noteNums($nums);
            $ret = CalculationDetail::$fun($areas);
            foreach ($ret as $k => $v) {
                $order[$n]["bet_val"] = $v;
                $order[$n]["play_code"] = $playCodes[$key];
                $order[$n]["play_name"] = $playNames[$key];
                $n++;
            }
        }
        return $order;
    }

    public function plfDetail($noteNums, $playCodes, $playNames) {
        $order = [];
        $n = 0;
        foreach ($noteNums as $key => $nums) {
            $fun = "plfNote_" . $playCodes[$key];
            $areas = CalculationDetail::noteNums($nums);
            $ret = CalculationDetail::$fun($areas);
            foreach ($ret as $k => $v) {
                $order[$n]["bet_val"] = $v;
                $order[$n]["play_code"] = $playCodes[$key];
                $order[$n]["play_name"] = $playNames[$key];
                $n++;
            }
        }
        return $order;
    }

    public function qxcDetail($noteNums, $playCodes, $playNames) {
        $order = [];
        $n = 0;
        foreach ($noteNums as $key => $nums) {
            $fun = "qxcNote_" . $playCodes[$key];
            $areas = CalculationDetail::noteNums($nums);
            $ret = CalculationDetail::$fun($areas);
            foreach ($ret as $k => $v) {
                $order[$n]["bet_val"] = $v;
                $order[$n]["play_code"] = $playCodes[$key];
                $order[$n]["play_name"] = $playNames[$key];
                $n++;
            }
        }
        return $order;
    }

    public static function getCode($lotteryType, $letter) {
        $time = date('ymd');
        $redisStr = "GLC:" . $lotteryType . ":" . $time . ":" . $letter;
        $likeStr = "GLC" . $lotteryType . $time . $letter;
//        $code = $likeStr . (self::getSerialnum($redisStr));
        return $likeStr;
    }

    public static function getSerialnum($redisStr) {
        $redis = Yii::$app->redis;
        $serialnum = $redis->executeCommand('incr', [$redisStr]);
        $serialnum = sprintf("%07d", $serialnum);
        return $serialnum;
    }

    public function insertDetail($infos) {
        $db = Yii::$app->db;
        $tran = $db->beginTransaction();
        $lotteryType = Constant::LOTTERY_ABBREVI;
        try {
            $vals = [];
            $keys = [
                'agent_id',
                'bet_double',
                'bet_money',
                'bet_val',
                'betting_detail_code',
                'create_time',
                'is_bet_add',
                'lottery_id',
                'lottery_name',
                'lottery_order_code',
                'lottery_order_id',
                'modify_time',
                'one_money',
                'periods',
                'play_code',
                'status',
                'cust_no',
                'play_name',
            ];
            foreach ($infos["content"] as $key => $val) {
                $oneMoney_1 = $infos["bet_money"] / $infos["count"];
                $oneMoney_2 = 2 * $infos["bet_double"];
                if ($infos["lottery_id"] == "2001" && $infos["is_bet_add"] == 1) {
                    $oneMoney_2 = $oneMoney_2 * 1.5;
                }
                if ($oneMoney_1 != $oneMoney_2) {
                    $tran->rollBack();
                    return [
                        "error" => false,
                        "msg" => "第{$key}条金额对不上,对应订单{$val['lottery_order_id']}",
                    ];
                }
                $vals[] = [
                    $infos["agent_id"],
                    $infos["bet_double"],
                    $oneMoney_1,
                    $val["bet_val"],
                    self::getCode($lotteryType[$infos["lottery_id"]], "X"),
                    date('y/m/d H:i:s'),
                    $infos["is_bet_add"],
                    $infos["lottery_id"],
                    $infos["lottery_name"],
                    $infos["lottery_order_code"],
                    $infos["lottery_order_id"],
                    date('y/m/d H:i:s'),
                    2,
                    $infos["periods"],
                    $val["play_code"],
                    $infos["status"],
                    $infos["cust_no"],
                    $val["play_name"]
                ];
            }
            $db->createCommand()->batchInsert("betting_detail", $keys, $vals)->execute();
            $tran->commit();
            return [
                "error" => true,
                "msg" => "操作成功！"
            ];
        } catch (\yii\db\Exception $e) {
            $tran->rollBack();
            return [
                "error" => false,
                "data" => $e,
                "msg" => "抛出错误！"
            ];
        }
    }

}
