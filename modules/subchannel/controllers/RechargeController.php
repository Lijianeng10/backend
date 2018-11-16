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
use app\modules\common\services\ApiSysService;
use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;

class RechargeController extends Controller {

    public function actionIndex() {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $get = $request->get();
//        $channel = Bussiness::find()->select(['bussiness_id', 'name'])->indexBy('bussiness_id')->asArray()->all();
//        $channelArr[0] = '全部';
//        foreach ($channel as $key => $item) {
//            $channelArr[$key] = $item['name'];
//        }
        $bussinessId = $request->get('bussiness_id', '0');
        $start = $request->get('startdate', '');
        $end = $request->get('enddate', '');
        $statusCode = $request->get('status', '');
        $query = new Query();
        $field = ['api_user_apply.api_user_apply_id', 'api_user_apply.money', 'api_user_apply.status', 'api_user_apply.remark', 'c.name', 'api_user_apply.create_time', 'api_user_apply.cust_no',
            'u.user_tel', 'api_user_apply.apply_code', 's.nickname', 'api_user_apply.modify_time'];
        $data = $query->select($field)
                ->from('api_user_apply')
                ->innerJoin('bussiness c', 'c.user_id = api_user_apply.user_id')
                ->leftJoin('user u', 'u.user_id = api_user_apply.user_id')
                ->leftJoin('sys_admin s', 's.admin_id = api_user_apply.opt_id')
                ->where(['api_user_apply.type' => 1]);
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
        return $this->render('apply-list', ['data' => $list, 'get' => $get]);
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
     * 新增充值申请
     * @return type
     */
    public function actionAddRecharge() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $session = Yii::$app->session;
            $apiUserAppyId = $request->post('bussinessId');
            $money = $request->post('money');
            $remark = $request->post('remark');
            $nowTime = date('Y-m-d H:i:s');
            $day = date('ymdHis', time());
            if (!isset($apiUserAppyId) || $apiUserAppyId == "") {
                //判断当前登录用户是咕啦内部用户还是合作渠道商户,合作渠道商是本账号还是所属操作账号
                if ($session["type"] == 1) {
                    if ($session["agent_code"] == "gl00015788") {
                        $bussiness = Bussiness::find()->where(['cust_no' => $session["admin_name"]])->asArray()->one();
                    } else {
                        $bussiness = Bussiness::find()->where(['cust_no' => $session["agent_code"]])->asArray()->one();
                    }
                }
            } else {
                $bussiness = Bussiness::find()->where(['bussiness_id' => $apiUserAppyId])->asArray()->one();
            }
//            充值凭证
            if (isset($_FILES['upfile'])) {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/voucher_pic/' .$bussiness['cust_no']  . '/';
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
            $apiUserAppy = new ApiUserApply();
            $apiUserAppy->apply_code = $bussiness['bussiness_appid'] . '-' . date('ymdHis');
            $apiUserAppy->user_id = $bussiness['user_id'];
            $apiUserAppy->cust_no = $bussiness['cust_no'];
            $apiUserAppy->type = 1;
            $apiUserAppy->money = $money;
            $apiUserAppy->voucher_pic = $picUrl;
            $apiUserAppy->remark = $remark;
            $apiUserAppy->status = 1;
            $apiUserAppy->create_time = $nowTime;
            if ($apiUserAppy->validate()) {
                $result = $apiUserAppy->save();
                if ($result == false) {
                    return $this->jsonResult(109, '充值提交审核失败');
                }
                //微信推送（给财务推送）
//            $wechatTool = new WechatTool();
//            $title = '您有一个待审核事项（请及时处理）！！！';
//            $name = '充值审核';
//            $userOpenId ='oV4Ujw-7Ymtu2vP8UCpWHje-v_iE';
//            if(YII_ENV_DEV){
//                $userOpenId ='otEbv0SK-oV4Ujw-7Ymtu2vP8UCpWHje-v_iE';
//            }
//
//            $cust_no ="{$bussiness['name']}({$custNo},{$bussiness['user_tel']})";
//            $czMoney ='金额: ' .$money . ' 元';
//            $czTime = $nowTime;
//            $wechatTool->sendTemplateMsgRechargeApiUser($title, $userOpenId, $name ,$cust_no,$czMoney, $czTime);
                return $this->jsonResult(600, '充值已提交审核');
            } else {
                return $this->jsonResult(109, '表单验证失败', $apiUserAppy->errors);
            }
        } else {
            $channel = Bussiness::find()->select(['bussiness_id', 'name'])->where(["status"=>1])->indexBy('bussiness_id')->asArray()->all();
            $channelArr[0] = '请选择';
            foreach ($channel as $key => $item) {
                $channelArr[$key] = $item['name'];
            }
            return $this->render("add-recharge", ["channel" => $channelArr]);
        }
    }

}
