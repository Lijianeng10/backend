<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = "权限管理";
?>
<!--<ol class="am-breadcrumb">
    <li class="am-active">权限管理</li>
</ol>-->
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">上级权限:
    <?php
    echo Html::dropDownList("search_by_auth_pid", (isset($_GET["auth_pid"]) ? $_GET["auth_pid"] : "-1"), $topAuthames, ["id" => "search_by_auth_pid", 'class' => 'form-control', "style" => "width:200px;display:inline;margin-right:5px;"]);
    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "onclick" => "search();"]);
    ?>
</div>
<div class="am-btn-group am-btn-group-xs operat">
    <a href="javascript:addAuth();" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 新增</a>
    <button id="allSelected" type="button" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-star"></span> 全选/反选</button>
    <button id="deleteSelected" type="button" class="am-btn am-btn-default am-btn-danger"><span class="am-icon-trash-o"></span> 删除</button>
</div>
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [
            'label' => '',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('checkbox', 'delSelect', $model["auth_id"], ["class" => 'delSelect']);
            }
                ], 
            [ 'class' => 'yii\grid\SerialColumn'],
                [
                    'label' => "权限ID",
                    'value' => 'auth_id'
                ], [
                    'label' => '权限名称',
                    'value' => 'auth_name'
                ], [
                    'label' => '父类权限',
                    'value' => 'auth_pname'
                ], [
                    'label' => '对应链接',
                    'value' => 'auth_url'
                ], [
                    'label' => '顺序',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input("text", "auth_sort", $model["auth_sort"], ["class" => 'form-control auth_sort', "style" => "width:50px;", "onchange" => 'changeSort(' . $model["auth_id"] . ',$(this))']);
                    }
                        ], [
                            'label' => '权限状态',
                            'value' => function($model) {
                                if ($model['auth_status'] == 1) {
                                    return '开启';
                                } else {
                                    return '关闭';
                                }
                            }
                        ], [
                            'label' => '创建时间',
                            'value' => 'auth_create_at'
                        ], [
                            'label' => '更新时间',
                            'value' => 'auth_update_at'
                        ], [
                            'label' => '操作',
                            'format' => 'raw',
                            'value' => function($model) {
                                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editAuth(' . $model["auth_id"] . ');"> 编辑</span>
                            <span class="handle pointer" onclick="deleteAuth(' . $model["auth_id"] . ');">| 删除</span>
                        </div>';
                            }
                        ]
                    ]
                ])
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
                    url: "/admin/auth/deletebyids",
                    type: "POST",
                    async: false,
                    data: {type: "auth_delete_by_ids", data: ids},
                    dataType: "json",
                    success: function (data) {
                        if (data["code"] != "1") {
                            msgAlert(data["msg"]);
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
    function deleteAuth(auth_id) {
        msgConfirm('提示', '确定删除该项', function () {
            $.ajax({
                url: "/admin/auth/deletebyid",
                type: "POST",
                async: false,
                data: {type: "auth_delete_by_id", auth_id: auth_id},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != "1") {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert("删除成功", function () {
                            location.reload();
                        });
                    }
                }
            });
        });
    }
    function editAuth(auth_id) {
        location.href = '/admin/auth/editauth?auth_id=' + auth_id;
    }
    function search() {
        var search_by_auth_pid = $("#search_by_auth_pid").val();
        if (search_by_auth_pid == "-1") {
            location.href = '/admin/auth/index';
        } else {
            location.href = '/admin/auth/index?auth_pid=' + search_by_auth_pid;
        }
    }
    function changeSort(auth_id, _this) {
        $.ajax({
            url: "/admin/auth/changesort",
            type: "POST",
            async: false,
            data: {auth_id: auth_id, auth_sort: _this.val()},
            dataType: "json",
            success: function (data) {
                if (data["code"] != "1") {
                    msgAlert(data["msg"]);
                } else {
                    msgAlert("删除成功", function () {
                        location.reload();
                    });
                }
            }
        });
    }
    function addAuth() {
        modDisplay({url: '/admin/auth/addauth',title:"新增权限",height:320,width:400});
    }
</script>



