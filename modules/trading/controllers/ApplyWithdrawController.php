<?php

namespace app\modules\trading\controllers;

use Yii;
use yii\web\Controller;
use app\modules\common\models\ApiUserApply;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Bussiness;
use yii\base\Exception;
use app\modules\common\models\PayRecord;
use app\modules\common\helpers\Commonfun;
use app\modules\common\models\UserFunds;
use yii\db\Expression;
use app\modules\common\models\Withdraw;
use app\modules\common\models\IceRecord;
use app\modules\common\services\ApiSysService;

class ApplyWithdrawController extends Controller {

    public function actionApplyList() {
        $request = Yii::$app->request;
        $get = $request->get();
        $channel = Bussiness::find()->select(['bussiness_id', 'name'])->indexBy('bussiness_id')->asArray()->all();
        $channelArr[0] = '全部';
        foreach ($channel as $key => $item) {
            $channelArr[$key] = $item['name'];
        }
        $bussinessId = $request->get('bussiness_id', '0');
        $start = $request->get('startdate', '');
        $end = $request->get('enddate', '');
        $statusCode = $request->get('status', '');
        $query = new Query();
        
        $field = ['api_user_apply.voucher_pic','api_user_apply.api_user_apply_id', 'api_user_apply.money', 'api_user_apply.status', 'api_user_apply.remark', 'c.name', 'api_user_apply.create_time', 'api_user_apply.cust_no', 'u.user_tel',
            'b.user_name', 'b.bank_open', 'api_user_apply.apply_code', 'b.card_number', 'api_user_apply.modify_time', 's.nickname'];
        $data = $query->select($field)
                ->from('api_user_apply')
                ->innerJoin('bussiness c', 'c.user_id = api_user_apply.user_id')
                ->leftJoin('api_user_bank b', 'b.api_user_bank_id = api_user_apply.api_user_bank_id')
                ->leftJoin('user u', 'u.user_id = api_user_apply.user_id')
                ->leftJoin('sys_admin s', 's.admin_id = api_user_apply.opt_id')
                ->where(['api_user_apply.type' => 2]);
        if (!empty($bussinessId)) {
            $data = $data->andWhere(['c.bussiness_id' => $bussinessId]);
        }
        if ($statusCode != '') {
            $data = $data->andWhere(["api_user_apply.status" => $statusCode]);
        }

        if ($start != '') {
            $data = $data->andWhere(['>=', 'api_user_apply.create_time', $start . " 00:00:00"]);
        }
//        else {
//            $data = $data->andWhere(['>=', 'api_user_apply.create_time', date("Y-m-d", strtotime("-1 weeks")) . " 00:00:00"]);
//        }
        if ($end != '') {
            $data = $data->andWhere(['<=', 'api_user_apply.create_time', $end . " 23:59:59"]);
        } else {
            $data = $data->andWhere(['<=', 'api_user_apply.create_time', date("Y-m-d") . " 23:59:59"]);
        }
        $listArr = $data->orderBy("api_user_apply.create_time desc");
        $list = new ActiveDataProvider([
            'query' => $listArr,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $this->render('apply-list', ['data' => $list, 'get' => $get, 'channel' => $channelArr]);
    }

    public function actionRead() {
        $this->layout = false;
        if (!Yii::$app->request->get()) {
            echo '操作错误';
            exit();
        }
        $request = Yii::$app->request;
        $applyId = $request->get('applyId', '');
        if (empty($applyId)) {
            echo '参数缺失';
            exit();
        }
        $field = ['b.user_name', 'b.bank_open', 'c.name', 'b.branch', 'b.card_number', 'b.province', 'b.city', 'api_user_apply.voucher_pic'];
        $model = ApiUserApply::find()->select($field)
                ->innerJoin('bussiness c', 'c.user_id = api_user_apply.user_id')
                ->leftJoin('api_user_bank b', 'b.api_user_bank_id = api_user_apply.api_user_bank_id')
                ->where(['api_user_apply.api_user_apply_id' => $applyId])
                ->asArray()
                ->one();
        return $this->render('read', ['model' => $model]);
    }

    public function actionReview() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $applyId = $post['applyId'];
            $apply = ApiUserApply::findOne(["api_user_apply_id" => $applyId, 'status' => 1]);
            if (empty($apply)) {
                return $this->jsonResult(109, '该申请订单已失效');
            }
            $db = \Yii::$app->db;
            $trans = $db->beginTransaction();
            $certStatus = $post['cert_status'];
            $bankInfo = ApiUserApply::find()->select(['b.user_name', 'b.bank_open', 'b.branch', 'b.card_number', 'b.province', 'b.city'])
                    ->innerJoin('api_user_bank b', 'b.api_user_bank_id = api_user_apply.api_user_bank_id')
                    ->where(['api_user_apply.api_user_apply_id' => $applyId])
                    ->asArray()
                    ->one();
            try {
                $apply->status = $certStatus;
                $apply->refuse_reson = $post["review_remark"];
                $apply->opt_id = \Yii::$app->session["admin_id"];
                $apply->modify_time = date('Y-m-d H:i:s');
                if (!$apply->save()) {
                    throw new Exception('提交失败, 申请修改失败');
                }
                $iceFunds = UserFunds::find()->select(['ice_funds'])->where(['cust_no' => $apply->cust_no])->asArray()->one();
                if(bccomp($apply->money, $iceFunds['ice_funds'], 2) === 1) {
                    throw new Exception('冻结余额不足');
                }
                if ($certStatus == 2) {
                    $update = ['all_funds' => new Expression('all_funds-' . $apply->money), 'ice_funds' => new Expression('ice_funds-' . $apply->money), 'modify_time' => date('Y-m-d H:i:s')];
                    $where = ['user_id' => $apply->user_id, 'cust_no' => $apply->cust_no];
                    $userFund = UserFunds::updateAll($update, $where);
                    if ($userFund === false) {
                        throw new Exception('提交失败, 余额更新失败');
                    }
                    $withdraw = new Withdraw;
                    $withdraw->cust_no = $apply->cust_no;
                    $withdraw->cust_type = 1;
                    $withdraw->withdraw_code = Commonfun::getCode('TX', 'T');
                    $withdraw->withdraw_money = $apply->money;
                    $withdraw->status = 2;
                    $withdraw->outer_no = $apply->apply_code;  // 第三方交易号
                    $withdraw->bank_info = $bankInfo['card_number']; // 银行卡号
                    $withdraw->cardholder = $bankInfo['user_name']; // 持卡人
                    $withdraw->bank_name = $bankInfo['bank_open']; // 银行
                    $withdraw->actual_money = $apply->money; // 实际到账金额
                    $withdraw->fee_money = 0; // 提现费用
                    $withdraw->toaccount_time = date('Y-m-d H:i:s');
                    $withdraw->create_time = date('Y-m-d H:i:s');
                    $withdraw->modify_time = date('Y-m-d H:i:s');
                    if(!$withdraw->save()) {
                        throw new Exception('提交失败, 提现交易记录写入失败');
                    }
                    $funds = UserFunds::find()->select(['all_funds', 'ice_funds'])->where(['cust_no' => $apply->cust_no])->asArray()->one();
                    $iceRecord = new IceRecord();
                    $iceRecord->cust_no = $apply->cust_no;
                    $iceRecord->order_code = $withdraw->withdraw_code;
                    $iceRecord->cust_type = 1;
                    $iceRecord->money = $apply->money;
                    $iceRecord->body = '提现-划账';
                    $iceRecord->type = 2;
                    $iceRecord->ice_balance = $funds["ice_funds"];
                    $iceRecord->create_time = date("Y-m-d H:i:s");
                    $iceRecord->modify_time = date('Y-m-d H:i:s');
                    if (!$iceRecord->save()) {
                        throw new Exception('提交失败, 冻结记录表写入失败');
                    }
                    $payRecord = new PayRecord;
                    $payRecord->order_code = $withdraw->withdraw_code;
                    $payRecord->cust_no = $apply->cust_no;
                    $payRecord->cust_type = 1;
                    $payRecord->pay_no = Commonfun::getCode('PAY', 'L');
                    $payRecord->outer_no = $apply->apply_code;
                    $payRecord->pay_pre_money = $apply->money;
                    $payRecord->pay_money =  $apply->money;
                    $payRecord->pay_name = '余额';
                    $payRecord->way_name = '余额';
                    $payRecord->way_type = 'YE';
                    $payRecord->pay_way = 3;
                    $payRecord->pay_type_name = '提现';
                    $payRecord->pay_type = 4;
                    $payRecord->body = '快捷提现';
                    $payRecord->status = 1;
                    $payRecord->pay_time = date('Y-m-d H:i:s');
                    $payRecord->balance = $funds['all_funds'];
                    $payRecord->create_time = date('Y-m-d H:i:s');
                    $payRecord->modify_time = date('Y-m-d H:i:s');
                    if (!$payRecord->save()) {
                        throw new Exception('提交失败,交易明细写入失败');
                    }
                }elseif ($certStatus == 3) {
                    $update = ['able_funds' => new Expression('able_funds+' . $apply->money), 'ice_funds' => new Expression('ice_funds-' . $apply->money), 'modify_time' => date('Y-m-d H:i:s')];
                    $where = ['user_id' => $apply->user_id, 'cust_no' => $apply->cust_no];
                    $userFund = UserFunds::updateAll($update, $where);
                    if ($userFund === false) {
                        throw new Exception('提交失败, 余额更新失败');
                    }
                    $funds = UserFunds::find()->select(['all_funds', 'ice_funds'])->where(['cust_no' => $apply->cust_no])->asArray()->one();
                    $iceRecord = new IceRecord();
                    $iceRecord->cust_no = $apply->cust_no;
                    $iceRecord->order_code = $apply->apply_code;
                    $iceRecord->cust_type = 1;
                    $iceRecord->money = $apply->money;
                    $iceRecord->body = '提现-解冻';
                    $iceRecord->type = 2;
                    $iceRecord->ice_balance = $funds["ice_funds"];
                    $iceRecord->create_time = date("Y-m-d H:i:s");
                    $iceRecord->modify_time = date('Y-m-d H:i:s');
                    if (!$iceRecord->save()) {
                        throw new Exception('提交失败, 冻结记录表写入失败');
                    }
                }
                $trans->commit();
                if($certStatus == 2) {
                    ApiSysService::payRecord($payRecord->pay_record_id);
                }
                ApiSysService::iceRecord($iceRecord->ice_record_id);
                return $this->jsonResult(600, '提交成功');
            } catch (Exception $ex) {
                $trans->rollBack();
                return $this->jsonResult(109, $ex->getMessage());
            }
        } else {
            $get = \Yii::$app->request->get();
            $applyId = $get['applyId'];
            $apply = ApiUserApply::findOne(["api_user_apply_id" => $get["applyId"], 'status' => 1]);
            if (empty($apply)) {
                echo '该申请已失效，请勿重复审核';
                exit;
            }
            return $this->render("review", ["applyId" => $applyId]);
        }
    }

}
