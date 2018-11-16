<?php

namespace app\modules\website\controllers;

use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Default controller for the `index` module
 */
class WxlogController extends Controller{
    /**
     * Renders the index view for the module
     * @return string
     */
//    public $enableCsrfValidation = false;

    public function actionIndex(){
        $get= \Yii::$app->request->get();
        $status=[
            "0"=>"请选择",
            "1"=>"成功",
            "2"=>"失败",
        ];
        $wxMsgRecord = (new Query())->select("wx.*,u.user_name,u.user_tel,u.cust_no")
                ->from(['wx' => 'wx_msg_record']);
        if(!empty($get["user_info"])){
            $wxMsgRecord=$wxMsgRecord->where(["or",["u.user_name"=>$get["user_info"]],["u.user_tel"=>$get["user_info"]],["u.cust_no"=>$get["user_info"]]]);
        }
        if(!empty($get["order_code"])){
            $wxMsgRecord=$wxMsgRecord->andWhere(["wx.order_code"=>$get["order_code"]]);
        }
        if(!empty($get["type"])){
            $wxMsgRecord=$wxMsgRecord->andWhere(["wx.type"=>$get["type"]]);
        }
         if(isset($get["status"])&&!empty($get["status"])){
            $wxMsgRecord=$wxMsgRecord->andWhere(["wx.status"=>$get["status"]]);
        }
        if (!empty($get["startdate"])) {
            $wxMsgRecord = $wxMsgRecord->andWhere([">", "wx.create_time", $get["startdate"] . " 00:00:00"]);
        }
        if (!empty($get["enddate"])) {  
            $wxMsgRecord = $wxMsgRecord->andWhere(["<", "wx.create_time", $get["enddate"] . " 23:59:59"]);
        }
        if (!empty($get["store_info"])) {
            $wxMsgRecord = $wxMsgRecord->andWhere(["or",["wx.store_name"=>$get["store_info"]],["wx.province"=>$get["store_info"]]]);
        }
        $wxMsgRecord=$wxMsgRecord->leftJoin(['t' => 'third_user'], 'wx.user_open_id=t.third_uid')
                ->leftJoin(['u' => 'user'], 't.uid=u.user_id')
                ->orderBy('create_time desc');
        $data = new ActiveDataProvider([
            'query' => $wxMsgRecord,
        ]);
        return $this->render('index', ['data' => $data,'get'=>$get,"status"=>$status]);
    }
}