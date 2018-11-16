<?php

namespace app\modules\agents\services;

use yii\base\Exception;
use app\modules\agents\models\Agents;
use app\modules\agents\models\Store;

interface IAgentsService {
    /**
     * 获取代理商账号
     * @auther GL ljn
     * @param type $custNo cust_no上级代理商编号
     */
    public function getJavaAgentsAccount($custNo);
     /**
     * 获取收款账户信息
     * @auther GL zyl
     * @param type $custNo
     */
    public function javaGetRealName($custNo);
    /**
     * 获取会员状态
     * @param type $custNo
     */
    public function javaGetStatus($custNo);
    /**
     * 获取会员收款账户信息
     * @param type $custNo
     */
    public function javaGetUserAccountDetail($custNo);
}
class AgentsService implements IAgentsService{
    /**
     * {@inheritDoc}
     * @see \app\modules\agents\services\AgentsService::getJavaAgentsAccount()
     */
    public function getJavaAgentsAccount($custNo){
        $surl = \Yii::$app->params['java_getAgentsAccount'];
        $post_data = ["custNo" => $custNo];
        $curl_ret = \Yii::sendCurlPost($surl, $post_data);
        return $curl_ret;
    }
    /**
     * {@inheritDoc}
     * @see \app\modules\agents\services\AgentsService::javaGetRealName()
     */
    public function javaGetRealName($custNo) {
        $surl = \Yii::$app->params['java_getRealName'];
        $postData = ['custNo' => $custNo];
        $curl_ret = \Yii::sendCurlPost($surl, $postData);
        return $curl_ret;
    }
     public function javaGetStatus($custNo) {
        $surl = \Yii::$app->params['java_getStatus'];
        $postData = ['custNo' => $custNo];
        $curl_ret = \Yii::sendCurlPost($surl, $postData);
        return $curl_ret;
    }
    /**
     * 门店信息更改同步给IM数据库
     * {@inheritDoc}
     */
    public static function syncStore($storesId) {
    	$token = \Yii::$app->params['sync_im_token'];
    	$data['tablename'] = Store::tableName();
    	$data['keyfield'] = 'store_id';
    	$data['signdata'] = md5($data['tablename'] . $data['keyfield'] . $token);
    	$data['data']=Store::find()->where(['store_id'=>$storesId])->asArray()->all();//接口只支持二维数组
    	$surl = \Yii::$app->params['sync_im_api'];
    	$data=  json_encode($data);
    	$curl_ret = \Yii::sendCurlPost($surl, $data);
    	if($curl_ret['code']==1){
    		return true;
    	}
    	\Yii::info(var_export($data,true), 'backuporder_log');
    	return false;
    }
    public function javaGetUserAccountDetail($custNo) {
        $surl = \Yii::$app->params['java_getAccountDetail'];
        $postData = ['custNo' => $custNo];
        $curl_ret = \Yii::sendCurlPost($surl, $postData);
        return $curl_ret;
    }
    
}

