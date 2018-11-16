<?php

namespace app\modules\trading\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use app\modules\member\models\User;
use app\modules\member\models\UserFunds;
use app\modules\common\services\ApiSysService;

class ActivityController extends Controller {

    public function actionIndex() {
        return $this->render("index");
    }

    public function actionAddMoney() {
        $post = \Yii::$app->request->post();
        if (!isset($post["money"]) || !isset($post["userTel"]) || empty($post["money"]) || empty($post["userTel"])) {
            return $this->jsonResult(109, "参数缺失！", "");
        }
        $money = $post["money"];
        $user = User::findOne(["user_tel" => $post["userTel"]]);
        if (empty($user)) {
            return $this->jsonResult(109, "未找到该用户！", "");
        }
        $db = \Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            $userFunds = UserFunds::findOne(["user_id" => $user->user_id]);
            $userFunds->all_funds = $userFunds->all_funds + $money;
            $userFunds->able_funds = $userFunds->able_funds + $money;
            if($userFunds->no_withdraw + $money < 0) {
                $userFunds->no_withdraw = 0;
            } else {
                $userFunds->no_withdraw = $userFunds->no_withdraw + $money;
            }
            $userFunds->modify_time = date("Y-m-d H:i:s");
            if ($userFunds->validate()) {
                $ret = $userFunds->save();
                if ($ret == false) {
                    $tran->rollBack();
                    return $this->jsonResult(109, "发放活动奖金失败！", "");
                }
                $order_code = "GLCACT" . date("YmdHis") . "O" . (sprintf("%06d", rand(1, 999999)));
                $retRecord = $db->createCommand()->insert("pay_record", [
                            "order_code" => $order_code,
                            "cust_no" => $user->cust_no,
                            "cust_type" => 1,
                            "pay_no" => "GLCACT" . date("YmdHis") . "P" . (sprintf("%06d", rand(1, 999999))),
                            "pay_name" => "余额",
                            "way_name" => "余额",
                            "way_type" => "YE",
                            "pay_way" => 3,
                            "pay_money" => $money,
                            "pay_pre_money" => $money,
                            "balance" => $userFunds->all_funds,
                            "pay_type_name" => "活动奖金发放",
                            "pay_type" => 20,
                            "body" => "活动奖金发放",
                            "status" => 1,
                            "pay_time" => date("Y-m-d H:i:s"),
                            "modify_time" => date("Y-m-d H:i:s"),
                            "create_time" => date("Y-m-d H:i:s")
                        ])->execute();
                if ($retRecord == false) {
                    $tran->rollBack();
                    return $this->jsonResult(109, "交易记录插入失败！", "");
                }
                //获取刚插入数据ID
                $nowId = $db->getLastInsertID();
            } else {
                $tran->rollBack();
                return $this->jsonResult(109, "数据错误！", $userFunds->getFirstErrors());
            }
            $tran->commit();
            //同步数据到小郑
            ApiSysService::payRecord($nowId);
            //微信通知
            $this->sendCampaignBonusMsg($order_code);
            return $this->jsonResult(600, "发放成功！", "");
        } catch (\yii\db\Exception $e) {
            $tran->rollBack();
            return $this->jsonResult(109, "数据错误！", $e);
        }
    }

    /**
     * 发送活动奖金发放微信推送
     * @param type $order_code
     */
    public function sendCampaignBonusMsg($order_code) {
        @file_get_contents(\Yii::$app->params["userDomain"] . "/api/cron/time/send-campaign-bonus-msg?order_code=" . $order_code);
    }

}
