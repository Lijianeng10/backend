<?php

namespace app\modules\common\services;

use app\modules\common\models\PayRecord;
use app\modules\common\models\IceRecord;
use app\modules\common\services\KafkaService;

class ApiSysService {
    
    /**
     * 获取sqlserver路由
     * @return type
     */
    public static function getBaseUrl() {
        return \Yii::$app->params['backup_sqlserver'];
    }
    
    /**
     * 同步交易处理明细表
     * @param type $payRecordId
     * @return type
     */
    public static function payRecord($payRecordId) {
        //获取路由
        $sendUrl = self::getBaseUrl() . 'order/pay_record';
        $retData['pay_record'] = PayRecord::find()->where(['pay_record_id' => $payRecordId])->asArray()->all();
        $jsonData = json_encode($retData);
        $ret = \Yii::sendCurlPost($sendUrl, $jsonData);
        KafkaService::addLog('sysRebatePayRecord', 'url:'.$sendUrl.$payRecordId.'; params:'.var_export($jsonData,true).'; result:'.var_export($ret,true));
        return $ret;
    }
    
    /**
     * 同步冻结明细表
     * @param type $iceRecordId
     * @return type
     */
    public static function iceRecord($iceRecordId) {
        //获取路由
        $sendUrl = self::getBaseUrl() . 'order/ice_record';
        $retData['pay_record'] = IceRecord::find()->where(['ice_record_id' => $iceRecordId])->asArray()->all();
        $jsonData = json_encode($retData);
        $ret = \Yii::sendCurlPost($sendUrl, $jsonData);
        return $ret;
    }
    
    /**
     * 获取sqlserver订单的中奖金额
     * @param type $orderCode
     * @return type
     */
    public static function getWinAamount($orderCode) {
        $sendUrl = self::getBaseUrl() . 'order/get_win_amount';
        $retData['lottery_order_code'] = $orderCode;
        $jsonData = json_encode($retData);
        $ret = \Yii::sendCurlPost($sendUrl, $jsonData);
        return $ret;
    }

    /**
     * 获取sqlserver订单的中奖金额
     * @param type $orderCode
     * @return type
     */
    public static function sycnRateRecord($records) {
        $sendUrl = self::getBaseUrl() . 'user/user_rate_record';
        $retData['user_rate_record'] = $records;
        $jsonData = json_encode($retData);
        $ret = \Yii::sendCurlPost($sendUrl, $jsonData);
        return $ret;
    }

    /**
     * 查询推广用户提成结算
     */
    public static function getCustSettleInfo($data){
        $resquestApi = self::getBaseUrl() . 'user/get_settles';
//        $resquestApi = 'http://27.155.105.177:8088/user/get_settles';
        $curl_ret = \Yii::sendCurlPost($resquestApi, $data);
        return $curl_ret;
    }
    /**
     * 推送消息
     */
    public static function sendChatPush($data){
        $resquestApi = \Yii::$app->params["chat_push_ip"];
//        $resquestApi = self::getBaseUrl() . 'user/get_settles';
//        $resquestApi =  'http://27.154.231.142:8000/publish_msg';
//        $resquestApi = 'http://27.155.105.177:8088/user/get_settles';
        $curl_ret = \Yii::sendCurlPost($resquestApi, $data);
        return $curl_ret;
    }
    
    public static function cancelCard($custNo) {
        $sendUrl = \Yii::$app->params['chat_sqlserver'] . '/del_chat_msg';
        $sign = self::getSign($custNo);
        $data = ['cust_no' => $custNo, 'sign' => $sign];
        $jsonData = json_encode($data);
        $ret = \Yii::sendCurlPost($sendUrl, $jsonData);
        \Yii::redisSet('delChat', $sendUrl, 600);
        \Yii::redisSet('chat', $jsonData, 600);
        return $ret;
    }
    
    /**
     * 获取签名
     * @param type $signData
     * @return type
     */
    public static function getSign($signData) {
        $sign = md5($signData . 'FB579760AA4C4429A5FEE972B3DF5677');
        return $sign;
    }
}

