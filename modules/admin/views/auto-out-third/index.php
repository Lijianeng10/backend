<?php

use yii\helpers\Html;
use yii\grid\GridView;

$status = [
    "" => "请选择",
    "1" => "正常",
    "2" => "禁用",
];
$type = [
    "" => "请选择",
    "1" => "流量单",
    "2" => "自营单",
    '3' => '全部'
];
echo '<form action="/lottery/auto-out-third/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("出票方信息", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "third_name", isset($getData["third_name"]) ? $getData["third_name"] : "", ["class" => "form-control", "placeholder" => "出票方名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("出票类型", "", ["style" => "margin-left:30px;"]);
echo Html::dropDownList("out_type", isset($getData["out_type"]) ? $getData["out_type"] : "", $type, ["class" => "form-control", "style" => "width:90px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("使用状态  ", "", ["style" => "margin-left:30px;"]);
echo Html::dropDownList("status", isset($getData["status"]) ? $getData["status"] : "", $status, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/lottery/auto-out-third/index'"]);
echo Html::tag("span", "新增", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addThird()"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '出票方编号',
            'value' => 'third_code'
        ], [
            'label' => '出票方名称',
            'value' => 'third_name'
        ], [
            'label' => '出票类型',
            'value' => function($model) {
                $type = [
                    "" => "请选择",
                    "1" => "流量单",
                    "2" => "自营单",
                    '3' => '全部'
                ];
                if (!empty($model["out_type"])) {
                    return $type[$model["out_type"]];
                } else {
                    return "";
                }
            }
        ], [
            'label' => '使用状态',
            'value' => function($model) {
                if ($model["status"] == null) {
                    return "";
                }
                $status = [
                    "1" => "正常",
                    "2" => "禁用"
                ];
                return $status[$model["status"]];
            }
        ], [
            'label' => '操作人',
            'value' => 'opt_name'
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function( $model) {
                return '<div class="am-btn-group am-btn-group-xs"><span class="handle pointer " onclick="setWeight(' . $model["auto_out_third_id"] . ')"> 权重设置 |</span>'
                        . ($model["status"] == "1" ? '<span class="handle pointer" onclick="edituse(' . $model["auto_out_third_id"] . ',2)"> 禁用 |</span>' : '<span class="handle pointer" onclick="edituse(' . $model["auto_out_third_id"] . ',1)"> 启用 |</span>') . '
                <span class="handle pointer" onclick="editThird(' . $model["auto_out_third_id"] . ')"> 编辑 |</span><span class="handle pointer" onclick="deleteApi(' . $model["auto_out_third_id"] . ')"> 删除</span>
            </div>';
            }
        ]
    ]
]);
?>

<script>
    //新增第三方出票方
    function addThird() {
        modDisplay({width: 800, height: 400, title: "新增出票方", url: "/admin/auto-out-third/add-third"});
    }
    
    //门店机器启用禁用状态切换
    function edituse(auto_out_third_id, sta) {
        var str = sta == 1 ? '启用' : '禁用';
        msgConfirm("提示", "确定" + str + "此出票方", function () {
            $.ajax({
                url: "/lottery/auto-out-third/edit-use",
                async: false,
                type: "POST",
                dataType: "json",
                data: {auto_out_third_id: auto_out_third_id, sta: sta},
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        })
    }
    
     //编辑第三方出票方
    function editThird(autoOutThirdId) {
        modDisplay({width: 800, height: 400, title: "编辑出票方", url: "/admin/auto-out-third/edit-third?auto_out_third_id=" + autoOutThirdId});
    }
    //删除接口
    function deleteApi(auto_out_third_id) {
        msgConfirm("提示", "确定删除？", function () {
            $.ajax({
                url: "/lottery/auto-out-third/del-third",
                async: false,
                type: "POST",
                dataType: "json",
                data: {auto_out_third_id: auto_out_third_id},
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        })
    }
    
    function setWeight(autoOutThirdId) {
        modDisplay({width: 350, height: 200, title: "权重设置", url: "/admin/auto-out-third/set-weight?third_id=" + autoOutThirdId});
    }
</script>

