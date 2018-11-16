<?php

namespace app\modules\core\filters;

use Yii;
use yii\base\ActionFilter;

/**
 * 权限过滤器
 */
class AuthFilter extends ActionFilter{
    
    public $children = [];

    public function beforeAction($action) {
        $module_id = $action->controller->module->id;
        $controller_id = $action->controller->id;
        $action_id = $action->id;
        $authUrls = [];
        $url = '/' . $module_id . '/' . $controller_id . '/' . $action_id;
        if (isset($this->children[$url])) {
            $authUrls=$this->children[$url];
        }
        $authUrls[] = $url;
        $session = Yii::$app->session;
        if (!is_array($session["authUrls"]) || count($session["authUrls"]) <= 0 || !$this->hasInArray($authUrls, $session["authUrls"])) {
            if (Yii::$app->request->isAjax) {
                if(Yii::$app->request->isPost){
                    echo json_encode([
                        "code" => 109,
                        "msg" => "无该权限，请联系管理员",
                    ]); 
                    exit();
                }else{
                   echo '<p style="text-align:center;margin-top:20px;">无该权限，请联系管理员</p>';
                   exit();
                }
            }else{
                echo '<script type="text/javascript">alert("无该权限，请联系管理员");window.history.go(-1);</script>';
                exit();
            }
        }
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result) {
        return parent::afterAction($action, $result);
    }

    public function hasInArray($authUrls, $auths) {
        foreach ($authUrls as $val) {
            $vs = explode('/', trim($val,'/'));
            if (count($vs) == 2 || (count($vs) == 3 && $vs[2] == "index")) {
                if (in_array(['auth_url' => ('/' . $vs[0] . '/' . $vs[1])], $auths)) {
                    return true;
            }
                if (in_array(['auth_url' => ('/' . $vs[0] . '/' . $vs[1] . '/')], $auths)) {
                    return true;
                }
                if (in_array(['auth_url' => ('/' . $vs[0] . '/' . $vs[1] . '/index')], $auths)) {
                    return true;
                }
            } else {
                if (in_array(['auth_url' => $val], $auths)) {
                    return true;
                }
            }
        }
        return false;
    }

}

?>