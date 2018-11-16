<?php

namespace app\modules\lottery\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\lottery\models\Team;
use app\modules\helpers\UploadForm;
use app\modules\lottery\models\Schedule;
use app\modules\lottery\models\LeagueTeam;
use app\modules\lottery\models\Country;
use app\modules\tools\helpers\Uploadfile;

class TeamController extends Controller {

    public function actionIndex() {
        $request = Yii::$app->request;
        $code = $request->get('team_name','');
        $teamCode = array();
        $teamShort = array();
        if($code != ''){
            $teamCode = ['like', 'team_code', $code . '%', false];
            $teamShort = ['like', 'team_short_name', $code . '%', false];
        }
        $team = Team::find()->orWhere($teamShort)->orWhere($teamCode)->orderBy('team_id');
        $data = new ActiveDataProvider([
            'query' => $team,
            'pagination' => [
                'pageSize' => 20
            ]
        ]);
        return $this->render("index", ["data" => $data]);
    }
    
    public function actionAddteam() {
        if (!Yii::$app->request->isAjax) {
            $country = new Country();
            $data = $country->getCountryList();
            return $this->render("addteam",['data'=>$data]);
        }
        $format = 'Y-m-d H:i:s';
        $request = Yii::$app->request;
        $code = $request->post('team_code','');
        $sname = $request->post('team_short_name','');
        $lname = $request->post('team_name','');
        if($code == '' || $sname == '' || $lname == ''){
            return $this->jsonResult(2,'请填写所有必填信心');
        }
        $cate = $request->post('country','');
        if($cate == '' || $cate == '0'){
            return $this->jsonResult(2,'请选择所属类型');
        }
        $cateData = Country::find()->select('country_code,country_name')->where(['country_code'=>$cate])->asArray()->one();
        $post = Yii::$app->request->post();
        $only = Team::find()->select(['team_id', 'team_long_name', 'team_short_name', 'team_code'])->where(['team_code' => $code])->orWhere(['team_short_name' => $sname])->orWhere(['team_long_name' => $lname])->asArray()->one();
        if(!empty($only)){
            if($only['team_long_name'] == $lname){
                return $this->jsonResult(2,'该球队全称已存在');
            }
            if($only['team_short_name'] == $sname){
                return $this->jsonResult(2,'该联赛简称已存在');
            }
            if($only['team_code'] == $code){
                return $this->jsonResult(2,'该联赛编码已存在');
            }
        }
        $post = Yii::$app->request->post();
        $model = new Team();
        $path = null;
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
            $model->team_img = $path['result']['ret_path'];
        }
        $model->team_code = $post["team_code"];
        $model->team_short_name = $post["team_short_name"];
        $model->team_long_name =  $post['team_name'];
        $model->country_code = $cateData['country_code'];
        $model->country_name = $cateData['country_name'];
        $model->team_remarks = $post['remark'];
        $model->team_status = 1;
        $model->opt_id = \Yii::$app->session["admin_id"];
        $model->create_time = date($format);
        if ($model->validate(["team_img", "team_code", "team_short_name", "team_long_name", "country_code", "country_name", "team_remarks", "team_status","create_time"])) {
            $id = $model->save();
            if($id != false){
                return $this->jsonResult(1,'新增成功','');
            } else {
                return $this->jsonResult(2,'新增失败','');
            }
        }else {
            return $this->jsonResult(2,'新增失败,参数有误，不可为空',$model->getFirstErrors());
        }
    }
    
    public function actionStatusTeam() {
        $request = Yii::$app->request;
        $status = $request->post('status','');
        $id = $request->post('id','');
        if($id == '' || $status == ''){
            return $this->jsonResult(2,'参数有误','');
        }
        if($status == 1){
            $status = 0;
        } else {
            $status = 1;
        }
        $model = Team::find()->where(['team_id'=>$id])->one();
        $model->team_status = $status;
        $model->modify_time = date('Y-m-d H:i:s');
        $model->opt_id = \Yii::$app->session["admin_id"];
        if($model->save()){
             return $this->jsonResult(1,'修改成功','');
        } else {
            return $this->jsonResult(2,'修改失败','');
        }
    }
    
    public function actionDeleteTeam() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/lottery/team/index');
        }

        $request = Yii::$app->request;
        $id = $request->post('id','');
        if($id == ''){
            return $this->jsonResult(2,'参数有误','');
        }
        $data = LeagueTeam::find()->select('league_team_id')->where(['team_id' => $id])->asArray()->one();
        if(!empty($exits) || !empty($data) ){
            return $this->jsonResult(2,'该球队已有联赛关联，不可删除','');
        }
        
        $model = Team::findOne($id);
        $result = $model->delete();
        if($result != false) {
            return $this->jsonResult(1,'删除成功','');
        } else {
            return $this->jsonResult(2,'删除失败','');
        }
           
    }
    
    public function actionEditteam() {
        if (!Yii::$app->request->get()) {
            echo '操作错误';
            exit();
        }
        $get = Yii::$app->request->get();
        $country = new Country();
        $data = $country->getCountryList();
       // $raceCate = ["请选择", "五大联赛", "其他"];
        $model = Team::find()
                    ->where(["team_id" => $get['team_id']])
                    ->asArray()
                    ->one();
        $model['country'] = $data; 
        return $this->render('editteam', ['model' => $model]);
    }
    
    /**
     * 编辑保存
     */
    public function actionDoEditTeam(){
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/lottery/team/editteam');
        }

        $format = 'Y-m-d H:i:s';
        $request = Yii::$app->request;
        $post = $request->post();
        $code = $request->post('team_code','');
        $sname = $request->post('team_short_name','');
        $lname = $request->post('team_name','');
        if($code == '' || $sname == '' || $lname == ''){
            return $this->jsonResult(2,'请填写所有必填信心');
        }
        $cate = $request->post('country','');
        if($cate == '' || $cate == '0'){
            return $this->jsonResult(2,'请选择所属类型');
        }
        $cateData = Country::find()->select('country_code,country_name')->where(['country_code'=>$cate])->asArray()->one();
        $only = Team::find()->select(['team_id', 'team_long_name', 'team_short_name', 'team_code'])->where(['team_code' => $code])->orWhere(['team_short_name' => $sname])->orWhere(['team_long_name' => $lname])->andWhere(['!=', 'team_id', $post['team_id']])->asArray()->one();
        if(!empty($only)){
            if($only['team_long_name'] == $lname){
                return $this->jsonResult(2,'该球队全称已存在');
            }
            if($only['team_short_name'] == $sname){
                return $this->jsonResult(2,'该联赛简称已存在');
            }
            if($only['team_code'] == $code){
                return $this->jsonResult(2,'该联赛编码已存在');
            }
        }
        $path = null;
        $model = Team::findOne(["team_id" => $post['team_id']]);
        $path = null;
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
            $model->team_img = $path['result']['ret_path'];
        }
        $model->team_code = $post["team_code"];
        $model->team_short_name = $post["team_short_name"];
        $model->team_long_name =  $post['team_name'];
        $model->country_code = $cateData['country_code'];
        $model->country_name = $cateData['country_name'];
        $model->team_remarks = $post['remark'];
        $model->team_status = 1;
        $model->modify_time = date($format);
        $model->opt_id = \Yii::$app->session["admin_id"];
        if ($model->validate(["team_img", "team_code", "team_short_name", "team_long_name", "team_remarks", "team_status","modify_time"])) {
            $id = $model->save();
            if($id != false){
                return $this->jsonResult(1,'编辑修改成功','');
            } else {
                return $this->jsonResult(2,'编辑修改失败','');
            }
        }else {
            return $this->jsonResult(2,'编辑失败,参数有误，不可为空',$model->getFirstErrors());
        }
    }
}
