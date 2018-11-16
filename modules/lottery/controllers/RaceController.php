<?php

namespace app\modules\lottery\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\League;
use app\modules\helpers\UploadForm;
use app\modules\lottery\models\Team;
use app\modules\lottery\models\LeagueTeam;
use app\modules\lottery\models\Schedule;
use yii\db\Query;
use app\modules\tools\helpers\Uploadfile;

class RaceController extends Controller {

    /**
     * 联赛基础信息查询
     * auther GL ZYL
     * @return json
     */
    public function actionIndex() {
        $request = Yii::$app->request;
        $category = $request->get('category', '');
        $code = $request->get('race_limit', '');
        $leagueCode = array();
        $leagueShort = array();
        $andwhere = array();
        if ($category != '') {
            $andwhere['league_category_id'] = $category;
        }
        if ($code != '') {
            $leagueCode = ['like', 'league_code', $code . '%', false];
            $leagueShort = ['like', 'league_short_name', $code . '%', false];
        }
        $league = League::find()->orWhere($leagueCode)->orWhere($leagueShort)->andWhere($andwhere)->orderBy('league_id');
        $data = new ActiveDataProvider([
            'query' => $league,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);
        return $this->render("index", ["data" => $data]);
    }

    /**
     * 添加新的联赛
     * auther GL ZYL
     * @return json
     */
    public function actionAddrace() {
        if (!Yii::$app->request->isAjax) {
//            $leCate = new LotteryCategory;
//            $category = $loCate->getCategoryList();
            return $this->render("addrace");
        }
        $format = 'Y-m-d H:i:s';
        $request = Yii::$app->request;
        $code = $request->post('race_code', '');
        $sname = $request->post('race_short_name', '');
        $lname = $request->post('race_name', '');
        if ($code == '' || $sname == '' || $lname == '') {
            return $this->jsonResult(2, '请填写所有必填信心');
        }
        $cate = $request->post('race_category', '');
        if ($cate == '' || $cate == '0') {
            return $this->jsonResult(2, '请选择所属类型');
        }

        $post = Yii::$app->request->post();
        $only = League::find()->select('league_id,league_short_name,league_long_name,league_code')->where(['league_code' => $code])->orWhere(['league_short_name' => $sname])->orWhere(['league_long_name' => $lname])->asArray()->one();
        if (!empty($only)) {
            if ($only['league_long_name'] == $lname) {
                return $this->jsonResult(2, '该联赛全称已存在');
            }
            if ($only['league_short_name'] == $sname) {
                return $this->jsonResult(2, '该联赛简称已存在');
            }
            if ($only['league_code'] == $code) {
                return $this->jsonResult(2, '该联赛编码已存在');
            }
        }
        $model = new League();
        $path = null;
        if (isset($_FILES['upfile'])) {
            $file = $_FILES['upfile'];
            $check = UploadForm::getUpload($file);
            if($check['code'] != 600){
                return $this->jsonResult($check['code'], $check['msg']);
            }
            $saveDir = '/race/';
            $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
            $path = json_decode($pathJson,true);
            if($path['code'] != 600){
                return $this->jsonResult($path['code'], $path['msg']);
            }
            $model->league_img = $path['result']['ret_path'];
        }
        $model->league_code = $post["race_code"];
        $model->league_short_name = $post["race_short_name"];
        $model->league_long_name = $post['race_name'];
        $model->league_category_id = $post["race_category"];
        $model->league_remarks = $post['remark'];
        $model->league_status = 1;
        $model->create_time = date($format);
        $model->opt_id = \Yii::$app->session["admin_id"];
        if ($model->validate(["league_code", "league_short_name", "league_long_name", "league_img", "league_category_id", "league_remarks", "league_status", "create_time"])) {
            $id = $model->save();
            if ($id != false) {
                return $this->jsonResult(1, '新增成功', '');
            } else {
                return $this->jsonResult(2, '新增失败', '');
            }
        } else {
            return $this->jsonResult(2, '新增失败,参数有误，不可为空', $model->getFirstErrors());
        }
    }

    /**
     * 编辑联赛状态
     * auther GL ZYL
     * @return json
     */
    public function actionEditStatus() {
        $request = Yii::$app->request;
        $status = $request->post('status', '');
        $id = $request->post('id', '');
        if ($id == '' || $status == '') {
            return $this->jsonResult(2, '参数有误', '');
        }
        if ($status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }
        $model = League::find()->where(['league_id' => $id])->one();
        $model->league_status = $status;
        $model->modify_time = date('Y-m-d H:i:s');
        $model->opt_id = \Yii::$app->session["admin_id"];
        if ($model->save()) {
            return $this->jsonResult(1, '修改成功', '');
        } else {
            return $this->jsonResult(2, '修改失败', '');
        }
    }

    /**
     * 删除联赛
     * auther GL ZYL
     * @return json
     */
    public function actionDeleteRace() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/lottery/rece/index');
        }

        $request = Yii::$app->request;
        $id = $request->post('id', '');

        if ($id == '') {
            return $this->jsonResult(2, '参数有误', '');
        }
        $exits = Schedule::find()->select('schedule_id')->where(['league_id' => $id])->asArray()->one();
        $data = LeagueTeam::find()->select('league_team_id')->where(['league_id' => $id])->asArray()->one();
        if (!empty($exits) || !empty($data)) {
            return $this->jsonResult(2, '该联赛已有赛程或球队关联，不可删除', '');
        }
        $model = League::findOne($id);
        $result = $model->delete();
        if ($result != false) {
            return $this->jsonResult(1, '删除成功', '');
        } else {
            return $this->jsonResult(2, '删除失败', '');
        }
    }

    /**
     * 编辑联赛的基础信息的页面
     * auther GL ZYL
     * @return 
     */
    public function actionEditrace() {
        if (!Yii::$app->request->get()) {
            echo '操作错误';
            exit();
        }
        $get = Yii::$app->request->get();
        $raceCate = ["请选择", "五大联赛", "其他"];
        $model = League::find()
                ->where(["league_id" => $get['race_id']])
                ->asArray()
                ->one();
        $model['category'] = $raceCate;
        return $this->render('editrace', ['model' => $model]);
    }

    /**
     * 编辑联赛的基础信息的保存
     * auther GL ZYL
     * return json
     */
    public function actionDoEditRace() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/lottery/race/editrace');
        }

        $format = 'Y-m-d H:i:s';
        $request = Yii::$app->request;
        $post = $request->post();
        $path = null;
        $cate = $request->post('race_category', '');
        if ($cate == '' || $cate == '0') {
            return $this->jsonResult(2, '请选择所属类型');
        }
        $code = $request->post('race_code', '');
        $sname = $request->post('race_short_name', '');
        $lname = $request->post('race_name', '');
        $model = League::findOne(["league_id" => $post['race_id']]);
        if ($model == NULL || $model == false) {
            return $this->jsonResult(2, '操作失败,此联赛不存在', '');
        }
        if ($code == '' || $sname == '' || $lname == '') {
            return $this->jsonResult(2, '请填写所有必填信心');
        }

        $post = Yii::$app->request->post();
        $only = League::find()->select('league_id')->where(['league_code' => $code])->orWhere(['league_short_name' => $sname])->orWhere(['league_long_name' => $lname])->andWhere(['!=', 'league_id', $post['race_id']])->asArray()->one();
        if (!empty($only)) {
            if (!empty($only)) {
                if ($only['league_long_name'] == $lname) {
                    return $this->jsonResult(2, '该联赛全称已存在');
                }
                if ($only['league_short_name'] == $sname) {
                    return $this->jsonResult(2, '该联赛简称已存在');
                }
                if ($only['league_code'] == $code) {
                    return $this->jsonResult(2, '该联赛编码已存在');
                }
            }
        }
        if (isset($_FILES['upfile'])) {
            $file = $_FILES['upfile'];
            $check = UploadForm::getUpload($file);
            if($check['code'] != 600){
                return $this->jsonResult($check['code'], $check['msg']);
            }
            $saveDir = '/team/';
            $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
            $path = json_decode($pathJson,true);
            if($path['code'] != 600){
                return $this->jsonResult($path['code'], $path['msg']);
            }
            $model->league_img = $path['result']['ret_path'];
        }
        $model->league_code = $post["race_code"];
        $model->league_short_name = $post["race_short_name"];
        $model->league_long_name = $post['race_name'];
        $model->league_category_id = $post["race_category"];
        $model->league_remarks = $post['remark'];
        $model->league_status = 1;
        $model->opt_id = \Yii::$app->session["admin_id"];
        $model->modify_time = date($format);
        if ($model->validate(["league_code", "league_short_name", "league_long_name", "league_img", "league_category_id", "league_remarks", "league_status", "modify_time"])) {
            $id = $model->save();
            if ($id != false) {
                return $this->jsonResult(1, '编辑成功', '');
            } else {
                return $this->jsonResult(2, '编辑失败', '');
            }
        } else {
            return $this->jsonResult(2, '编辑失败,参数有误，不可为空', $model->getFirstErrors());
        }
    }

    /**
     * 联赛添加球队
     * auther GL ZYL
     * @return json
     */
    public function actionLeagueTeam() {
        if (Yii::$app->request->isGet) {
            $getRequest = Yii::$app->request;
            $leagueId = $getRequest->get('race_id', '');
            if ($leagueId == '') {
                echo '操作失败，参数有误';
                exit();
            }
            $league = League::find()->seleCt(['league_id', 'league_long_name'])->where(['league_id' => $leagueId])->asArray()->one();
            $team = Team::find()->select(['team_id', 'team_long_name'])->orderBy('team_id')->asArray()->all();
            $leagueTeam = LeagueTeam::find()->where(['league_id' => $leagueId])->orderBy('league_team_id')->asArray()->all();
            foreach ($leagueTeam as &$val) {
                foreach ($team as $item) {
                    if ($val['team_id'] == $item['team_id']) {
                        $val['team_name'] = $item['team_long_name'];
                        break;
                    }
                }
            }
            $model['league'] = $league;
            $model['team'] = $team;
            $model['league_team'] = $leagueTeam;
            return $this->render('league-team', ['model' => $model]);
        } elseif (Yii::$app->request->isAjax) {
            $psotRequest = Yii::$app->request;
            $leagueId = $psotRequest->post('league_id', '');
            $chosen = $psotRequest->post('chosen');
            if ($leagueId == '') {
                return $this->jsonResult(2, '添加失败，参数有误，请重新添加', '');
            }
            $rows = [];
            if ($chosen != null) {
                foreach ($chosen as $val) {
                    $rows[] = [$val, $leagueId];
                }
            }
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $detail = LeagueTeam::find()->where(['league_id' => $leagueId])->all();
                if (count($detail) > 0) {
                    $deleteId = $db->createCommand()->delete('league_team', [ 'league_id' => $leagueId])->execute();
                    if ($deleteId == false) {
                        throw new \Exception('操作删除已有数据失败！');
                    }
                }
                $updateId = $db->createCommand()->batchInsert('league_team', [ 'team_id', 'league_id'], $rows)->execute();

                if ($updateId === false) {
                    throw new \Exception('操作添加新数据失败！');
                }

                $transaction->commit();
                return $this->jsonResult(1, '添加成功', '');
            } catch (Exception $e) {
                $transaction->rollBack();
                return $this->jsonResult(2, '添加失败', $e->getMessage());
            }
        } else {
            echo '错误操作';
            exit();
        }
    }

}
