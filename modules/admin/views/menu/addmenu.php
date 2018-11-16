<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\widgets\DetailView;
use yii\helpers\Html;

$this->title = "新增栏目";
?>
<ol class="am-breadcrumb">
    <li><a href="/admin/menu">栏目管理</a></li>
    <li class="am-active">新增栏目</li>
</ol>
<?php
echo '<form id="addmenu">';
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '上级栏目',
            'format' => 'raw',
            'value' => function($model) {
                return Html::dropDownList("menu_pid", "0", $model, ['class' => 'form-control']);
            }
                ], [
                    'label' => '栏目名',
                    'format' => 'raw',
                    'value' => function() {
                        return Html::input('text', 'menu_name', '', ['class' => 'form-control']);
                    }
                        ], [
                            'label' => '栏目URL',
                            'format' => 'raw',
                            'value' => function() {
                                return Html::input('text', 'menu_url', '', ['class' => 'form-control']);
                            }
                                ], [
                                    'label' => '操作',
                                    'format' => 'raw',
                                    'value' => function() {
                                        return Html::button('提交', ['class' => 'am-btn am-btn-primary save']);
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
                url: '/admin/menu/addmenu',
                async: false,
                type: 'POST',
                data: $('#addmenu').serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
//                        $.each(data['err'], function (key, val) {
//                            $("[name=" + key + "]").parent("tb").addClass("has-error");
//                        });
                    } else {
                        msgAlert(data['msg'], function () {
                            location.href = '/admin/menu/index';
                        });
                    }
                }
            });
        });
    });
</script>

