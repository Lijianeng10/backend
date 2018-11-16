<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
//var_dump($this->context->id);exit();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link href="/css/font-awesome.min.css" rel="stylesheet">
        <script src="/js/jquery.min.js"></script>
        <script src="/js/laydate/laydate.js"></script>
        <script src="/js/jquery.magnify.js"></script>
    </head>
    <body style="background: #fff;height: 100%;">
        <?php $this->beginBody() ?>
        <div id="content-body" style="overflow: auto;"> 
<!--                    <h2><span onclick="javascript:location.href = '/<?= $this->context->module->id ?>/<?= $this->context->id ?>/index'" style='color:#0e90d2;'><?= Html::encode($this->title) ?></span></h2>-->
            <button type="button" class="am-btn am-btn-primary freshFrame"  onclick="javascript:location.reload();"><i class="am-icon-refresh">刷新</i></button>     

            <?= $content ?>
        </div>
        <?php $this->endBody() ?>
        <script type="text/javascript">
            $(function () {
                if ($(".pagination").length > 0) {
                    $("#content-body").height($("body").height() - 40);
                } else {
                    $("#content-body").height($("body").height());
                }
                window.onresize = function () {
                    if ($(".pagination").length > 0) {
                        $("#content-body").height($("body").height() - 40);
                    } else {
                        $("#content-body").height($("body").height());
                    }
                }
            });
        </script>
    </body>

</html>
<?php $this->endPage() ?>
