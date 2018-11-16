<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\common\services;

use Yii;
use app\modules\common\models\LotteryOrder;
use app\modules\common\models\LotteryAdditional;
use app\modules\common\helpers\Commonfun;
use app\modules\common\models\BettingDetail;
use app\modules\common\helpers\Constants;
use yii\db\Query;
use app\modules\common\services\FundsService;
use app\modules\user\models\UserFollow;
use app\modules\common\services\PayService;
use app\modules\common\models\PayRecord;
use app\modules\competing\controllers\CompetingController;
use app\modules\user\models\User;
use app\modules\common\helpers\orderNews;
use app\modules\sports\services\GuangdongService;
use app\modules\sports\services\ShandongService;
use app\modules\sports\services\JiangxiService;
use app\modules\competing\services\OptionalService;
use app\modules\competing\services\BasketService;
use app\modules\competing\helpers\CompetConst;
use yii\db\Exception;
use app\modules\store\models\Store;
use app\modules\common\models\Programme;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use app\modules\common\models\Lottery;
use app\modules\orders\models\OrderShare;
use app\modules\common\models\UserFunds;

require_once \Yii::$app->basePath . '/vendor/resque/lottery/lottery_queue.php';

class OrderService {

    /**
     * 数字彩投注单
     * auther 咕啦 zyl
     * create_time 2017/05/24
     * update_time 2017/5/26
     * @return json
     */
    public static function numsOrder($custNo, $userId, $storeId, $storeCode, $source = 1, $sourceId = "") {
        $request = Yii::$app->request;
        $post = $request->post();
        $orderData = json_decode($post["order_data"], JSON_UNESCAPED_UNICODE);
        $current = Commonfun::currentPeriods($orderData['lottery_code']);
        if ($current['error'] == true) {
            if (($orderData['periods'] != $current['periods']) || strtotime($current["data"]["limit_time"]) < time()) {
                return [
                    "code" => 40008,
                    "msg" => "投注失败，超时,此期已过期，请重新投注"
                ];
            }
        } else {
            return [
                "code" => 40007,
                "msg" => "投注失败，此彩种已经停止投注，请选择其他彩种进行投注"
            ];
        }

        if (!isset($orderData['contents'])) {
            return [
                "code" => 2,
                "msg" => "投注内容不可为空,请重新投注"
            ];
        }

        if (!is_array($orderData['contents'])) {
            return [
                "code" => 2,
                "msg" => "投注失败，请重新投注"
            ];
        }

        if (!Commonfun::numsDifferent($orderData['contents'])) {
            return [
                "code" => 2,
                "msg" => "投注失败，请重新投注"
            ];
        }
        if (isset($orderData['chase']) && $orderData['chase'] > 1) {
            if (isset($orderData['is_limit']) && $orderData['is_limit'] == 1) {
                if (!isset($orderData['limit_amount'])) {
                    return [
                        "code" => 2,
                        "msg" => "投注失败，请填写追期限制"
                    ];
                }
            }
            $boolStr = true;
        } else {
            $boolStr = false;
        }
        switch ($orderData['lottery_code']) {
            case "1001":
                $con = new \app\modules\welfare\controllers\ColorBollController("productSuborder", "welfare");
                $ret = $con->playOrder($custNo, $userId, $storeId, $storeCode, $source, $sourceId, $boolStr);
                break;
            case "1002":
                $con = new \app\modules\welfare\controllers\ThreeDWelController("productSuborder", "welfare");
                $ret = $con->playOrder($custNo, $userId, $storeId, $storeCode, $source, $sourceId, $boolStr);
                break;
            case "1003":
                $con = new \app\modules\welfare\controllers\SevenFunColController("productSuborder", "welfare");
                $ret = $con->playOrder($custNo, $userId, $storeId, $storeCode, $source, $sourceId, $boolStr);
                break;
            case "2001":
                $con = new \app\modules\sports\controllers\SuperlottoController("productSuborder", "sports");
                $ret = $con->playOrder($custNo, $userId, $storeId, $storeCode, $source, $sourceId, $boolStr);
                break;
            case "2002":
                $con = new \app\modules\sports\controllers\ArrangedthreeController("productSuborder", "sports");
                $ret = $con->playOrder($custNo, $userId, $storeId, $storeCode, $source, $sourceId, $boolStr);
                break;
            case "2003":
                $con = new \app\modules\sports\controllers\ArrangedfiveController("productSuborder", "sports");
                $ret = $con->playOrder($custNo, $userId, $storeId, $storeCode, $source, $sourceId, $boolStr);
                break;
            case "2004":
                $con = new \app\modules\sports\controllers\SevenstarController("productSuborder", "sports");
                $ret = $con->playOrder($custNo, $userId, $storeId, $storeCode, $source, $sourceId, $boolStr);
                break;
            case "2005":
                $con = new GuangdongService();
                $ret = $con->playOrder($custNo, $userId, $storeId, $storeCode, $source, $sourceId, $boolStr);
                break;
            case '2006':
                $con = new JiangxiService();
                $ret = $con->playOrder($custNo, $userId, $storeId, $storeCode, $source, $sourceId, $boolStr);
                break;
            case '2007':
                $con = new ShandongService();
                $ret = $con->playOrder($custNo, $userId, $storeId, $storeCode, $source, $sourceId, $boolStr);
                break;
            default :
                $ret = ["code" => 2, "msg" => "错误彩种"];
        }
        return $ret;
    }

    /**
     * 
     * 竞彩下订单
     * auther GL ctx
     * @return json
     */
    public static function competingOrder($custNo, $userId, $storeId, $storeCode, $source = 1, $sourceId = "") {
        $request = Yii::$app->request;
        $post = $request->post();
        $orderData = json_decode($post["order_data"], JSON_UNESCAPED_UNICODE);
        $lotteryCode = $orderData["lottery_code"];
        $classCopeting = new \app\modules\competing\controllers\CompetingController();
        $ret = $classCopeting->playOrder($lotteryCode, $custNo, $userId, $storeId, $storeCode, $source, $sourceId);
        return $ret;
    }

    /**
     * 插入投注单
     * @param array $info
     * @return array
     */
//    public static function insertOrder($info, $proAdditional = false) {
//        $footballs = Constants::MADE_FOOTBALL_LOTTERY;
//        $nums = Constants::MADE_NUMS_LOTTERY;
//        $optional = Constants::MADE_OPTIONAL_LOTTERY;
//        $basketball = CompetConst::MADE_BASKETBALL_LOTTERY;
//        
//        if (in_array($info['lottery_id'], $footballs)) {
//            $type = 2;
//        } elseif (in_array($info['lottery_id'], $nums)) {
//            $type = 1;
//        } elseif (in_array($info['lottery_id'], $optional)) {
//            $type = 3;
//        }  elseif(in_array($info['lottery_id'], $basketball)) {
//            $type = 4;
//        }  else {
//            return ['error' => false, '此彩种暂未开放'];
//        }
//        $db = Yii::$app->db;
//        $format = 'y/m/d H:i:s';
//        $tran = $db->beginTransaction();
////        print_r($info);die;
//        try {
//            $order = new LotteryOrder();
//            $order->lottery_order_code = Commonfun::getCode($info["lottery_type"], "T");
//            $order->play_code = $info["play_code"];
//            $order->play_name = $info["play_name"];
//            $order->lottery_id = $info["lottery_id"];
//            $order->lottery_type = $type;
//            $order->lottery_name = $info["lottery_name"];
//            $order->periods = $info["periods"];
//            $order->cust_no = $info["cust_no"];
//            $order->user_id = isset($info['user_id']) ? $info['user_id'] : '';
//            $order->cust_type = isset($info["cust_type"]) ? $info["cust_type"] : 1;
//            $order->store_no = isset($info["store_no"]) ? $info["store_no"] : "";
//            $order->store_id = isset($info["store_id"]) ? $info["store_id"] : "";
//            $order->source_id = isset($info["source_id"]) ? $info["source_id"] : "";
//            $order->agent_id = $info["agent_id"];
//            $order->bet_val = $info["bet_val"];
//            $order->chased_num = isset($info["chased_num"]) ? $info["chased_num"] : 1;
//            $order->additional_periods = isset($info["periods_total"]) ? $info["periods_total"] : 1;
//            $order->bet_double = $info["bet_double"];
//            $order->is_bet_add = (isset($info["is_bet_add"]) && !empty($info["is_bet_add"])) ? $info["is_bet_add"] : 0;
//            $order->bet_money = $info["bet_money"];
//            $order->status = 1;
//            $order->source = $info['source'];
//            $order->count = $info["count"];
//            $order->odds = isset($info["odds"]) ? $info["odds"] : "";
//            $order->programme_code = isset($info["programme_code"]) ? $info["programme_code"] : "";
//            $order->modify_time = date($format);
//            $order->create_time = date($format);
//            $order->end_time = $info['end_time'];
//
//            if ($proAdditional == true) {
//                $additional = new LotteryAdditional();
////                $additional->lottery_additional_id = $info["lottery_additional_id"];
//                $additional->cust_no = $info["cust_no"];
//                $additional->cust_type = isset($info["cust_type"]) ? $info["cust_type"] : 1;
//                $additional->store_no = isset($info["store_no"]) ? $info["store_no"] : "";
//                $additional->store_id = isset($info["store_id"]) ? $info["store_id"] : "";
//                $additional->user_plan_id = isset($info["user_plan_id"]) ? $info["user_plan_id"] : "";
////                $additional->user_name = $info["user_name"];
//                $additional->agent_id = $info["agent_id"];
////                $additional->agent_name = $info["agent_name"];
//                $additional->lottery_id = $info["lottery_id"];
//                $additional->lottery_name = $info["lottery_name"];
////                $additional->bet_time = $info["bet_time"];
//                $additional->bet_val = $info["bet_val"];
//                $additional->play_code = $info["play_code"];
//                $additional->play_name = $info["play_name"];
//                $additional->status = $info["periods_total"] > 1 ? 1 : 3;
//                $additional->periods = $info["periods"];
//                $additional->periods_total = $info["periods_total"];
//                $additional->is_random = $info["is_random"];
//                $additional->bet_double = $info["bet_double"];
//                $additional->is_bet_add = (isset($info["is_bet_add"]) && !empty($info["is_bet_add"])) ? $info["is_bet_add"] : 0;
//                $additional->bet_money = $info["bet_money"];
//                $additional->total_money = $info["bet_money"] * $info["periods_total"];
//                $additional->win_limit = $info["win_limit"];
//                $additional->is_limit = $info["is_limit"];
//                $additional->pay_status = 0;
//                $additional->lottery_additional_code = Commonfun::getCode($info["lottery_type"], "Z");
//                $additional->chased_num = 1;
//                $additional->count = $info["count"];
//                $additional->modify_time = date($format);
//                $additional->create_time = date($format);
//                $additional->store_no = isset($info["store_no"]) ? $info["store_no"] : "";
//                $additional->user_id = isset($info['user_id']) ? $info['user_id'] : '';
//                $order->source = ($info['source'] == 1) ? ($info["periods_total"] > 1 ? 2 : 1) : $info['source'];
//                if ($additional->validate()) {
//                    $addId = $additional->save();
//                    if (!$addId) {
//                        $tran->rollBack();
//                        return [
//                            "error" => false,
//                            "msg" => "追期表插入失败！",
//                        ];
//                    }
//                    $order->lottery_additional_id = $additional->lottery_additional_id;
//                    if ($order->validate()) {
//                        $orderId = $order->save();
//                        if ($orderId) {
//                            $tran->commit();
//                            return [
//                                "error" => true,
//                                "orderId" => $order->lottery_order_id,
//                                "additionalId" => $additional->lottery_additional_id,
//                                "orderCode" => $order->lottery_order_code
//                            ];
//                        } else {
//                            $tran->rollBack();
//                            return [
//                                "error" => false,
//                                "msg" => "投注表插入失败！",
//                            ];
//                        }
//                    } else {
//                        $tran->rollBack();
//                        return [
//                            "error" => false,
//                            "msg" => "投注表验证失败！",
//                            "data" => $order->getFirstErrors()
//                        ];
//                    }
//                } else {
//                    return [
//                        "error" => false,
//                        "msg" => "追期表验证失败！",
//                        "data" => $additional->getFirstErrors()
//                    ];
//                }
//            } else if ($proAdditional == false) {
//                if (isset($info["lottery_additional_id"])) {
//                    $order->lottery_additional_id = $info["lottery_additional_id"];
//                }
//                if ($order->validate()) {
//                    $orderId = $order->save();
//                    if ($orderId) {
//                        $tran->commit();
//                        return [
//                            "error" => true,
//                            "orderId" => $order->lottery_order_id,
//                            "orderCode" => $order->lottery_order_code
//                        ];
//                    } else {
//                        $tran->rollBack();
//                        return [
//                            "error" => false,
//                            "msg" => "投注表插入失败！",
//                        ];
//                    }
//                } else {
//                    return [
//                        "error" => false,
//                        "msg" => "投注表验证失败！",
//                        "data" => $order->getFirstErrors()
//                    ];
//                }
//            } else {
//                return [
//                    "err" => false,
//                    "msg" => "提供追号总期数错误",
//                ];
//            }
//        } catch (\yii\db\Exception $e) {
//            $tran->rollBack();
//            return [
//                "error" => false,
//                "msg" => "抛出错误",
//                "data" => $e
//            ];
//        }
//    }

    /**
     * 插入投注单
     * @param array $info
     * @return array
     */
    public static function insertOrder($info, $proAdditional = false) {
        $footballs = Constants::MADE_FOOTBALL_LOTTERY;
        $nums = Constants::MADE_NUMS_LOTTERY;
        $optional = Constants::MADE_OPTIONAL_LOTTERY;
        $basketball = CompetConst::MADE_BASKETBALL_LOTTERY;
        if (in_array($info['lottery_id'], $footballs)) {
            $info['type'] = 2;
        } elseif (in_array($info['lottery_id'], $nums)) {
            $info['type'] = 1;
        } elseif (in_array($info['lottery_id'], $optional)) {
            $info['type'] = 3;
        } elseif (in_array($info['lottery_id'], $basketball)) {
            $info['type'] = 4;
        } else {
            return ['error' => false, '此彩种暂未开放'];
        }
        $db = Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            if ($proAdditional == true) {//如果是追期
                $addInfo = self::createAdditional($info);
                if ($addInfo['code'] != 1) {
                    throw new Exception($addInfo['msg']);
                }
                $insert = ['lottery_type' => $info['lottery_type'], 'lottery_name' => $info['lottery_name'], 'lottery_id' => $info['lottery_id'], 'play_code' => $info['play_code'], 'play_name' => $info['play_name'], 'periods' => $info['periods'],
                    'cust_no' => $info['cust_no'], "store_id" => $info['store_id'], 'source_id' => $addInfo['data']['lottery_additional_id'], 'bet_val' => $info['bet_val'], 'agent_id' => '0', 'bet_double' => $info['bet_double'], 'bet_money' => $info['bet_money'],
                    'source' => 2, 'count' => $info['count'], 'is_bet_add' => $info['is_bet_add'], 'end_time' => $info['end_time'], 'user_id' => $info['user_id'], 'store_no' => $info['store_no'], 'periods_total' => $info['periods_total'], 'type' => $info['type']];
                $orderData = self::createOrder($insert);
                if ($orderData['code'] != 1) {
                    throw new Exception($orderData['msg']);
                }
            } else if ($proAdditional == false) {
                $orderData = self::createOrder($info);
                if ($orderData['code'] != 1) {
                    throw new Exception($orderData['msg']);
                }
            }
            $tran->commit();
            return ["error" => true, "orderId" => $orderData['data']['lottery_order_id'], "orderCode" => $orderData['data']['lottery_order_code']];
        } catch (\yii\db\Exception $e) {
            $tran->rollBack();
            return ["error" => false, "msg" => "抛出错误", "data" => $e->getMessage()];
        }
    }

    /**
     * 说明: 生成追期表
     * @author  kevi
     * @date 2017年11月3日 下午6:12:37
     * @param
     * @return 
     */
    public static function createAdditional($addInfo) {
        $additional = new LotteryAdditional();
        $additional->cust_no = $addInfo["cust_no"];
        $additional->cust_type = 1;
        $additional->store_no = $addInfo['store_no'];
        $additional->store_id = $addInfo["store_id"];
        $additional->agent_id = (string) $addInfo["agent_id"];
        $additional->lottery_id = $addInfo["lottery_id"];
        $additional->lottery_name = $addInfo["lottery_name"];
        $additional->bet_val = $addInfo["bet_val"];
        $additional->play_code = $addInfo["play_code"];
        $additional->play_name = $addInfo["play_name"];
        $additional->status = $addInfo["periods_total"] > 1 ? 1 : 3;
        $additional->periods = $addInfo["periods"];
        $additional->periods_total = $addInfo["periods_total"];
        $additional->is_random = $addInfo["is_random"];
        $additional->bet_double = $addInfo["bet_double"];
        $additional->is_bet_add = $addInfo["is_bet_add"];
        $additional->bet_money = $addInfo["bet_money"];
        $additional->total_money = $addInfo["bet_money"] * $addInfo["periods_total"];
        $additional->win_limit = $addInfo["win_limit"];
        $additional->is_limit = $addInfo["is_limit"];
        $additional->pay_status = 0;
        $additional->lottery_additional_code = Commonfun::getCode($addInfo["lottery_type"], "Z");
        $additional->chased_num = 1;
        $additional->count = $addInfo["count"];
        $additional->modify_time = $additional->create_time = date('Y-m-d H:i:s');
        $additional->store_no = isset($addInfo["store_no"]) ? $addInfo["store_no"] : "";
        $additional->user_id = isset($addInfo['user_id']) ? $addInfo['user_id'] : '';
        if ($additional->validate()) {
            if ($additional->save()) {
                return ['code' => 1, 'msg' => '写入成功', 'data' => $additional->attributes];
            } else {
                return ['code' => 0, 'msg' => '追期表写入失败'];
            }
        } else {
            return ['code' => 0, 'msg' => '追期表验证失败'];
        }
    }

    /**
     * 说明: 生成订单表
     * @author  kevi
     * @date 2017年11月3日 下午5:45:17
     * @param $info
     * @return 
     */
    public static function createOrder($lotteryOrderData) {
        $order = new LotteryOrder();
        $order->lottery_order_code = Commonfun::getCode($lotteryOrderData["lottery_type"], "T");
        $order->play_code = $lotteryOrderData["play_code"];
        $order->play_name = $lotteryOrderData["play_name"];
        $order->lottery_id = $lotteryOrderData["lottery_id"];
        $order->lottery_type = $lotteryOrderData['type'];
        $order->lottery_name = $lotteryOrderData["lottery_name"];
        $order->periods = $lotteryOrderData["periods"];
        $order->cust_no = $lotteryOrderData["cust_no"];
        $order->user_id = $lotteryOrderData['user_id'];
        $order->cust_type = 1;
        $order->store_no = $lotteryOrderData["store_no"];
        $order->store_id = $lotteryOrderData["store_id"];
        $order->source_id = $lotteryOrderData["source_id"];
        $order->agent_id = (string) $lotteryOrderData["agent_id"];
        $order->bet_val = $lotteryOrderData["bet_val"];
        $order->chased_num = isset($lotteryOrderData["chased_nums"]) ? $lotteryOrderData["chased_nums"] : 1;
        $order->additional_periods = isset($lotteryOrderData["periods_total"]) ? $lotteryOrderData["periods_total"] : 1;
        $order->bet_double = $lotteryOrderData["bet_double"];
        $order->is_bet_add = (isset($lotteryOrderData["is_bet_add"]) && !empty($lotteryOrderData["is_bet_add"])) ? $lotteryOrderData["is_bet_add"] : 0;
        $order->bet_money = $lotteryOrderData["bet_money"];
        $order->status = 1;
        $order->source = $lotteryOrderData['source'];
        $order->count = $lotteryOrderData["count"];
        $order->odds = isset($lotteryOrderData["odds"]) ? $lotteryOrderData["odds"] : "";
        $order->create_time = $order->modify_time = date('Y-m-d H:i:s');
        $order->end_time = $lotteryOrderData['end_time'];
        $order->build_code = (isset($lotteryOrderData['build_code'])) ? $lotteryOrderData['build_code'] : '';
        $order->build_name = (isset($lotteryOrderData['build_name'])) ? $lotteryOrderData['build_name'] : '';
        $order->major_type = (isset($lotteryOrderData['major_type'])) ? $lotteryOrderData['major_type'] : 0;
        if ($order->validate()) {
            if ($order->saveData()) {
                return ['code' => 1, 'msg' => '写入成功', 'data' => $order->attributes];
            } else {
                return ['code' => 0, 'msg' => '订单表写入失败'];
            }
        } else {
            return ['code' => 0, 'msg' => '订单表验证失败', 'data' => $order->getFirstErrors()];
        }
    }

    /**
     * 详情投注表插入信息
     * @param array $infos
     * @return array
     */
    public static function insertDetail($infos) {
        $db = Yii::$app->db;
        $tran = $db->beginTransaction();
        $lotteryType = Constants::LOTTERY_ABBREVI;
        $chuan = Constants::CHUAN_CODE;
        $footballCode = Constants::MADE_FOOTBALL_LOTTERY;
        $basketballCode = CompetConst::MADE_BASKETBALL_LOTTERY;
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
                'schedule_nums',
                'status',
                'cust_no',
                'play_name',
                'odds',
                'win_amount',
                'fen_json'
            ];
            foreach ($infos["content"] as $key => $val) {
                $oneMoney_1 = $infos["bet_money"] / $infos["count"];
                $oneMoney_2 = Constants::PRICE * $infos["bet_double"];
                if ($infos["lottery_id"] == "2001" && $infos["is_bet_add"] == 1) {
                    $oneMoney_2 = $oneMoney_2 * 1.5;
                } elseif ($infos['lottery_id'] == '2007') {
                    if (in_array($val['play_code'], [200763, 200766])) {
                        $oneMoney_1 = $oneMoney_2 = 6 * $infos['bet_double'];
                    } elseif (in_array($val['play_code'], [200764, 200767, 200769])) {
                        $oneMoney_1 = $oneMoney_2 = 10 * $infos['bet_double'];
                    } elseif (in_array($val['play_code'], [200765, 200768, 200770])) {
                        $oneMoney_1 = $oneMoney_2 = 14 * $infos['bet_double'];
                    } else {
                        $oneMoney_1 = $oneMoney_2 = 2 * $infos['bet_double'];
                    }
                }
                if ($oneMoney_1 != $oneMoney_2) {
                    $tran->rollBack();
                    return [
                        "error" => false,
                        "msg" => "第{$key}条金额对不上,对应订单{$infos['lottery_order_id']}_{$oneMoney_1}_{$oneMoney_2}",
                    ];
                }
                if (in_array($infos['lottery_id'], $footballCode)) {
                    $dealNums = (int) $chuan[$val['play_code']];
                } elseif (in_array($infos['lottery_id'], $basketballCode)) {
                    $dealNums = (int) $chuan[$val['play_code']];
                } else {
                    $dealNums = 1;
                }
                $allBetDouble = $infos["bet_double"];
                $count = ceil($allBetDouble / 99);
                for ($num = 1; $num <= $count; $num++) {
                    if ($allBetDouble > 99) {
                        $betDouble = 99;
                    } else {
                        $betDouble = $allBetDouble;
                    }
                    $allBetDouble = $allBetDouble - $betDouble;
                    if ($infos["lottery_id"] == "2001" && $infos["is_bet_add"] == 1) {
                        $betMoney = Constants::PRICE * $betDouble * 1.5;
                    } else {
                        $betMoney = Constants::PRICE * $betDouble;
                    }
//                    if (in_array($infos['lottery_id'], $footballCode)) {
//                        $winAmount = $betMoney;
//                    } else {
//                        $winAmount = 0;
//                    }
                    $winAmount = 0;
                    $vals[] = [
                        $infos["agent_id"],
                        $betDouble, //$infos["bet_double"],
                        $betMoney, //$oneMoney_1,
                        $val["bet_val"],
                        Commonfun::getCode($lotteryType[$infos["lottery_id"]], "X"),
                        date('y/m/d H:i:s'),
                        $infos["is_bet_add"],
                        $infos["lottery_id"],
                        $infos["lottery_name"],
                        $infos["lottery_order_code"],
                        $infos["lottery_order_id"],
                        date('y/m/d H:i:s'),
                        Constants::PRICE,
                        $infos["periods"],
                        $val["play_code"],
                        $dealNums,
                        $infos["status"],
                        $infos["cust_no"],
                        $val["play_name"],
                        isset($val['odds']) ? $val['odds'] : '',
                        $winAmount, //$oneMoney_1
                        (isset($val["fen_json"]) ? $val["fen_json"] : '')
                    ];
                }
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

    /**
     * 获取交易情况
     */
    public static function getPayRecord($orderCode) {
        $data = (new \yii\db\Query())->select("*")->from("pay_record")->where(["order_code" => $orderCode])->one();
        return $data;
    }

    public static function rechargeNotify($orderCode, $outer_no, $total_amount, $payTime) {
        $db = \Yii::$app->db;
        $tran = $db->beginTransaction();
		try
		{
			$record = (new \yii\db\Query())->select("*")->from("pay_record")->where(["order_code" => $orderCode])->one();
			if ($record == null)
			{
				return ["code" => 109,"msg" => "未找到该记录"];
			}
			if ($record["status"] != 0)
			{
				return true;
			}
//            $userFunds = (new Query())->select("*")->from("user_funds")->where(["cust_no" => $record["cust_no"]])->one();
//            Yii::$app->db->createCommand()->update("user_funds", [
//                "all_funds" => $userFunds["all_funds"] + $record["pay_pre_money"],
//                "able_funds" => $userFunds["able_funds"] + $record["pay_pre_money"],
//                    ], [
//                "cust_no" => $record["cust_no"]
//            ])->execute();
            $fundsSer = new FundsService();
            $fundsSer->operateUserFunds($record["cust_no"], $record["pay_pre_money"], $record["pay_pre_money"], 0, true);
            $funds = (new Query())->select("all_funds")->from("user_funds")->where(["cust_no" => $record["cust_no"]])->one();
            PayRecord::upData([
                "status" => 1,
                "outer_no" => $outer_no,
                "modify_time" => date("Y-m-d H:i:s"),
                "pay_time" => $payTime,
                "pay_money" => $total_amount,
                "balance" => $funds["all_funds"]
                    ], [
                "order_code" => $orderCode,
            ]);
            /*$db->createCommand()->update("pay_record", [
                "status" => 1,
                "outer_no" => $outer_no,
                "modify_time" => date("Y-m-d H:i:s"),
                "pay_time" => $payTime,
                "pay_money" => $total_amount,
                "balance" => $funds["all_funds"]
                    ], [
                "order_code" => $orderCode,
            ])->execute();*/
            $tran->commit();
            $key = 'waitting_recharge:' . $orderCode;
            $code = \Yii::redisGet($key);
            if(!empty($code)) {
                $service = new PayService();
                $service->order_code = $code;
                $service->way_type = "YE";
                $service->pay_way = "3";
                $service->cust_no = $record["cust_no"];
                $service->activeType = 1;
                $service->Pay();
            }
        } catch (\yii\base\Exception $e) {
            $tran->rollBack();
            return ["code" => 109, "msg" => json_encode($e, true)];
        }
        return true;
    }

    /**
     * 购彩回调
     * @param string $orderCode
     * @param string $outer_no
     * @param decimal $total_amount
     * @return boolean
     */
    public static function orderNotify($orderCode, $outer_no, $total_amount, $payTime) {
        $db = \Yii::$app->db;
        $lotOrder = LotteryOrder::find()
                ->where(["lottery_order_code" => $orderCode])
                ->andWhere(["status" => "1"])
                ->asArray()
                ->one();
        $record = (new \yii\db\Query())->select("*")->from("pay_record")->where(["order_code" => $orderCode])->one();
        if ($record == null) {
            return ["code" => 109, "msg" => "未找到该记录"];
        }
        if ($record["status"] != 0) {
            return true;
        }
        $fundsSer = new FundsService();
        if ($lotOrder['source'] == 2) {
            $lotAddInfo = LotteryAdditional::find()
                    ->where(["lottery_additional_id" => $lotOrder["source_id"]])
                    ->andWhere(["pay_status" => "0"])
                    ->asArray()
                    ->one();
            $iceMoney = $lotAddInfo["total_money"] - $lotAddInfo["bet_money"];
            if ($lotOrder != null) {
                $ret3 = $fundsSer->operateUserFunds($lotAddInfo["cust_no"], 0, (0 - $iceMoney), $iceMoney, true);
                if ($iceMoney > 0) {
                    $fundsSer->iceRecord($lotAddInfo["cust_no"], $record["cust_type"], $lotAddInfo["lottery_additional_code"], $iceMoney, 1, "追号冻结");
                }
                $funds = (new Query())->select("all_funds")->from("user_funds")->where(["cust_no" => $record["cust_no"]])->one();
                $ret2=PayRecord::upData(["status" => 1, "outer_no" => $outer_no, "modify_time" => date("Y-m-d H:i:s"), "pay_time" => $payTime, "pay_money" => $total_amount, "balance" => $funds["all_funds"]], ["order_code" => $orderCode]);
                //$ret2 = \Yii::$app->db->createCommand()->update("pay_record", ["status" => 1, "outer_no" => $outer_no, "modify_time" => date("Y-m-d H:i:s"), "pay_time" => $payTime, "pay_money" => $total_amount, "balance" => $funds["all_funds"]], ["order_code" => $orderCode])->execute();
                LotteryAdditional::upData(["pay_status" => "1", "status" => "2"], ["lottery_additional_id"=>$lotOrder["source_id"],'pay_status'=>0]);
                //LotteryAdditional::updateAll(["pay_status" => "1", "status" => "2"], "lottery_additional_id='{$lotOrder["source_id"]}' and pay_status=0");
                $ret1=LotteryOrder::upData(["status" => "2"],["lottery_order_id" => $lotOrder["lottery_order_id"]]);
                //$ret1 = $db->createCommand()->update("lottery_order", ["status" => "2"], ["lottery_order_id" => $lotOrder["lottery_order_id"]])->execute();
                if ($ret3["code"] != 0) {
                    return false;
                }
            }
        } else {
            $funds = (new Query())->select("all_funds")->from("user_funds")->where(["cust_no" => $record["cust_no"]])->one();
            $ret2=PayRecord::upData(["status" => 1, "outer_no" => $outer_no, "modify_time" => date("Y-m-d H:i:s"), "pay_time" => $payTime, "pay_money" => $total_amount, "balance" => $funds["all_funds"]], ["order_code" => $orderCode]);
            //$ret2 = \Yii::$app->db->createCommand()->update("pay_record", ["status" => 1, "outer_no" => $outer_no, "modify_time" => date("Y-m-d H:i:s"), "pay_time" => $payTime, "pay_money" => $total_amount, "balance" => $funds["all_funds"]], ["order_code" => $orderCode])->execute();
            $ret1=LotteryOrder::upData(["status" => "2"],["lottery_order_id" => $lotOrder["lottery_order_id"]]);
            //$ret1 = $db->createCommand()->update("lottery_order", ["status" => "2"], ["lottery_order_id" => $lotOrder["lottery_order_id"]])->execute();
            if ($lotOrder['source'] == 5) {
//            	OrderShare::upData(['with_nums'=>new Expression('with_nums+1'),"modify_time" => date("Y-m-d H:i:s")], ['order_share_id'=>$lotOrder['source_id']]);
                $updata = "update order_share set with_nums = with_nums + 1, modify_time = '" . date('Y-m-d H:i:s') . "' where order_share_id={$lotOrder['source_id']}";
                $db->createCommand($updata)->execute();
            }
        }
        if ($ret1 == false || $ret2 == false) {
            Commonfun::stationLetter(); //站内信通知-----!!未完成
            return false;
        } else {
            $lotteryqueue = new \LotteryQueue();
            $lotteryqueue->pushQueue('lottery_job', 'default', ["orderId" => $lotOrder["lottery_order_id"]]);
			// $lotteryqueue->pushQueue('backupOrder_job', 'backup', ['tablename' => 'lottery_order', "keyname" => 'lottery_order_id', 'keyval' => $lotOrder['lottery_order_id']]);
			// $lotteryqueue->pushQueue('backupOrder_job', 'backup_pay_record', ['tablename' => 'pay_record', "keyname" => 'order_code', 'keyval' => $orderCode]);
			// $lotteryqueue->pushQueue('backupOrder_job', 'backup_userfunds', ['tablename' => 'user_funds', "keyname" => 'cust_no', 'keyval' => $record["cust_no"]]);
		}
		return true;
	}

	/**
	 * 出票
	 * 
	 * @param type $orderId
	 * @return boolean
	 */
	public static function outOrder($orderCode, $storeCode)
	{
		$lotOrder = LotteryOrder::find()->select(['lottery_order.*','user.user_tel','user.user_name','store.cust_no as store_cust_no','store.store_name'])
            ->leftJoin('user','user.cust_no = lottery_order.cust_no')
            ->leftJoin('store','store.store_code = lottery_order.store_no and store.status = 1')
            ->where(["lottery_order_code" => $orderCode, "lottery_order.store_no" => $storeCode,"lottery_order.status"=>2, "suborder_status" => 1])
            ->asArray()
            ->one();
        if (!$lotOrder) {
            return ['code'=>0,'msg'=>'找不到该订单！'];
        }
        $optId = \Yii::$storeOperatorId;
        $db = \Yii::$app->db;
        
        $tran = $db->beginTransaction();
        try{
				// $orderUpdateSql = "UPDATE lottery_order SET status=3 ,opt_id = {$optId} WHERE lottery_order_code = '{$orderCode}';
				// UPDATE betting_detail SET status=3 WHERE lottery_order_code = '{$orderCode}';";
			$orderUpdateSql = "UPDATE betting_detail SET status=3 WHERE lottery_order_code = '{$orderCode}';";
			$update = ['status' => 3,'opt_id' => $optId];
			$where = ['lottery_order_code' => $orderCode];
			LotteryOrder::upData($update, $where);//修改订单和详情的状态为待开奖3
            $a = $db->createCommand($orderUpdateSql)->execute();//修改订单和详情的状态为待开奖3
            if($lotOrder['source'] == 4){//该订单如果是合买
                $programmeroUpdateSql = "UPDATE programme SET status=4 WHERE programme_id = {$lotOrder['source_id']};
                    UPDATE programme_user SET status=4 WHERE programme_id = {$lotOrder['source_id']};";
                $db->createCommand($programmeroUpdateSql)->execute();//修改合买订单、子单状态
            }
            if($lotOrder['source'] != 6){//资金变动
                $fundsSer = new FundsService();
                $ret = $fundsSer->operateUserFunds($lotOrder['store_cust_no'], $lotOrder['bet_money'], $lotOrder['bet_money'], 0, false);
                if ($ret["code"] != 0) {
                    $tran->rollBack();
                    return ['code'=>2,'msg'=>$ret['msg']];
                }
                $funds = (new Query())->select("all_funds")->from("user_funds")->where(["cust_no" => $lotOrder['store_cust_no']])->one();
                $payRecord = new PayRecord();
                $payRecord->order_code = $orderCode;
                $payRecord->pay_no = Commonfun::getCode("PAY", "L");
                $payRecord->cust_no = $lotOrder['store_cust_no'];
                $payRecord->cust_type = 2;
                $payRecord->user_name = $lotOrder['store_name'];
                $payRecord->pay_pre_money =$lotOrder['bet_money'];
                $payRecord->pay_money = $lotOrder['bet_money'];
                $payRecord->pay_name = '余额';
                $payRecord->way_name = '余额';
                $payRecord->way_type = 'YE';
                $payRecord->pay_way = 3;
                $payRecord->pay_type_name = '门店出票';
                $payRecord->pay_type = 9;
                $payRecord->body = '门店出票-' . (!empty($lotOrder['user_tel']) ? substr($lotOrder['user_tel'], -4) : "") . "({$lotOrder['user_name']})";
                $payRecord->status = 1;
                $payRecord->balance = $funds["all_funds"];
                $payRecord->pay_time = date('Y-m-d H:i:s');
                $payRecord->modify_time = date("Y-m-d H:i:s");
                $payRecord->create_time = date('Y-m-d H:i:s');
                
                if(!$payRecord->saveData()){//交易记录表保存失败
                    $log = new Logger('winning_detail');
                    $log->pushHandler(new StreamHandler(BASE_PATH . '/logs/pay_record/save_error.log'));
                    $log->info("订单编号： - {$orderCode} - 交易订单 保存失败", ['失败原因: ==>'],$payRecord->errors);
                }
                
                $serviceCharge = (ceil($lotOrder['bet_money'] * 0.2) / 100);
                $ret = $fundsSer->operateUserFunds($lotOrder['store_cust_no'], 0 - $serviceCharge, 0 - $serviceCharge, 0, false);
                if ($ret["code"] != 0) {
                    $tran->rollBack();
                    return ['code'=>2,'msg'=>$ret['msg']];
                }
                $funds = (new Query())->select("all_funds")->from("user_funds")->where(["cust_no" => $lotOrder['store_cust_no']])->one();
                $payRecord2 = new PayRecord();//服务手续费
                $payRecord2->order_code = $orderCode;
                $payRecord2->pay_no = Commonfun::getCode("PAY", "L");
                $payRecord2->cust_no = $lotOrder['store_cust_no'];
                $payRecord2->cust_type = 2;
                $payRecord2->user_name = $lotOrder['store_name'];
                $payRecord2->pay_pre_money = $serviceCharge;
                $payRecord2->pay_money = $serviceCharge;
                $payRecord2->pay_name = '余额';
                $payRecord2->way_name = '余额';
                $payRecord2->way_type = 'YE';
                $payRecord2->pay_way = 3;
                $payRecord2->pay_type_name = '出票服务费';
                $payRecord2->pay_type = 16;
                $payRecord2->body = '出票服务费';
                $payRecord2->status = 1;
                $payRecord2->balance = $funds["all_funds"];
                $payRecord2->pay_time = date('Y-m-d H:i:s');
                $payRecord2->modify_time = date("Y-m-d H:i:s");
                $payRecord2->create_time = date('Y-m-d H:i:s');
                if(!$payRecord2->saveData()){//交易记录表保存失败
                    $log = new Logger('winning_detail');
                    $log->pushHandler(new StreamHandler(BASE_PATH . '/logs/pay_record/save_error.log'));
                    $log->info("订单编号： - {$orderCode} - 交易订单(手续费) 保存失败", ['失败原因: ==>'],$payRecord2->errors);
                }
            }else{//如果是计划购买
                $format = date('Y-m-d H:i:s');
                $userPlan = "update user_plan set betting_funds = betting_funds - {$lotOrder['bet_money']}, modify_time = '" . $format . "' where user_plan_id = {$lotOrder['user_plan_id']} ;";
                $upId = $db->createCommand($userPlan)->execute();
                if ($upId == false) {
                    $tran->rollBack();
                    return ['code'=>2,'msg'=>'计划表更新失败'];
                }
            }
            $tran->commit();
        } catch (\yii\db\Exception $e){
            $tran->rollBack();
            print $e->getMessage();
            return ['code'=>2,'msg'=>'出票异常,请联系管理员'];
        }
        return ['code'=>1,'msg'=>'订单处理成功'];
    }

    /**
     * 出票失败
     * @param type $orderId
     * @return boolean
     */
    public static function outOrderFalse($orderCode, $falseStatus, $storeId = null, $body = "彩彩宝-彩票出票失败") {
        if ($storeId == null) {
            $lotOrder = LotteryOrder::findOne(["lottery_order_code" => $orderCode,"status" => $falseStatus,"deal_status" => 0]);
		}else
		{
			$lotOrder = LotteryOrder::findOne(["lottery_order_code" => $orderCode,"status" => $falseStatus,"deal_status" => 0,"store_no" => $storeId]);
		}
		if ($lotOrder == null)
		{
			return false;
		}
		if ($lotOrder->source == 4)
		{ // 4、合买退款是退给每个认购人
			$proSer = new ProgrammeService();
			$ret = $proSer->outOrderFalse($lotOrder->source_id, $falseStatus);
//            BettingDetail::updateAll([
//                "status" => 6
//                    ], 'lottery_order_id=' . $lotOrder->lottery_order_id);
//            return true;
        } elseif ($lotOrder->source == 6) {
            $ret = PlanService::outFalse($lotOrder->source_id, $lotOrder->bet_money);
        } else {
//            $lotAddInfo = LotteryAdditional::findOne($lotOrder->lottery_additional_id);
            $paySer = new PayService();
            $ret = $paySer->refund($lotOrder->lottery_order_code, $body, $lotOrder->bet_money);
        }
            if ($ret == false) {
            $lotOrder->deal_status = 4; //退款失败
            $lotOrder->saveData();
            return false;
        }
        $lotOrder->deal_status = 5; //5、退款成功
        if ($lotOrder->validate()) {
            $lotOrder->saveData();
//            BettingDetail::updateAll([
//                "status" => 6
//                    ], 'lottery_order_id=' . $lotOrder->lottery_order_id);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 追号下单
     * @param integer $lotteryAdditionalId
     * @param string $periods
     * @return boolean
     */
    public static function additionalOrder($lotteryAdditionalId) {
        $lotAddInfo = LotteryAdditional::findOne(["lottery_additional_id" => $lotteryAdditionalId, "status" => 2]);
        $currentPeriodsInfo = Commonfun::currentPeriods($lotAddInfo->lottery_id);
        if ($currentPeriodsInfo['error'] === false) {
            return false;
        }
        $periods = $currentPeriodsInfo["periods"];
        $endTime = $currentPeriodsInfo["data"]['limit_time'];
        $status = ["2", "3", "4", "5"];
        $lotteryOrder = LotteryOrder::find()->where(["lottery_additional_id" => $lotteryAdditionalId, "periods" => $periods])->andWhere(["in", "status", $status])->one();
        if ($lotteryOrder != null) {
            return false;
        }
        if ($lotAddInfo->is_limit == 1) { //是否限制中奖金额
            $lotteryOrder = LotteryOrder::find()->select(["sum(win_amount) win"])->where(["lottery_additional_id" => $lotteryAdditionalId, "status" => 4])->asArray()->one();
            if (!empty($lotteryOrder) && !empty($lotteryOrder->win) && $lotteryOrder->win >= $lotAddInfo->win_limit) {   //中奖金额是否已超过限制
                $lotAddInfo->status = 0;        //0、停止追号
                $ret = self::refundAdditional($lotteryAdditionalId, $lotAddInfo->periods_total - $lotAddInfo->chased_num);    //解冻冻结金额
                if ($ret == false) {
                    return false;
                }
                $lotAddInfo->save();
                return false;
            }
        }
        if ($lotAddInfo->is_random == 1) {
            $betVal = self::randomOrder($lotAddInfo->lottery_id, $lotAddInfo->bet_val);
        } else {
            $betVal = $lotAddInfo->bet_val;
        }
        $lotAddInfo->chased_num = $lotAddInfo->chased_num + 1;
        $lotteryType = Constants::LOTTERY_ABBREVI;
        $ret = self::insertOrder([//插入订单
                    "lottery_type" => $lotteryType[$lotAddInfo->lottery_id],
                    "play_code" => $lotAddInfo->play_code,
                    "play_name" => $lotAddInfo->play_name,
                    "lottery_id" => $lotAddInfo->lottery_id,
                    "lottery_name" => $lotAddInfo->lottery_name,
                    "periods" => $periods,
                    "cust_no" => $lotAddInfo->cust_no,
                    "user_id" => $lotAddInfo->user_id,
                    "store_no" => $lotAddInfo->store_no,
                    "store_id" => $lotAddInfo->store_id,
                    "agent_id" => "0",
                    "bet_val" => $betVal,
                    "bet_double" => $lotAddInfo->bet_double,
                    "is_bet_add" => $lotAddInfo->is_bet_add,
                    "bet_money" => $lotAddInfo->bet_money,
                    "source" => 2,
                    "count" => $lotAddInfo->count,
                    "chased_num" => $lotAddInfo->chased_num,
                    "periods_total" => $lotAddInfo->periods_total,
//                    "is_random" => 0,
//                    "win_limit" => 0,
//                    "is_limit" => 0,
                    "odds" => "",
                    "lottery_additional_id" => $lotAddInfo->lottery_additional_id,
                    "end_time" => $endTime
                        ], false);          //这里的false表示直插入订单表中的记录
        if ($ret["error"] === true) {
            $orderCode = $ret["orderCode"];
            $betMoney = $lotAddInfo->bet_money;
            $paySer = new PayService();
            $paySer->productPayRecord($lotAddInfo->cust_no, $orderCode, 1, 1, $betMoney, 5);

            $payRecord = \app\modules\common\models\PayRecord::findOne(["order_code" => $orderCode, "status" => 0]);
            $payRecord->pay_way = 3;
            $payRecord->pay_name = "余额";
            $payRecord->way_type = "YE";
            $payRecord->way_name = "余额";
            $payRecord->save();
            $fundsSer = new FundsService();
            $fundRet = $fundsSer->operateUserFunds($lotAddInfo->cust_no, (0 - $betMoney), 0, (0 - $betMoney), true, "追号扣除冻结金额");
            if ($fundRet["code"] != 0) {
                return false;
            }
            $fundsSer->iceRecord($lotAddInfo->cust_no, $payRecord->cust_type, $payRecord->order_code, $betMoney, 2, "追号扣除冻结金额");
            Yii::$app->db->createCommand()->update("lottery_order", [
                "status" => "2"
                    ], [
                "lottery_order_id" => $ret["orderId"]
            ])->execute();
            $outer_no = Commonfun::getCode("YEP", "Z");
            $funds = (new Query())->select("all_funds")->from("user_funds")->where(["cust_no" => $lotAddInfo->cust_no])->one();
            Yii::$app->db->createCommand()->update("pay_record", [
                "status" => 1,
                "outer_no" => $outer_no,
                "modify_time" => date("Y-m-d H:i:s"),
                "pay_time" => date("Y-m-d H:i:s"),
                "pay_money" => $betMoney,
                "balance" => $funds["all_funds"]
                    ], [
                "order_code" => $orderCode,
            ])->execute();
            if ($lotAddInfo->chased_num == $lotAddInfo->periods_total) {
                $lotAddInfo->status = 3;
            }
            $lotAddInfo->save();
            $lotteryqueue = new \LotteryQueue();
            $lotteryqueue->pushQueue('lottery_job', 'default', ["orderId" => $ret["orderId"]]);
            return true;
        } else {
            self::refundAdditional($lotteryAdditionalId, 1);    //解冻冻结金额
            if ($lotAddInfo->chased_num == $lotAddInfo->periods_total) {
                $lotAddInfo->status = 3;
            }
            $lotAddInfo->save();
            return false;
        }
    }

    /**
     * 追号解冻
     * @param integer $lotteryAdditionalId  
     * @param integer $chasedNum 退款几期
     */
    public static function refundAdditional($lotteryAdditionalId, $chasedNum) {
        $lotAddInfo = LotteryAdditional::findOne(["lottery_additional_id" => $lotteryAdditionalId, "status" => 2]);
        if ($lotAddInfo == null) {
            return false;
        }
        $refundMoney = $lotAddInfo->bet_money * $chasedNum;
        $fundsSer = new FundsService();
        $ret = $fundsSer->operateUserFunds($lotAddInfo->cust_no, 0, $refundMoney, (0 - $refundMoney), true, "追号解冻冻结金额");
        if ($refundMoney > 0) {
            $fundsSer->iceRecord($lotAddInfo->cust_no, $lotAddInfo->cust_type, $lotAddInfo->lottery_additional_code, $refundMoney, 2, "追号解冻冻结金额");
        }
        if ($ret["code"] == 0) {
            return true;
        }
        return false;
    }

    /**
     * 生成对应追号单
     * @param string $lotteryCode
     * @param string $oldVal
     * @return string
     */
    public static function randomOrder($lotteryCode, $oldVal) {
        $oldVal = trim($oldVal, "^");
        $vals = explode("^", $oldVal);
        $data = [];
        foreach ($vals as $val) {
            switch ($lotteryCode) {
                case "1001":       //双色球
                    $vs = explode("|", $val);
                    $redBall = explode(",", $vs[0]);
                    $blueBall = explode(",", $vs[1]);
                    $rBallArr = self::randomBall(1, 33, count($redBall), true);
                    $bBallArr = self::randomBall(1, 16, count($blueBall), true);
                    $data[] = implode(",", $rBallArr) . "|" . implode(",", $bBallArr);
                    break;
                case "1003":      //七乐彩
                    $redBall = explode(",", $val);
                    $rBallArr = self::randomBall(1, 30, count($redBall), true);
                    $data[] = implode(",", $rBallArr);
                    break;
                case "2001":      //大乐透
                    $vs = explode("|", $val);
                    $redBall = explode(",", $vs[0]);
                    $blueBall = explode(",", $vs[1]);
                    $rBallArr = self::randomBall(1, 35, count($redBall), true);
                    $bBallArr = self::randomBall(1, 12, count($blueBall), true);
                    $data[] = implode(",", $rBallArr) . "|" . implode(",", $bBallArr);
                    break;
                case "1002":     //福彩3D
                case "2002":     //排列三
                case "2003":     //排列五
                case "2004":     //七星彩
                    $vs = explode("|", $val);
                    $val = [];
                    foreach ($vs as $v) {
                        $balls = explode(",", $vs[0]);
                        $balls = self::randomBall(0, 9, count($balls));
                        $val[] = implode(",", $balls);
                    }
                    $data[] = implode("|", $val);
                    break;
            }
        }
        return implode("^", $data) . "^";
    }

    /**
     * 生成随机数数组
     * @param integer $min
     * @param integer $max
     * @param integer $num
     * @param boolean $addZero
     * @return array
     */
    public static function randomBall($min, $max, $num, $addZero = false) {
        $data = [];
        for ($key = 0; $key < $num; $key++) {
            while (1) {
                $val = rand($min, $max);
                if ($addZero == true) {
                    $val = sprintf("%02d", $val);
                }
                if (!in_array($val, $data)) {
                    $data[] = $val;
                    break;
                }
            }
        }

        sort($data);
        return $data;
    }

    /**
     * 
     * 任选下订单
     * auther GL ctx
     * @return json
     */
    public static function optionalOrder($custNo, $userId, $storeId, $storeCode, $source = 1, $sourceId = "") {
        $request = Yii::$app->request;
        $post = $request->post();
        $orderData = json_decode($post["order_data"], JSON_UNESCAPED_UNICODE);
        $lotteryCode = $orderData["lottery_code"];
        $classCopeting = new OptionalService();
        $ret = $classCopeting->optionalOrder($lotteryCode, $custNo, $userId, $storeId, $storeCode, $source, $sourceId);
        return $ret;
    }

    /**
     * 生成体彩子单
     * auther 咕啦 ctx
     * @param model $model
     * create_time 2017-06-09
     * @return int
     */
    public static function proSuborder($model) {
        set_time_limit(0);
        $ret = "";
        switch ($model->lottery_id) {
            case "1001":
                $con = new \app\modules\welfare\controllers\ColorBollController("productSuborder", "welfare");
                $ret = $con->productSuborder($model);
                break;
            case "1002":
                $con = new \app\modules\welfare\controllers\ThreeDWelController("productSuborder", "welfare");
                $ret = $con->productSuborder($model);
                break;
            case "1003":
                $con = new \app\modules\welfare\controllers\SevenFunColController("productSuborder", "welfare");
                $ret = $con->productSuborder($model);
                break;
            case "2001":
                $con = new \app\modules\sports\controllers\SuperlottoController("productSuborder", "sports");
                $ret = $con->productSuborder($model);
                break;
            case "2002":
                $con = new \app\modules\sports\controllers\ArrangedthreeController("productSuborder", "sports");
                $ret = $con->productSuborder($model);
                break;
            case "2003":
                $con = new \app\modules\sports\controllers\ArrangedfiveController("productSuborder", "sports");
                $ret = $con->productSuborder($model);
                break;
            case "2004":
                $con = new \app\modules\sports\controllers\SevenstarController("productSuborder", "sports");
                $ret = $con->productSuborder($model);
                break;
            case "2005":
                $con = new GuangdongService();
                $ret = $con->productSuborder($model);
                break;
            case "2006":
                $con = new JiangxiService();
                $ret = $con->productSuborder($model);
                break;
            case "2007":
                $con = new ShandongService();
                $ret = $con->productSuborder($model);
                break;
            case "3006":
            case "3007":
            case "3008":
            case "3009":
            case "3010":
            case "3011":
                $classCopeting = new CompetingController();
                $ret = $classCopeting->productSuborder($model);
                break;
            case "4001":
            case "4002":
                $classCopeting = new OptionalService();
                $ret = $classCopeting->optionalDetail($model);
                break;
            case '3001':
            case '3002':
            case '3003':
            case '3004':
            case '3005':
                $lanService = new BasketService();
                $ret = $lanService->productSuborder($model);
                break;
            default :$ret = [
                    "code" => 2,
                    "msg" => "错误彩种"
                ];
        }
        if ($ret["code"] == 0) {
            orderNews::outOrderNotice($model->store_id, $model->store_no, $model->cust_no, $model->lottery_order_code, $model->bet_money, $model->end_time, $model->lottery_id, $model->periods);
            $redis = Yii::$app->redis;
            $user = User::findOne(["user_id" => $model->store_id]);
            $redis->sadd("sockets:new_order_list", $user->cust_no);
            $userOperators = (new Query())->select("cust_no")->from("store_operator s")->join("left join", "user u", "u.user_id=s.user_id")->where(["s.store_id" => $model->store_id, "s.status" => 1])->all();
            foreach ($userOperators as $val) {
                $redis->sadd("sockets:new_order_list", $val["cust_no"]);
            }
        }
        return $ret;
    }

    /**
     * 篮球投注
     * @param type $custNo
     * @param type $storeId
     * @param type $source
     * @param type $sourceId
     * @return type
     */
    public static function basketOrder($custNo, $userId, $storeId, $storeCode, $source = 1, $sourceId = "") {
        $request = Yii::$app->request;
        $post = $request->post();
        $orderData = json_decode($post["order_data"], JSON_UNESCAPED_UNICODE);
        $lotteryCode = $orderData["lottery_code"];
        $classCopeting = new BasketService();
        $ret = $classCopeting->playOrder($lotteryCode, $custNo, $userId, $storeId, $storeCode, $source, $sourceId);
        return $ret;
    }

    /**
     * 说明: 扣除服务费
     * @author  kevi
     * @date 2017年11月20日 下午2:53:19
     * @param
     * @return 
     */
    public function serviceMoney(){
        $fundsSer = new FundsService();
        $serviceCharge = (ceil($lotOrder->bet_money * 0.2) / 100);
        $ret = $fundsSer->operateUserFunds($store->cust_no, 0 - $serviceCharge, 0 - $serviceCharge, 0, false);
        if ($ret["code"] != 0) {
            $tran->rollBack();
            return false;
        }
        $funds = (new Query())->select("all_funds")->from("user_funds")->where(["cust_no" => $store->cust_no])->one();
        $payRecord = new PayRecord();
        $payRecord->order_code = $orderCode;
        $payRecord->pay_no = Commonfun::getCode("PAY", "L");
        $payRecord->cust_no = $store->cust_no;
        $payRecord->cust_type = 2;
        $payRecord->user_name = $store->store_name;
        $payRecord->pay_pre_money = $serviceCharge;
        $payRecord->pay_money = $serviceCharge;
        $payRecord->pay_name = '余额';
        $payRecord->way_name = '余额';
        $payRecord->way_type = 'YE';
        $payRecord->pay_way = 3;
        $payRecord->pay_type_name = '出票服务费';
        $payRecord->pay_type = 16;
        $payRecord->body = '出票服务费';
        $payRecord->status = 1;
        $payRecord->balance = $funds["all_funds"];
        $payRecord->pay_time = date('Y-m-d H:i:s');
        $payRecord->modify_time = date("Y-m-d H:i:s");
        $payRecord->create_time = date('Y-m-d H:i:s');
        if ($payRecord->validate()) {
            $ret = $payRecord->save();
            if ($ret == false) {
                $tran->rollBack();
                return false;
            }
        } else {
            $tran->rollBack();
            return false;
        }
    }
}
