<?php

namespace app\modules\agents\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Bussiness;
use app\modules\common\models\ApiIp;
use app\modules\common\models\ApiList;
use app\modules\common\models\BussinessIpWhite;

class ApilistController extends \yii\web\Controller {
     /**
     * api接口列表
     */
    public function actionIndex(){
        $get = \Yii::$app->request->get();
        $query = (new Query())->select("*")
                ->from("api_list");
        if (isset($get["api_name"]) && !empty($get["api_name"])) {
            $query = $query->andWhere(["or",["api_name" => $get["api_name"]],["api_url" => $get["api_name"]]]);
        }
        if (isset($get["status"]) && !empty($get["status"])) {
            $query = $query->andWhere(["status" => $get["status"]]);
        }
        $query = $query->orderBy("api_list_id desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
       return $this->render("index",["data" => $data]); 
    }
    /**
     * 新增接口
     */
    public function  actionAddApi(){
        $this->layout=false;
       if (Yii::$app->request->isGet) {
          return $this->render("add-api");
        } elseif (Yii::$app->request->isAjax) {
            $post=Yii::$app->request->post();
            $api_name =$post["api_name"];
            $api_url =$post["api_url"];
            if($api_name==""||$api_url==""){
               return $this->jsonResult(109, "参数缺失，请将参数填写完整");
            }
            $ApiRes=ApiList::find()->where(["or",["api_name"=>$api_name],["api_url"=>$api_url]])->asArray()->one();
            if(!empty($ApiRes)){
                return $this->jsonResult(109, "接口名称重复或者URL重复，请重新填写");
            }
            $apilist=new ApiList();
            $apilist->api_name=$api_name;
            $apilist->api_url=$api_url;
            if($apilist->validate()){
                $res=$apilist->save();
                if($res){
                    return $this->jsonResult(600, "新增成功");
                }else{
                    return $this->jsonResult(109, "新增失败");
                }
            }else{
                return $this->jsonResult(109, "新增失败,接口表单验证失败");
            }
        }  
    }
    /**
     * 接口启用禁用
     */
    public function actionEdituse() {
        $post = \Yii::$app->request->post();
        $res = ApiList::updateAll(["status" => $post["sta"],], ["api_list_id" => $post["api_list_id"]]);
        if ($res){
            return $this->jsonResult(600, "修改成功");
        } else {
            return $this->jsonResult(109, "修改失败");
        }
    }
    /**
     * 编辑修改接口
     */
    public function actionEditApi(){
        $this->layout=false;
       if (Yii::$app->request->isGet) {
           $get=Yii::$app->request->get();
           $api_list_id=$get["api_list_id"];
           if(empty($api_list_id)){
               return $this->jsonResult(109, "参数缺失");
           }
           $ApiRes=ApiList::find()->where(["api_list_id"=>$api_list_id])->asArray()->one();
           return $this->render("edit-api",["data"=>$ApiRes]);
        } elseif (Yii::$app->request->isAjax) {
            $post=Yii::$app->request->post();
            $api_list_id =$post["api_list_id"];
            $api_name =$post["api_name"];
            $api_url =$post["api_url"];
            if($api_name==""||$api_url==""){
               return $this->jsonResult(109, "参数缺失，请将参数填写完整");
            }
            $ApiRes=ApiList::find()->where(["or",["api_name"=>$api_name],["api_url"=>$api_url]])->andWhere(["<>","api_list_id",$api_list_id])->asArray()->one();
            if(!empty($ApiRes)){
                return $this->jsonResult(109, "接口名称重复或者URL重复，请重新填写");
            }
            $api=  ApiList::find()->where(["api_list_id"=>$api_list_id])->one();
            $api->api_name=$api_name;
            $api->api_url=$api_url;
            if($api->validate()){
                $res=$api->save();
                if($res){
                    return $this->jsonResult(600, "修改成功");
                }else{
                    return $this->jsonResult(109, "修改失败");
                }
            }else{
                return $this->jsonResult(109, "修改失败,接口表单验证失败");
            }
        }  
    }
    /**
     * 删除接口
     */
    public function actionDeleteApi() {
        $post = \Yii::$app->request->post();
        $db = \Yii::$app->db;
        $tran = $db->beginTransaction();
        try {
            $res = ApiList::deleteAll(["api_list_id" => $post["api_list_id"]]);
            if (!$res){
                throw new Exception('接口删除失败');
            }
            $result = ApiIp::deleteAll(["api_list_id"=>$post["api_list_id"]]);
            $tran->commit();
            return $this->jsonResult(600, "删除成功", "");
        }catch (\yii\db\Exception $e) {
            return $this->jsonResult(109, "删除失败", $e->getMessage());
        }
       
    }
}

