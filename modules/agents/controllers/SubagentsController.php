<?php

namespace app\modules\agents\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\tools\helpers\Uploadfile;
use app\modules\tools\helpers\Toolfun;
use app\modules\agents\models\Agents;
use app\modules\agents\models\AgentsIp;
use app\modules\agents\services\IAgentsService;

class SubagentsController extends \yii\web\Controller {

    private $agentsService;

    public function __construct($id, $module, $config = [], IAgentsService $agentsService) {
        $this->agentsService = $agentsService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 下级代理商信息列表
     * @return 
     */
    public function actionIndex() {
        $this->enableCsrfValidation = false;
        $get = \Yii::$app->request->get();
        $query = (new Query())->select("agents_id,agents_appid,secret_key,agents_account,agents_name,agents_code,upagents_code,upagents_name,to_url,agents_type,check_time,pass_status,use_status")
                ->from("agents");
        if (isset($get["agents_account"]) && !empty($get["agents_account"])) {
            $query = $query->andWhere("(agents_account like '%{$get['agents_account']}%')");
        }
        if (isset($get["agents_name"]) && !empty($get["agents_name"])) {
            $query = $query->andWhere(["agents_name" => $get["agents_name"]]);
        }
        if (isset($get["agents_type"]) && !empty($get["agents_type"])) {
            $query = $query->andWhere(["agents_type" => $get["agents_type"]]);
        }
        if (isset($get["upagents_info"]) && !empty($get["upagents_info"])) {
            $query = $query->andWhere("(upagents_code like '%{$get['upagents_info']}%' or upagents_name like '%{$get['upagents_info']}%')");
        }
        if (isset($get["startdate"]) && !empty($get["startdate"])) {
            $query = $query->andWhere([">", "check_time", $get["startdate"] . " 00:00:00"]);
        }
        if (isset($get["enddate"]) && !empty($get["enddate"])) {
            $query = $query->andWhere(["<", "check_time", $get["enddate"] . " 23:59:59"]);
        }
        if (isset($get["pass_status"]) && !empty($get["pass_status"])) {
            $query = $query->andWhere(["pass_status" => $get["pass_status"]]);
        }
        $query = $query->orderBy("create_time desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $this->render("index", ["data" => $data]);
    }
    /**
     * 增加下级代理商
     */
    public function actionAddagents() {
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $agentsName = trim($post["agentsName"]);
            $agentsType = (int)$post["agentsType"];
            $agentsCode = $post["agentsCode"];
            $toUrl = $post["toUrl"];
            $remark = $post["remark"];
            if (isset($agentsName) && !empty($agentsName)){
                $query = Agents::find()->where(["agents_name" => $agentsName])->orWhere(["agents_code"=>$agentsCode])->one();
                if ($query != null) {
                    return $this->jsonResult(2, "代理商名字或者简码重复，请重新填写");
                } else {
                    $agents = new Agents();
                    $agents->agents_appid = $this->getAppid();
                    $info = Agents::find()->where(["agents_appid" => $agents->agents_appid])->one();
                    if ($info != null) {
                        $agents->agents_appid = $this->getAppid();
                    }
                    $agents->secret_key = $this->getSecret();
                    $infokey = Agents::find()->where(["secret_key" => $agents->secret_key])->one();
                    if ($infokey != null) {
                        $agents->secret_key = $this->getSecret();
                    }
                    $agents->agents_name = $agentsName;
                    $agents->agents_type = $agentsType;
                    $agents->upagents_code = "gl00015788";
                    $agents->upagents_name = "咕啦体育";
                    $agents->create_time = date('Y-m-d H:i:s');
                    $agents->opt_id = \Yii::$app->session["admin_id"];
                    $javaAgentsAccount = $this->agentsService->getJavaAgentsAccount($agents->upagents_code);
                    if (!empty($javaAgentsAccount)) {
                        $agents->agents_account = $javaAgentsAccount["data"];
                    } else {
                        return $this->jsonResult(2, '代理商新增失败,JAVA接口调用返回失败');
                    }
                    if (!empty($agentsCode)) {
                        $agents->agents_code = $agentsCode;
                    }
                    if (!empty($toUrl)) {
                        $agents->to_url = $toUrl;
                    }
                    if (!empty($remark)) {
                        $agents->agents_remark = $remark;
                    }
                    $res = $agents->save();
                    if ($res) {
                        return $this->jsonResult(600, "代理商新增成功");
                    } else {
                       return $this->jsonResult(2, "代理商新增失败");
                    }
                }
            }
        }elseif(\Yii::$app->request->isGet) {
           return $this->render("addsubagents");
        }
    }

    /**
     * 下级代理商审核页面
     */
    public function actionReviewAgents() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            if($post["pass_status"]==3){
                $res=Agents::find()->where(["agents_id" => $post["agents_id"]])->one();
                if(!empty($res->agents_code)&&!empty($res->to_url)){
                    $agents = Agents::updateAll(["pass_status" => $post["pass_status"], "review_remark" => $post["review_remark"], "opt_id" => \Yii::$app->session["admin_id"], "check_time" => date('Y-m-d H:i:s')], ["agents_id" => $post["agents_id"]]);
                    if ($agents) {
                        return $this->jsonResult(600, "操作成功,该代理商通过审核");
                    } else {
                        return $this->jsonResult(2, "操作失败");
                    }
                }else{
                    return $this->jsonResult(2, "操作失败,代理商code或跳转链接为空");
                }
            }else{
                $agents = Agents::updateAll(["pass_status" => $post["pass_status"], "review_remark" => $post["review_remark"], "opt_id" => \Yii::$app->session["admin_id"], "check_time" => date('Y-m-d H:i:s')], ["agents_id" => $post["agents_id"]]);
                if ($agents){
                    return $this->jsonResult(600, "操作成功,该代理商未通过审核");
                } else {
                    return $this->jsonResult(2, "操作失败");
                }
            }
           
        } else {
            $get = \Yii::$app->request->get();
            $Agents = Agents::findOne(["agents_id" => $get["agents_id"]]);
            return $this->render("review-agents", ["data" => $Agents]);
        }
    }

    /**
     * 代理商分配IP地址
     */
    public function actionAddAgentsIp() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $ipAddress = trim($post["ipAddress"]);
            $query = AgentsIp::find()->where(["ip_address" => $ipAddress, "agents_id" => $post["agents_id"]])->one();
            if (!empty($query)) {
                return $this->jsonResult(2, "代理商IP地址重复，请重新填写");
            }
            $agentsIp = new AgentsIp();
            $agentsIp->agents_id = intval($post["agents_id"]);
            $agentsIp->ip_address = $ipAddress;
            $res = $agentsIp->save();
            if ($res) {
                return $this->jsonResult(600, "添加成功");
            } else {
                return $this->jsonResult(2, "添加失败");
            }
        } else {
            $get = \Yii::$app->request->get();
            $Agents = Agents::findOne(["agents_id" => $get["agents_id"]]);
            return $this->render("addAgentsIp", ["data" => $Agents]);
        }
    }

    /**
     * 查看代理商信息
     * @return
     */
    public function actionReadagents() {
//        $this->layout = false;
        $get = \Yii::$app->request->get();
        $agents = Agents::find()->where(["agents_id" => $get["agents_id"]])->one();
        $query = (new Query())->select("agents_ip.agents_ip_id,agents_ip.agents_id,agents_ip.ip_address,agents_ip.status,agents.agents_name")
                ->from("agents_ip")
                ->join("left join", "agents", "agents.agents_id=agents_ip.agents_id")
                ->where(["agents_ip.agents_id" => $get["agents_id"]]);
        $AgentsIp = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' =>20,
            ]
        ]);
        return $this->render("readagents", ["data" => $agents, "ipInfo" => $AgentsIp]);
    }

    /**
     * 编辑代理商信息
     * @return
     */
    public function actionEditinfo() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        $agents = Agents::find()->where(["agents_id" => $get["agents_id"]])->one();
        return $this->render("editInfo", ["data" => $agents]);
    }

    /**
     * 修改代理商信息
     */
    public function actionEditAgentsInfo() {
        $post = \Yii::$app->request->post();
        $agentsName = trim($post["agents_name"]);
        $agentsCode = $post["agents_code"];
        $query = Agents::find()->where(["agents_name" => $agentsName])->andWhere(["<>","agents_id",$post["agents_id"]])->one();
        if ($query != null){
            return $this->jsonResult(2, "代理商名字重复，请重新填写");
        }
        if(!empty($agentsCode)){
            $info = Agents::find()->andWhere(["agents_code"=>$agentsCode])->andWhere(["<>","agents_id",$post["agents_id"]])->one();
            if(!empty($info)){
                return $this->jsonResult(2, "代理商简码重复，请重新填写");
            }
        }
        $Agents = Agents::updateAll(["agents_name" => $agentsName,"upagents_name" =>$post["upagents_name"],"upagents_code" =>$post["upagents_code"],"agents_type" =>$post["agents_type"],"agents_remark" =>$post["agents_remark"],"agents_code" =>$post["agents_code"],"to_url" =>$post["to_url"]], ["agents_id" => $post["agents_id"]]);
        if ($Agents){
             return $this->jsonResult(600, "修改成功");
         } else {
            return $this->jsonResult(2, "无需更新，修改失败");
         }
    }

    /**
     * 代理商IP地址启用禁用
     */
    public function actionEditIpSta() {
        $post = \Yii::$app->request->post();
        $AgentsIp = AgentsIp::updateAll(["status" => $post["sta"],], ["agents_ip_id" => $post["agents_ip_id"]]);
        if ($AgentsIp){
            return $this->jsonResult(600, "修改成功");
        } else {
            return $this->jsonResult(2, "修改失败");
        }
    }

    /**
     * 代理商启用禁用
     */
    public function actionEdituse() {
        $post = \Yii::$app->request->post();
        $agents = Agents::updateAll(["use_status" => $post["sta"],], ["agents_id" => $post["agents_id"]]);
        if ($agents) {
            return $this->jsonResult(600, "修改成功");
        } else {
            return $this->jsonResult(2, "修改失败");
        }
    }

    /**
     * 生成16位APPID
     */
    public function getAppid() {
        $str = md5(uniqid(md5(microtime(true)), true));
        $appid = "GL" . substr($str, 0, 14);
        return $appid;
    }

    /**
     * 生成32位唯一秘钥
     */
    public function getSecret() {
        $str = md5(uniqid(md5(microtime(true)), true));
        return $str;
    }

}
