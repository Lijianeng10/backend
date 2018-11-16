<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\widgets\DetailView;
use yii\helpers\Html;

$this->title = "编辑权限";
?>
<ol class="am-breadcrumb">
    <li><a href="/admin/auth">权限管理</a></li>
    <li class="am-active">编辑权限</li>
</ol>
<?php
echo '<form id="saveauth">';
echo Html::input('hidden', 'auth_id', $model['auth_id']);

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '上级权限',
            'format' => 'raw',
            'value' => function($model) {
                return Html::dropDownList("auth_pid", $model["auth_pid"], $model["tree"], ['class' => 'form-control']);
            }
                ],
                [
                    'label' => '权限名称<span class="requiredIcon">*</span>',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input('text', 'auth_name', $model['auth_name'], ['class' => 'form-control','placeholder'=>"必须填"]);
                    }
                        ], [
                            'label' => '权限对应路径',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input('text', 'auth_url', $model['auth_url'], ['class' => 'form-control','placeholder'=>"必须填"]);
                            }
                                ], [
                                    'label' => '权限状态',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::radioList('auth_status', $model['auth_status'], ['1' => '开启', '0' => '关闭']);
                                    }
                                        ], [
                                            'label' => '操作',
                                            'format' => 'raw',
                                            'value' => function() {
                                                return Html::button('提交', ['class' => 'am-btn am-btn-primary save']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class'=>'am-btn am-btn-primary', 'id'=>'reback']);;
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
                url: '/admin/auth/saveauth',
                async: false,
                type: 'POST',
                data: $('#saveauth').serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            history.go(-1);
                        });
                    }
                }
            });
        });
        
        $("#reback").click(function () {
            location.href = '/admin/auth/index';
        });
    });
</script>