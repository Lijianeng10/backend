<?php

namespace app\modules\promote\controllers;

use Yii;
use yii\db\Query;
use yii\db\Exception;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\promote\models\StoreUser;

class StoreController extends Controller {
    /**
     * 门店列表
     * @return type
     */
    public function actionIndex() {
        $request = Yii::$app->request;
        $get = $request->get();
        $storeInfo = $request->get('storeInfo', '');
        $status = $request->get('status', '');
        $userStatus=[
            ""=>"请选择",
            "0"=>"待审核",
            "1"=>"未通过",
            "2"=>"已通过",
            
        ];
        $userInfo = StoreUser::find()
                ->select("store_user.*,user.user_tel")
                ->leftJoin("user","user.user_id = store_user.user_id");
        if (isset($storeInfo)&&!empty($storeInfo)) {
            $userInfo = $userInfo->andWhere(["or",["store_user.store_code"=>$storeInfo],["store_user.store_name"=>$storeInfo],["user.user_tel"=>$storeInfo]]);
        }
        if (isset($status)&&$status!="") {
            $userInfo = $userInfo->andWhere(["store_user.status" => $status]);
        }
        if (!empty($get["startdate"])) {
            $userInfo = $userInfo->andWhere([">", "store_user.create_time", $get["startdate"] . " 00:00:00"]);
        }
        if (!empty($get["enddate"])) {  
            $userInfo = $userInfo->andWhere(["<", "store_user.create_time", $get["enddate"] . " 23:59:59"]);
        }
        $userInfo =$userInfo->orderBy('store_user.create_time desc');
        $data = new ActiveDataProvider([
            'query' => $userInfo,
        ]);
        return $this->render('index', ['data'=>$data,'get' => $get,"userStatus"=>$userStatus]);
    }
    /**
     * 用户审核
     * @return json
     */
    public function actionEditStatus() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/promote/store/index');
        }
        $request = Yii::$app->request;
        $id = $request->post('id', '');
        $status = $request->post('status', '');
        if ($id == "" || $status == "") {
            return $this->jsonResult(109, '参数有误');
        }
        $result = StoreUser::updateAll(['status' => $status], ['id' => $id]);
        if ($result != false) {
            return $this->jsonResult(600, '操作成功');
        } else {
            return $this->jsonResult(109, '操作失败');
        }
    }
    /**
     * 用户删除
     */
    public function  actionDelUser(){
       $request = Yii::$app->request;
       $id = $request->post('id', '');
       if ($id == "") {
            return $this->jsonResult(109, '参数有误');
        }
        $result = StoreUser::deleteAll(['id' => $id]);
        if ($result != false) {
            return $this->jsonResult(600, '操作成功');
        } else {
            return $this->jsonResult(109, '操作失败');
        }
    }
}
