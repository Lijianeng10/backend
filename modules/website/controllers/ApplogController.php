<?php

namespace app\modules\website\controllers;

use yii;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\common\models\JpushRecord;
use app\modules\common\helpers\Jpush;


/**
 * Default controller for the `index` module
 */
class ApplogController extends Controller{
    /**
     * Renders the index view for the module
     * @return string
     */
//    public $enableCsrfValidation = false;

    public function actionIndex(){
        $get= \Yii::$app->request->get();
        $status=[
            "0"=>"请选择",
            "1"=>"审核中",
            "2"=>"已通过",
            "3"=>"未通过",
            "4"=>"已发送",
        ];
        $jpushRecord = (new Query())->select("j.*,s.admin_name")
                ->from(['j' => 'jpush_record']);
        if(!empty($get["user_info"])){
            $jpushRecord=$jpushRecord->where(["or",["s.admin_name"=>$get["user_info"]]]);
        }
        if(!empty($get["status"])){
            $jpushRecord=$jpushRecord->where(["j.status"=>$get["status"]]);
        }
        if (!empty($get["startdate"])) {
            $jpushRecord = $jpushRecord->andWhere([">", "j.create_time", $get["startdate"] . " 00:00:00"]);
        }
        if (!empty($get["enddate"])) {  
            $jpushRecord = $jpushRecord->andWhere(["<", "j.create_time", $get["enddate"] . " 23:59:59"]);
        }
        $jpushRecord=$jpushRecord->leftJoin(['s' => 'sys_admin'], 'j.opt_id=s.admin_id')
                ->orderBy('j.create_time desc');
        $data = new ActiveDataProvider([
            'query' => $jpushRecord,
        ]);
        return $this->render('index', ['data' => $data,'get'=>$get,'status'=>$status]);
    }
    
    /**
     * 新增app推送消息
     */
    public function actionAddAppLog(){
        $this->layout=false;
       if (Yii::$app->request->isGet) {
          return $this->render("addlog");
        } elseif (Yii::$app->request->isAjax) {
            $post=Yii::$app->request->post();
            $session = Yii::$app->session;
            $title =$post["title"];
            $url =$post["url"];
            $msg =$post["msg"];
            $pushtime =$post["pushtime"];
            $nowdate = date("Y-m-d H:i:s");
            if(empty($msg)){
                return $this->jsonResult(109, '推送内容不得为空，请检查重新提交');
            }
            if(!empty($pushtime)){
                if(strtotime($pushtime)<=strtotime($nowdate)){
                    return $this->jsonResult(109, '推送时间小于等于当前时间，请重新设置');
                }
             
            }

            //添加数据
            $jpushRecord = new JpushRecord();
            $jpushRecord->titile = $title;
            $jpushRecord->jump_url = $url;
            $jpushRecord->msg = $msg;
            $jpushRecord->push_time = $pushtime;
            $jpushRecord->opt_id =$session["admin_id"];
            $jpushRecord->create_time = date("Y-m-d H:i:s");
            if($jpushRecord->validate()){
                $res = $jpushRecord->save();
                if ($res == false) {
                    return $this->jsonResult(109, "推送记录新增失败");
                }else{
                    return $this->jsonResult(600, "推送记录新增成功"); 
                }
            }else{
                print_r($jpushRecord->getFirstErrors());
                return $this->jsonResult(109, "APP推送表单验证失败");
            }
            
            
        }  
    }
    /**
     * app消息推送商审核页面
     */
    public function actionAuditAppLog() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $res=JpushRecord::find()->where(["jpush_notice_id" => $post["jpush_notice_id"]])->one();
            if(empty($res)){
                return $this->jsonResult(109, "操作失败，未找到该条数据！");
             }
            if($post["pass_status"]==3){
                if(empty($post["review_remark"])){
                    return $this->jsonResult(109, "未通过审核备注不得为空！"); 
                }
            }
            $result = JpushRecord::updateAll(["status" => $post["pass_status"], "remark" => $post["review_remark"], "remark_name" => \Yii::$app->session["admin_name"]], ["jpush_notice_id" => $post["jpush_notice_id"]]);
            if ($result) {
                return $this->jsonResult(600, "操作成功！");
            } else {
                return $this->jsonResult(109, "操作失败！");
            }
        } else {
            $get = \Yii::$app->request->get();
            $result = JpushRecord::findOne(["jpush_notice_id" => $get["jpush_notice_id"]]);
            return $this->render("audit-log", ["data" => $result]);
        }
    }
    /**
     * 发送推送消息
     */
    public function actionSendPush(){
        $post=Yii::$app->request->post();
        $session = Yii::$app->session; 
        $jpush = new Jpush();
        $jpush_notice_id=$post["jpush_notice_id"]; 
        $nowdate = date("Y-m-d H:i:s");
        if(empty($jpush_notice_id)){
            return $this->jsonResult(109, "参数缺失！");  
        }
        $res=JpushRecord::find()->where(["jpush_notice_id" => $jpush_notice_id])->asArray()->one();
        if(!empty($res["push_time"])){
            if(strtotime($pushtime)<=strtotime($nowdate)){
                return $this->jsonResult(109, '推送时间小于当前时间，推送已过期');
            }
           //定时推送
            $result = $jpush->AppJpushTimeNotice($res["titile"],$res["msg"],$res["jump_url"],$res["push_time"]);  
        }else{
           //立即推送
           $result = $jpush->AddJpushNotice($res["titile"],$res["msg"],$res["jump_url"]); 
        }
        $push = JpushRecord::updateAll(["status" => 4,"send_name" => $session["admin_name"],"response"=>json_encode($result)], ["jpush_notice_id" => $post["jpush_notice_id"]]);
         if ($push){
            return $this->jsonResult(600, "操作成功！");
        } else {
            return $this->jsonResult(109, "操作失败！");
        } 
    }
    /**
     * 删除推送消息
     */
    public function actionDeletePush(){
       $post=Yii::$app->request->post();
       if(empty($post["jpush_notice_id"])){
          return $this->jsonResult(109, "参数缺失！");  
       }
       $push = JpushRecord::deleteAll(["jpush_notice_id" => $post["jpush_notice_id"]]);
         if ($push){
            return $this->jsonResult(600, "操作成功！");
        } else {
            return $this->jsonResult(109, "操作失败！");
        } 
    }
}
