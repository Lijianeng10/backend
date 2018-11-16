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
use app\modules\common\models\SysConf;

/**
 * Default controller for the `index` module
 */
class StartController extends Controller {

    public function actionIndex() {
        $request = Yii::$app->request;
        $get = $request->get();
        $picName = $request->get('picName', '');
        $type = $request->get('picType', '');
        $status = $request->get('picStatus', '');
        $picType = PublicHelpers::BANANER_TYPE;
        $picStatus = PublicHelpers::BANANER_STATUS;
        $bananer=  Bananer::find()->where(["type"=>2]);
        if (!empty($picName)) {
            $bananer = $bananer->andWhere(["pic_name"=>$picName]);
        }
        if (isset($type)&&!empty($type)) {
            $bananer = $bananer->andWhere(["type"=>$type]);
        }
        if (isset($status)&&$status!="") {
            $bananer = $bananer->andWhere(["status" => $status]);
        }
        $bananer =$bananer->orderBy('create_time desc');
        $data = new ActiveDataProvider([
            'query' => $bananer,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('index', ['dataList' => $data,"get" => $get,"type" => $picType,"status" => $picStatus]);
    }

    /**
     * 新增动态广告
     */
    public function actionAddBananer() {
        if (Yii::$app->request->isGet) {
            $picType = PublicHelpers::BANANER_TYPE;
            $picStatus = PublicHelpers::BANANER_STATUS;
            return $this->render("add-bananer", ["picType" => $picType, "picStatus" => $picStatus]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $picName = $post["articleTitle"];
//            $content = $post["content"];
//            $jumpUrl = $post["jump_url"];
//            $type = $post["type"];
//            $status = $post["status"];
            $timer = date('ymdHis', time());
            $now = date("Y-m-d H:i:s");
//            if($picName==""||$content==""){
//                return $this->jsonResult(109, '参数有误，请检查重新上传');
//            }
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
//           $bananer->content= $content;
           $bananer->pic_url= $picUrl;
//           $bananer->jump_url= $jumpUrl;
           $bananer->type= 2;
//           $bananer->status= $status;
           $bananer->create_time= $now;
           $bananer->opt_id= $session['admin_id'];
           if ($bananer->validate()) {
                $result = $bananer->save();
                if ($result == false) {
                    return $this->jsonResult(109, '启动页面广告新增失败');
                }
               //新增启动页广告同步更新初始化参数表中的ad_pic字段
               SysConf::updateAll(['value' => $picUrl], ['code' =>"ad_pic"]);
                return $this->jsonResult(600, '启动页面广告新增成功');
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
            return $this->redirect('/website/start/index');
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
            return $this->redirect('/website/start/index');
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
            $bananerId=$get["bananer_id"];
            if(empty($bananerId)){
               return $this->jsonResult(109, '参数有误', '');  
            }
            $bananer = Bananer::find()->where(["bananer_id"=>$bananerId])->asArray()->one();
            return $this->render('edit-bananer',["data"=>$bananer,"picType" => $picType, "picStatus" => $picStatus]);
        }elseif(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $bananerId=$post["bananer_id"];
            $picName = $post["articleTitle"];
//            $content = $post["content"];
//            $jumpUrl = $post["jump_url"];
//            $type = $post["type"];
//            $status = $post["status"];
            $timer = date('ymdHis', time());
//            if($picName==""||$content==""){
//                return $this->jsonResult(109, '参数有误，请检查重新上传');
//            }
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
//           $bananer->content= $content;

//           $bananer->jump_url= $jumpUrl;
//           $bananer->type= 1;
//           $bananer->status= $status;
           $bananer->opt_id= $session['admin_id'];
           if ($bananer->validate()) {
                $result = $bananer->save();
                if ($result == false) {
                    return $this->jsonResult(109, '启动页面广告编辑失败');
                }
               //新增启动页广告同步更新初始化参数表中的ad_pic字段
               SysConf::updateAll(['value' => $picUrl], ['code' =>"ad_pic"]);
                return $this->jsonResult(600, '启动页面广告编辑成功');
            } else {
                return $this->jsonResult(109, 'Bananer表单验证失败', $bananer->errors);
            }
        }
    }
}
