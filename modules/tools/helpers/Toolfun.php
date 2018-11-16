<?php

/*
 * 普通工具类
 */

namespace app\modules\tools\helpers;

/**
 * 说明 ：工具类
 * @author  kevi
 * @date 2017年7月6日 下午1:41:34
 */
class Toolfun {

    /**
     * 高德地图地理位数上传
     * @auther GL zyl
     * @param type $storeData
     * @return type
     */
    public static function setLbsAddress($storeData) {
        $surl = \Yii::$app->params['test_amap_create'];
        $key = \Yii::$app->params['test_amap_key'];
        $tableid = \Yii::$app->params['test_amap_tableid'];
        $loctype = \Yii::$app->params['test_amap_loctype'];
        $address = $storeData['province'] . $storeData['city'] . $storeData['area'] . $storeData['address'];
        if(empty($storeData['store_img'])) {
            $data = ['_name' => $storeData['store_name'], '_address' => $address, '_location' => $storeData['coordinate'], 'telephone' => $storeData['phone_num'], 'store_code' => $storeData['store_code']];
        }  else {
            $data = ['_name' => $storeData['store_name'], '_img' => $storeData['store_img'], '_address' => $address, '_location' => $storeData['coordinate'], 'telephone' => $storeData['phone_num'], 'store_code' => $storeData['store_code']];
        }
        $jsonData = json_encode($data);
        $postData = ['key' => $key, 'tableid' => $tableid, 'loctype' => $loctype, 'data' => $jsonData];
        $curl_ret = \Yii::sendCurlPost($surl, $postData);
        return $curl_ret;
    }

    /**
     * 高德地图地理位置更新
     * @auther GL zyl
     * @param type $storeData
     * @return type
     */
    public static function updateLbsAddress($storeData) {
        $surl = \Yii::$app->params['test_amap_update'];
        $key = \Yii::$app->params['test_amap_key'];
        $tableid = \Yii::$app->params['test_amap_tableid'];
        $loctype = \Yii::$app->params['test_amap_loctype'];
        $address = $storeData['province'] . $storeData['city'] . $storeData['area'] . $storeData['address'];
        if(empty($storeData['store_img'])) {
            $data = ['_id' => $storeData['amap_id'], '_name' => $storeData['store_name'], '_address' => $address, '_location' => $storeData['coordinate'], 'telephone' => $storeData['phone_num'], 'store_code' => $storeData['store_code']];
        }  else {
            $data = ['_id' => $storeData['amap_id'], '_name' => $storeData['store_name'], '_img' => $storeData['store_img'], '_address' => $address, '_location' => $storeData['coordinate'], 'telephone' => $storeData['phone_num'], 'store_code' => $storeData['store_code']];
        }
        $jsonData = json_encode($data);
        $postData = ['key' => $key, 'tableid' => $tableid, 'loctype' => $loctype, 'data' => $jsonData];
        $curl_ret = \Yii::sendCurlPost($surl, $postData);
        return $curl_ret;
    }
    
    /**
     * 高德地图地理位置删除
     * @param type $amapId
     * @return type
     */
    public static function deleteLbsAddress($amapId) {
        $surl = \Yii::$app->params['test_amap_delete'];
        $key = \Yii::$app->params['test_amap_key'];
        $tableid = \Yii::$app->params['test_amap_tableid'];
        $postData = ['key' => $key, 'tableid' => $tableid, 'ids' => $amapId];
        $curl_ret = \Yii::sendCurlPost($surl, $postData);
        return $curl_ret;
    }
}
