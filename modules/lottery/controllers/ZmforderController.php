<?php

namespace app\modules\lottery\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\common\models\ZmfOrder;
use app\modules\common\helpers\Constants;
class ZmforderController extends \yii\web\Controller {
    /**
     * 自动出票记录列表
     */
    public function actionIndex(){
        $get = \Yii::$app->request->get();
        $status = Constants::ZMF_STATUS;
        $query = ZmfOrder::find();
        if (isset($get["order_code"]) && !empty($get["order_code"])) {
            $query = $query->andWhere(["order_code" => $get["order_code"]]);
        }
        if (isset($get["messageId"]) && !empty($get["messageId"])) {
            $query = $query->andWhere(["messageId" => $get["messageId"]]);
        }
        if (isset($get["status"]) && $get["status"]!="") {
            $query = $query->andWhere(["status" => $get["status"]]);
        }
        $query=$query->orderBy("zmf_order_id desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
       return $this->render("index",["data" => $data,"get"=>$get,"status"=>$status]); 
    }
}


