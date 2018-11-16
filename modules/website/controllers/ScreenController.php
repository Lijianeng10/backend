<?php

namespace app\modules\website\controllers;
use yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use app\modules\common\models\StoreScreen;

/**
 * Default controller for the `index` module
 */
class ScreenController extends Controller{
    /**
     * Renders the index view for the module
     * @return string
     */
//    public $enableCsrfValidation = false;

    public function actionIndex(){
        $get= \Yii::$app->request->get();
        $record = (new Query())->select("*")
                ->from("store_screen");
//        if(!empty($get["user_info"])){
//            $wxMsgRecord=$wxMsgRecord->where(["or",["u.user_name"=>$get["user_info"]],["u.user_tel"=>$get["user_info"]],["u.cust_no"=>$get["user_info"]]]);
//        }
//        if(!empty($get["order_code"])){
//            $wxMsgRecord=$wxMsgRecord->andWhere(["wx.order_code"=>$get["order_code"]]);
//        }
//        if(!empty($get["type"])){
//            $wxMsgRecord=$wxMsgRecord->andWhere(["wx.type"=>$get["type"]]);
//        }
//         if(isset($get["status"])&&!empty($get["status"])){
//            $wxMsgRecord=$wxMsgRecord->andWhere(["wx.status"=>$get["status"]]);
//        }
//        if (!empty($get["startdate"])) {
//            $wxMsgRecord = $wxMsgRecord->andWhere([">", "wx.create_time", $get["startdate"] . " 00:00:00"]);
//        }
//        if (!empty($get["enddate"])) {  
//            $wxMsgRecord = $wxMsgRecord->andWhere(["<", "wx.create_time", $get["enddate"] . " 23:59:59"]);
//        }
        $record=$record->orderBy('create_time desc');
        $data = new ActiveDataProvider([
            'query' => $record,
        ]);
        return $this->render('index', ['data' => $data,'get'=>$get]);
    }
    /**
     * 新增授权码
     */
    public function actionAddScreen(){
        $this->layout=false;
        if (!Yii::$app->request->isPost) {
            return $this->render("add-screen");
        }
        $post = Yii::$app->request->post();
        $remark = $post["remark"];
        if(empty($remark)){
            return $this->jsonResult(109, '备注不能为空');
        }
        $screen = new StoreScreen();
        $screen->screen_key = $this->getRedeemMark();
        $screen->store_code = $remark;
        $screen->create_time = date("Y-m-d H:i:s");
        if($screen->validate()){
            $res = $screen->save();
            if($res){
               return $this->jsonResult(600, '新增成功'); 
            }else{
              return $this->jsonResult(109, '新增失败');   
            }
        }else{
            return $this->jsonResult(109,$screen->getFirstErrors());
        }
        
    }
     /**
     * 生成6位授权码
     */
    public function getRedeemMark() {
        $str = strtoupper(substr(md5(uniqid(microtime(true), true)),0,6));
        return $str;
    }
}