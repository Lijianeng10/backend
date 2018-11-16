<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\helpers;

class InterfaceDock {
    /**
     * 获取会员实名信息 
     * @param type $custNo
     * @return type
     */
    public static function javaGetRealName($custNo) {
        $surl = \Yii::$app->params['java_getRealName'];
        $postData = ['custNo' => $custNo];
        $curl_ret = \Yii::sendCurlPost($surl, $postData);
        return $curl_ret;
    }
    /**
     * 收款账户信息
     * @param type $custNo
     * @return type
     */
    public static function javaGetAccountDetail($custNo) {
        $surl = \Yii::$app->params['java_getAccountDetail'];
        $postData = ['custNo' => $custNo];
        $curl_ret = \Yii::sendCurlPost($surl, $postData);
        return $curl_ret;
    }
    /**
     * 获取实名认证信息
     * @param type $custNo
     * @return type
     */
    public static function javaGetAuthInfo($custNo) {
        $surl = \Yii::$app->params['java_getAuthInfo'];
        $postData = ['custNo' => $custNo];
        $curl_ret = \Yii::sendCurlPost($surl, $postData);
        return $curl_ret;
    }
}
