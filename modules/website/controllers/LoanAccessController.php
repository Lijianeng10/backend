<?php

namespace app\modules\website\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\modules\common\helpers\Constants;


class LoanAccessController extends Controller{
    public $enableCsrfValidation = false;

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionGetAccess(){
            $request= \Yii::$app->request;
            $startdate = $request->post('startdate');
            $enddate = $request->post('enddate');
            $where =['and'];
            $where[] = ['info_type'=>6];
            $accessRecord = (new Query())->select("*")
                ->from("access")
                ->where($where);
            if (!empty($startdate)) {
                $accessRecord = $accessRecord->andWhere([">=", "date_time", $startdate . " 00:00:00"]);
            }
            if (!empty($enddate)) {
                $accessRecord = $accessRecord->andWhere(["<=", "date_time", $enddate . " 23:59:59"]);
            }
            $accessRecord = $accessRecord->orderBy('date_time desc')->all();
            return $this->jsonResult(600, "获取成功", $accessRecord);
    }
    public function actionGetAccessDetail(){
        $request= \Yii::$app->request;
        $start_date = $request->post('start_date');
        $end_date = $request->post('end_date');
        $where =['and'];
        $where[] = ['info_type'=>6];
        $accessDetail = (new Query())->select("*")
            ->from("access_detail")
            ->where($where);
        if (!empty($start_date)) {
            $accessDetail = $accessDetail->andWhere([">=", "date_time", $start_date . " 00:00:00"]);
        }
        if (!empty($end_date)) {
            $accessDetail = $accessDetail->andWhere(["<=", "date_time", $end_date . " 23:59:59"]);
        }
        $accessDetail = $accessDetail->orderBy('date_time desc')->all();
        return $this->jsonResult(600, "获取成功", $accessDetail);
    }
}