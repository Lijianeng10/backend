<?php

namespace app\modules\lottery\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\common\models\AutoOutOrder;
use app\modules\common\helpers\Constants;

class AutoticketController extends \yii\web\Controller {

    /**
     * 自动出票记录列表
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $status = Constants::AUTO_STATUS;
        $play = Constants::AUTO_PLAY;
        $play[""] = "请选择";
        $query = AutoOutOrder::find();
        if (isset($get["out_order_code"]) && !empty($get["out_order_code"])) {
            $query = $query->andWhere(["out_order_code" => $get["out_order_code"]]);
        }
        if (isset($get["order_code"]) && !empty($get["order_code"])) {
            $query = $query->andWhere(["order_code" => $get["order_code"]]);
        }
        if (isset($get["ticket_code"]) && !empty($get["ticket_code"])) {
            $query = $query->andWhere(["ticket_code" => $get["ticket_code"]]);
        }
        if (isset($get["status"]) && !empty($get["status"])) {
            $query = $query->andWhere(["status" => $get["status"]]);
        }
        if (isset($get["play"]) && !empty($get["play"])) {
            $query = $query->andWhere(["lottery_code" => $get["play"]]);
        }
        $query = $query->orderBy("out_order_id desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
        return $this->render("index", ["data" => $data, "get" => $get, "status" => $status, "play" => $play]);
    }

    public function actionAutoOrderEdit() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $autoId = $request->post('out_order_id', '');
            $freeType = $request->post('free_type', '');
            $lotteryCode = $request->post('lottery_code', '');
            $playCode = $request->post('play_code', '');
            $periods = $request->post('periods', '');
            $betVal = $request->post('bet_val', '');
            $amount = $request->post('amount', '');
            $autoData = AutoOutOrder::findOne(['out_order_id' => $autoId, 'status' => 3]);
            if (empty($autoData)) {
                return $this->jsonError(109, '该自动出票单无需编辑');
            }
            $autoData->free_type = $freeType;
            $autoData->lottery_code = $lotteryCode;
            $autoData->play_code = $playCode;
            $autoData->periods = $periods;
            $autoData->bet_val = $betVal;
            $autoData->amount = $amount;
            $autoData->status = 1;
            $autoData->modify_time = date('Y-m-d H:i:s');
            if (!$autoData->save()) {
                return $this->jsonError(109, '修改失败');
            }
            $surl = \Yii::$app->params['userDomain'] . '/api/cron/cron/again-auto-out';
            $data = ['autoCode' => $autoData->out_order_code, 'thirdOrderCode' => ''];
            \Yii::sendCurlPost($surl, $data);
            return $this->jsonResult(600, '修改成功');
        } else {
            $request = \Yii::$app->request;
            $autoOrderId = $request->get('outId', '');
            if (empty($autoOrderId)) {
                echo '参数有误！！';
                exit;
            }
            $field = ['out_order_id', 'out_order_code', 'free_type', 'lottery_code', 'play_code', 'periods', 'bet_val', 'bet_add', 'multiple', 'amount', 'count', 'status'];
            $autoOrderData = AutoOutOrder::find()->select($field)->where(['out_order_id' => $autoOrderId, 'status' => 3])->asArray()->one();
            if (empty($autoOrderData)) {
                echo '该自动出票单无需编辑！！';
                exit;
            }
            return $this->render('edit', ['data' => $autoOrderData]);
        }
    }

    public function actionAutoOrderRead() {
        $this->layout = false;
        $request = \Yii::$app->request;
        $autoOrderId = $request->get('outId', '');
        if (empty($autoOrderId)) {
            echo '参数有误！！';
            exit;
        }
        $field = ['out_order_id', 'out_order_code', 'free_type', 'lottery_code', 'play_code', 'periods', 'bet_val', 'bet_add', 'multiple', 'amount', 'count', 'status'];
        $autoOrderData = AutoOutOrder::find()->select($field)->where(['out_order_id' => $autoOrderId])->asArray()->one();
        return $this->render('read', ['data' => $autoOrderData]);
    }

}
