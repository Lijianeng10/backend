<?php

namespace app\modules\core\filters;

use yii\base\ActionFilter;

// use app\modules\core\model\User;

class LoginFilter extends ActionFilter {

    public function beforeAction($action) {
//         $url = \Yii::$app->request->pathInfo;
        $session = \Yii::$app->session;
        if (empty($session["admin_name"]) || empty($session["admin_id"]) || empty($session["login_port"])) {
            //登录页面
//            echo 'wo yao qu login ';die;
            if (empty($session["login_port"])) {
                if ($session["inPort"] == "exp") {
                    \Yii::$app->response->redirect('/agents/login/index');
                } elseif ($session["inPort"] == "sys") {
                    \Yii::$app->response->redirect('/admin/admin/login');
                } else {
                    \Yii::$app->response->redirect('/admin/admin/login');
                }
            } else {
                if ($session["login_port"] == "exp") {
                    \Yii::$app->response->redirect('/agents/login/index');
                } elseif ($session["login_port"] == "sys") {
                    \Yii::$app->response->redirect('/admin/admin/login');
                } else {
                    \Yii::$app->response->redirect('/admin/admin/login');
                }
            }
        }
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result) {
        return parent::afterAction($action, $result);
    }

}

?>