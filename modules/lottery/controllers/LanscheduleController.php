<?php

namespace app\modules\lottery\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\Team;
use app\modules\lottery\models\League;
use app\modules\lottery\models\LanLeagueTeam;
use app\modules\lottery\models\LanSchedule;
use app\modules\lottery\helpers\Constant;
use app\modules\common\models\Odds3001;
use app\modules\common\models\Odds3002;
use app\modules\common\models\Odds3003;
use app\modules\common\models\Odds3004;
use app\modules\common\models\LanScheduleResult;
use yii\data\ArrayDataProvider;


class LanscheduleController extends Controller {
     /**
     * 篮球赛程列表
     * @return html
     */
    public function  actionIndex(){
        $get = \Yii::$app->request->get();
        $data = [];
        $league = new League();
        $data["leagues"] = $league->getLeagueitems(1,2);
        $query = LanSchedule::find()->where("1=1");
        if (isset($get["league"])&&!empty($get["league"])){
            $query = $query->andWhere(["league_id" => $get["league"]]);
        }
        if (isset($get["league_id"])&&!empty($get["league_id"])){
            $query = $query->andWhere(["league_id" => $get["league_id"]]);
        }
        if (isset($get["ball_name"])&&!empty($get["ball_name"])){
            $query = $query->andWhere(["or",["visit_short_name" => $get["ball_name"]],["home_short_name" => $get["ball_name"]]]);
        }
        if (isset($get["startdate"])&&!empty($get["startdate"])) {
            $query = $query->andWhere([">", "start_time", $get["startdate"] . " 00:00:00"]);
        }else{
            $query = $query->andWhere([">", "start_time", date("Y-m-d",strtotime("-4 days"))." 00:00:00"]);
        }
        if (isset($get["enddate"])&&!empty($get["enddate"])) {  
            $query = $query->andWhere(["<", "start_time", $get["enddate"] . " 23:59:59"]);
        }else{
            $query = $query->andWhere(["<", "start_time",date("Y-m-d",strtotime("+3 days")) . " 23:59:59"]);
        }
        $LanscheduleList = $query->orderBy("start_time desc,(schedule_mid+0) desc");
        $data["list"] = new ActiveDataProvider([
            'query' => $LanscheduleList,
            'pagination' => [
                'pageSize' => 20
            ]
        ]);
        return $this->render("index", ["data" => $data,"get"=>$get]);
    }
    /**
     * 编辑篮球赛程
     */
    public function actionEditschedule() {
        $data = [];
        $get = \Yii::$app->request->get();
        $league = new League();
        $data["leagues"] = $league->getLeagueitems(1,2);
       
        $data["schedule"] = LanSchedule::find()->where(["lan_schedule_id" => $get["lan_schedule_id"]])->asArray()->one();
        $id=League::find()->where(["league_code"=>$data["schedule"]["league_id"]])->asArray()->one();
        $query = LanLeagueTeam::find()->select("lan_team_id")->where(["lan_league_id" => $id["league_id"]])->asArray()->all();
        $teamAry=[];
        foreach ($query as $v) {
            array_push($teamAry, $v["lan_team_id"]);
        }
        $teams = Team::find()->where(["in", "team_id", $teamAry])->asArray()->all();
       
        $data["team"] = [];
        foreach ($teams as $team) {
            $data["team"][$team["team_code"]] = $team["team_short_name"];
        }
        $data["items"] = [
            "" => "请选择",
            "0" => "待开售此玩法",
            "1" => "仅开售过关方式",
            "2" => "开售单关方式和过关方式",
            "3" => "未开售此玩法"
        ];
        return $this->render("editlanschedule", ["data" => $data]);
    }
    /**
     * 编辑修改篮球赛程
     * @return type
     */
    public function actionSaveLanSchedule(){
        $post = \Yii::$app->request->post();
        $lan_schedule_id=$post["lan_schedule_id"];
        $date=$post["schedule_date"];
        $code=$post["schedule_code"];
        $mid=$post["schedule_mid"];
        $stime=$post["start_time"];
        $btime=$post["beginsale_time"];
        $etime=$post["endsale_time"];
        $league=$post["league"];
        $visit=$post["visit"];
        $home=$post["home"];
        $s_sf=$post["schedule_sf"];
        $s_rfsf=$post["schedule_rfsf"];
        $s_dxf=$post["schedule_dxf"];
        $s_sfc=$post["schedule_sfc"];
        $hot_sta=$post["hot_status"];
        $high_win_sta=$post["high_win_status"];
        $leagueName=League::find()->select("league_short_name")->where(["league_code"=>$league])->asArray()->one();
        $visitName = Team::find()->select("team_short_name")->where(["team_code" => $visit])->asArray()->one();
        $homeName = Team::find()->select("team_short_name")->where(["team_code" => $home])->asArray()->one();
        if (empty($date)||empty($code)||empty($mid)||empty($stime)||empty($btime)||empty($etime)||empty($league)||empty($visit)||empty($home)||!isset($s_sf)&&empty($s_sf)||!isset($s_rfsf)&&empty($s_rfsf)||!isset($s_dxf)&&empty($s_dxf)||!isset($s_sfc)&&empty($s_sfc)){
            return $this->jsonResult(2, "参数缺失，请将表单填写完整");
        }
        $db = Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            if (isset($post["lan_schedule_id"])){
                $schedule = LanSchedule::findOne($post["lan_schedule_id"]);
            } else {
                $schedule = new Schedule();
            }
            $schedule->schedule_code = $code;
            $schedule->schedule_date = $date;
            $schedule->schedule_mid = $mid;
            $schedule->league_id = $league;
            $schedule->league_name = $leagueName["league_short_name"];
            $schedule->visit_short_name = $visitName["team_short_name"];
            $schedule->home_short_name = $homeName["team_short_name"];
            $schedule->visit_team_id = $visit;
            $schedule->home_team_id = $home;
            $schedule->start_time = $stime;
            $schedule->beginsale_time = $btime;
            $schedule->endsale_time = $etime;
            $schedule->schedule_sf = $s_sf;
            $schedule->schedule_rfsf = $s_rfsf;
            $schedule->schedule_dxf = $s_dxf;
            $schedule->schedule_sfc = $s_sfc;
            $schedule->high_win_status = $high_win_sta;
            $schedule->hot_status = $hot_sta;
            $schedule->modify_time = date("Y-m-d H:i:s");
            $schedule->opt_id = \Yii::$app->session["admin_id"];
            if ($schedule->validate()) {
                $ret = $schedule->save();
                if ($ret != false) {
                    $tran->commit();
                    return $this->jsonResult(600, "修改成功",$schedule->lan_schedule_id);
                }else{
                    $tran->rollBack();
                    return $this->jsonResult(2, "操作失败");
                }
            } else {
                $tran->rollBack();
                return $this->jsonResult(2, "操作失败,表单验证失败", $schedule->getFirstErrors());
            }
        } catch (yii\db\Exception $e) {
            $tran->rollBack();
            return $this->jsonResult(2, "操作失败", $e);
        }
    }
    
     /**
     * 修改赛程状态
     * @return type
     */
    public function actionEditLanStatus() {
        $post = \Yii::$app->request->post();
        if (isset($post["lan_schedule_id"])){
            $schedule = LanSchedule::findOne($post["lan_schedule_id"]);
            $schedule->schedule_status = ($schedule->schedule_status == "0" ? "1" : ($schedule->schedule_status == "1" ? "0" : "1"));
            $schedule->opt_id = \Yii::$app->session["admin_id"];
            if ($schedule->validate()) {
                $ret = $schedule->save();
                if ($ret != false) {
                    return $this->jsonResult(0, "修改成功");
                }
                return $this->jsonResult(2, "操作失败");
            } else {
                return $this->jsonResult(2, "操作失败,表单验证错误", $schedule->getFirstErrors());
            }
        } else {
            return $this->jsonResult(2, "参数缺失");
        }
    }
     /**
     * 获取赔率列表
     */
    public function actionReadlanbonus() {
        $get = \Yii::$app->request->get();
        if (isset($get["schedule_mid"])) {
            $data = [];
            $data["schedule"] = LanSchedule::find()->where(["schedule_mid" => $get["schedule_mid"]])->asArray()->one();
            $data["3001"] = $this->getOdds("3001", $get["schedule_mid"]);
            $data["3002"] = $this->getOdds("3002", $get["schedule_mid"]);
            $data["3003"] = $this->getOdds("3003", $get["schedule_mid"]);
            $data["3004"] = $this->getOdds("3004", $get["schedule_mid"]);
            $query = new Query();
            $data["lan_schedule_result"] = $query->select("result_3001,result_3002,result_3003,result_3004")->from("lan_schedule_result")->where(["schedule_mid" => $get["schedule_mid"]])->all();
            $data["lan_schedule_result"] = new ArrayDataProvider([
                'allModels' => $data["lan_schedule_result"],
                'pagination' => [
                    'pageSize' => 100
                ]
            ]);
            return $this->render("readlanbonus", ["data" => $data, 'scheduleMid' => $get['schedule_mid']]);
        }
    }
    /**
     * 读取篮球赔率表
     */
    public function getOdds($code, $schedule_mid) {
        $query = new Query();
        $result = $query->select("*")->from("odds_" . $code)->where(["schedule_mid" => $schedule_mid])->all();
        $result = new ArrayDataProvider([
            'allModels' => $result,
            'pagination' => [
                'pageSize' => 100
            ]
        ]);
        return $result;
    }
    /**
     * 新增篮球胜负赔率
     */
    public function actionAddodds3001() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $mid = $request->get('schedule_mid', '');
            if ($mid == '') {
                echo '参数错误';
                exit();
            }
            return $this->render('addodds3001', ['schedule_mid' => $mid]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $scheduleMid = $post['schedule_mid'];
            if (!isset($scheduleMid)&&empty($scheduleMid)) {
                return $this->jsonResult(2, '新增失败，参数有误', '');
            }
            $wins_3001 = $post['wins_3001'];
            $lose_3001 = $post['lose_3001'];
            $winTrend = 0;
            $loseTrend = 0;
            $updateNums = 1;
            $next = Odds3001::find()->where(['schedule_mid' => $scheduleMid])->orderBy('odds_3001_id desc')->asArray()->one();
            if ($next != null) {
                if ($next['wins_3001'] > $wins_3001) {
                    $winTrend = -1;
                } elseif ($next['wins_3001'] < $wins_3001) {
                    $winTrend = 1;
                }

                if ($next['lose_3001'] > $lose_3001) {
                    $loseTrend = -1;
                } elseif ($next['lose_3001'] < $lose_3001) {
                    $loseTrend = 1;
                }
                
                if ($next['update_nums'] != null) {
                    $updateNums = $next['update_nums'] + 1;
                }
            }
            $model = new Odds3001();
            $model->schedule_mid = $scheduleMid;
            $model->update_nums = $updateNums;
            $model->wins_3001 = $wins_3001;
            $model->wins_trend = $winTrend;
            $model->lose_3001 = $lose_3001;
            $model->lose_trend = $loseTrend;
            $model->create_time = date('Y-m-d H:i:s');
            $model->opt_id = \Yii::$app->session["admin_id"];
            if ($model->validate(["schedule_mid", "update_nums", "wins_3001", "wins_trend", "lose_3001", "lose_trend","create_time"])) {
                $id = $model->save();
                if ($id != false) {
                    return $this->jsonResult(600, '新增成功', '');
                } else {
                    return $this->jsonResult(2, '新增失败', '');
                }
            } else {
                return $this->jsonResult(2, '新增失败,参数有误，表单验证不通过', $model->getFirstErrors());
            }
        } else {
            echo '参数有误';
            exit();
        }
    }
    /*
     * 修改篮球胜负赔率
     */
    public function actionEditodds3001(){
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $id = $request->get('odds_3001_id', '');
            if ($id == '') {
                echo '参数错误';
                exit();
            }
            $data = Odds3001::find()->where(['odds_3001_id' => $id])->asArray()->one();
            return $this->render('editodds3001', ["data"=>$data,'odds_3001_id' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $id = $post['odds_3001_id'];
            if (!isset($id)&&empty($id)) {
                return $this->jsonResult(2, '修改失败，参数有误', '');
            }
            $wins_3001 = $post['wins_3001'];
            $lose_3001 = $post['lose_3001'];
            $winTrend = 0;
            $loseTrend = 0;
            $next = Odds3001::find()->where(['odds_3001_id' => $id])->one();
            $lastRes=Odds3001::find()->where(["<","odds_3001_id",$id])->andWhere(['schedule_mid' =>$next->schedule_mid])->orderBy('odds_3001_id desc')->asArray()->one();
            if ($lastRes != null){
                if ($lastRes["wins_3001"] > $wins_3001) {
                    $winTrend= -1;
                } elseif ($lastRes["wins_3001"] < $wins_3001){
                    $winTrend = 1;
                }

                if ($lastRes["lose_3001"] > $lose_3001) {
                     $loseTrend = -1;
                } elseif ($lastRes["lose_3001"] < $lose_3001) {
                     $loseTrend = 1;
                }
            }
            $next->wins_3001 = $wins_3001;
            $next->wins_trend = $winTrend;
            $next->lose_3001 = $lose_3001;
            $next->lose_trend = $loseTrend;
            $next->modify_time = date('Y-m-d H:i:s');
            $next->opt_id = \Yii::$app->session["admin_id"];
            if ($next->validate()){
                $id = $next->save();
                if ($id != false) {
                    return $this->jsonResult(600, '修改成功', '');
                } else {
                    return $this->jsonResult(2, '修改失败', '');
                }
            } else {
                return $this->jsonResult(2, '修改失败,参数有误，表单验证不通过', $model->getFirstErrors());
            }
        } else {
            echo '参数有误';
            exit();
        }
    }
    /*
     * 删除篮球胜负赔率记录
     */
    public function actionDeleteodds3001(){
        $post = Yii::$app->request->post();
        $id = $post['odds_3001_id'];
        if (!isset($id)&&empty($id)) {
            return $this->jsonResult(2, '修改失败，参数有误', '');
        }
        $res = Odds3001::deleteAll(['odds_3001_id' => $id]);
        if($res){
            return $this->jsonResult(600, '删除成功');
        }else{
            return $this->jsonResult(2, '删除失败');
        }
    }
    /**
     * 新增篮球让分胜负赔率
     */
    public function actionAddodds3002() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $mid = $request->get('schedule_mid', '');
            if ($mid == '') {
                echo '参数错误';
                exit();
            }
            return $this->render('addodds3002', ['schedule_mid' => $mid]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $scheduleMid = $post['schedule_mid'];
            $rf_nums = $post['rf_nums'];
            $wins_3002 = $post['wins_3002'];
            $lose_3002 = $post['lose_3002'];
            if (!isset($scheduleMid)&&empty($scheduleMid)||empty($wins_3002)||empty($lose_3002)) {
                return $this->jsonResult(2, '新增失败，参数有误', '');
            }
            $winTrend = 0;
            $loseTrend = 0;
            $next = Odds3002::find()->where(['schedule_mid' => $scheduleMid])->orderBy('odds_3002_id desc')->asArray()->one();
            if ($next != null) {
                if ($next['wins_3002'] > $wins_3002) {
                    $winTrend = -1;
                } elseif ($next['wins_3002'] < $wins_3002) {
                    $winTrend = 1;
                }

                if ($next['lose_3002'] > $lose_3002) {
                    $loseTrend = -1;
                } elseif ($next['lose_3002'] < $lose_3002) {
                    $loseTrend = 1;
                }
                
                if ($next['update_nums'] != null) {
                    $updateNums = $next['update_nums'] + 1;
                }
            }
            $model = new Odds3002();
            $model->schedule_mid = $scheduleMid;
            $model->update_nums = $updateNums;
            $model->rf_nums = $rf_nums;
            $model->wins_3002 = $wins_3002;
            $model->wins_trend = $winTrend;
            $model->lose_3002 = $lose_3002;
            $model->lose_trend = $loseTrend;
            $model->create_time = date('Y-m-d H:i:s');
            $model->opt_id = \Yii::$app->session["admin_id"];
            if ($model->validate(["schedule_mid","rf_nums", "update_nums", "wins_3002", "wins_trend", "lose_3002", "lose_trend","create_time"])) {
                $id = $model->save();
                if ($id != false) {
                    return $this->jsonResult(600, '新增成功', '');
                } else {
                    return $this->jsonResult(2, '新增失败', '');
                }
            } else {
                return $this->jsonResult(2, '新增失败,参数有误，表单验证不通过', $model->getFirstErrors());
            }
        } else {
            echo '参数有误';
            exit();
        }
    }
     /*
      * 修改篮球让分胜负赔率
      */
    public function actionEditodds3002(){
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $id = $request->get('odds_3002_id', '');
            if ($id == ''){
                echo '参数错误';
                exit();
            }
            $data = Odds3002::find()->where(['odds_3002_id' => $id])->asArray()->one();
            return $this->render('editodds3002', ["data"=>$data,'odds_3002_id' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $id = $post['odds_3002_id'];
            $rf_nums = $post['rf_nums'];
            $wins_3002 = $post['wins_3002'];
            $lose_3002 = $post['lose_3002'];
            if (!isset($id)&&empty($id)||empty($wins_3002)||empty($lose_3002)) {
                return $this->jsonResult(2, '修改失败，参数有误', '');
            }
            $winTrend = 0;
            $loseTrend = 0;
            $next = Odds3002::find()->where(['odds_3002_id' => $id])->one();
            $lastRes=Odds3002::find()->where(["<","odds_3002_id",$id])->andWhere(['schedule_mid' =>$next->schedule_mid])->orderBy('odds_3002_id desc')->asArray()->one();
            if ($lastRes != null){
                if ($lastRes["wins_3002"] > $wins_3002) {
                    $winTrend= -1;
                } elseif ($lastRes["wins_3002"] < $wins_3002){
                    $winTrend = 1;
                }

                if ($lastRes["lose_3002"] > $lose_3002) {
                     $loseTrend = -1;
                } elseif ($lastRes["lose_3002"] < $lose_3002) {
                     $loseTrend = 1;
                }
            }
            $next->rf_nums = $rf_nums;
            $next->wins_3002 = $wins_3002;
            $next->wins_trend = $winTrend;
            $next->lose_3002 = $lose_3002;
            $next->lose_trend = $loseTrend;
            $next->modify_time = date('Y-m-d H:i:s');
            $next->opt_id = \Yii::$app->session["admin_id"];
            if ($next->validate()){
                $id = $next->save();
                if ($id != false) {
                    return $this->jsonResult(600, '修改成功', '');
                } else {
                    return $this->jsonResult(2, '修改失败', '');
                }
            } else {
                return $this->jsonResult(2, '修改失败,参数有误，表单验证不通过', $model->getFirstErrors());
            }
        } else {
            echo '参数有误';
            exit();
        }
    }
    /*
     * 删除篮球让分胜负赔率记录
     */
    public function actionDeleteodds3002(){
        $post = Yii::$app->request->post();
        $id = $post['odds_3002_id'];
        if (!isset($id)&&empty($id)) {
            return $this->jsonResult(2, '修改失败，参数有误', '');
        }
        $res = Odds3002::deleteAll(['odds_3002_id' => $id]);
        if($res){
            return $this->jsonResult(600, '删除成功');
        }else{
            return $this->jsonResult(2, '删除失败');
        }
    }
     /**
     * 新增篮球胜分差赔率
     */
    public function actionAddodds3003() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $mid = $request->get('schedule_mid', '');
            if ($mid == '') {
                echo '参数错误';
                exit();
            }
            return $this->render('addodds3003', ['schedule_mid' => $mid]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $scheduleMid = $post['schedule_mid'];
            $cha_01 = $post['cha_01'];
            $cha_02 = $post['cha_02'];
            $cha_03 = $post['cha_03'];
            $cha_04 = $post['cha_04'];
            $cha_05 = $post['cha_05'];
            $cha_06 = $post['cha_06'];
            $cha_11 = $post['cha_11'];
            $cha_12 = $post['cha_12'];
            $cha_13 = $post['cha_13'];
            $cha_14 = $post['cha_14'];
            $cha_15 = $post['cha_15'];
            $cha_16 = $post['cha_16'];
            if (!isset($scheduleMid)&&empty($scheduleMid)||empty($cha_01)||empty($cha_02)||empty($cha_03)||empty($cha_04)||empty($cha_05)||empty($cha_06)||empty($cha_11)||empty($cha_12)||empty($cha_13)||empty($cha_14)||empty($cha_15)||empty($cha_16)) {
                return $this->jsonResult(2, '新增失败，参数有误', '');
            }
            $next = Odds3003::find()->where(['schedule_mid' => $scheduleMid])->orderBy('odds_3003_id desc')->asArray()->one();
            $updateNums=0;
            if ($next != null){
                if ($next['update_nums'] != null) {
                    $updateNums = $next['update_nums'] + 1;
                }
            }
            $model = new Odds3003();
            $model->schedule_mid = $scheduleMid;
            $model->update_nums = $updateNums;
            $model->cha_01 = $cha_01;
            $model->cha_02 = $cha_02;
            $model->cha_03 = $cha_03;
            $model->cha_04 = $cha_04;
            $model->cha_05 = $cha_05;
            $model->cha_06 = $cha_06;
            $model->cha_11 = $cha_11;
            $model->cha_12 = $cha_12;
            $model->cha_13 = $cha_13;
            $model->cha_14 = $cha_14;
            $model->cha_15 = $cha_15;
            $model->cha_16 = $cha_16;
            $model->create_time = date('Y-m-d H:i:s');
            $model->opt_id = \Yii::$app->session["admin_id"];
            if ($model->validate()) {
                $id = $model->save();
                if ($id != false) {
                    return $this->jsonResult(600, '新增成功', '');
                } else {
                    return $this->jsonResult(2, '新增失败', '');
                }
            } else {
                return $this->jsonResult(2, '新增失败,参数有误，表单验证不通过', $model->getFirstErrors());
            }
        } else {
            echo '参数有误';
            exit();
        }
    }
    /*
     * 修改篮球胜分差赔率
     */
    public function actionEditodds3003(){
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $id = $request->get('odds_3003_id', '');
            if ($id == ''){
                echo '参数错误';
                exit();
            }
            $data = Odds3003::find()->where(['odds_3003_id' => $id])->asArray()->one();
            return $this->render('editodds3003', ["data"=>$data,'odds_3003_id' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $id = $post['odds_3003_id'];
            $cha_01 = $post['cha_01'];
            $cha_02 = $post['cha_02'];
            $cha_03 = $post['cha_03'];
            $cha_04 = $post['cha_04'];
            $cha_05 = $post['cha_05'];
            $cha_06 = $post['cha_06'];
            $cha_11 = $post['cha_11'];
            $cha_12 = $post['cha_12'];
            $cha_13 = $post['cha_13'];
            $cha_14 = $post['cha_14'];
            $cha_15 = $post['cha_15'];
            $cha_16 = $post['cha_16'];
            if (!isset($id)&&empty($id)||empty($cha_01)||empty($cha_02)||empty($cha_03)||empty($cha_04)||empty($cha_05)||empty($cha_06)||empty($cha_11)||empty($cha_12)||empty($cha_13)||empty($cha_14)||empty($cha_15)||empty($cha_16)) {
                return $this->jsonResult(2, '修改失败，参数有误', '');
            }
            $model = Odds3003::find()->where(['odds_3003_id' => $id])->one();
            $model->cha_01 = $cha_01;
            $model->cha_02 = $cha_02;
            $model->cha_03 = $cha_03;
            $model->cha_04 = $cha_04;
            $model->cha_05 = $cha_05;
            $model->cha_06 = $cha_06;
            $model->cha_11 = $cha_11;
            $model->cha_12 = $cha_12;
            $model->cha_13 = $cha_13;
            $model->cha_14 = $cha_14;
            $model->cha_15 = $cha_15;
            $model->cha_16 = $cha_16;
            $model->create_time = date('Y-m-d H:i:s');
            $model->opt_id = \Yii::$app->session["admin_id"];
            if ($model->validate()){
                $id = $model->save();
                if ($id != false) {
                    return $this->jsonResult(600, '修改成功', '');
                } else {
                    return $this->jsonResult(2, '修改失败', '');
                }
            } else {
                return $this->jsonResult(2, '修改失败,参数有误，表单验证不通过', $model->getFirstErrors());
            }
        } else {
            echo '参数有误';
            exit();
        }
    }
    /*
     * 删除篮球胜分差赔率记录
     */
    public function actionDeleteodds3003(){
        $post = Yii::$app->request->post();
        $id = $post['odds_3003_id'];
        if (!isset($id)&&empty($id)) {
            return $this->jsonResult(2, '修改失败，参数有误', '');
        }
        $res = Odds3003::deleteAll(['odds_3003_id' => $id]);
        if($res){
            return $this->jsonResult(600, '删除成功');
        }else{
            return $this->jsonResult(2, '删除失败');
        }
    }
    /**
     * 新增篮球大小分赔率
     */
    public function actionAddodds3004() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $mid = $request->get('schedule_mid', '');
            if ($mid == '') {
                echo '参数错误';
                exit();
            }
            return $this->render('addodds3004', ['schedule_mid' => $mid]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $scheduleMid = $post['schedule_mid'];
            $fen_cutoff = $post['fen_cutoff'];
            $da_3004 = $post['da_3004'];
            $xiao_3004 = $post['xiao_3004'];
            if (!isset($scheduleMid)&&empty($scheduleMid)||empty($fen_cutoff)||empty($da_3004)||empty($xiao_3004)) {
                return $this->jsonResult(2, '新增失败，参数有误', '');
            }
            $winTrend = 0;
            $loseTrend = 0;
            $updateNums=0;
            $next = Odds3004::find()->where(['schedule_mid' => $scheduleMid])->orderBy('odds_3004_id desc')->asArray()->one();
            if ($next != null) {
                if ($next['da_3004'] > $da_3004) {
                    $winTrend = -1;
                } elseif ($next['da_3004'] < $da_3004) {
                    $winTrend = 1;
                }

                if ($next['xiao_3004'] > $xiao_3004) {
                    $loseTrend = -1;
                } elseif ($next['xiao_3004'] < $xiao_3004) {
                    $loseTrend = 1;
                }
                
                if ($next['update_nums'] != null) {
                    $updateNums = $next['update_nums'] + 1;
                }
            }
            $model = new Odds3004();
            $model->schedule_mid = $scheduleMid;
            $model->update_nums = $updateNums;
            $model->fen_cutoff = $fen_cutoff;
            $model->da_3004 = $da_3004;
            $model->da_3004_trend = $winTrend;
            $model->xiao_3004 = $xiao_3004;
            $model->xiao_3004_trend = $loseTrend;
            $model->create_time = date('Y-m-d H:i:s');
            $model->opt_id = \Yii::$app->session["admin_id"];
            if ($model->validate()){
                $id = $model->save();
                if ($id != false) {
                    return $this->jsonResult(600, '新增成功', '');
                } else {
                    return $this->jsonResult(2, '新增失败', '');
                }
            } else {
                return $this->jsonResult(2, '新增失败,参数有误，表单验证不通过', $model->getFirstErrors());
            }
        } else {
            echo '参数有误';
            exit();
        }
    }
     /*
      * 修改篮球大小分赔率
      */
    public function actionEditodds3004(){
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $id = $request->get('odds_3004_id', '');
            if ($id == ''){
                echo '参数错误';
                exit();
            }
            $data = Odds3004::find()->where(['odds_3004_id' => $id])->asArray()->one();
            return $this->render('editodds3004', ["data"=>$data,'odds_3004_id' => $id]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $id = $post['odds_3004_id'];
            $fen_cutoff = $post['fen_cutoff'];
            $da_3004 = $post['da_3004'];
            $xiao_3004 = $post['xiao_3004'];
            if (!isset($id)&&empty($id)||empty($fen_cutoff)||empty($da_3004)||empty($xiao_3004)) {
                return $this->jsonResult(2, '修改失败，参数有误', '');
            }
            $winTrend = 0;
            $loseTrend = 0;
            $next = Odds3004::find()->where(['odds_3004_id' => $id])->one();
            $lastRes=Odds3004::find()->where(["<","odds_3004_id",$id])->andWhere(['schedule_mid' =>$next->schedule_mid])->orderBy('odds_3004_id desc')->asArray()->one();
            if ($lastRes != null){
                if ($lastRes["da_3004"] > $da_3004) {
                    $winTrend= -1;
                } elseif ($lastRes["da_3004"] < $da_3004){
                    $winTrend = 1;
                }
                if ($lastRes["xiao_3004"] > $xiao_3004) {
                     $loseTrend = -1;
                } elseif ($lastRes["xiao_3004"] < $xiao_3004) {
                     $loseTrend = 1;
                }
            }
            $next->fen_cutoff = $fen_cutoff;
            $next->da_3004 = $da_3004;
            $next->da_3004_trend = $winTrend;
            $next->xiao_3004 = $xiao_3004;
            $next->xiao_3004_trend = $loseTrend;
            $next->modify_time = date('Y-m-d H:i:s');
            $next->opt_id = \Yii::$app->session["admin_id"];
            if ($next->validate()){
                $id = $next->save();
                if ($id != false) {
                    return $this->jsonResult(600, '修改成功', '');
                } else {
                    return $this->jsonResult(2, '修改失败', '');
                }
            } else {
                return $this->jsonResult(2, '修改失败,参数有误，表单验证不通过', $model->getFirstErrors());
            }
        } else {
            echo '参数有误';
            exit();
        }
    }
    /*
     * 删除篮球大小分赔率记录
     */
    public function actionDeleteodds3004(){
        $post = Yii::$app->request->post();
        $id = $post['odds_3004_id'];
        if (!isset($id)&&empty($id)) {
            return $this->jsonResult(2, '修改失败，参数有误', '');
        }
        $res = Odds3004::deleteAll(['odds_3004_id' => $id]);
        if($res){
            return $this->jsonResult(600, '删除成功');
        }else{
            return $this->jsonResult(2, '删除失败');
        }
    }
    /*
     * 修改赛程结果
     */
    public function actionEditlanresult(){
         $this->layout = false;
        if (Yii::$app->request->isGet) {
            $request = Yii::$app->request;
            $id = $request->get('schedule_mid', '');
            if ($id == ''){
                echo '参数错误';
                exit();
            }
            $data= (new Query())->select("result_3001,result_3002,result_3003,result_3004")->from("lan_schedule_result")->where(["schedule_mid" => $id])->one();
            return $this->render('editlanresult', ["data"=>$data,'schedule_mid' => $id]);
        } elseif (Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $info = LanScheduleResult::find()->where(['schedule_mid' => $post["schedule_mid"]])->one();
            $info->result_3001 = $post["result_3001"];
            $info->result_3002 = $post["result_3002"];
            $info->result_3003 = $post["result_3003"];
            $info->result_3004 = $post["result_3004"];
            $info->modify_time = date('Y-m-d H:i:s');
            $info->opt_id = \Yii::$app->session["admin_id"];
            if ($info->validate()){
                $res = $info->save();
                if ($res != false) {
                    return $this->jsonResult(600, '修改成功');
                } else {
                    return $this->jsonResult(109, '修改失败');
                }
            } else {
                return $this->jsonResult(109, '修改失败,参数有误，表单验证不通过', $model->getFirstErrors());
            }
        }
    }
}

