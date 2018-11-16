<?php

namespace app\modules\lottery\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\Team;
use app\modules\lottery\models\League;
use app\modules\lottery\models\LeagueTeam;
use app\modules\lottery\models\Schedule;
use app\modules\lottery\helpers\Constant;
use app\modules\lottery\models\Odds3006;
use app\modules\lottery\models\Odds3007;
use app\modules\lottery\models\Odds3008;
use app\modules\lottery\models\Odds3009;
use app\modules\lottery\models\Odds3010;
use yii\db\Query;
use yii\data\ArrayDataProvider;

class ScheduleController extends Controller {

    /**
     * 足彩赛程列表
     * @return html
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $data = [];
        $league = new League();
        $data["leagues"] = $league->getLeagueitems(1,1);
        $teams = Team::find()->select("team_long_name,team_id")->indexBy("team_id")->asArray()->all();
        $query = Schedule::find()
                ->select("schedule.schedule_id,schedule.league_id,schedule.league_name,schedule.home_short_name,schedule.visit_short_name,schedule.schedule_code,schedule.periods,schedule.start_time,schedule.beginsale_time,schedule.endsale_time,schedule.schedule_status,schedule.schedule_spf,schedule.schedule_rqspf,schedule.schedule_bf,schedule.schedule_zjqs,schedule.schedule_bqcspf,pre_result.json_data")
                ->leftJoin("pre_result","pre_result.schedule_mid=schedule.schedule_mid");
        if (isset($get["league"])&&!empty($get["league"])){
            $query = $query->andWhere(["schedule.league_id" => $get["league"]]);
        }
        if (isset($get["league_id"])&&!empty($get["league_id"])){
            $query = $query->andWhere(["schedule.league_id" => $get["league_id"]]);
        }
        if (isset($get["ball_name"])&&!empty($get["ball_name"])){
            $query = $query->andWhere(["or",["schedule.visit_team_name" => $get["ball_name"]],["schedule.home_team_name" => $get["ball_name"]]]);
        }
        if (isset($get["startdate"])&&!empty($get["startdate"])) {
            $query = $query->andWhere([">", "schedule.start_time", $get["startdate"] . " 00:00:00"]);
        }else{
            $query = $query->andWhere([">", "schedule.start_time", date("Y-m-d",strtotime("-4 days"))." 00:00:00"]);
        }
        if (isset($get["enddate"])&&!empty($get["enddate"])) {  
            $query = $query->andWhere(["<", "schedule.start_time", $get["enddate"] . " 23:59:59"]);
        }else{
            $query = $query->andWhere(["<", "schedule.start_time",date("Y-m-d",strtotime("+3 days")) . " 23:59:59"]);
        }
        $scheduleList = $query->orderBy("schedule.start_time desc,(schedule.schedule_mid+0) desc");
        $data["list"] = new ActiveDataProvider([
            'query' => $scheduleList,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);
        return $this->render("index", ["data" => $data,"get"=>$get,"league"=>$data["leagues"]]);
    }

    /**
     * 添加赛程
     */
    public function actionAddschedule() {
        $league = new League();
        $data = [];
        $data["leagues"] = $league->getLeagueitems(1,1);
        $data["items"] = [
            "" => "请选择",
            "0" => "待开售此玩法",
            "1" => "仅开售过关方式",
            "2" => "开售单关方式和过关方式",
            "3" => "未开售此玩法"
        ];
        return $this->render("addschedule", ["data" => $data]);
    }

    /**
     * 获取参加联赛队伍
     * @return json
     */
    public function actionGetleague_team() {
        $post = \Yii::$app->request->post();
        if (isset($post["league"])) {
            if ($post["league"] == "0") {
                return $this->jsonResult(0, "获取成功", []);
            } else {
                $leagueTeamquery = LeagueTeam::find()
                        ->select("team_id")
                        ->where(["league_id" => $post["league"]]);
                $teams = Team::find()
                        ->where(["in", "team_id", $leagueTeamquery])
                        ->asArray()
                        ->all();
                $data = [];
                $data[0] = "请选择";
                foreach ($teams as $val) {
                    $data[$val["team_id"]] = $val["team_long_name"];
                }
                return $this->jsonResult(0, "获取成功", $data);
            }
        } else {
            return $this->jsonResult(2, "操作错误");
        }
    }

    /**
     * 新增修改赛程
     * @return type
     */
    public function actionSaveschedule() {
        $post = \Yii::$app->request->post();
        $db = Yii::$app->db;
        if (!isset($post["team_1"]) || !isset($post["team_2"]) || $post["team_1"] == 0 || $post["team_2"] == 0 || $post["team_1"] == "" || $post["team_2"] == "") {
            return $this->jsonResult(2, "请选择球队");
        }
        $tran = $db->beginTransaction();
        try {
            if (isset($post["schedule_id"])) {
                $schedule = Schedule::findOne($post["schedule_id"]);
                $schedule->create_time = date("Y-m-d H:i:s");
            } else {
                $schedule = new Schedule();
            }
            $team_1 = Team::findOne(["team_id" => $post["team_1"]]);
            $team_2 = Team::findOne(["team_id" => $post["team_2"]]);
            $schedule->periods = $post["periods"];
            $schedule->schedule_code = $post["schedule_code"];
            $schedule->schedule_mid = $post["schedule_mid"];
            $schedule->start_time = $post["start_time"];
            $schedule->beginsale_time = $post["beginsale_time"];
            $schedule->endsale_time = $post["endsale_time"];
            $schedule->rq_nums = $post["rq_nums"];
            $schedule->schedule_spf = $post["schedule_spf"];
            $schedule->schedule_rqspf = $post["schedule_rqspf"];
            $schedule->schedule_bf = $post["schedule_bf"];
            $schedule->schedule_zjqs = $post["schedule_zjqs"];
            $schedule->schedule_bqcspf = $post["schedule_bqcspf"];
            $schedule->league_id = $post["league"];
            $schedule->hot_status = $post['hot_status'];
            $schedule->high_win_status = $post['high_win_status'];
            $schedule->visit_team_id = $post["team_2"];
            $schedule->home_team_id = $post["team_1"];
            $schedule->home_team_name = $team_1["team_long_name"];
            $schedule->visit_team_name = $team_2["team_long_name"];
            $schedule->visit_short_name = $team_2['team_short_name'];
            $schedule->home_short_name = $team_1['team_short_name'];
            $schedule->modify_time = date("Y-m-d H:i:s");
            $schedule->opt_id = \Yii::$app->session["admin_id"];
            if ($schedule->validate(['periods', 'schedule_code', 'schedule_mid', 'schedule_mid', 'start_time', 'beginsale_time', 'endsale_time', 'schedule_spf', 'schedule_rqspf', 'schedule_bf', 'schedule_zjqs',
                        'schedule_bqcspf', 'league_id', 'hot_status', 'high_win_status', 'visit_team_id', 'home_team_id', 'home_team_name', 'visit_team_name'])) {
                $ret = $schedule->save();
                if ($ret != false) {
                    if (!isset($post["schedule_id"])) {
                        $ret = $db->createCommand()->insert("schedule_result", [
                                    "schedule_id" => $schedule->schedule_id,
                                    "schedule_mid" => $schedule->schedule_mid
                                ])->execute();
                        if ($ret == false) {
                            $tran->rollBack();
                            return $this->jsonResult(2, "操作失败");
                        }
                    } else {
                        $ret = $db->createCommand()->update("schedule_result", [
                                    "schedule_mid" => $schedule->schedule_mid
                                        ], [
                                    "schedule_id" => $schedule->schedule_id
                                ])->execute();
                        if ($ret === false) {
                            $tran->rollBack();
                            return $this->jsonResult(2, "操作失败");
                        }
                    }
                    $tran->commit();
                    return $this->jsonResult(0, "保存成功", $schedule->schedule_id);
                }
                $tran->rollBack();
                return $this->jsonResult(2, "操作失败");
            } else {
                $tran->rollBack();
                return $this->jsonResult(2, "操作失败", $schedule->getFirstErrors());
            }
        } catch (yii\db\Exception $e) {
            $tran->rollBack();
            return $this->jsonResult(2, "参数有误，不可为空", $e);
        }
    }

    /**
     * 暂无用
     * @return type
     */
    public function actionSettingbonus() {
        $data = [];
        $data["provider"] = new ArrayDataProvider([
            'allModels' => [],
            'pagination' => [
                'pageSize' => 10
            ]
        ]);
        $data["schedule_code"] = "周日014";
        $data["schedule_start_time"] = "2017-06-04 22:00";
        $data["team_1"] = "弗拉门戈";
        $data["team_2"] = "博塔弗戈";
        return $this->render("settingbonus", ["data" => $data]);
    }

    /**
     * 编辑赛程
     */
    public function actionEditschedule() {
        $data = [];
        $get = \Yii::$app->request->get();
        $data["schedule"] = Schedule::find()->where(["schedule_id" => $get["schedule_id"]])->asArray()->one();
        $query = LeagueTeam::find()->select("team_id");
//        ->where(["league_id" => $data["schedule"]["league_id"]])
        $teams = Team::find()->where(["in", "team_id", $query])->asArray()->all();
        $data["team"] = [];
        foreach ($teams as $team) {
            $data["team"][$team["team_id"]] = $team["team_long_name"];
        }
        $league = new League();
        $data["leagues"] = $league->getLeagueitems(1,1);
        $data["items"] = [
            "" => "请选择",
            "0" => "待开售此玩法",
            "1" => "仅开售过关方式",
            "2" => "开售单关方式和过关方式",
            "3" => "未开售此玩法"
        ];
        return $this->render("editschedule", ["data" => $data]);
    }

//    public function actionSwicthplay() {
//        $post = \Yii::$app->request->post();
//        $arr = [
//            "spf", "rqspf", "bf", "zjqs", "bqcspf"
//        ];
//        if (isset($post["schedule_id"]) && isset($post["play"]) && in_array($post["play"], $arr) && isset($post["status"]) && ($post["status"] == "1" || $post["status"] == "0")) {
//            $schedule = Schedule::findOne($post["schedule_id"]);
//            $field = "schedule_" . $post["play"];
//            $schedule->$field = $post["status"];
//            if ($schedule->validate()) {
//                $ret = $schedule->save();
//                if ($ret != false) {
//                    return $this->jsonResult(0, "修改成功");
//                }
//                return $this->jsonResult(2, "操作失败");
//            } else {
//                return $this->jsonResult(2, "操作失败", $schedule->getFirstErrors());
//            }
//        } else {
//            return $this->jsonResult(2, "数据错误");
//        }
//    }

    /**
     * 修改赛程状态
     * @return type
     */
    public function actionReleaseschedule() {
        $post = \Yii::$app->request->post();
        if (isset($post["schedule_id"])) {
            $schedule = Schedule::findOne($post["schedule_id"]);
            $schedule->schedule_status = ($schedule->schedule_status == "0" ? "1" : ($schedule->schedule_status == "1" ? "2" : "2"));
            $schedule->opt_id = \Yii::$app->session["admin_id"];
            if ($schedule->validate()) {
                $ret = $schedule->save();
                if ($ret != false) {
                    return $this->jsonResult(0, "修改成功");
                }
                return $this->jsonResult(2, "操作失败");
            } else {
                return $this->jsonResult(2, "操作失败", $schedule->getFirstErrors());
            }
        } else {
            return $this->jsonResult(2, "数据错误");
        }
    }

    /**
     * 删除赛程
     * @return type json
     */
    public function actionDeleteschedule() {
        $post = \Yii::$app->request->post();
        if (isset($post["schedule_id"])) {
            $db = Yii::$app->db;
            $tran = $db->beginTransaction();
            try {
                $schedule = Schedule::findOne($post["schedule_id"]);
                $ret = $schedule->delete();
                $ret = ($ret && $this->deleteOdds($db, "3006", $post["schedule_id"]));
                $ret = ($ret && $this->deleteOdds($db, "3007", $post["schedule_id"]));
                $ret = ($ret && $this->deleteOdds($db, "3008", $post["schedule_id"]));
                $ret = ($ret && $this->deleteOdds($db, "3009", $post["schedule_id"]));
                $ret = ($ret && $this->deleteOdds($db, "3010", $post["schedule_id"]));
                $ret = ($ret && $db->createCommand()->delete("schedule_result", ["schedule_id" => $post["schedule_id"]])->execute());
                if ($ret != false) {
                    $tran->commit();
                    return $this->jsonResult(0, "删除成功");
                }
            } catch (yii\db\Exception $e) {
                $tran->rollBack();
                return $this->jsonResult(2, "操作失败");
            }
            $tran->rollBack();
            return $this->jsonResult(2, "操作失败", $ret);
        } else {
            return $this->jsonResult(2, "数据错误");
        }
    }

    /**
     * 获取赔率列表
     */
    public function actionReadbonus() {
        $get = \Yii::$app->request->get();
        if (isset($get["schedule_id"])) {
            $data = [];
            $data["schedule"] = Schedule::find()->where(["schedule_id" => $get["schedule_id"]])->asArray()->one();
            $teams = Team::find()->where(["team_id" => $data["schedule"]["home_team_id"]])->orWhere(["team_id" => $data["schedule"]["visit_team_id"]])->indexBy("team_id")->asArray()->all();
            $data["team_long_name"] = [];
            $data["team_long_name"][] = $teams[$data["schedule"]["home_team_id"]]["team_long_name"];
            $data["team_long_name"][] = $teams[$data["schedule"]["visit_team_id"]]["team_long_name"];
            $data["3006"] = $this->getOdds("3006", $get["schedule_id"]);
            $data["3007"] = $this->getOdds("3007", $get["schedule_id"]);
            $data["3008"] = $this->getOdds("3008", $get["schedule_id"]);
            $data["3009"] = $this->getOdds("3009", $get["schedule_id"]);
            $data["3010"] = $this->getOdds("3010", $get["schedule_id"]);
            $query = new Query();
            $data["schedule_result"] = $query->select("*")->from("schedule_result")->where(["schedule_id" => $get["schedule_id"]])->all();
            $data["schedule_result"] = new ArrayDataProvider([
                'allModels' => $data["schedule_result"],
                'pagination' => [
                    'pageSize' => 100
                ]
            ]);

            return $this->render("readbonus", ["data" => $data, 'scheduleId' => $get['schedule_id']]);
        }
    }

    /**
     * 读取赔率表
     * @param string $code
     * @param integer $schedule_id
     * @return ArrayDataProvider
     */
    public function getOdds($code, $schedule_id) {
        $query = new Query();
        $result = $query->select("*")->from("odds_" . $code)->where(["schedule_id" => $schedule_id])->all();
        $result = new ArrayDataProvider([
            'allModels' => $result,
            'pagination' => [
                'pageSize' => 100
            ]
        ]);
        return $result;
    }

    /**
     * 新增胜平负的赔率
     * @return type
     */
    public function actionAddodds3010() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $id = $request->get('schedule_id', '');
            if ($id == '') {
                echo '参数错误';
                exit();
            }
            return $this->render('addodds3010', ['scheduleId' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $scheduleId = $request->post('schedule_id', '');
            $post = $request->post();
            if ($scheduleId == '') {
                return $this->jsonResult(2, '新增失败，参数有误', '');
            }
            $wins = $post['wins'];
            $level = $post['level'];
            $negative = $post['negative'];
            $model = new Odds3010();
            $winTrend = 0;
            $levelTrend = 0;
            $negTrend = 0;
            $updateNums = 1;
            $next = Odds3010::find()->where(['schedule_id' => $scheduleId])->orderBy('odds_outcome_id desc')->asArray()->one();
            $mid = Schedule::find()->select('schedule_mid')->where(['schedule_id' => $scheduleId])->asArray()->one();
            if ($next != null) {
                if ($next['outcome_wins'] > $wins) {
                    $winTrend = -1;
                } elseif ($next['outcome_wins'] < $wins) {
                    $winTrend = 1;
                }

                if ($next['outcome_level'] > $level) {
                    $levelTrend = -1;
                } elseif ($next['outcome_level'] < $level) {
                    $levelTrend = 1;
                }

                if ($next['outcome_negative'] > $negative) {
                    $negTrend = -1;
                } elseif ($next['outcome_negative'] < $negative) {
                    $negTrend = 1;
                }
                if ($next['updates_nums'] != null) {
                    $updateNums = $next['updates_nums'] + 1;
                }
            }
            $model = new Odds3010();
            $model->schedule_id = $scheduleId;
            $model->schedule_mid = $mid['schedule_mid'];
            $model->updates_nums = $updateNums;
            $model->outcome_wins = $wins;
            $model->outcome_wins_trend = $winTrend;
            $model->outcome_level = $level;
            $model->outcome_level_trend = $levelTrend;
            $model->outcome_negative = $negative;
            $model->outcome_negative_trend = $negTrend;
            $model->modify_time = date('Y-m-d H:i:s');
            $model->create_time = date('Y-m-d H:i:s');
            $model->opt_id = \Yii::$app->session["admin_id"];
            if ($model->validate(["schedule_id", "schedule_mid", "updates_nums", "outcome_wins", "outcome_wins", "outcome_wins_trend", "outcome_level", "outcome_level_trend", "outcome_negative", "outcome_negative_trend", "create_time"])) {
                $id = $model->save();
                if ($id != false) {
                    return $this->jsonResult(1, '新增成功', '');
                } else {
                    return $this->jsonResult(2, '新增失败', '');
                }
            } else {
                return $this->jsonResult(2, '新增失败,参数有误，不可为空', $model->getFirstErrors());
            }
        } else {
            echo '参数有误';
            exit();
        }
    }

    /**
     * 删除对应的赔率
     * @param type $db
     * @param type $code
     * @param type $schedule_id
     * @return boolean
     */
    public function deleteOdds($db, $code, $schedule_id) {
        $ret = $db->createCommand()->delete("odds_" . $code, ["schedule_id" => $schedule_id])->execute();
        if ($ret === false) {
            return false;
        }
        return true;
    }

    /**
     * 新增固定比分的赔率
     * @return json
     */
    public function actionAddodds3007() {
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $id = $request->get('schedule_id', '');
            if ($id == '') {
                echo '参数错误';
                exit();
            }
            return $this->render('addodds3007', ['scheduleId' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $post = $request->post();
            $scheduleId = $request->post('schedule_id', '');
            if ($scheduleId == '') {
                return $this->jsonResult(2, '参数错误', '');
            }
            $updateNums = 1;
            $next = Odds3007::find()->where(['schedule_id' => $scheduleId])->orderBy('odds_score_id desc')->asArray()->one();
            $mid = Schedule::find()->select('schedule_mid')->where(['schedule_id' => $scheduleId])->asArray()->one();
            if ($next != null) {
                if ($next['updates_nums'] != null) {
                    $updateNums = $next['updates_nums'] + 1;
                }
            }
            $model = new Odds3007();
            $model->schedule_id = $scheduleId;
            $model->schedule_mid = $mid['schedule_mid'];
            $model->updates_nums = $updateNums;
            $model->score_wins_10 = $post['wins_10'];
            $model->score_wins_21 = $post['wins_21'];
            $model->score_wins_30 = $post['wins_30'];
            $model->score_wins_31 = $post['wins_31'];
            $model->score_wins_32 = $post['wins_32'];
            $model->score_wins_40 = $post['wins_40'];
            $model->score_wins_41 = $post['wins_41'];
            $model->score_wins_42 = $post['wins_42'];
            $model->score_wins_50 = $post['wins_50'];
            $model->score_wins_51 = $post['wins_51'];
            $model->score_wins_52 = $post['wins_52'];
            $model->score_wins_90 = $post['wins_other'];
            $model->score_level_00 = $post['level_00'];
            $model->score_level_11 = $post['level_11'];
            $model->score_level_22 = $post['level_22'];
            $model->score_level_33 = $post['level_33'];
            $model->score_level_99 = $post['level_other'];
            $model->score_negative_01 = $post['negative_01'];
            $model->score_negative_02 = $post['negative_02'];
            $model->score_negative_12 = $post['negative_12'];
            $model->score_negative_03 = $post['negative_03'];
            $model->score_negative_13 = $post['negative_13'];
            $model->score_negative_23 = $post['negative_23'];
            $model->score_negative_04 = $post['negative_04'];
            $model->score_negative_14 = $post['negative_14'];
            $model->score_negative_24 = $post['negative_24'];
            $model->score_negative_05 = $post['negative_05'];
            $model->score_negative_15 = $post['negative_15'];
            $model->score_negative_25 = $post['negative_25'];
            $model->score_negative_09 = $post['negative_other'];
            $model->create_time = date('Y-m-d H:i:s');
            $model->modify_time = date('Y-m-d H:i:s');
            $model->opt_id = \Yii::$app->session["admin_id"];
            if ($model->validate([
                        "schedule_id", "schedule_mid", "updates_nums", "score_wins_10", "score_wins_21", "score_wins_30", "score_wins_31", "score_wins_32", "score_wins_40",
                        "score_wins_41", "score_wins_42", "score_wins_50", "score_wins_51", "score_wins_51", "score_wins_52", "score_wins_90", "score_level_00", "score_level_11",
                        "score_level_22", "score_level_33", "score_level_99", "score_negative_01", "score_negative_02", "score_negative_12", "score_negative_03", "score_negative_13",
                        "score_negative_23", "score_negative_04", "score_negative_14", "score_negative_24", "score_negative_05", "score_negative_15", "score_negative_25",
                        "score_negative_09", "create_time"])) {
                $id = $model->save();
                if ($id != false) {
                    return $this->jsonResult(1, '新增成功', '');
                } else {
                    return $this->jsonResult(2, '新增失败', '');
                }
            } else {
                return $this->jsonResult(2, '新增失败,参数有误，不可为空', $model->getFirstErrors());
            }
        } else {
            echo '参数错误';
            exit();
        }
    }

    /**
     * 编辑赛程结果
     * @return json
     */
    public function actionEditscheduleresult() {
        $get = \Yii::$app->request->get();
        if (isset($get["schedule_id"])) {
            $data = [];
            $data["schedule"] = Schedule::find()->where(["schedule_id" => $get["schedule_id"]])->asArray()->one();

            $query = new Query();
            $data["schedule_result"] = $query->select("*")->from("schedule_result")->where(["schedule_id" => $get["schedule_id"]])->all();
            $data["schedule_result"] = new ArrayDataProvider([
                'allModels' => $data["schedule_result"],
                'pagination' => [
                    'pageSize' => 100
                ]
            ]);

            return $this->render("editscheduleresult", ["data" => $data]);
        }
    }

    /**
     * 保存赛程结果
     * @return json
     */
    public function actionSavescheduleresult() {
        $post = \Yii::$app->request->post();
        $schResult = ScheduleResult::findOne(["schedule_id" => $post["schedule_id"]]);
        $schResult->schedule_result_3010 = $post["schedule_result_3010"];
        $schResult->schedule_result_3007 = $post["schedule_result_3007"];
        $schResult->schedule_result_3008 = $post["schedule_result_3008"];
        $schResult->schedule_result_3009 = $post["schedule_result_3009"];
        $schResult->schedule_result_3006 = $post["schedule_result_3006"];
        $schResult->modify_time = date('Y-m-d H:i:s');
        $schResult->opt_id = \Yii::$app->session["admin_id"];
        if ($schResult->validate()) {
            $ret = $schResult->save();
            if ($ret != false) {
                return $this->jsonResult(0, "保存成功");
            }
        } else {
            return $this->jsonResult(2, "操作失败", $schResult->getFirstErrors());
        }
    }

    /**
     * 新增胜平负
     * @return json
     */
    public function actionAddrqspfbonus() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        if (isset($get["schedule_id"])) {
            $data["schedule_id"] = $get["schedule_id"];
            return $this->render("addrqspfbonus", ["data" => $data]);
        } else {
            echo "操作错误！";
            exit();
        }
    }

    /**
     * 编辑让球赔率
     * @return json
     */
    public function actionEditrqspfbonus() {
        $get = \Yii::$app->request->get();
        if (isset($get["odds_let_id"])) {
            $query = new Query();
            $data = $query->select("*")->from("odds_3006")->where(["odds_let_id" => $get["odds_let_id"]])->one();
            return $this->render("editrqspfbonus", ["data" => $data]);
        } else {
            echo "操作错误！";
            exit();
        }
    }

    /**
     * 让球赔率新增修改的保存
     * @return json
     */
    public function actionSaverqspfbonus() {
        $post = \Yii::$app->request->post();
        $db = \Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            if (isset($post["odds_let_id"])) {
                $oldData = $db->createCommand("select * from odds_3006 where odds_let_id=:odds_let_id")->bindValue(":odds_let_id", $post["odds_let_id"])->queryOne();
                $lastData = $db->createCommand("select * from odds_3006 where schedule_id=:schedule_id and updates_nums=:updates_nums")
                                ->bindValues([
                                    ":schedule_id" => $oldData["schedule_id"],
                                    ":updates_nums" => $oldData["updates_nums"] - 1
                                ])->queryOne();
                $nextData = $db->createCommand("select * from odds_3006 where schedule_id=:schedule_id and updates_nums=:updates_nums")
                                ->bindValues([
                                    ":schedule_id" => $oldData["schedule_id"],
                                    ":updates_nums" => $oldData["updates_nums"] + 1
                                ])->queryOne();
                $data = $this->getTrend($post, $lastData, ['let_wins', 'let_level', 'let_negative']);
                $data["let_ball_nums"] = $post["let_ball_nums"];
                $data["let_wins"] = $post["let_wins"];
                $data["let_level"] = $post["let_level"];
                $data["let_negative"] = $post["let_negative"];
                $data["modify_time"] = date("Y-m-d H:i:s");
                $data['opt_id'] = \Yii::$app->session["admin_id"];
                $ret = $db->createCommand()->update("odds_3006", $data, ["odds_let_id" => $post["odds_let_id"]])->execute();

                if ($nextData != null) {
                    $data = $this->getTrend($nextData, $post, ['let_wins', 'let_level', 'let_negative']);
                    $db->createCommand()->update("odds_3006", $data, ["odds_let_id" => $nextData["odds_let_id"]])->execute();
                }
                if ($ret !== false) {
                    $tran->commit();
                    return $this->jsonResult(0, "修改成功");
                }
            } else {
                if (!isset($post["schedule_id"])) {
                    $tran->rollBack();
                    return $this->jsonResult(2, "数据错误");
                }
                $lastData = $db->createCommand("select * from odds_3006 where schedule_id=:schedule_id order by updates_nums desc")
                                ->bindValues([
                                    ":schedule_id" => $post["schedule_id"]
                                ])->queryOne();
                $schedule = Schedule::find()->select("schedule_mid")->where(["schedule_id" => $post["schedule_id"]])->asArray()->one();
                $data = $this->getTrend($post, $lastData, ['let_wins', 'let_level', 'let_negative']);
                $data["updates_nums"] = isset($lastData["updates_nums"]) ? ($lastData["updates_nums"] + 1) : 1;
                $data["let_ball_nums"] = $post["let_ball_nums"];
                $data["let_wins"] = $post["let_wins"];
                $data["let_level"] = $post["let_level"];
                $data["let_negative"] = $post["let_negative"];
                $data["schedule_id"] = $post["schedule_id"];
                $data["schedule_mid"] = $schedule["schedule_mid"];
                $data["modify_time"] = date("Y-m-d H:i:s");
                $data['create_time'] = date("Y-m-d H:i:s");
                $data['opt_id'] = \Yii::$app->session["admin_id"];
                $ret = $db->createCommand()->insert("odds_3006", $data)->execute();
                if ($ret != false) {
                    $tran->commit();
                    return $this->jsonResult(0, "新增成功");
                }
            }

            $tran->rollBack();
            return $this->jsonResult(2, "数据错误");
        } catch (yii\db\Exception $e) {
            $tran->rollBack();
            return $this->jsonResult(2, "数据错误", $e);
        }
    }

    /**
     * 升平降的判断
     * @param array $arr1
     * @param array $arr2
     * @param array $keys
     * @return array
     */
    public function getTrend($arr1, $arr2, $keys) {
        $data = [];
        if (is_array($keys)) {
            if ($arr2 == null) {
                foreach ($keys as $key) {
                    $data[$key . "_trend"] = "0";
                }
                return $data;
            }
            foreach ($keys as $key) {
                if ($arr1[$key] > $arr2[$key]) {
                    $data[$key . "_trend"] = "1";
                } else if ($arr1[$key] == $arr2[$key]) {
                    $data[$key . "_trend"] = "0";
                } else {
                    $data[$key . "_trend"] = "-1";
                }
            }
        }
        return $data;
    }

    /**
     * 删除让球赔率
     * @return json
     */
    public function actionDeleterqspfbonus() {
        $db = Yii::$app->db;
        $post = Yii::$app->request->post();
        if (isset($post["odds_let_id"])) {
            $ret = $db->createCommand()->delete("odds_3006", ["odds_let_id" => $post["odds_let_id"]])->execute();
            if ($ret !== false) {
                return $this->jsonResult(0, "删除成功");
            }
            return $this->jsonResult(2, "操作失败");
        } else {
            return $this->jsonResult(2, "数据错误");
        }
    }

    /**
     * 新增总进球数赔率的页面
     * @return html
     */
    public function actionAddzjqsbonus() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        if (isset($get["schedule_id"])) {
            $data["schedule_id"] = $get["schedule_id"];
            return $this->render("addzjqsbonus", ["data" => $data]);
        } else {
            echo "操作错误！";
            exit();
        }
    }

    /**
     * 编辑总进球数赔率页面
     * @return html
     */
    public function actionEditzjqsbonus() {
        $get = \Yii::$app->request->get();
        if (isset($get["odds_3008_id"])) {
            $query = new Query();
            $data = $query->select("*")->from("odds_3008")->where(["odds_3008_id" => $get["odds_3008_id"]])->one();
            return $this->render("editzjqsbonus", ["data" => $data]);
        } else {
            echo "操作错误！";
            exit();
        }
    }

    /**
     * 总进球数赔率的新增，编辑保存
     * @return json
     */
    public function actionSavezjqsbonus() {
        $post = \Yii::$app->request->post();
        $db = \Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            if (isset($post["odds_3008_id"])) {
                $oldData = $db->createCommand("select * from odds_3008 where odds_3008_id=:odds_3008_id")->bindValue(":odds_3008_id", $post["odds_3008_id"])->queryOne();
                $lastData = $db->createCommand("select * from odds_3008 where schedule_id=:schedule_id and updates_nums=:updates_nums")
                                ->bindValues([
                                    ":schedule_id" => $oldData["schedule_id"],
                                    ":updates_nums" => $oldData["updates_nums"] - 1
                                ])->queryOne();
                $nextData = $db->createCommand("select * from odds_3008 where schedule_id=:schedule_id and updates_nums=:updates_nums")
                                ->bindValues([
                                    ":schedule_id" => $oldData["schedule_id"],
                                    ":updates_nums" => $oldData["updates_nums"] + 1
                                ])->queryOne();
                $data = $this->getTrend($post, $lastData, ['total_gold_0', 'total_gold_1', 'total_gold_2', 'total_gold_3', 'total_gold_4', 'total_gold_5', 'total_gold_6', 'total_gold_7']);

                $data["total_gold_0"] = $post["total_gold_0"];
                $data["total_gold_1"] = $post["total_gold_1"];
                $data["total_gold_2"] = $post["total_gold_2"];
                $data["total_gold_3"] = $post["total_gold_3"];
                $data["total_gold_4"] = $post["total_gold_4"];
                $data["total_gold_5"] = $post["total_gold_5"];
                $data["total_gold_6"] = $post["total_gold_6"];
                $data["total_gold_7"] = $post["total_gold_7"];
                $data["modify_time"] = date("Y-m-d H:i:s");
                $data["opt_id"] = \Yii::$app->session["admin_id"];
                $ret = $db->createCommand()->update("odds_3008", $data, ["odds_3008_id" => $post["odds_3008_id"]])->execute();

                if ($nextData != null) {
                    $data = $this->getTrend($nextData, $post, ['total_gold_0', 'total_gold_1', 'total_gold_2', 'total_gold_3', 'total_gold_4', 'total_gold_5', 'total_gold_6', 'total_gold_7']);
                    $db->createCommand()->update("odds_3008", $data, ["odds_3008_id" => $nextData["odds_3008_id"]])->execute();
                }
                if ($ret !== false) {
                    $tran->commit();
                    return $this->jsonResult(0, "修改成功");
                }
            } else {
                if (!isset($post["schedule_id"])) {
                    $tran->rollBack();
                    return $this->jsonResult(2, "数据错误");
                }
                $lastData = $db->createCommand("select * from odds_3008 where schedule_id=:schedule_id order by updates_nums desc")
                                ->bindValues([
                                    ":schedule_id" => $post["schedule_id"]
                                ])->queryOne();
                $schedule = Schedule::find()->select("schedule_mid")->where(["schedule_id" => $post["schedule_id"]])->asArray()->one();
                $data = $this->getTrend($post, $lastData, ['total_gold_0', 'total_gold_1', 'total_gold_2', 'total_gold_3', 'total_gold_4', 'total_gold_5', 'total_gold_6', 'total_gold_7']);

                $data["updates_nums"] = isset($lastData["updates_nums"]) ? ($lastData["updates_nums"] + 1) : 1;

                $data["total_gold_0"] = $post["total_gold_0"];
                $data["total_gold_1"] = $post["total_gold_1"];
                $data["total_gold_2"] = $post["total_gold_2"];
                $data["total_gold_3"] = $post["total_gold_3"];
                $data["total_gold_4"] = $post["total_gold_4"];
                $data["total_gold_5"] = $post["total_gold_5"];
                $data["total_gold_6"] = $post["total_gold_6"];
                $data["total_gold_7"] = $post["total_gold_7"];
                $data["schedule_id"] = $post["schedule_id"];
                $data["schedule_mid"] = $schedule["schedule_mid"];
                $data["modify_time"] = date("Y-m-d H:i:s");
                $data["create_time"] = date("Y-m-d H:i:s");
                $data["opt_id"] = \Yii::$app->session["admin_id"];
                $ret = $db->createCommand()->insert("odds_3008", $data)->execute();
                if ($ret != false) {
                    $tran->commit();
                    return $this->jsonResult(0, "新增成功");
                }
            }

            $tran->rollBack();
            return $this->jsonResult(2, "数据错误");
        } catch (yii\db\Exception $e) {
            $tran->rollBack();
            return $this->jsonResult(2, "数据错误", $e);
        }
    }

    /**
     * 删除总进球数赔率
     * @return json
     */
    public function actionDeletezjqsbonus() {
        $db = Yii::$app->db;
        $post = Yii::$app->request->post();
        if (isset($post["odds_3008_id"])) {
            $ret = $db->createCommand()->delete("odds_3008", ["odds_3008_id" => $post["odds_3008_id"]])->execute();
            if ($ret !== false) {
                return $this->jsonResult(0, "删除成功");
            }
            return $this->jsonResult(2, "操作失败");
        } else {
            return $this->jsonResult(2, "数据错误");
        }
    }

    /**
     * 新增半全场胜平负的赔率页面
     * @return html
     */
    public function actionAddbqcspfbonus() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        if (isset($get["schedule_id"])) {
            $data["schedule_id"] = $get["schedule_id"];
            return $this->render("addbqcspfbonus", ["data" => $data]);
        } else {
            echo "操作错误！";
            exit();
        }
    }

    /**
     * 编辑半全场胜平负的赔率页面
     * @return html
     */
    public function actionEditbqcspfbonus() {
        $get = \Yii::$app->request->get();
        if (isset($get["odds_3009_id"])) {
            $query = new Query();
            $data = $query->select("*")->from("odds_3009")->where(["odds_3009_id" => $get["odds_3009_id"]])->one();
            return $this->render("editbqcspfbonus", ["data" => $data]);
        } else {
            echo "操作错误！";
            exit();
        }
    }

    /**
     * 半全场胜平负赔率的新增编辑保存
     * @return json
     */
    public function actionSavebqcspfbonus() {
        $post = \Yii::$app->request->post();
        $db = \Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            if (isset($post["odds_3009_id"])) {
                $oldData = $db->createCommand("select * from odds_3009 where odds_3009_id=:odds_3009_id")->bindValue(":odds_3009_id", $post["odds_3009_id"])->queryOne();
                $lastData = $db->createCommand("select * from odds_3009 where schedule_id=:schedule_id and updates_nums=:updates_nums")
                                ->bindValues([
                                    ":schedule_id" => $oldData["schedule_id"],
                                    ":updates_nums" => $oldData["updates_nums"] - 1
                                ])->queryOne();
                $nextData = $db->createCommand("select * from odds_3009 where schedule_id=:schedule_id and updates_nums=:updates_nums")
                                ->bindValues([
                                    ":schedule_id" => $oldData["schedule_id"],
                                    ":updates_nums" => $oldData["updates_nums"] + 1
                                ])->queryOne();
                $data = $this->getTrend($post, $lastData, ['bqc_33', 'bqc_31', 'bqc_30', 'bqc_13', 'bqc_11', 'bqc_10', 'bqc_03', 'bqc_01', 'bqc_00']);

                $data["bqc_33"] = $post["bqc_33"];
                $data["bqc_31"] = $post["bqc_31"];
                $data["bqc_30"] = $post["bqc_30"];
                $data["bqc_13"] = $post["bqc_13"];
                $data["bqc_11"] = $post["bqc_11"];
                $data["bqc_10"] = $post["bqc_10"];
                $data["bqc_03"] = $post["bqc_03"];
                $data["bqc_01"] = $post["bqc_01"];
                $data["bqc_00"] = $post["bqc_00"];
                $data["modify_time"] = date("Y-m-d H:i:s");
                $data['opt_id'] = \Yii::$app->session["admin_id"];
                $ret = $db->createCommand()->update("odds_3009", $data, ["odds_3009_id" => $post["odds_3009_id"]])->execute();

                if ($nextData != null) {
                    $data = $this->getTrend($nextData, $post, ['bqc_33', 'bqc_31', 'bqc_30', 'bqc_13', 'bqc_11', 'bqc_10', 'bqc_03', 'bqc_01', 'bqc_00']);
                    $db->createCommand()->update("odds_3009", $data, ["odds_3009_id" => $nextData["odds_3009_id"]])->execute();
                }
                if ($ret !== false) {
                    $tran->commit();
                    return $this->jsonResult(0, "修改成功");
                }
            } else {
                if (!isset($post["schedule_id"])) {
                    $tran->rollBack();
                    return $this->jsonResult(2, "数据错误");
                }
                $lastData = $db->createCommand("select * from odds_3009 where schedule_id=:schedule_id order by updates_nums desc")
                                ->bindValues([
                                    ":schedule_id" => $post["schedule_id"]
                                ])->queryOne();
                $schedule = Schedule::find()->select("schedule_mid")->where(["schedule_id" => $post["schedule_id"]])->asArray()->one();
                $data = $this->getTrend($post, $lastData, ['bqc_33', 'bqc_31', 'bqc_30', 'bqc_13', 'bqc_11', 'bqc_10', 'bqc_03', 'bqc_01', 'bqc_00']);

                $data["updates_nums"] = isset($lastData["updates_nums"]) ? ($lastData["updates_nums"] + 1) : 1;
                $data["bqc_33"] = $post["bqc_33"];
                $data["bqc_31"] = $post["bqc_31"];
                $data["bqc_30"] = $post["bqc_30"];
                $data["bqc_13"] = $post["bqc_13"];
                $data["bqc_11"] = $post["bqc_11"];
                $data["bqc_10"] = $post["bqc_10"];
                $data["bqc_03"] = $post["bqc_03"];
                $data["bqc_01"] = $post["bqc_01"];
                $data["bqc_00"] = $post["bqc_00"];
                $data["modify_time"] = date("Y-m-d H:i:s");
                $data['create_time'] = date("Y-m-d H:i:s");
                $data['opt_id'] = \Yii::$app->session["admin_id"];
                $data["schedule_id"] = $post["schedule_id"];
                $data["schedule_mid"] = $schedule["schedule_mid"];
                $ret = $db->createCommand()->insert("odds_3009", $data)->execute();
                if ($ret != false) {
                    $tran->commit();
                    return $this->jsonResult(0, "新增成功");
                }
            }

            $tran->rollBack();
            return $this->jsonResult(2, "数据错误");
        } catch (yii\db\Exception $e) {
            $tran->rollBack();
            return $this->jsonResult(2, "数据错误", $e);
        }
    }

    /**
     * 半全场胜平负赔率的删除
     * @return json
     */
    public function actionDeletebqcspfbonus() {
        $db = Yii::$app->db;
        $post = Yii::$app->request->post();
        if (isset($post["odds_3009_id"])) {
            $ret = $db->createCommand()->delete("odds_3009", ["odds_3009_id" => $post["odds_3009_id"]])->execute();
            if ($ret !== false) {
                return $this->jsonResult(0, "删除成功");
            }
            return $this->jsonResult(2, "操作失败");
        } else {
            return $this->jsonResult(2, "数据错误");
        }
    }

    /**
     * 胜平负赔率的编辑
     * @return html json
     */
    public function actionEditodds3010() {
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $ocId = $request->get('oc_id', '');
            if ($ocId == '') {
                echo '参数有误';
                exit();
            }
            $detail = Odds3010::find()->where(['odds_outcome_id' => $ocId])->asArray()->one();
            return $this->render('editodds3010', ['model' => $detail]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $post = $request->post();
            $ocId = $request->post('odds_outcome_id', '');
            if ($ocId == '') {
                return $this->jsonResult(2, '参数错误', '');
            }
            $db = Yii::$app->db;
            $tran = $db->beginTransaction();
            try {
                $oldData = $db->createCommand("select * from odds_3010 where odds_outcome_id=:odds_outcome_id")->bindValue(":odds_outcome_id", $post["odds_outcome_id"])->queryOne();
                $lastData = $db->createCommand("select * from odds_3010 where schedule_id=:schedule_id and updates_nums=:updates_nums")
                                ->bindValues([":schedule_id" => $oldData["schedule_id"], ":updates_nums" => $oldData["updates_nums"] - 1])->queryOne();
                $nextData = $db->createCommand("select * from odds_3010 where schedule_id=:schedule_id and updates_nums=:updates_nums")
                                ->bindValues([":schedule_id" => $oldData["schedule_id"], ":updates_nums" => $oldData["updates_nums"] + 1])->queryOne();
                $data = $this->getTrend($post, $lastData, ['outcome_wins', 'outcome_level', 'outcome_negative']);
                $data["outcome_wins"] = $post["outcome_wins"];
                $data["outcome_level"] = $post["outcome_level"];
                $data["outcome_negative"] = $post["outcome_negative"];
                $data['modify_time'] = date('Y-m-d H:i:s');
                $data['opt_id'] = \Yii::$app->session["admin_id"];
                $curData = $db->createCommand()->update("odds_3010", $data, ["odds_outcome_id" => $post["odds_outcome_id"]])->execute();
                if ($curData === false) {
                    $tran->rollBack();
                    return $this->jsonResult(0, "操作更新下条数据失败", '');
                }
                if ($nextData != null) {
                    $data = $this->getTrend($nextData, $post, ['outcome_wins', 'outcome_level', 'outcome_negative']);
                    $upNext = $db->createCommand()->update("odds_3010", $data, ["odds_outcome_id" => $nextData["odds_outcome_id"]])->execute();
                    if ($upNext === false) {
                        $tran->rollBack();
                        return $this->jsonResult(0, "操作更新下条数据失败", '');
                    }
                }
                $tran->commit();
                return $this->jsonResult(0, "修改成功", '');
            } catch (yii\db\Exception $e) {
                $tran->rollBack();
                return $this->jsonResult(2, $e->getMessage(), '');
            }
        } else {
            echo '参数有误';
            exit();
        }
    }

    /**
     * 胜平负赔率的删除
     * @return json
     */
    public function actionDeleteodds3010() {
        $db = Yii::$app->db;
        $post = Yii::$app->request->post();
        if (isset($post["oc_id"])) {
            $ret = $db->createCommand()->delete("odds_3010", ["odds_outcome_id" => $post["oc_id"]])->execute();
            if ($ret !== false) {
                return $this->jsonResult(0, "删除成功", '');
            }
            return $this->jsonResult(2, "操作失败", '');
        } else {
            return $this->jsonResult(2, "数据错误", '');
        }
    }

    /**
     * 编辑固定比分胜平负的赔率
     * @return html json
     */
    public function actionEditodds3007() {
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $ocId = $request->get('score_id', '');
            if ($ocId == '') {
                echo '参数有误';
                exit();
            }
            $detail = Odds3007::find()->where(['odds_score_id' => $ocId])->asArray()->one();
            return $this->render('editodds3007', ['model' => $detail]);
        } elseif (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $post = $request->post();
            $scoreId = $request->post('odds_score_id', '');
            if ($scoreId == '') {
                return $this->jsonResult(2, '参数错误', '');
            }
            $model = Odds3007::findOne($scoreId);
            $model->score_wins_10 = $post['wins_10'];
            $model->score_wins_21 = $post['wins_21'];
            $model->score_wins_30 = $post['wins_30'];
            $model->score_wins_31 = $post['wins_31'];
            $model->score_wins_32 = $post['wins_32'];
            $model->score_wins_40 = $post['wins_40'];
            $model->score_wins_41 = $post['wins_41'];
            $model->score_wins_42 = $post['wins_42'];
            $model->score_wins_50 = $post['wins_50'];
            $model->score_wins_51 = $post['wins_51'];
            $model->score_wins_52 = $post['wins_52'];
            $model->score_wins_90 = $post['wins_other'];
            $model->score_level_00 = $post['level_00'];
            $model->score_level_11 = $post['level_11'];
            $model->score_level_22 = $post['level_22'];
            $model->score_level_33 = $post['level_33'];
            $model->score_level_99 = $post['level_other'];
            $model->score_negative_01 = $post['negative_01'];
            $model->score_negative_02 = $post['negative_02'];
            $model->score_negative_12 = $post['negative_12'];
            $model->score_negative_03 = $post['negative_03'];
            $model->score_negative_13 = $post['negative_13'];
            $model->score_negative_23 = $post['negative_23'];
            $model->score_negative_04 = $post['negative_04'];
            $model->score_negative_14 = $post['negative_14'];
            $model->score_negative_24 = $post['negative_24'];
            $model->score_negative_05 = $post['negative_05'];
            $model->score_negative_15 = $post['negative_15'];
            $model->score_negative_25 = $post['negative_25'];
            $model->score_negative_09 = $post['negative_other'];
            $model->modify_time = date('Y-m-d H:i:s');
            $model->opt_id = \Yii::$app->session["admin_id"];
            if ($model->validate([
                        "score_wins_10", "score_wins_21", "score_wins_30", "score_wins_31", "score_wins_32", "score_wins_40",
                        "score_wins_41", "score_wins_42", "score_wins_50", "score_wins_51", "score_wins_51", "score_wins_52", "score_wins_90", "score_level_00", "score_level_11",
                        "score_level_22", "score_level_33", "score_level_99", "score_negative_01", "score_negative_02", "score_negative_12", "score_negative_03", "score_negative_13",
                        "score_negative_23", "score_negative_04", "score_negative_14", "score_negative_24", "score_negative_05", "score_negative_15", "score_negative_25",
                        "score_negative_09", "modify_time"])) {
                $id = $model->save();
                if ($id != false) {
                    return $this->jsonResult(1, '编辑成功', '');
                } else {
                    return $this->jsonResult(2, '编辑失败', '');
                }
            } else {
                return $this->jsonResult(2, '新增失败,参数有误，不可为空', $model->getFirstErrors());
            }
        } else {
            echo '参数错误';
            exit();
        }
    }

    /**
     * 删除固定比分胜平负的赔率
     * @return json
     */
    public function actionDeleteodds3007() {
        $db = Yii::$app->db;
        $post = Yii::$app->request->post();
        if (isset($post["score_id"])) {
            $ret = $db->createCommand()->delete("odds_3007", ["odds_score_id" => $post["oc_id"]])->execute();
            if ($ret !== false) {
                return $this->jsonResult(0, "删除成功", '');
            }
            return $this->jsonResult(2, "操作失败", '');
        } else {
            return $this->jsonResult(2, "数据错误", '');
        }
    }

}
