<?php

namespace app\modules\lottery\controllers;

use Yii;
use yii\web\Controller;
use app\modules\lottery\models\LotteryTime;
use app\modules\lottery\models\LotteryCategory;
use yii\data\ArrayDataProvider;
use app\modules\lottery\models\Lottery;

class TimeController extends Controller {

    public function actionIndex() {
        $request = Yii::$app->request;
        $category = $request->get('category', '');
        $code = $request->get('code', '');
        $where = array();
        $andwhere = array();
        $orwhere = [];
        if ($category != '') {
            $cateName = LotteryCategory::find()->select('cp_category_name')->where(['lottery_category_id' => $category])->asArray()->one();
            $andwhere['category_name'] = $cateName['cp_category_name'];
        }
        if ($code != '') {
            $where['lottery_code'] = $code;
            $orwhere['lottery_name'] = $code;
        }
        $time = LotteryTime::find()->orderBy('lottery_code,week')->select(['lottery_code', 'lottery_name', 'category_name', 'start_time', 'stop_time', 'limit_time', 'rate', 'week', 'changci'])->where($where)->orWhere($orwhere)->andWhere($andwhere)->asArray()->all();
        $timeList = [];
        foreach ($time as &$val) {
            if (array_key_exists($val['lottery_code'], $timeList)) {
                $open = [ 'week' => $val['week']];
                $timeList[$val['lottery_code']]['open_time'][] = $open;
            } else {
                $timeList[$val['lottery_code']] = ['lottery_code' => $val['lottery_code'], 'lottery_name' => $val['lottery_name'], 'category_name' => $val['category_name'],
                    'open_time' => [['week' => $val['week']]], 'start_time' => $val['start_time'], 'stop_time' => $val['stop_time'], 'limit_time' => $val['limit_time'], 'rate' => $val['rate'], 'changci' => $val['changci']];
            }
        }

        $provider = new ArrayDataProvider([
            'allModels' => $timeList,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['lottery_code'],
            ],
        ]);
        $loCate = new LotteryCategory;
        $category = $loCate->getCategoryList();
        return $this->render('index', ['result' => $provider, 'category' => $category]);
    }

    public function actionSetting() {
        $get = \Yii::$app->request->get();
        if (!isset($get["lotterycode"])) {
            echo "操作错误！";
            exit();
        }

        $rates = [
            "" => "请选择",
            "每天" => "每天",
            "每周" => "每周",
            "时间段" => "时间段"
        ];
        $lotTime = LotteryTime::find()
                ->where(["lottery_code" => $get["lotterycode"]])
                ->asArray()
                ->all();
        $weeks = [
            '星期日' => ["name" => "星期日", "isSelect" => false],
            '星期一' => ["name" => "星期一", "isSelect" => false],
            '星期二' => ["name" => "星期二", "isSelect" => false],
            '星期三' => ["name" => "星期三", "isSelect" => false],
            '星期四' => ["name" => "星期四", "isSelect" => false],
            '星期五' => ["name" => "星期五", "isSelect" => false],
            "星期六" => ["name" => "星期六", "isSelect" => false]
        ];
        if ($lotTime[0]["rate"] == "每周") {
            foreach ($lotTime as $val) {
                $weeks[$val["week"]]["isSelect"] = true;
            }
        }
        $weeks = new ArrayDataProvider([
            'allModels' => $weeks,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        $data = [];
        $data["lotTime"] = $lotTime;
        $data["rates"] = $rates;
        $data["weeks"] = $weeks;
        return $this->render('setting', ['data' => $data]);
    }

    public function actionSavedata() {
        $post = \Yii::$app->request->post();
        $lotteryInfo = Lottery::find()
                ->where(["lottery_code" => $post["lottery_code"]])
                ->asArray()
                ->one();
        $lotteryCat = LotteryCategory::find()
                ->where(["lottery_category_id" => $lotteryInfo["lottery_category_id"]])
                ->asArray()
                ->one();
        $db = \Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            if ($post["rate"] == "") {
                $tran->rollBack();
                return $this->jsonResult(0, "操作失败，请选择开奖频率", "");
            }
            LotteryTime::deleteAll(["lottery_code" => $post["lottery_code"]]);
            if ($post["rate"] == "每周") {
                if (!isset($post["week"]) || !is_array($post["week"]) || count($post["week"]) < 1) {
                    $tran->rollBack();
                    return $this->jsonResult(0, "操作失败，请选择开奖时间", "");
                }
                foreach ($post["week"] as $week) {
                    $lotTime = new LotteryTime();
                    $lotTime->rate = $post["rate"];
                    $lotTime->changci = 1;
                    $lotTime->start_time = $post["start_time"];
                    $lotTime->stop_time = $post["stop_time"];
                    $lotTime->limit_time = $post["limit_time"];
                    $lotTime->week = $week;
                    $lotTime->remark = $post["remark"];
                    $lotTime->lottery_code = $post["lottery_code"];
                    $lotTime->lottery_name = $lotteryInfo["lottery_name"];
                    $lotTime->category_name = $lotteryCat["cp_category_name"];
                    $lotTime->opt_id = \Yii::$app->session["admin_id"];
                    if ($lotTime->validate()) {
                        $ret = $lotTime->save();
                        if ($ret == false) {
                            $tran->rollBack();
                            return $this->jsonResult(0, "操作失败", $lotTime->getFirstErrors());
                        }
                    } else {
                        $tran->rollBack();
                        return $this->jsonResult(0, "操作失败", $lotTime->getFirstErrors());
                    }
                }
            } else {
                $lotTime = new LotteryTime();
                $lotTime->rate = $post["rate"];
                $lotTime->changci = $post["changci"];
                $lotTime->start_time = $post["start_time"];
                $lotTime->stop_time = $post["stop_time"];
                $lotTime->limit_time = $post["limit_time"];
                $lotTime->week = "";
                $lotTime->remark = $post["remark"];
                $lotTime->lottery_code = $post["lottery_code"];
                $lotTime->lottery_name = $lotteryInfo["lottery_name"];
                $lotTime->category_name = $lotteryCat["cp_category_name"];
                $lotTime->opt_id = \Yii::$app->session["admin_id"];
                if ($lotTime->validate()) {
                    $ret = $lotTime->save();
                    if ($ret == false) {
                        $tran->rollBack();
                        return $this->jsonResult(0, "操作失败", $lotTime->getFirstErrors());
                    }
                } else {
                    $tran->rollBack();
                    return $this->jsonResult(0, "操作失败", $lotTime->getFirstErrors());
                }
            }
            $tran->commit();
            return $this->jsonResult(1, "操作成功", "");
        } catch (yii\db\Exception $e) {
            $tran->rollBack();
            return $this->jsonResult(0, "操作失败", "");
        }
    }

}
