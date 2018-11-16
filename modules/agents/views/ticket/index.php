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
    "1" => "手工出票",
    "2" => "自动出票"
    ];
echo '<form action="/agents/ticket/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("门店信息", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "store_name", isset($_GET["store_name"]) ? $_GET["store_name"] : "", ["class" => "form-control", "placeholder" => "门店名称、门店编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("出票机类型  ", "", ["style" => "margin-left:30px;"]);
echo Html::dropDownList("type", isset($_GET["type"]) ? $_GET["type"] : "", $type, ["class" => "form-control", "style" => "width:90px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("使用状态  ", "", ["style" => "margin-left:30px;"]);
echo Html::dropDownList("status", isset($_GET["status"]) ? $_GET["status"] : "", $status, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/agents/ticket/index'"]);
//echo Html::tag("span", "新增", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addApi()"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '门店名称',
            'value' => 'store_name'
        ], [
            'label' => '机器编号',
            'value' => 'dispenser_code'
        ],  [
            'label' => '销售商代码',
            'value' => 'vender_id'
        ], [
            'label' => '智魔方机子代码',
            'value' => 'sn_code'
        ],[
            'label' => '预出票数',
            'value' => 'pre_out_nums'
        ], [
            'label' => '剩余票数',
            'value' => 'mod_nums'
        ],[
            'label' => '出票机类型',
            'value' => function($model){
                $type = [
                    "1" => "手工出票",
                    "2" => "自动出票"
                ];
                if(!empty($model["type"])){
                    return $type[$model["type"]]; 
                }else{
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
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => function( $model) {
                        return '<div class="am-btn-group am-btn-group-xs">'
                 . ($model["status"] == "1" ? '<span class="handle pointer" onclick="edituse(' . $model["ticket_dispenser_id"] . ',2)"> 禁用 |</span>' : '<span class="handle pointer" onclick="edituse(' . $model["ticket_dispenser_id"] . ',1)"> 启用 |</span>') . '
                <span class="handle pointer" onclick="editDispenser(' . $model["ticket_dispenser_id"] . ')"> 编辑 |</span><span class="handle pointer" onclick="deleteApi(' . $model["ticket_dispenser_id"] . ')"> 删除</span>
            </div>';
                    }
                ]
            ]
        ]);
?>

<script>
   //门店机器启用禁用状态切换
    function edituse(ticket_dispenser_id,sta){
        msgConfirm("提示", "确定修改？", function () {
            $.ajax({
                url: "/agents/ticket/edituse",
                async: false,
                type: "POST",
                dataType: "json",
                data: {ticket_dispenser_id: ticket_dispenser_id,sta:sta},
                success: function (json) {
                    if (json["code"] == 600){
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    }else{
                        msgAlert(json["msg"]);
                    }
                }
            });
        })
    }
    //编辑门店机器信息
    function editDispenser(ticket_dispenser_id){
        modDisplay({width: 800, height: 500, title: "编辑门店机器信息", url: "/agents/ticket/edit-dispenser?ticket_dispenser_id=" + ticket_dispenser_id});
    }
     //删除接口
    function deleteApi(ticket_dispenser_id){
        msgConfirm("提示", "确定删除？", function () {
            $.ajax({
                url: "/agents/ticket/delete-ticket",
                async: false,
                type: "POST",
                dataType: "json",
                data: {ticket_dispenser_id: ticket_dispenser_id},
                success: function (json) {
                    if (json["code"] == 600){
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    }else{
                        msgAlert(json["msg"]);
                    }
                }
            });
        })
    }
</script>

