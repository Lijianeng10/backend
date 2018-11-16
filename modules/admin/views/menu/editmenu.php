<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\widgets\DetailView;
use yii\helpers\Html;

$this->title = "编辑栏目";
?>
<ol class="am-breadcrumb">
    <li><a href="/admin/menu">栏目管理</a></li>
    <li class="am-active">编辑栏目</li>
</ol>
<?php
echo '<form id="savemenu" style="width:600px;">';
echo Html::input('hidden', 'menu_id', $model['menu_id']);

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '上级权限',
            'format' => 'raw',
            'value' => function($model) {
                return Html::dropDownList("menu_pid", $model["menu_pid"], $model["topMenunames"], ['class' => 'form-control']);
            }
                ],
                [
                    'label' => '权限名称',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input('text', 'menu_name', $model['menu_name'], ['class' => 'form-control']);
                    }
                        ], [
                            'label' => '权限对应路径',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input('text', 'menu_url', $model['menu_url'], ['class' => 'form-control']);
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
                url: '/admin/menu/savemenu',
                async: false,
                type: 'POST',
                data: $('#savemenu').serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
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