<?php

namespace app\modules\admin\controllers;

class SyncController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 同步订单
     */
    public function actionSyncOrder(){
        $post=\Yii::$app->request->post();
        $orderCode=$post['orderCode'];
        $surl = \Yii::$app->params['userDomain']."/api/test/sync/sync-order?orderCode=".$orderCode;
        $curl_ret = \Yii::sendSyncCurlGet($surl);
        if($curl_ret=='success'){
            return $this->jsonResult(600, '成功');
        }else{
            return $this->jsonError(109,'失败');
        }
    }
    /**
     * 更新订单赔率
     */
    public function actionSyncUpdateodd(){
        $post=\Yii::$app->request->post();
        $orderCode=$post['orderCode'];
        $surl = \Yii::$app->params['userDomain']."/api/test/sync/sync-updateodd?orderCode=".$orderCode;
        $curl_ret = \Yii::sendSyncCurlGet($surl);
        if($curl_ret=='success'){
            return $this->jsonResult(600, '成功');
        }else{
            return $this->jsonError(109,'失败');
        }
    }
    /**
     * 同步交易明细
     */
    public function actionSyncPayre(){
        $post=\Yii::$app->request->post();
        $id=$post['id'];
        $surl = \Yii::$app->params['userDomain']."/api/test/sync/sync-payre?id=".$id;
        $curl_ret = \Yii::sendSyncCurlGet($surl);
        if($curl_ret=='success'){
            return $this->jsonResult(600, '成功');
        }else{
            return $this->jsonError(109,'失败');
        }
    }

}
