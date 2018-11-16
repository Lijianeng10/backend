<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ Confirm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>

<div class="am-btn-group am-btn-group-xs operat">
    <a href="javascript:addRole();" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 新增</a>
    <button type="button" class="am-btn am-btn-default am-btn-secondary " id = "checkAll">全选</button>
    <button type="button" class="am-btn am-btn-default am-btn-danger del" id="delIds"><span class="am-icon-trash-o"></span> 删除</button>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $provider,
    'columns' => [
        [
            'label' => '',
            'format' => 'raw',
            'value' => function ($model) {
                return \yii\bootstrap\Html::input('checkbox', 'box', $model["role_id"], ["class" => 'delSel']);
            }
                ],
//            [ 'class' => 'yii\grid\SerialColumn'],
                [
                    'label' => '角色ID',
                    'value' => 'role_id'
                ],
                [
                    'label' => '角色名称 ',
                    'value' => 'role_name'
                ],
//                [
//                    'label' => '登录口 ',
//                    'value' => function($model) {
//                        $loginPorts = [
//                            "sys" => "系统后台",
//                            "exp" => "专家后台",
//                        ];
//                        return isset($loginPorts[$model["login_port"]]) ? $loginPorts[$model["login_port"]] : "未知登录口";
//                    }
//                        ],
                        [
                            'label' => '状态',
                            'value' => function ($model) {
                                return $model->role_status == 1 ? '启用' : '停用';
                            }
                        ],[
                            'label' => '所属用户ID ',
                            'value' => 'admin_id'
                        ],
                        [
                            'label' => '添加时间',
                            'value' => 'role_create_at'
                        ],
                        [
                            'label' => '修改时间',
                            'value' => 'role_update_at'
                        ],
                        [
                            'label' => '操作',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $sta = $model->role_status == 1 ? '停用' : '启用';
                                return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <a href="/admin/role/access?role_id=' . $model->role_id . '" class="handle pointer">权限配置</a>
                                <a href="/admin/role/adminrole?role_id=' . $model->role_id . '" class="handle pointer">| 用户查看</a>
                                <span class="handle pointer" onclick="doSta(' . $model->role_id . ',' . $model->role_status . ');">| ' . $sta . '</span>
                                <span class="handle pointer" onclick="delRole(' . $model->role_id . ');">| 删除</span>
                            </div>
                        </div>';
                            }
                        ]
                    ],
                ]);
                $this->title = 'Role';
                ?>

<script>
    $(function () {
        $('#checkAll').click(function () {
            $.each($(".delSel"), function () {
                if ($(this).is(':checked')) {
                    $(this).prop("checked", false);
                } else {
                    $(this).prop("checked", true);
                }
            })   
        });

        $('#page').change(function () {
            var pagesize = $(this).val();
            if ((!$.isNumeric(pagesize)) || pagesize > 100 || pagesize < 0) {
                msgAlert("输入信息必须为数字且必须大于0小于100");
            }
        })
    });

    function delRole(ids) {
        msgConfirm('提醒', '确定要删除此角色吗？', function () {
            $.ajax({
                url: "/admin/role/delbyid",
                type: "POST",
                async: false,
                data: {type: "role_delete_by_id", ids: ids},
                dataType: "json",
                success: function (data) {
                    console.log(1111);
                    if (data["code"] != "1") {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert("删除成功");
                        location.reload();
                    }
                }
            });
        })
    }

    $(function () {
        $('#delIds').click(function () {
            var ids = [];
            $.each($(".delSel"), function () {
                if ($(this).is(':checked')) {
                    ids.push($(this).val());
                }
            });
            delRole(ids);
        })
    })

    function doSta(id, sta) {
        msgConfirm('提醒', '确定要修改此角色的状态吗？', function () {
            if (sta == 1) {
                sta = 0;
            } else {
                sta = 1;
            }
            //console.log(sta);
            $.ajax({
                url: "/admin/role/editsta",
                type: "POST",
                async: false,
                data: {type: "role_edit_by_id", id: id, status: sta},
                dataType: "json",
                success: function (data) {
                    //console.log(1111);
                    if (data["code"] != "1") {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert("修改成功");
                        location.reload();
                    }
                }
            });
        })
    }

    function addRole() {
        modDisplay({url: '/admin/role/addrole', title: "新增角色", height: 220, width: 400});
    }

</script>