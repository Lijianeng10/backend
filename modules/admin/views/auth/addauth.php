<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\bootstrap\Alert;

$this->title = "新增权限";
if (isset($msg)) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-success',
        ],
        'body' => $msg
    ]);
}
echo '<form id="addauth">';
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '上级权限',
            'format' => 'raw',
            'value' => function($model) {
                return Html::dropDownList("auth_pid", "0", $model, ['class' => 'form-control']) ;
            }
                ], [
                    'label' => '权限名称<span class="requiredIcon">*</span>',
                    'format' => 'raw',
                    'value' => function() {
                        return Html::input('text', 'auth_name', '', ['class' => 'form-control', 'placeholder'=>"必须填"]);
                    }
                        ], [
                            'label' => '权限对应路径',
                            'format' => 'raw',
                            'value' => function() {
                                return Html::input('text', 'auth_url', '', ['class' => 'form-control','placeholder'=>"必须填"]);
                            }
                                ], [
                                    'label' => '权限状态',
                                    'format' => 'raw',
                                    'value' => function() {
                                        return Html::radioList('auth_status', '1', ['1' => '开启', '0' => '关闭']);
                                    }
                                        ], [
                                            'label' => '操作',
                                            'format' => 'raw',
                                            'value' => function() {
                                                return Html::button('提交', ['class' => 'am-btn am-btn-primary save']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class'=>'am-btn am-btn-primary', 'id'=>'reback']);
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
                url: '/admin/auth/addauth',
                async: false,
                type: 'POST',
                data: $('#addauth').serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
//                            location.href = '/admin/auth/index';
                        });
                    }
                }
            });
        });
        
        $("#reback").click(function () {
            closeMask();
//            location.href = '/admin/auth/index';
        });
    });
</script>