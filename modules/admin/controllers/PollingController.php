<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use app\modules\common\models\Store;
use app\modules\agents\models\Agents;
use app\modules\common\models\Bussiness;
use app\modules\common\models\PollingConf;

class PollingController extends Controller {

    public function actionIndex() {
        $pollingArr = [
            '0' => '请选择',
            '1' => '平台',
            '2' => '门店',
            '3' => '代理商',
            '4' => '渠道方'
        ];
        return $this->render('index', ['data' => $pollingArr]);
    }

    public function actionGetPollingInfo() {
        $request = \Yii::$app->request;
        $infoType = $request->post('infoType', 1);
        switch ($infoType) {
            case 1:
                $infoArr = ['1' => '咕啦自营', '2' => '代理商', '3' => '流量方', '4' => '推广'];
                break;
            case 2:
                $infoArr = Store::find()->select(['store_code', 'store_name', 'store_type'])->where(['status' => 1, 'cert_status' => 3])->indexBy('store_code')->asArray()->all();
                break;
            case 3:
                $infoArr = Agents::find()->select(['agents_code', 'agents_name'])->where(['pass_status' => 3])->indexBy('agents_code')->asArray()->all();
                break;
            case 4:
                $infoArr = Bussiness::find()->select(['cust_no', 'name'])->where(['status' => 1])->indexBy('cust_no')->asArray()->all();
                break;
            default :
                $infoArr = [];
                break;
        }
        if (empty($infoArr)) {
            return $this->jsonError(109, '暂无可配置的信息');
        }
        return $this->jsonResult(600, '获取成功', $infoArr);
    }

    public function actionGetSetInfo() {
        $request = \Yii::$app->request;
        $sourceCode = $request->post('sourceCode', '');
        $infoType = $request->post('infoType', '');
        if (empty($sourceCode) || empty($infoType)) {
            return $this->jsonResult(109, '获取失败,参数错误');
        }
        if ($infoType == 1) {
            $sourceType = 1;
        } else {
            $sourceType = 2;
        }
        $setInfo = PollingConf::find()->select(['polling_type'])->where(['source_code' => $sourceCode, 'source_type' => $sourceType])->asArray()->one();
        if (empty($setInfo)) {
            $setInfo = [];
        }
        return $this->jsonResult(600, '获取成功', $setInfo);
    }

    public function actionSetPolling() {
        $request = \Yii::$app->request;
        $sourceCode = $request->post('sourceCode', '');
        $infoType = $request->post('infoType', '');
        $isPolling = $request->post('isPolling', 2);
        $sourceName = $request->post('sourceName', '');
        if (empty($sourceCode) || empty($infoType)) {
            return $this->jsonResult(109, '获取失败,参数错误');
        }
        if ($infoType == 1) {
            $sourceType = 1;
        } else {
            $sourceType = 2;
        }
        if (empty($isPolling)) {
            return $this->jsonResult(600, '设置成功！！未进行配置', true);
        }
        $pollingConf = PollingConf::findOne(['source_code' => $sourceCode, 'source_type' => $sourceType]);
        if (empty($pollingConf)) {
            $pollingConf = new PollingConf();
            $pollingConf->create_time = date('Y-m-d H:i:s');
        } else {
            $pollingConf->modify_time = date('Y-m-d H:i:s');
        }
        $pollingConf->source_code = $sourceCode;
        $pollingConf->source_name = $sourceName;
        $pollingConf->source_type = $sourceType;
        $pollingConf->polling_type = $isPolling;
        if(!$pollingConf->save()) {
            return $this->jsonError(109, '设置失败');
        }
        return $this->jsonResult(600, '设置成功', true);
    }

}
