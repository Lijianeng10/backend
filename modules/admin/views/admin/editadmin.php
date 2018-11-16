<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\bootstrap\Alert;

$this->title = "编辑用户";
?>
<ol class="am-breadcrumb">
    <li><a href="/admin/admin">用户管理</a></li>
    <li class="am-active">编辑用户</li>
</ol>
<?php
if (isset($msg)) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-success',
        ],
        'body' => $msg
    ]);
}
//action="/admin/admin/saveadmin"  method="post"
echo '<form id="saveadmin">';
echo Html::input('hidden', 'admin_id', $model['admin_id']);

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '账户名称<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'admin_name', $model['admin_name'], ['class' => 'form-control', 'placeholder' => "只能含有英文、数字；最少6个字符","readonly"=>"readonly"]);
            }
                ], [
                    'label' => '用户昵称',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input('text', 'nickname', $model['nickname'], ['class' => 'form-control', 'placeholder' => "只能含有英文、数字；最少6个字符"]);
                    }
                        ], [
                            'label' => '用户密码<span class="requiredIcon"> </span>',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input('text', 'password', '**********', ['class' => 'form-control', 'placeholder' => "只能含有英文、数字；最少6个字符"]);
                            }
                                ], [
                                    'label' => '手机号码',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::input('text', 'admin_tel', $model['admin_tel'], ['class' => 'form-control', 'placeholder' => "只能是数字"]);
                                    }
                                        ], [
                                                    'label' => '用户状态',
                                                    'format' => 'raw',
                                                    'value' => function($model) {
                                                        return Html::radioList('status', $model['status'], ['1' => '开启', '0' => '关闭']);
                                                    }
                                                        ], [
                                                            'label' => '用户角色',
                                                            'format' => 'raw',
                                                            'value' => function($model) {
                                                                return Html::checkboxList('role_ids', $model["adminRoleIds"], $model['roleInfos']);
                                                            }
                                                        ], [
                                                            'label' => '用户类型',
                                                            'format' => 'raw',
                                                            'value' => function($model) {
                                                                return Html::radioList('type', $model['type'], ['0' => '内部用户', '1' => '合作商户', '2' => '专家']);
                                                            }
                                                                ],[
                                                    'label' => '类型身份',
                                                    'format' => 'raw',
                                                    'value' => function($model) {
                                                            if(\Yii::$app->session['type'] == 0) {
                                                                return Html::radioList('type_identity', $model['type_identity'], $model['sourceData']);
                                                            } else {
                                                                return Html::radioList('type_identity', '0', ['0' => '无身份']);
                                                            }
                                                    }
                                                        ], [
                                                                    'label' => '操作',
                                                                    'format' => 'raw',
                                                                    'value' => function() {
                                                                        return Html::button('提交', ['class' => 'am-btn am-btn-primary save']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class' => 'am-btn am-btn-primary', 'id' => 'reback']);
                                                                    }
                                                                        ]
                                                                    ]
                                                                ]);
                                                                echo '</form>';
                                                                ?>
<script type="text/javascript">
    $(function () {
        $('.save').click(function () {
            $.ajax({
                url: '/admin/admin/saveadmin',
                async: false,
                type: 'POST',
                data: $('#saveadmin').serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
                        $.each(data['err'], function (key, val) {
                            $("input[name=" + key + "]").css("color", "red").val(val)
                        });
                    } else {
                        msgAlert(data['msg'], function () {
                            location.href = '/admin/admin/index';
                        });
                    }
                }
            });
        });
    });
    $(function () {
        $("#reback").click(function () {
            location.href = '/admin/admin/index';
        });
    })
</script>

