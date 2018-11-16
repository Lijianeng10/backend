<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\widgets\DetailView;

$this->title = '新增角色';
?>

<!--<ol class="am-breadcrumb">
    <li><a href="/admin/role">角色管理</a></li>
    <li class="am-active">新增用户</li>
</ol>-->

<?php
echo '<form id="addrole">';
echo DetailView::widget([
    'model' => [],
    'attributes' => [
        [
            'label' => '角色名称<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'role_name', '', ['class' => 'form-control', 'id' => 'rname']);
            }
                ], 
//                        [
//                    'label' => '登录口<span class="requiredIcon">*</span>',
//                    'format' => 'raw',
//                    'value' => function() {
//                        $loginPorts = [
//                            "sys" => "系统后台",
//                            "exp" => "专家后台",
//                        ];
//                        return Html::dropDownList('login_port', 'sys', $loginPorts, ["class" => "form-control", "style" => "width:100px;"]);
//                    }
//                        ], 
                                [
                            'label' => '',
                            'format' => 'raw',
                            'value' => function() {

                                return Html::button('提交', ['class' => 'am-btn am-btn-primary', 'id' => 'addSubmit']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class' => 'am-btn am-btn-primary', 'id' => 'reback']);
                            }
                                ]
                            ]
                        ]);
                        echo '</form>';
                        ?>

<script type="text/javascript">
    $(function () {
        $('#addSubmit').click(function () {
            $.ajax({
                url: '/admin/role/addrole',
                async: false,
                type: 'POST',
                data: $('#addrole').serialize(),
                dataType: 'json',
                success: function (data) {
                    if (1 != data['code']) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
//                            location.href = '/admin/role/index';
                        });
                    }
                }
            });
        });

        $("#reback").click(function () {
            closeMask();
//            location.href = '/admin/role/index';
        });
    });
</script>
