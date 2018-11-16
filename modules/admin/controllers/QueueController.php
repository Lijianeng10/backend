<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * Admin controller for the `admin` module
 */
class QueueController extends Controller {

    /**
      /**
     * 用户列表
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $query = new Query();
        $query = $query->select("*")->from("queue")->where("1=1");
        if (isset($get["queue_id"]) && !empty($get["queue_id"])) {
            $query = $query->andWhere(["queue_id" => $get["queue_id"]]);
        }
        if (isset($get["args"]) && !empty($get["args"])) {
            $query = $query->andWhere(["like", "args", $get["args"]]);
        }
        if (isset($get["push_status"]) && !empty($get["push_status"])) {
            $query = $query->andWhere(["push_status" => $get["push_status"]]);
        }
        if (isset($get["status"]) && !empty($get["status"])) {
            $query = $query->andWhere(["status" => $get["status"]]);
        }
        if (isset($get["create_time_start"]) && !empty($get["create_time_start"])) {
            $detail = $query->andWhere([">", "create_time", $get["create_time_start"]]);
        }
        if (isset($get["create_time_end"]) && !empty($get["create_time_start"])) {
            $detail = $query->andWhere(["<", "create_time", $get["create_time_end"] . " 23:59:59"]);
        }
        $data = $query->orderBy("create_time desc");

        $data = new ActiveDataProvider([
            'query' => $data,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $this->render('index', ['data' => $data]);
    }

}
