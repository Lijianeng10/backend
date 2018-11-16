<?php

namespace app\modules\website\controllers;

use app\modules\common\models\ApiCreditCard;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;

class CreditCardController extends Controller {

    public function actionIndex() {
        $request = \Yii::$app->request;
        $get = $request->get();
        $cardName = $request->get('card_name', '');
        $status = $request->get('status', '');
        $where = ['and'];
        $query = ApiCreditCard::find()->select(['credit_card_id', 'card_name', 'cash_quota', 'free_periods', 'pic_url', 'status', 'sort', 'card_activity', 'create_time', 'jump_url']);
        if (!empty($cardName)) {
            $where[] = ['like', 'card_name', $cardName];
        }
        if (isset($status) && $status != '') {
            $where[] = ['status' => $status];
        }
        $statusList = ['' => '全部', '0' => '禁用', '1' => '启用'];
        $cardList = $query->where($where)->orderBy('create_time desc');
        $data = new ActiveDataProvider([
            'query' => $cardList,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('index', ['dataList' => $data, "get" => $get, "status" => $statusList]);
    }

    /**
     * 新增贷款
     * @return type
     */
    public function actionAdd() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $cardName = $request->post('cardName', '');
            $cashQuota = $request->post('cashQuota', '');
            $freePeriods = $request->post('freePeriods', '');
            $cardActivity = $request->post('cardActivity', '');
            $sort = $request->post('sort', 99);
            $jumpUrl = $request->post('jumpUrl', '');
            if(empty($cardName) || ctype_space($cardName)) {
                return $this->jsonError(109, '请输入有效信用卡名称');
            }
            if(empty($cashQuota) || ctype_space($cashQuota)) {
                return $this->jsonError(109, '请输入有效的取现额度');
            }
            if(empty($freePeriods) || ctype_space($freePeriods)) {
                return $this->jsonError(109, '请输入有效的免息期');
            }
            $creditCard = new ApiCreditCard();
            if (isset($_FILES['upfile'])) {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/credit_card/';
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $creditCard->pic_url = $path['result']['ret_path'];
            } else {
                return $this->jsonResult(109, '请上传图片', '');
            }
            $creditCard->card_name = trim($cardName);
            $creditCard->cash_quota = trim($cashQuota);
            $creditCard->free_periods = trim($freePeriods);
            $creditCard->card_activity = str_replace('；', ';', $cardActivity);
            $creditCard->jump_url = trim($jumpUrl);
            $creditCard->sort = $sort;
            $creditCard->create_time = date('Y-m-d H:i:s');
            if(!$creditCard->save()) {
                return $this->jsonResult(109, '新增失败', $creditCard->errors);
            }
            return $this->jsonResult(600, '新增成功', true);
        } else {
            return $this->render('add');
        }
    }
    
    /**
     * 编辑贷款
     * @return type
     */
    public function actionEdit() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $cardId = $request->post('cardId', '');
            $cardName = $request->post('cardName', '');
            $cashQuota = $request->post('cashQuota', '');
            $freePeriods = $request->post('freePeriods', '');
            $cardActivity = $request->post('cardActivity', '');
            $sort = $request->post('sort', 99);
            $jumpUrl = $request->post('jumpUrl', '');
            if(empty($cardId)) {
                return $this->jsonError(109, '参数缺失');
            }
            if(empty($cardName) || ctype_space($cardName)) {
                return $this->jsonError(109, '请输入有效信用卡名称');
            }
            if(empty($cashQuota) || ctype_space($cashQuota)) {
                return $this->jsonError(109, '请输入有效的取现额度');
            }
            if(empty($freePeriods) || ctype_space($freePeriods)) {
                return $this->jsonError(109, '请输入有效的免息期');
            }
            $creditCard = ApiCreditCard::findOne(['credit_card_id' => $cardId]);
            if(empty($creditCard)) {
                return $this->jsonError(109, '数据失效！请重新操作');
            }
            if (isset($_FILES['upfile'])) {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/credit_card/';
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $creditCard->pic_url = $path['result']['ret_path'];
            } else {
                $creditCard->pic_url = $creditCard->pic_url;
            }
            $creditCard->card_name = trim($cardName);
            $creditCard->cash_quota = trim($cashQuota);
            $creditCard->free_periods = trim($freePeriods);
            $creditCard->card_activity = str_replace('；', ';', $cardActivity);
            $creditCard->jump_url = trim($jumpUrl);
            $creditCard->sort = $sort;
            $creditCard->modify_time = date('Y-m-d H:i:s');
            if(!$creditCard->save()) {
                return $this->jsonResult(109, '编辑失败', $creditCard->errors);
            }
            return $this->jsonResult(600, '编辑成功', true);
        } else {
            $request = \Yii::$app->request;
            $id = $request->get('id', '');
            $creditCard = ApiCreditCard::find()->select(['credit_card_id', 'card_name', 'cash_quota', 'free_periods', 'pic_url', 'status', 'sort', 'card_activity', 'create_time', 'jump_url'])->where(['credit_card_id' => $id])->asArray()->one();
            if(empty($creditCard)) {
                echo '无效数据！请刷新重新操作！';
                exit;
            }
            return $this->render('edit', ['cardData' => $creditCard]);
        }
    }
    
    public function actionEditStatus() {
        $request = \Yii::$app->request;
        $cardId = $request->post('cardId', '');
        $status = $request->post('status', '');
        $creditCard = ApiCreditCard::findOne(['credit_card_id' => $cardId]);
        if(empty($creditCard)) {
            return $this->jsonError(109, '无效数据！请重新刷新载入');
        }
        $creditCard->status = $status;
        $creditCard->modify_time = date('Y-m-d H:i:s');
        if(!$creditCard->save()) {
            return $this->jsonError(109, '状态修改失败');
        }
        return $this->jsonResult(600, '修改成功', true);
    }
}
