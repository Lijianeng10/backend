<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\modules\admin\models\SysAdmin;
use yii\filters\VerbFilter;
use app\modules\admin\models\SysAuth;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use app\modules\admin\models\SysRole;
use app\modules\common\models\ExpertSource;

/**
 * Admin controller for the `admin` module
 */
class AdminController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public $enableCsrfValidation = false;

    public function behaviors() {
        parent::behaviors();
        return [
            "verbs" => [
                "class" => VerbFilter::className(),
                "actions" => [
                    "login" => ["get", "post"],
                    'editadmin' => ['get']
                ]
            ]
        ];
    }

    /**
     * 用户列表
     */
    public function actionIndex() {
        $session = Yii::$app->session;
        $get = Yii::$app->request->get();
        $admin_role_model = new Query();
        $adminIds = $this->getChildAdmin($session["admin_id"]);
        $admin_role_infos = $admin_role_model->select('a.admin_role_id,a.admin_id,a.role_id,r.role_name,r.role_status')
                ->from(['a' => 'sys_admin_role'])
                ->leftJoin(['r' => 'sys_role'], 'a.role_id=r.role_id')
                ->indexBy('admin_id')
                ->all();
        $adminModel = new Query();
        $allAdmins = $adminModel->select('*')
                ->from('sys_admin')
                ->where(["in", "admin_id", $adminIds]);
        if(!empty($get["admin_info"])){
            $allAdmins=$allAdmins->andWhere(["or",["like","admin_name",$get["admin_info"]],["like","nickname",$get["admin_info"]],["like","admin_tel",$get["admin_info"]]]);
        }
        $allAdmins=$allAdmins->indexBy('admin_id')
                ->all();

        foreach ($allAdmins as &$value) {
//            $allAdmins[$value['admin_id']]['role_info'][] = $value;
            $value['role_info'][] = $admin_role_infos[$value['admin_id']];
        }
        $data = new ArrayDataProvider([
            'allModels' => $allAdmins,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);
        return $this->render('index', ['data' => $data]);
    }

    /**
     * 后台登录
     */
    public function actionLogin() {
        $session = Yii::$app->session;
        $this->layout = FALSE;
        if (isset($session["admin_name"]) && !empty($session["admin_name"]) && isset($session["admin_id"]) && !empty($session["admin_id"]&& isset($session["login_port"]) && $session["login_port"]=="sys") ) {
            return $this->redirect('/');
        }

        if (isset($_POST["admin_name"]) && isset($_POST["password"])) {
            $model = new SysAdmin();
            $model->admin_name = $_POST["admin_name"];
            $model->password = md5($_POST["admin_name"].$_POST["password"]);
            if ($model->validate(["admin_name", "password"])) {
                $ret = $model->validatePassword();
                if ($ret === true) {
                    $roles = (new Query())->select("*")->from("sys_admin_role sar")->join("left join", "sys_role sr", "sar.role_id=sr.role_id")->where(["sar.admin_id" => $model->admin_id])->all();
                    if ($roles == null) {
                        return $this->render('login', ["msg" => "请输入正确的账号密码！"]);
                    }
                    $session["admin_name"] = $model->admin_name;
                    $session["admin_id"] = $model->admin_id;
                    $session["nickname"] = $model->nickname;
                    $session['agent_code'] = $model->admin_code;
                    $session['login_port'] = 'sys';
                    $session['type'] = $model->type;
                    $session['type_identity'] = $model->type_identity;
                    $authModel = new SysAuth();
                    $session["authUrls"] = $authModel->getAuthurls();
                    $format = 'y/m/d h:i:s';
                    SysAdmin::updateAll(["last_login" => date($format)], ["admin_id" => $session["admin_id"]]);
                    return $this->redirect('/');
                } elseif ($ret === false) {
                    return $this->render('login', ["msg" => "请输入正确的账号密码！"]);
                } else {
                    return $this->render('login', ["msg" => $ret]);
                }
            } else {
                return $this->render('login', ["msg" => "请输入正确的账号密码！"]);
            }
        } else {
            return $this->render('login');
        }
    }

    /**
     * 获取java组接口用户数据
     * @return string
     */
    public function curlGetinfo($account, $password) {
        $url = "http://116.255.186.117:6081/user/login";
        $post_data = array("account" => $account, "password" => $password, "checkType" => 0); //account=gl00001043&password=crz123
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output);
//        var_dump($output);
//        exit();
        $format = 'y/m/d h:i:s';
        if ($output->httpCode == "1") {
            $adminInfo = SysAdmin::findOne(["admin_name" => $account]);
            if ($adminInfo == null) {
                $model = new SysAdmin();
                $model->admin_name = $output->data["account"];
                $model->password = "NULL";
                $model->status = 1;
                $model->admin_tel = $output->data["admin_tel"];
                $model->nickname = $output->data["nickname"];
                $model->created_at = date($format);
                $model->updated_at = date($format);
                if ($model->validate(["admin_name", "status", "created_at", "updated_at", "admin_tel", "nickname"])) {
                    $id = $model->save();
                    if ($id != false) {
                        return "login";
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else if ($adminInfo["updated_at"] >= date($format, $output["data"]["updated_at"])) {
                $adminInfo->admin_name = $output->data["account"];
                $adminInfo->status = 1;
                $adminInfo->admin_tel = $output->data["admin_tel"];
                $adminInfo->nickname = $output->data["nickname"];
                $adminInfo->updated_at = date($format);
                if ($adminInfo->validate(["admin_name", "status", "updated_at", "admin_tel", "nickname"])) {
                    $id = $adminInfo->save();
                    if ($id != false) {
                        return "login";
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
            return "login";
        } else if ($output->httpCode == "412") {
            return "notFound";
        } else {
            return false;
        }
    }

    /**
     * 退出
     */
    public function actionLogout() {
        $session = Yii::$app->session;
        session_unset();
        return $this->redirect('/admin/admin/login');
    }

    /**
     * 通过id批量删除
     * @return json
     */
    public function actionDeletebyids() {
        $post = Yii::$app->request->post();
        if ($post["type"] == "admin_delete_by_ids") {
            $session = Yii::$app->session;
            $adminIds = $this->getChildAdmin($session["admin_id"]);
            foreach ($post["data"] as $val) {
                if (!in_array($val, $adminIds)) {
                    return json_encode([
                        "msg" => "无权限删除{$val}管理员！",
                        "code" => 2
                    ]);
                }
            }
            $model = new SysAdmin();
            $model->ids = $post["data"];
            $result = $model->deleteByids();
            if ($result != false) {
                return json_encode([
                    "msg" => "操作成功",
                    "code" => 1
                ]);
            } else {
                return json_encode([
                    "msg" => "操作错误",
                    "code" => 2
                ]);
            }
        } else {
            return json_encode([
                "msg" => "操作错误",
                "code" => 2
            ]);
        }
    }

    /**
     * 删除用户
     * @return json
     */
    public function actionDeletebyid() {
        if (!Yii::$app->request->isPost) {
            return $this->redirect('/admin/admin/index');
        }

        $post = Yii::$app->request->post();
        $session = Yii::$app->session;
        if($post["admin_id"]==$session["admin_id"]){
            return json_encode([
                    "msg" => "该用户不可删除！",
                    "code" => 2
                ]);
        }
        if ($post["type"] == "admin_delete_by_id") {
            $adminIds = $this->getChildAdmin($session["admin_id"]);
            if (!in_array($post["admin_id"], $adminIds)) {
                return json_encode([
                    "msg" => "无权限删除该管理员！",
                    "code" => 2
                ]);
            }
            $model = new SysAdmin();
            $model->admin_id = $post["admin_id"];
            $result = $model->deleteByid();
            if ($result != false) {
                return json_encode([
                    "msg" => "操作成功",
                    "code" => 1
                ]);
            } else {
                return json_encode([
                    "msg" => "操作错误",
                    "code" => 2
                ]);
            }
        } else {
            return json_encode([
                "msg" => "操作错误",
                "code" => 2
            ]);
        }
    }

    /**
     * 添加用户
     * @return json
     */
    public function actionAddadmin() {
        $this->layout = false;
        $session = Yii::$app->session;
        //新增用户页面
        if (!Yii::$app->request->isPost) {
            $where=[];
            if($session["admin_id"]!="1"){
                $where=["or", ["s.admin_id" => $session["admin_id"]], ["sys_role.admin_id" => $session["admin_id"]]];
            }
            $roleInfos = SysRole::find()->select("sys_role.*")->join("left join", "sys_admin_role s", "s.role_id=sys_role.role_id")->where($where)->indexBy('role_id')->asArray()->all();
            $infos = array();
            foreach ($roleInfos as $key => $value) {
                $infos[$key] = $value["role_name"];
            }
            $data = [];
            $data["role_ids"] = $infos;
            $source = ExpertSource::find()->select(['source_id', 'source_name'])->where(['status' => 1])->asArray()->all();
            $data['sourceData'][0] = '无身份';
            foreach ($source as $val) {
                $data['sourceData'][$val['source_id']] = $val['source_name'];
            }
            return $this->render("addadmin", ['model' => $data]);
        }
        //保存新增用户信息
        $format = 'y/m/d h:i:s';
        $post = Yii::$app->request->post();
        $hasAdmin = SysAdmin::findAll(["admin_name" => $post["admin_name"]]);
        if (!empty($hasAdmin)) {
            return $this->jsonResult(2, '该用户名管理员已存在');
        }
        //代理商账户只能添加代理商类型账号，不可添加内部人员类型
        if($session["type"]!=0){
            if($session['type'] == 1 && $post["type"]!=1){
                return $this->jsonResult(2, '请将用户类型选择为代理商用户'); 
            }
            if($session['type'] == 2 && $post['type'] != 2) {
                return $this->jsonResult(2, '请将用户类型选择为专家'); 
            }
        }
        if($post['type'] != 2 && isset($post['type_identity']) && !empty($post['type_identity'])) {
            return $this->jsonResult(2, '非专家用户类型, 不必选择类型身份');
        }
        $model = new SysAdmin();
        $model->admin_name = $post["admin_name"];
        $model->password = md5($post["admin_name"].$post["password"]);
        $model->status = $post["status"];
        //咕啦内部用户创建的用户上级代理code均为gl00015788，代理商创建的用户上级代理code为创建者
        if($session["type"]==0){
            $model->admin_code ='gl00015788';
        }else{
            $model->admin_code = $session["admin_name"];
        }
        $model->type = $post["type"];
        $model->admin_tel = $post["admin_tel"];
        $model->nickname = $post["nickname"];
        $model->admin_pid = $session["admin_id"];
        $model->created_at = date($format);
        $model->updated_at = date($format);
        $model->type_identity = $post['type_identity'];
        if(!isset($post['role_ids'])|| count($post['role_ids']) <= 0){
            return $this->jsonResult(2, '新增失败，未分配用户角色');
        }
        if ($model->validate(["admin_name", "password", "status", "created_at", "updated_at", "admin_tel", "nickname"])){
            $id = $model->save();
            if ($id != false) {
                if (isset($post['role_ids']) && is_array($post['role_ids']) && count($post['role_ids']) > 0) {
                    $roleInfos = SysRole::find()->select("sys_role.role_id")->join("left join", "sys_admin_role s", "s.role_id=sys_role.role_id")->where(["or", ["s.admin_id" => $session["admin_id"]], ["sys_role.admin_id" => $session["admin_id"]]])->andWhere(["in", "sys_role.role_id", $post['role_ids']])->indexBy('role_id')->asArray()->all();
                    $admin_roles = [];
                    foreach ($roleInfos as $value) {
                        $admin_roles[] = [$model->admin_id, $value["role_id"]];
                    }
                    Yii::$app->db->createCommand()->batchInsert('sys_admin_role', ['admin_id', 'role_id'], $admin_roles)->execute();
                }
                return $this->jsonResult(1, '新增成功');
            } else {
                return $this->jsonResult(2, '操作错误');
            }
        } else {
            $err = $model->getFirstErrors();
            return $this->jsonResult(2, '参数格式错误',$err);
        }
    }

    /**
     * 编辑用户
     */
    public function actionEditadmin() {
        $get = Yii::$app->request->get();
        $session = Yii::$app->session;
        $adminIds = $this->getChildAdmin($session["admin_id"]);
        if (!in_array($get["admin_id"], $adminIds)) {
            return json_encode([
                "msg" => "无权限编辑该管理员！",
                "code" => 2
            ]);
        }
        if (isset($get['admin_id']) && $get['admin_id'] != null) {
            //总账户才能看到所有角色信息，否则只能看到所属自己的角色
            $where=[];
            if($session["admin_id"]!="1"){
                $where=["or", ["s.admin_id" => $session["admin_id"]], ["sys_role.admin_id" => $session["admin_id"]]];
            }
            $model = SysAdmin::find()->where(['admin_id' => $get['admin_id']])->asArray()->one();
            $roleInfos = SysRole::find()->select("sys_role.*")->join("left join", "sys_admin_role s", "s.role_id=sys_role.role_id")->where($where)->indexBy('role_id')->asArray()->all();
            
            $model["roleInfos"] = [];
            foreach ($roleInfos as $key => $value) {
                $model["roleInfos"][$key] = $value["role_name"];
            }
            $admin_role_model = new Query();
            $adminRoleIds = $admin_role_model->select('role_id')
                    ->from('sys_admin_role')
                    ->where(['admin_id' => $get['admin_id']])
                    ->all();
            $model["adminRoleIds"] = array();

            foreach ($adminRoleIds as $value) {
                $model["adminRoleIds"][] = $value["role_id"];
            }
            $source = ExpertSource::find()->select(['source_id', 'source_name'])->where(['status' => 1])->asArray()->all();
            $model['sourceData'][0] = '无身份';
            foreach ($source as $val) {
                $model['sourceData'][$val['source_id']] = $val['source_name'];
            }
            return $this->render('editadmin', ['model' => $model]);
        } else {
            echo '操作错误';
            exit();
        }
    }

    /**
     * 修改保存用户
     * @return json
     */
    public function actionSaveadmin() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/admin/admin/index');
        }

        $session = Yii::$app->session;
        $format = 'y/m/d h:i:s';
        $post = Yii::$app->request->post();
        $adminIds = $this->getChildAdmin($session["admin_id"]);
        if (!in_array($post['admin_id'], $adminIds)) {
            return $this->jsonResult(2, '该用户不可操作');
        }
        $hasAdmin = SysAdmin::find()->where(["!=", "admin_id", $post['admin_id']])->andWhere(["admin_name" => $post["admin_name"]])->one();
        if (!empty($hasAdmin)) {
            return $this->jsonResult(2, '用户名重复，修改失败！');
        }
        if(!isset($post['role_ids'])|| count($post['role_ids']) < 0){
            return $this->jsonResult(2, '修改失败，未分配用户角色');
        }
         //代理商账户只能添加代理商类型账号，不可添加内部人员类型
        if($session["type"]==1){
            if($post["type"]!=1){
                return $this->jsonResult(2, '请将用户类型选择为代理商用户'); 
            }
        }
        if($post['type'] != 2 && isset($post['type_identity']) && !empty($post['type_identity'])) {
            return $this->jsonResult(2, '非专家用户类型, 不必选择类型身份');
        }
        $model = SysAdmin::findOne(["admin_id" => $post['admin_id']]);
        $model->admin_name = $post["admin_name"];
        if($post["password"]!="**********"){
            $model->password = md5($post["admin_name"].$post["password"]);
        }
        $model->status = $post["status"];
        $model->admin_tel = $post["admin_tel"];
        $model->nickname = $post["nickname"];
        $model->updated_at = date($format);
        $model->type = $post["type"];
        $model->type_identity = $post['type_identity'];
        if ($model->validate(["admin_name", "status", "updated_at", "admin_tel", "nickname"])) {
            $id = $model->save();
            if ($id != false) {
                if (isset($post['role_ids']) && is_array($post['role_ids']) && count($post['role_ids']) > 0) {
                    $roleInfos = SysRole::find()->select("sys_role.role_id")->join("left join", "sys_admin_role s", "s.role_id=sys_role.role_id")->where(["or", ["s.admin_id" => $session["admin_id"]], ["sys_role.admin_id" => $session["admin_id"]]])->andWhere(["in", "sys_role.role_id", $post['role_ids']])->indexBy('role_id')->asArray()->all();
                    $admin_roles = [];
                    foreach ($roleInfos as $value) {
                        $admin_roles[] = [$model->admin_id, $value["role_id"]];
                    }

                    Yii::$app->db->createCommand()->delete('sys_admin_role', ['admin_id' => $model->admin_id])->execute();
                    $ret = Yii::$app->db->createCommand()->batchInsert('sys_admin_role', ['admin_id', 'role_id'], $admin_roles)->execute();
                }
                return $this->jsonResult(1, '修改成功'); 
            } else {
                return $this->jsonResult(2, '操作错误'); 
            }
        } else {
            $err = $model->getFirstErrors();
            return $this->jsonResult(2, '参数格式错误',$err);
        }
    }

    /**
     * 用户查看
     */
    public function actionReadadmin() {
        $get = Yii::$app->request->get();
        if (isset($get['admin_id']) && $get['admin_id'] != null) {
            $session = Yii::$app->session;
            $adminIds = $this->getChildAdmin($session["admin_id"]);
            if (!in_array($get["admin_id"], $adminIds)) {
                return $this->jsonResult(2, '无权限查看该用户信息'); 
            }
            $model = SysAdmin::find()->where(['admin_id' => $get['admin_id']])->asArray()->one();
            return $this->render('readadmin', ['model' => $model]);
        } else {
            return $this->jsonResult(2, '操作错误'); 
        }
    }

    /**
     * 修改用户状态
     */
    public function actionEditstatus() {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect('/admin/admin/index');
        }
        $post = Yii::$app->request->post();
        $session = Yii::$app->session;
        $adminIds = $this->getChildAdmin($session["admin_id"]);
        if (!in_array($post["admin_id"], $adminIds)) {
            return json_encode([
                "msg" => "无权限编辑该管理员！",
                "code" => 2
            ]);
        }
        $model = SysAdmin::findOne(["admin_id" => $post['admin_id']]);
        $model->status = $model->status ? 0 : 1;
        if ($model->validate(["admin_id", "status"])) {
            $id = $model->save();
            if ($id != false) {
                return json_encode([
                    "msg" => ($model->status ? "启用" : "停用") . "成功",
                    "code" => 1
                ]);
            } else {
                return json_encode([
                    "msg" => "操作错误",
                    "code" => 2
                ]);
            }
        } else {
            $err = $model->getFirstErrors();
            return json_encode([
                "msg" => "参数格式错误",
                "code" => 2,
                "err" => $err
            ]);
        }
    }

    public function getChildAdmin($adminPid) {
        global $returnInfo;
        $returnInfo[] = $adminPid;
        $admins = SysAdmin::find()->where(["admin_pid" => $adminPid])->asArray()->all();
        if ($admins != null) {
            foreach ($admins as $val) {
                $this->getChildAdmin($val["admin_id"]);
            }
        }
        return $returnInfo;
    }

}
