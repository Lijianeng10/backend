<?php

namespace app\modules\trading\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use app\modules\trading\models\Withdraw;
use yii\data\ActiveDataProvider;

class WithdrawController extends Controller {
    
    public function actionIndex() {
        $request = Yii::$app->request;
        $get= $request->get();
        $outerNo = $request->get('outer_no', '');
        $orderCode = $request->get('order_code', '');
        $custNo = $request->get('cust_no', '');
        $type = $request->get('cust_type', '0');
        $start = $request->get('startdate', '');
        $end = $request->get('enddate', '');
        $statusCode = $request->get('status', '');
        $query = new Query();
        $data = $query->select(["withdraw.*","user.user_tel"])
                ->from("withdraw")
                ->leftJoin("user","user.cust_no = withdraw.cust_no");
        if($outerNo != '') {
            $data=$data->andWhere(["withdraw.outer_no"=>$outerNo]);
        }
        if($orderCode != '') {
            $data=$data->andWhere(["withdraw.withdraw_code"=>$orderCode]);
        }
        if($custNo != '') {
            $data=$data->andWhere(['or',["withdraw.cust_no"=>$custNo],["user.user_tel"=>$custNo]]);
        }
        if($statusCode != '') {
            $data=$data->andWhere(["withdraw.status"=>$statusCode]);
        }
        if($type != 0) {
            $data=$data->andWhere(["withdraw.cust_type"=>$type]);
        }
        if($start != '') {
            $data=$data->andWhere(['>=', 'withdraw.create_time', $start. " 00:00:00"]);
        }else{
            $data=$data->andWhere(['>=', 'withdraw.create_time', date("Y-m-d",strtotime("-1 weeks"))." 00:00:00"]);
        }
        if($end != '') {
            $data=$data->andWhere(['<=', 'withdraw.create_time', $end. " 23:59:59"]);
        }else{
            $data=$data->andWhere(['<=', 'withdraw.create_time', date("Y-m-d") . " 23:59:59"]);
        }     
        $data=$data->orderBy("withdraw.create_time desc");
        $list = new ActiveDataProvider([
            'query' => $data,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $this->render('index', ['data' => $list, 'get' => $get]);
    }
}
