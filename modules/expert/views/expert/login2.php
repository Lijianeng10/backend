<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;

$this->title = '咕啦彩票云后台';
$this->params['breadcrumbs'][] = $this->title;
?>
<!doctype html>
<html lang="<?= Yii::$app->language ?>">

    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?= Html::encode($this->title) ?></title>
        <link rel="shortcut icon" type="image/png" href="/i/favicon.jpg">
        <link rel="stylesheet" href="/css/amazeui.min.css" />
        <link rel="stylesheet" href="/css/admin.css">
        <link rel="stylesheet" href="/css/app.css">
    </head>

    <body data-type="login">

        <div class="am-g myapp-login">
            <div class="myapp-login-logo-block  tpl-login-max">
                <div class="myapp-login-logo-text">
                    <div class="myapp-login-logo-text">
                        咕啦彩票专家后台<span> 登录</span> <i class="am-icon-skyatlas"></i>

                    </div>
                </div>
                <div class="am-u-sm-10 login-am-center">
                    <form class="am-form" action="/expert/expert/login" method="post">
                        <fieldset>
                            <div class="am-form-group">
                                <input type="text" name="admin_name" value="<?php echo isset($_POST["admin_name"]) ? $_POST["admin_name"] : ""; ?>" class="" placeholder="输入账号">
                            </div>
                            <div class="am-form-group">
                                <input type="password" name="password" value="<?php echo isset($_POST["password"]) ? $_POST["password"] : ""; ?>" class="" id="doc-ipt-pwd-1" placeholder="输入密码">
                            </div>
                            <div class="am-form-group">
                                <span style="color:red;"><?php if (isset($msg)) {
    echo $msg;
} ?></span>
                            </div>
                            <p><button type="submit" class="am-btn am-btn-default">登录</button></p>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>

        <script src="/js/jquery.min.js"></script>
        <script src="/js/amazeui.min.js"></script>
        <script src="/js/app.js"></script>
    </body>

</html>