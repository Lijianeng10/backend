<?php

namespace app\modules\agents\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\common\models\TicketDispenser;
use app\modules\common\helpers\Constants;

class TicketController extends \yii\web\Controller {

    /**
     * 门店机器信息列表
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $query = (new Query())->select("t.*,s.store_name")
                ->from("ticket_dispenser as t")
                ->leftJoin("store as s", "s.store_code = t.store_no and s.status= 1");
        if (isset($get["store_name"]) && !empty($get["store_name"])) {
            $query = $query->andWhere(["or", ["like", "s.store_name", $get["store_name"]], ["s.store_code" => $get["store_name"]]]);
        }
        if (isset($get["type"]) && !empty($get["type"])) {
            $query = $query->andWhere(["t.type" => $get["type"]]);
        }
        if (isset($get["status"]) && !empty($get["status"])) {
            $query = $query->andWhere(["t.status" => $get["status"]]);
        }
        $query = $query->orderBy("ticket_dispenser_id desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        return $this->render("index", ["data" => $data]);
    }

    /**
     * 门店机器启用禁用
     */
    public function actionEdituse() {
        $post = \Yii::$app->request->post();
        $res = TicketDispenser::updateAll(["status" => $post["sta"],], ["ticket_dispenser_id" => $post["ticket_dispenser_id"]]);
        if ($res) {
            return $this->jsonResult(600, "修改成功");
        } else {
            return $this->jsonResult(109, "修改失败");
        }
    }

    /**
     * 编辑门店机器信息
     */
    public function actionEditDispenser() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            $ticket_dispenser_id = $get["ticket_dispenser_id"];
            if (empty($ticket_dispenser_id)) {
                return $this->jsonResult(109, "参数缺失");
            }
            $field = ['ticket_dispenser.ticket_dispenser_id', 'ticket_dispenser.type', 'ticket_dispenser.dispenser_code', 'ticket_dispenser.vender_id', 'ticket_dispenser.sn_code', 'ticket_dispenser.store_no',
                'ticket_dispenser.pre_out_nums', 'ticket_dispenser.mod_nums', 'ticket_dispenser.status', 'ticket_dispenser.out_lottery','s.sale_lottery'];
            $ticketRes = TicketDispenser::find()->select($field)
                    ->innerJoin('store s', 's.store_code = ticket_dispenser.store_no and s.status = 1')
                    ->where(["ticket_dispenser_id" => $ticket_dispenser_id])
                    ->asArray()
                    ->one();
            $saleArr = explode(',', $ticketRes['sale_lottery']);
            $outArr = explode(',', $ticketRes['out_lottery']);
            $lotteryName = Constants::LOTTERY;
            $saleLottery = [];
            foreach ($saleArr as $v) {
                $saleLottery[$v] = $lotteryName[$v];
            }
            $ticketRes['sale_lottery'] = $saleLottery;
            $ticketRes['out_lottery'] = $outArr;
            return $this->render("edit-dispenser", ["data" => $ticketRes]);
        } elseif (Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            $ticket_dispenser_id = $request->post('ticket_dispenser_id', '');
            $type = $request->post('type', '');
            $dispenser_code = $request->post('dispenser_code', '');
            $pre_out_nums = $request->post('pre_out_nums', '');
            $mod_nums = $request->post('mod_nums', '');
            $vender_id = $request->post('vender_id', '');
            $sn_code = $request->post('sn_code', '');
            $codes = $request->post('codes', '');
            if (empty($ticket_dispenser_id) || empty($type) || empty($dispenser_code) || empty($pre_out_nums)) {
                return $this->jsonResult(109, '参数缺失');
            }
            $ticketRes = TicketDispenser::find()->where(["ticket_dispenser_id" => $ticket_dispenser_id])->one();
            if (empty($ticketRes)) {
                return $this->jsonResult(109, '数据有误 ，未找到该机器');
            }
            $ticketRes->type = $type;
            $ticketRes->dispenser_code = $dispenser_code;
            $ticketRes->vender_id = $vender_id;
            $ticketRes->sn_code = $sn_code;
            $ticketRes->pre_out_nums = $pre_out_nums;
            $ticketRes->mod_nums = $mod_nums;
            $ticketRes->out_lottery = implode(',', $codes);
            $ticketRes->modify_time = date("Y-m-d,H:i:s");
            if ($ticketRes->validate()) {
                $ret = $ticketRes->save();
                if ($ret === false) {
                    return $this->jsonResult(109, "失败，门店机器信息修改失败");
                } else {
                    return $this->jsonResult(600, "门店机器修改成功");
                }
            } else {
                return $this->jsonResult(109, "失败，门店机器表验证失败");
            }
        }
    }

    /**
     * 删除门店机器数据
     */
    public function actionDeleteTicket() {
        $post = \Yii::$app->request->post();
        $result = TicketDispenser::deleteAll(["ticket_dispenser_id" => $post["ticket_dispenser_id"]]);
        if ($result) {
            return $this->jsonResult(600, "删除成功");
        } else {
            return $this->jsonResult(109, "删除失败");
        }
    }

}
