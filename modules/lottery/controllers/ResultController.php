<?php

namespace app\modules\lottery\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\LotteryRecord;
use app\modules\lottery\models\LotteryTime;
use app\modules\lottery\models\Lottery;

class ResultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $query = LotteryRecord::find()
                ->select("max(lottery_record_id) as lottery_record_id")
                ->where("status=2")
                ->groupBy("lottery_code");
        $data = LotteryRecord::find()
                ->where(["in", "lottery_record_id", $query])
                ->asArray()
                ->all();
        $lotTime = LotteryTime::find()->asArray()->all();
        $timeStrs = [];
        foreach ($lotTime as $val) {
            if (!isset($timeStrs[$val["lottery_code"]])) {
                $timeStrs[$val["lottery_code"]] = $val["remark"];
            }
        }
        return $this->render('index', ["data" => $data, "timeStrs" => $timeStrs]);
    }

    public function actionList() {
        $get = \Yii::$app->request->get();
        if (!isset($get["lotterycode"])) {
            echo "错误操作!";
            exit();
        }
        $lottery = Lottery::find()
                ->where(["lottery_code" => $get["lotterycode"]])
                ->one();
        $data = LotteryRecord::find()
                ->where(["lottery_code" => $get["lotterycode"], "status" => 2])
                ->orderBy("periods desc");
        $pageSize = 10;
        $data = new ActiveDataProvider([
            'query' => $data,
            'pagination' => [
                'pageSize' => $pageSize,
            ]
        ]);
        return $this->render('list', ["data" => $data, "lotteryName" => $lottery["lottery_name"]]);
    }

}
