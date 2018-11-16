<?php

namespace app\modules\subchannel\controllers;

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
use app\modules\common\models\ApiUserBank;
use app\modules\common\services\FundsService;
use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;

class WithdrawController extends Controller {

    public function actionIndex() {
        $session = Yii::$app->session;
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
        //判断当前登录用户是咕啦内部用户还是合作渠道商户,合作渠道商是本账号还是所属操作账号
        if ($session["type"] == 1) {
            if ($session["agent_code"] == "gl00015788") {
                $query = $query->andWhere(['c.cust_no' => $session["admin_name"]]);
            } else {
                $query = $query->andWhere(['c.cust_no' => $session["agent_code"]]);
            }
        }
        if (!empty($bussinessId)) {
            $data = $data->andWhere(['c.bussiness_id' => $bussinessId]);
        }
        if ($statusCode != '') {
            $data = $data->andWhere(["api_user_apply.status" => $statusCode]);
        }

        if ($start != '') {
            $data = $data->andWhere(['>=', 'api_user_apply.create_time', $start . " 00:00:00"]);
        }
        if ($end != '') {
            $data = $data->andWhere(['<=', 'api_user_apply.create_time', $end . " 23:59:59"]);
        }
        $listArr = $data->orderBy("api_user_apply.create_time desc");
        $list = new ActiveDataProvider([
            'query' => $listArr,
            'pagination' => [
                'pageSize' => 20,
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

    /**
     * 获取渠道商户银行卡信息、账户资金信息
     */
    public function actionGetBussinessInfo() {
        $session = Yii::$app->session;
        $request = \Yii::$app->request;
        $bussinessId = $request->post('bussinessId');
        if ($bussinessId == "") {
            if ($session["type"] == 1) {
                if ($session["agent_code"] == "gl00015788") {
                    $bussiness = Bussiness::find()->where(["cust_no" => $session["admin_name"]])->asArray()->one();
                } else {
                    $bussiness = Bussiness::find()->where(["cust_no" => $session["agent_code"]])->asArray()->one();
                }
            }
        } else {
            $bussiness = Bussiness::find()->where(["bussiness_id" => $bussinessId])->asArray()->one();
        }

        //银行卡列表
        $bankLists = ApiUserBank::find()
                ->where(['bussiness_id' => $bussiness["bussiness_id"], 'status' => 1])
                ->orderBy('is_default desc , api_user_bank_id desc')
                ->asArray()
                ->all();
        //资金信息
        $fundInfo = \app\modules\member\models\UserFunds::find()->select(["able_funds", "ice_funds", "no_withdraw"])
                ->where(["cust_no" => $bussiness["cust_no"]])
                ->asArray()
                ->one();
        return $this->jsonResult(600, "获取成功", ["bankLists" => $bankLists, "fundInfo" => $fundInfo, "bussiness" => $bussiness]);
    }

    /**
     * 新增提现申请
     * @return type
     */
    public function actionAddWithdraw() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $session = Yii::$app->session;
            $bussinessId = $request->post('bussinessId');
            $userId = $request->post('userId');
            $custNo = $request->post('custNo');
            $bussinessAppid = $request->post('bussinessAppid');
            $bankId = $request->post('banksId');
            $money = $request->post('money');
            $remark = $request->post('remark');
            $nowTime = date('Y-m-d H:i:s');
            $day = date('ymdHis', time());
            if (isset($_FILES['upfile'])) {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/voucher_pic/' .$custNo  . '/';
                $type = strtolower(substr($file['name'], strrpos($file['name'], '.') + 1));
                $name = $day . '.' . $type;
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir, $name);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $picUrl = $path['result']['ret_path'];
            } else {
                return $this->jsonResult(109, '请上传充值凭证', '');
            }
            //事务
            $db = Yii::$app->db;
            $tran = $db->beginTransaction();
            try {
                $apiUserAppy = new ApiUserApply();
                $apiUserAppy->apply_code = $bussinessAppid . '-' . date('ymdHis');
                $apiUserAppy->user_id = $userId;
                $apiUserAppy->cust_no = $custNo;
                $apiUserAppy->type = 2;
                $apiUserAppy->money = $money;
                $apiUserAppy->voucher_pic = $picUrl;
                $apiUserAppy->remark = $remark;
                $apiUserAppy->status = 1;
                $apiUserAppy->api_user_bank_id = $bankId;
                $apiUserAppy->create_time = $nowTime;
                $apiUserAppy->modify_time = $nowTime;
                if (!$apiUserAppy->save()) {
                    throw new \Exception('表单验证失败', 109, $apiUserAppy->errors);
                }
                //可用余额到冻结余额
                $funds = new FundsService();
                $r = $funds->operateUserFunds($custNo, 0, $money, $money, $optWithdraw = false, $body = "");
                if ($r['code'] != 0) {
                    throw new \Exception('资金明细变动失败', 109);
                }
                $res = $funds->iceRecord($custNo, 1, $apiUserAppy->apply_code, $money, 1, $body = "提现冻结");
                if (!$res) {
                    throw new \Exception('冻结明细新增失败', 109);
                }
                $tran->commit();
                return $this->jsonResult(600, '提现已提交审核');
            } catch (\Exception $e) {
                $tran->rollBack();
                return $this->jsonResult(109, $e->getMessage());
            }

//            if ($r['code'] == 0) {
//                //微信推送（给财务推送）
//                $wechatTool = new WechatTool();
//                $title = '您有一个待审核事项（请及时处理）！！！';
//                $name = '提现审核';
//                $userOpenId = 'otEbv0SK-A0T5dBi17TPIOA1dXkg';
//                $cust_no = "{$bussiness['name']}({$custNo},{$bussiness['user_tel']})";
//                $czMoney = '金额: ' . $money . ' 元';
//                $czTime = $nowTime;
//                $wechatTool->sendTemplateMsgRechargeApiUser($title, $userOpenId, $name, $cust_no, $czMoney, $czTime);
//                return $this->jsonResult(600, '提现已提交审核');
//            }
        } else {
            $channel = Bussiness::find()->select(['bussiness_id', 'name'])->where(["status" => 1])->indexBy('bussiness_id')->asArray()->all();
            $channelArr[0] = '请选择';
            foreach ($channel as $key => $item) {
                $channelArr[$key] = $item['name'];
            }
            return $this->render("add-withdraw", ["channel" => $channelArr]);
        }
    }

    /**
     * 新增银行卡
     * @return type
     */
    public function actionAddBanks() {
        $this->layout = false;
        $session = Yii::$app->session;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $bussinessId = $request->post('bussinessId');
            $userId = $request->post('userId');
            $name = $request->post('name');
            $banks = $request->post('banks');
            $province = $request->post('province');
            $city = $request->post('city');
            $branch = $request->post('branch');
            $card = $request->post('card');
            if(empty($bussinessId)||empty($userId)||empty($name)||empty($banks)||empty($province)||empty($city)||empty($branch)||empty($card)){
                return $this->jsonResult(109, '参数缺失');
            }
            $bank = new ApiUserBank();
            $bank->user_id = $userId;
            $bank->bussiness_id = $bussinessId;
            $bank->user_name = $name;
            $bank->bank_open = $banks;
            $bank->branch = $branch;
            $bank->card_number = $card;
            $bank->province = $province;
            $bank->city = $city;
            if(!$bank->save()){
                return $this->jsonResult(109,'添加失败');
            }
            return $this->jsonResult(600,'添加成功',$bank->attributes);
        } else {
            $request = \Yii::$app->request;
            $bId = $request->get("bussiness_id");
            if ($bId == "") {
                if ($session["type"] == 1) {
                    if ($session["agent_code"] == "gl00015788") {
                        $bussiness = Bussiness::find()->where(["cust_no" => $session["admin_name"]])->asArray()->one();
                    } else {
                        $bussiness = Bussiness::find()->where(["cust_no" => $session["agent_code"]])->asArray()->one();
                    }
                }
            } else {
                $bussiness = Bussiness::find()->where(["bussiness_id" => $bId])->asArray()->one();
            }
            return $this->render("add-banks", ["bussiness" => $bussiness]);
        }
    }

}
