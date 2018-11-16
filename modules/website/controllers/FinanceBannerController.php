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
class FinanceBannerController extends Controller {

    public function actionIndex() {
        $request = Yii::$app->request;
        $get = $request->get();
        $picName = $request->get('picName', '');
        $status = $request->get('picStatus', '');
        $useType = $request->get('picUse', '');
        $picStatus = PublicHelpers::BANANER_STATUS;
        $picUse = PublicHelpers::BANANER_USE;
        $banner=  Bananer::find()->where(["type"=>7]);
        if (!empty($picName)) {
            $banner = $banner->andWhere(["pic_name"=>$picName]);
        }
        if (isset($status)&&$status!="") {
            $banner = $banner->andWhere(["status" => $status]);
        }
        if (isset($useType)&&$useType!="") {
            $banner = $banner->andWhere(["use_type" => $useType]);
        }
        $banner =$banner->orderBy('create_time desc');
        $data = new ActiveDataProvider([
            'query' => $banner,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('index', ['dataList' => $data,"get" => $get,"status" => $picStatus,"picUse"=>$picUse]);
    }

    /**
     * 新增动态广告
     */
    public function actionAdd() {
        if (Yii::$app->request->isGet) {
            $picType = PublicHelpers::BANANER_TYPE;
            $picStatus = PublicHelpers::BANANER_STATUS;
            $picUse = PublicHelpers::BANANER_USE;
            $picArea = PublicHelpers::BANANER_AREA;
            $picJump = PublicHelpers::AC_JUMP_TYPE;
            return $this->render("add", ["picType" => $picType, "picStatus" => $picStatus,"picUse"=>$picUse,"picArea"=>$picArea,"picJump"=>$picJump]);
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $picName = $post["articleTitle"];
            $content = $post["content"];
            $jumpUrl = $post["jump_url"];
//            $area = $post["area"];
            $jumpType = $post["jumpType"];
            $jumpTitle = $post["jump_title"];
//            $status = $post["status"];
            $timer = date('Y-m-d H:i:s');
//            $useType = $post["use_type"];
//            if($picName==""){
//                return $this->jsonResult(109, '参数有误，请检查重新上传');
//            }
            //外部链接需要添加typeNav 控制导航条，自己内部链接则不需要
            if($jumpType==2){
                if(!strpos($jumpUrl,\Yii::$app->params["banner_url"])){
                    $jumpUrl = $post["jump_url"].'?typeNav=1';
                }
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
                return $this->jsonResult(109, '请上传APP图片', '');
            }
//            $picUrl2="";
//            if (isset($_FILES['upfile2'])) {
//                $file = $_FILES['upfile2'];
//                $check = UploadForm::getUpload($file);
//                if ($check['code'] != 600) {
//                    return $this->jsonResult($check['code'], $check['msg']);
//                }
//                $saveDir = '/bananer/';
//                $str = substr(strrchr($file['name'], '.'), 1);
//                $name = rand(0,99).'.' . $str;
//                $pathJson = Uploadfile::pic_host_upload($file, $saveDir,$name);
//                $path = json_decode($pathJson, true);
//                if ($path['code'] != 600) {
//                    return $this->jsonResult($path['code'], $path['msg']);
//                }
//                $picUrl2 = $path['result']['ret_path'];
//            }
//            elseif($useType==1) {
//                return $this->jsonResult(109, '请上传PC图片', '');
//            }


           $banner = new Bananer();
           $banner->pic_name= $picName;
           $banner->content= $content;
           $banner->pic_url= $picUrl;
//           $bananer->pc_pic_url= $picUrl2;
           $banner->jump_url= $jumpUrl;
           $banner->type= 7;
//            $bananer->use_type= $useType;
            $banner->create_time= $timer;
            $banner->jump_type= $jumpType;
            $banner->jump_title= $jumpTitle;
//            $bananer->area= $area;
           $banner->opt_id= $session['admin_id'];
           if ($banner->validate()) {
                $result = $banner->save();
               if ($jumpType == 1) {
                   $id = $bananer->attributes['bananer_id'];
                   $url = \Yii::$app->params["userDomain"] . "/article/" . $id;
                   Bananer::updateAll(["jump_url" => $url], ["bananer_id" => $id]);
               }
                if ($result == false) {
                    return $this->jsonResult(109, '广告新增失败');
                }
                return $this->jsonResult(600, '广告新增成功');
            } else {
                return $this->jsonResult(109, 'Bananer表单验证失败', $banner->errors);
            }
           
        }
    }
     /**
     * 修改广告启用禁用状态
     * @return json
     */
    public function actionEditSta() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/website/finance-banner/index');
        }
        $request = Yii::$app->request;
        $bannerId = $request->post('banner_id', '');
        $status = $request->post('status', '');
        if ($bannerId == "" || $status == "") {
            return $this->jsonResult(109, '参数有误', '');
        }
        $result = Bananer::updateAll(['status' => $status], ['bananer_id' => $bannerId]);
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
    public function actionDel() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/website/finance-banner/index');
        }
        $request = Yii::$app->request;
        $bannerId = $request->post('banner_id', '');
        if ($bannerId == "") {
            return $this->jsonResult(109, '参数有误', '');
        }
        $result = Bananer::deleteAll(['bananer_id' => $bannerId]);
        if ($result != false) {
            return $this->jsonResult(600, '广告删除成功', '');
        } else {
            return $this->jsonResult(109, '广告删除失败', '');
        }
    }
    /**
     * 编辑广告
     */
    public function actionEdit(){
//        $this->layout = false;
        if (Yii::$app->request->isGet) {
            $get=Yii::$app->request->get();
            $picType = PublicHelpers::BANANER_TYPE;
            $picStatus = PublicHelpers::BANANER_STATUS;
            $picUse = PublicHelpers::BANANER_USE;
            $picArea = PublicHelpers::BANANER_AREA;
            $picJump = PublicHelpers::AC_JUMP_TYPE;
            $bannerId=$get["bananer_id"];
            if(empty($bannerId)){
               return $this->jsonResult(109, '参数有误', '');  
            }
            $banner = Bananer::find()->where(["bananer_id"=>$bannerId])->asArray()->one();
            return $this->render('edit',["data"=>$banner,"picType" => $picType, "picStatus" => $picStatus,"picUse"=>$picUse,"picArea"=>$picArea,"picJump"=>$picJump]);
        }elseif(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $bannerId=$post["bananer_id"];
            $picName = $post["articleTitle"];
            $content = $post["content"];
            $jumpUrl = $post["jump_url"];
//            $area = $post["area"];
            $jumpType = $post["jumpType"];
//            $useType = $post["use_type"];
            $jumpTitle = $post["jump_title"];
            $timer = date('Y-m-d H:i:s');
//            if($picName==""){
//                return $this->jsonResult(109, '参数有误，请检查重新上传');
//            }
            //外部链接需要添加typeNav 控制导航条，自己内部链接则不需要
            if($jumpType==2){
                if(!strpos($jumpUrl,\Yii::$app->params["banner_url"])){
                    $jumpUrl = $post["jump_url"].'?typeNav=1';
                }
            }
            if (isset($_FILES['upfile'])&&$_FILES['upfile']!="Undefined") {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/bananer/';
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $picUrl = $path['result']['ret_path'];
            }else{
               $banner = Bananer::find()->where(["bananer_id"=>$bannerId])->one(); 
               $picUrl=$banner->pic_url;
            }

//            if (isset($_FILES['upfile2'])&&$_FILES['upfile2']!="Undefined") {
//                $file = $_FILES['upfile2'];
//                $check = UploadForm::getUpload($file);
//                if ($check['code'] != 600) {
//                    return $this->jsonResult($check['code'], $check['msg']);
//                }
//                $saveDir = '/bananer/';
//                $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
//                $path = json_decode($pathJson, true);
//                if ($path['code'] != 600) {
//                    return $this->jsonResult($path['code'], $path['msg']);
//                }
//                $picUrl2 = $path['result']['ret_path'];
//            }else{
//                $bananer = Bananer::find()->where(["bananer_id"=>$bananerId])->one();
//                $picUrl2=$bananer->pc_pic_url;
//            }
//            elseif($useType==1) {
//                $bananer = Bananer::find()->where(["bananer_id"=>$bananerId])->one();
//                $picUrl2=$bananer->pc_pic_url;
//            }
           $banner = Bananer::find()->where(["bananer_id"=>$bannerId])->one(); 
           $banner->pic_name= $picName;
           $banner->pic_url= $picUrl;
//           $bananer->pc_pic_url= $picUrl2;
//           if($useType==1){
//               $bananer->pc_pic_url= $picUrl2;
//           }
           $banner->content= $content;
//           $bananer->use_type= $useType;
           $banner->jump_url= $jumpUrl;
            $banner->jump_type= $jumpType;
//            $bananer->area= $area;
            $banner->jump_title = $jumpTitle;
           $banner->opt_id= $session['admin_id'];
           if ($banner->validate()) {
                $result = $banner->save();
               if ($jumpType == 1) {
                   $id = $bananer->attributes['bananer_id'];
                   $url = \Yii::$app->params["userDomain"] . "/article/" . $id;
                   Bananer::updateAll(["jump_url" => $url], ["bananer_id" => $id]);
               }
                if ($result == false) {
                    return $this->jsonResult(109, '广告编辑失败');
                }
                return $this->jsonResult(600, '广告编辑成功');
            } else {
                return $this->jsonResult(109, 'Bananer表单验证失败',$banner->errors);
            }
        }
    }
}
