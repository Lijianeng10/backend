<?php

namespace app\modules\agents\controllers;

use app\modules\common\models\Store;
use yii\web\Controller;
use yii\db\Expression;
use yii\db\Query;
use yii\data\ActiveDataProvider;

class StoreOptlogController extends Controller {
    
    public function actionIndex() {
        $request = \Yii::$app->request;
        $field = ['store.store_code', 'store.store_name', 'ol.content', new Expression('case when ol.operator_id = 0 then (select sd.consignee_name from store_detail sd where sd.cust_no = store.cust_no) when ol.operator_id != 0 then (select u.user_name from user u where u.user_id = ol.operator_id) end opt_name'), 'ol.create_time'];
        $startDate = $request->get('startdate', date('Y-m-d'));
        $endDate = $request->get('enddate', date('Y-m-d'));
        $storeInfo = $request->get('store_info', '');
        $query = (new Query())->select($field)
                ->from("store")
                ->leftJoin("store_opt_log ol", "ol.store_code = store.store_code")
                ->leftJoin('store_detail sd', 'sd.cust_no = store.cust_no')
                ->where(["in", "store.status", [1, 2]]);
        if (!empty($storeInfo)) {
            $query = $query->andWhere("(store.store_code like '%{$storeInfo}%' or store.store_name like '%{$storeInfo}%' or store.cust_no like '%{$storeInfo}%' or sd.consignee_name like '%{$storeInfo}%')");
        }
        
        if (!empty($startDate)) {
            $query = $query->andWhere([">", "ol.create_time", $startDate . " 00:00:00"]);
        }
        if (!empty($endDate)) {
            $query = $query->andWhere(["<", "ol.create_time", $endDate . " 23:59:59"]);
        }
        
        $query = $query->orderBy("store.store_code, ol.create_time desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
        return $this->render("index", ["data" => $data]);
    }
}

