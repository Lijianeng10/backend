<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = "用户管理";
echo '<form action="/admin/admin/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("用户信息", "", ["style" => "margin-left:30px;"]);
echo Html::input("input", "admin_info", isset($_GET["admin_info"]) ? $_GET["admin_info"] : "", ["class" => "form-control", "placeholder" => "用户名、昵称、电话号码", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:21px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;margin-right:5px;", "onclick" => "location.href = '/admin/admin/index'"]);
?>
<a href="javascript:addAdmin();" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 新增</a>
    <button id="allSelected" type="button" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-star"></span> 全选/反选</button>
    <button id="deleteSelected" type="button" class="am-btn am-btn-default am-btn-danger"><span class="am-icon-trash-o"></span> 删除</button>
<?php
echo '</li>';
echo "</ul>";
echo '</form>';
?>
<!--<div class="am-btn-group am-btn-group-xs operat">
    <a href="javascript:addAdmin();" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 新增</a>
    <button id="allSelected" type="button" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-star"></span> 全选/反选</button>
    <button id="deleteSelected" type="button" class="am-btn am-btn-default am-btn-danger"><span class="am-icon-trash-o"></span> 删除</button>
</div>-->
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [
            'label' => '',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('checkbox', 'delSelect', $model["admin_id"], ["class" => 'delSelect']);
            }
                ], 
//                [ 'class' => 'yii\grid\SerialColumn'],
                [
                    'label' => '用户ID',
                    'value' => 'admin_id'
                ], [
                    'label' => '用户名',
                    'value' => 'admin_name'
                ], [
                    'label' => '昵称',
                    'value' => 'nickname'
                ], [
                    'label' => '电话号码',
                    'value' => 'admin_tel'
                ], [
                    'label' => '状态',
                    'value' => function($model) {
                        return ($model['status'] == 1) ? '启用' : '停用';
                    }
                ], [
                    'label' => '角色',
                    'format' => 'raw',
                    'value' => function($model) {
                        $html = '<ul style="display: inline-block;">';
                        if (isset($model['role_info']) && is_array($model['role_info'])) {
                            foreach ($model['role_info'] as $value) {
                                $html.='<li style="display:inline;margin-right:10px;">' . $value['role_name'] . '(' . ($value['role_status'] == 1 ? '启用' : '停用') . ')</li>';
                            }
                        }
                        $html.='</ul>';
                        return $html;
                    }
                ],  [
                    'label' => '所属用户ID',
                    'value' => 'admin_pid'
                ],[
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => function($model) {
                        return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="readAdmin(' . $model["admin_id"] . ');">查看用户</span>
                            <span class="handle pointer" onclick="editAdmin(' . $model["admin_id"] . ');">| 编辑</span>
                            <span class="handle pointer" onclick="statusAdmin(' . $model["admin_id"] . ');">| '.($model["status"]?"停用":"启用").'</span>
                            <span class="handle pointer" onclick="deleteAdmin(' . $model["admin_id"] . ');">| 删除</span>
                        </div>';
                    }
                ]
            ]
        ]);
        ?>

<script type="text/javascript">
    $(function () {
        $("#allSelected").click(function () {
            $.each($(".delSelect"), function () {
                if ($(this).is(':checked')) {
                    $(this).prop("checked", false);
                } else {
                    $(this).prop("checked", true);
                }
            });
        });
        $("#deleteSelected").click(function () {
            var ids = [];
            var key = 0;
            $.each($(".delSelect"), function () {
                if ($(this).is(':checked')) {
                    ids["" + key] = $(this).val();
                    key++;
                }
            });
            msgConfirm('提示', '确定删除被选中项', function () {
                $.ajax({
                    url: "/admin/admin/deletebyids",
                    type: "POST",
                    async: false,
                    data: {type: "admin_delete_by_ids", data: ids},
                    dataType: "json",
                    success: function (data) {
                        if (data["code"] != "1") {
                            msgAlert(data['msg']);
                        } else {
                            msgAlert("删除成功", function () {
                                location.reload();
                            });
                        }
                    }
                });
            });
        });
    });
    function deleteAdmin(admin_id) {
        msgConfirm('提示', '确定删除该项', function () {
            $.ajax({
                url: "/admin/admin/deletebyid",
                type: "POST",
                async: false,
                data: {type: "admin_delete_by_id", admin_id: admin_id},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != "1") {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert("删除成功", function () {
                            location.reload();
                        });
                    }
                }
            });
        });
    }
    function editAdmin(admin_id) {
        location.href = '/admin/admin/editadmin?admin_id=' + admin_id;
    }
    
    function readAdmin(admin_id) {
        location.href = '/admin/admin/readadmin?admin_id=' + admin_id;
    }
    function statusAdmin(admin_id) {
        $.ajax({
            url: "/admin/admin/editstatus",
            type: "POST",
            async: false,
            data: {admin_id: admin_id},
            dataType: "json",
            success: function (data) {
                if (data["code"] != "1") {
                    msgAlert(data['msg']);
                } else {
                    msgAlert(data['msg'], function () {
                        location.reload();
                    });
                }
            }
        });
    }
    function addAdmin() {
        modDisplay({url: '/admin/admin/addadmin',title:"新增用户",height:520,width:650});
    }
</script>
