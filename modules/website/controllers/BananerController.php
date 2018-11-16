<?php

namespace app\modules\website\controllers;

use yii;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;
use app\modules\common\helpers\PublicHelpers;
use app\modules\common\models\Bananer;

/**
 * Default controller for the `index` module
 */
class BananerController extends Controller {

    public function actionIndex() {
        $request = Yii::$app->request;
        $get = $request->get();
        $picName = $request->get('picName', '');
        $type = $request->get('picType', '');
        $status = $request->get('picStatus', '');
        $infotype = $request->get('infoType', '');
        $leaguecode = $request->get('leagueCode', '');
        $picType = PublicHelpers::BANANER_TYPE;
        $picStatus = PublicHelpers::BANANER_STATUS;
        $infoType = PublicHelpers::INFO_TYPE;
        $leagueCode = PublicHelpers::LEAGUE_CODE;
        $bananer=  Bananer::find()->where(["type"=>1]);
        if (!empty($picName)) {
            $bananer = $bananer->andWhere(["pic_name"=>$picName]);
        }
        if (isset($type)&&!empty($type)) {
            $bananer = $bananer->andWhere(["type"=>$type]);
        }
        if (isset($status)&&$status!="") {
            $bananer = $bananer->andWhere(["status" => $status]);
        }
        if (!empty($infotype)) {
            $bananer = $bananer->andWhere(["info_type"=>$infotype]);
        }
        if (!empty($leaguecode)) {
            $bananer = $bananer->andWhere(["league_code"=>$leaguecode]);
        }
        $bananer =$bananer->orderBy('create_time desc');
        $data = new ActiveDataProvider([
            'query' => $bananer,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('index', ['dataList' => $data,"get" => $get,"type" => $picType,"status" => $picStatus,"infoType"=>$infoType,"leagueCode"=>$leagueCode]);
    }

    /**
     * 新增动态广告
     */
    public function actionAddBananer() {
        if (Yii::$app->request->isGet) {
            $picType = PublicHelpers::BANANER_TYPE;
            $picStatus = PublicHelpers::BANANER_STATUS;
            $infoType = PublicHelpers::INFO_TYPE;
            $leagueCode = PublicHelpers::LEAGUE_CODE;
            return $this->render("add-bananer", ["picType" => $picType, "picStatus" => $picStatus,"infoType"=>$infoType,"leagueCode"=>$leagueCode]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $picName = $post["articleTitle"];
            $content = $post["content"];
            $info_type = $post["info_type"];
            $league_code = $post["league_code"];
//            $type = $post["type"];
//            $status = $post["status"];
            $timer = date('Y-m-d H:i:s');
            if($picName==""||$content==""||empty($info_type)||empty($league_code)){
                return $this->jsonResult(109, '参数有误，请检查重新上传');
            }
            if (isset($_FILES['upfile'])) {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/bananer/';
                $str = substr(strrchr($file['name'], '.'), 1);
                $name = rand(0,99).'.' . $str;
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir,$name);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $picUrl = $path['result']['ret_path'];
            } else {
                return $this->jsonResult(109, '请上传广告图', '');
            }
           $bananer = new Bananer();
           $bananer->pic_name= $picName;
           $bananer->content= $content;
           $bananer->pic_url= $picUrl;
           $bananer->league_code= $league_code;
           $bananer->type= 1;
           $bananer->info_type= $info_type;
//           $bananer->status= $status;
           $bananer->create_time= $timer;
           $bananer->opt_id= $session['admin_id'];
           if ($bananer->validate()) {
                $result = $bananer->save();
                if ($result == false) {
                    return $this->jsonResult(109, '资讯页面新增失败');
                }
                return $this->jsonResult(600, '资讯页面广告新增成功');
            } else {
                return $this->jsonResult(109, 'Bananer表单验证失败', $bananer->errors);
            }
           
        }
    }
     /**
     * 修改广告启用禁用状态
     * @return json
     */
    public function actionEditStatus() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/website/bananer/index');
        }
        $request = Yii::$app->request;
        $bananerId = $request->post('bananer_id', '');
        $status = $request->post('status', '');
        if ($bananerId == "" || $status == "") {
            return $this->jsonResult(109, '参数有误', '');
        }
        $result = Bananer::updateAll(['status' => $status], ['bananer_id' => $bananerId]);
        if ($result != false) {
            return $this->jsonResult(600, '状态修改成功', '');
        } else {
            return $this->jsonResult(109, '状态修改失败', '');
        }
    }
    /**
     * 删除广告
     * @return json
     */
    public function actionDelBananer() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/website/bananer/index');
        }
        $request = Yii::$app->request;
        $bananerId = $request->post('bananer_id', '');
        if ($bananerId == "") {
            return $this->jsonResult(109, '参数有误', '');
        }
        $result = Bananer::deleteAll(['bananer_id' => $bananerId]);
        if ($result != false) {
            return $this->jsonResult(600, '广告删除成功', '');
        } else {
            return $this->jsonResult(109, '广告删除失败', '');
        }
    }
    /**
     * 编辑广告
     */
    public function actionEditBananer(){
//        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $get=Yii::$app->request->get();
            $picType = PublicHelpers::BANANER_TYPE;
            $picStatus = PublicHelpers::BANANER_STATUS;
            $infoType = PublicHelpers::INFO_TYPE;
            $leagueCode = PublicHelpers::LEAGUE_CODE;
            $bananerId=$get["bananer_id"];
            if(empty($bananerId)){
               return $this->jsonResult(109, '参数有误', '');  
            }
            $bananer = Bananer::find()->where(["bananer_id"=>$bananerId])->asArray()->one();
            return $this->render('edit-bananer',["data"=>$bananer,"picType" => $picType, "picStatus" => $picStatus,"infoType"=>$infoType,"leagueCode"=>$leagueCode]);
        }elseif(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $bananerId=$post["bananer_id"];
            $picName = $post["articleTitle"];
            $content = $post["content"];
            $info_type = $post["info_type"];
            $league_code = $post["league_code"];
//            $type = $post["type"];
//            $status = $post["status"];
            $timer = date('Y-m-d H:i:s');
            if($picName==""||$content==""||empty($info_type||empty($league_code))){
                return $this->jsonResult(109, '参数有误，请检查重新上传');
            }
            if (isset($_FILES['upfile'])&&$_FILES['upfile']!="Undefined") {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/bananer/';
                $str = substr(strrchr($file['name'], '.'), 1);
                $name = rand(0,99).'.' . $str;
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir,$name);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $picUrl = $path['result']['ret_path'];
            }else{
               $bananer = Bananer::find()->where(["bananer_id"=>$bananerId])->one(); 
               $picUrl=$bananer->pic_url;
            }
           $bananer = Bananer::find()->where(["bananer_id"=>$bananerId])->one(); 
           $bananer->pic_name= $picName;
           $bananer->pic_url= $picUrl;
           $bananer->content= $content;
           $bananer->info_type= $info_type;
           $bananer->league_code= $league_code;
//           $bananer->status= $status;
           $bananer->opt_id= $session['admin_id'];
           if ($bananer->validate()) {
                $result = $bananer->save();
                if ($result == false) {
                    return $this->jsonResult(109, '赛事页面广告编辑失败');
                }
                return $this->jsonResult(600, '赛事页面广告编辑成功');
            } else {
                return $this->jsonResult(109, 'Bananer表单验证失败', $bananer->errors);
            }
        }
    }
}
