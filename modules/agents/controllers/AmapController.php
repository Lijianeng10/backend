<?php

namespace app\modules\agents\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\agents\models\Store;
use app\modules\agents\models\StoreDetail;
//use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;
use app\modules\tools\helpers\Toolfun;

class AmapController extends \yii\web\Controller {

    /**
     * 门店信息列表
     * @return 
     */
    public function actionIndex() {
        $this->enableCsrfValidation = false;
        $get = \Yii::$app->request->get();
        $query = (new Query())->select(['store_code', 'cust_no', 'store_name', 'phone_num', 'province', 'city', 'address', 'cert_status', 'status', 'store_id', 'coordinate'])
                ->from("store")
                ->where(["status"=>1]);
        if (isset($get["store_info"]) && !empty($get["store_info"])) {
            $query = $query->andWhere("(store.store_code like '%{$get['store_info']}%' or store.store_name like '%{$get['store_info']}%' or store.cust_no like '%{$get['store_info']}%')");
        }
        if (isset($get["province"]) && !empty($get["province"])) {
            $query = $query->andWhere(["store.province" => $get["province"]]);
        }
        if (isset($get["phone_num"]) && !empty($get["phone_num"])) {
            $query = $query->andWhere(["store.phone_num" => $get["phone_num"]]);
        }
        if (isset($get["cert_status"]) && !empty($get["cert_status"])) {
            $query = $query->andWhere(["store.cert_status" => $get["cert_status"]]);
        }
        $query = $query->orderBy("store.create_time desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $this->render("index", ["data" => $data]);
    }

    /**
     * 删除地图上的门店
     * @return json
     */
    public function actionDeleteAmap() {
        $request = \Yii::$app->request;
        $storeNo = $request->post('store_no', '');
        if (empty($storeNo)) {
            return $this->jsonResult('100', '参数缺失');
        }
        $storeData = Store::find()->where(['store_code' => $storeNo, 'status' => 1])->one();
        if (empty($storeData)) {
            return $this->jsonResult(109, '参数有误');
        }
        if (empty($storeData['amap_id'])) {
            return $this->jsonResult(109, '该门店位置还未上传到地图');
        }
        $amap = Toolfun::deleteLbsAddress($storeData->amap_id);
        if ($amap['status'] != 1) {
            return $this->jsonResult(109, $amap['info'], $amap);
        }
        $data = Store::updateAll(['amap_id' => '', 'modify_time' => date('Y-m-d H:i:s')], ['store_code' => $storeNo]);
//        $storeData->amap_id = '';
//        $storeData->modify_time = date('Y-m-d H:i:s');
        if ($data == false) {
            return $this->jsonResult(109, '处理失败', $amap);
        }
        return $this->jsonResult(600, '删除成功', $amap);
    }

    public function actionCreateAmap() {
        $request = \Yii::$app->request;
        $storeNo = $request->post('store_no', '');
        if (empty($storeNo)) {
            return $this->jsonResult('100', '参数缺失');
        }
        $storeData = Store::find()->where(['store_code' => $storeNo, 'status' => 1])->one();
        if ($storeData['amap_id']) {
            $amap = Toolfun::updateLbsAddress($storeData);
        } else {
            $amap = Toolfun::setLbsAddress($storeData);
        }
        if ($amap['status'] != 1) {
            return $this->jsonResult(109, $amap['info'] . '地理位置不可为空');
        }
        if (empty($storeData['amap_id'])) {
            $update['amap_id'] = $amap['_id'];
        }
        $update['modify_time'] = date('Y-m-d H:i:s');
        $data = Store::updateAll($update, ['store_code' => $storeNo]);
        if ($data == false) {
            return $this->jsonResult(109, '处理失败');
        }
        return $this->jsonResult(600, '上传成功');
    }

}
