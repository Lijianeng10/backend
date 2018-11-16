<?php

namespace app\modules\agents\controllers;

use yii;
use \yii\db\Query;
use yii\web\Controller;
use app\modules\admin\models\SysAdmin;
use app\modules\admin\models\SysAuth;
use app\modules\helpers\Commonfun;
use app\modules\helpers\Constant;

class LoginController extends Controller {

    /**
     * 说明: 为了域名的跳转/agents/login
     * @param
     * @return 
     */
    public function actionIndex() {
        $this->layout = false;
        return $this->render("index");
    }

    /**
     * 登录
     */
    public function actionLogin() {
        $session = Yii::$app->session;
        $this->layout = FALSE;
        if (isset($session["admin_name"]) && !empty($session["admin_name"]) && isset($session["admin_id"]) && !empty($session["admin_id"]) && isset($session["login_port"]) && $session["login_port"]=="exp") {
            return $this->redirect('/agents/login/success');
        }
        if (isset($_POST["admin_name"]) && isset($_POST["password"])) {
            $model = new SysAdmin();
            $model->admin_name = $_POST["admin_name"];
            $model->password = md5($_POST["admin_name"] . $_POST["password"]);
            if ($model->validate(["admin_name", "password"])) {
                $ret = $model->validatePassword();
                if ($ret === true) {
                    $roles = (new Query())->select("*")->from("sys_admin_role sar")->join("left join", "sys_role sr", "sar.role_id=sr.role_id")->where(["sar.admin_id" => $model->admin_id])->all();
                    if ($roles == null) {
                        return $this->render('index', ["msg" => "请输入正确的账号密码！"]);
                    }
                    $session["admin_name"] = $model->admin_name;
                    $session["admin_id"] = $model->admin_id;
                    $session["nickname"] = $model->nickname;
                    $session['agent_code'] = $model->admin_code;
                    $session['login_port'] = 'exp';
                    $session['type'] = $model->type;
                    $session['type_identity'] = $model->type_identity;
                    $authModel = new SysAuth();
                    $session["authUrls"] = $authModel->getAuthurls();
                    $format = 'y/m/d h:i:s';
                    SysAdmin::updateAll(["last_login" => date($format)], ["admin_id" => $session["admin_id"]]);
                    return $this->redirect('/agents/login/success');
                } elseif ($ret === false) {
                    return $this->render('index', ["msg" => "请输入正确的账号密码！"]);
                } else {
                    return $this->render('index', ["msg" => $ret]);
                }
            } else {
                return $this->render('index', ["msg" => "请输入正确的账号密码！"]);
            }
        } else {
            return $this->render('index');
        }
    }
    /**
     * 登录成功跳转页面
     * @return type
     */
    public function actionSuccess() {
        $session = Yii::$app->session;
        $this->layout = 'pageframe';
        $menus = Commonfun::getAuthurls();
        //需要隐藏模块
        $ret=Commonfun::getSysConf('agent_hidden_module');
        $closeMenu =explode(",",$ret["agent_hidden_module"]);
        $authIds = SysAuth::find()->select("auth_id")->where(["in","auth_url",$closeMenu])->asArray()->all();
        foreach ($authIds as $key => $value) {
            $menus = Commonfun::delCloseAuth($value["auth_id"], $menus);
        }
        $menus = Commonfun::getChildrens(0, $menus);
        
        $session["inPort"] = "exp";
        return $this->render('success', ["admin_name" => $session["nickname"], "menus" => $menus]);
    }
    /**
     * 退出
     */
    public function actionLogout() {
        $session = Yii::$app->session;
        session_unset();
        return $this->redirect('/agents/login/index');
    }

}
