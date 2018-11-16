<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Confirm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>

<ol class="am-breadcrumb">
    <li><a href="/admin/role">用户管理</a></li>
    <li class="am-active">查看用户</li>
</ol>

<?php
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '登录名',
            'value' => function ($model) {
                return $model['admin_name'];
            }
        ], [
            'label' => '昵称',
            'value' => function ($model) {
                return $model['nickname'];
            }
        ], [
            'label' => '联系电话',
            'value' => function ($model) {
                return $model['admin_tel'];
            }
        ], [
            'label' => '状态',
            'value' => function ($model) {
                return $model['status'] == 1 ? '启用' : '停用';
            }
        ], [
            'label' => '最新一次登录',
            'value' => function ($model) {
                return $model['last_login'];
            }
        ],
    ],
]);

echo ' <button type="button" class="am-btn am-btn-primary" id="reback">返回</button>';
?>

<script type="text/javascript">
    $("#reback").click(function () {
       history.go(-1);
    });
</script>