<?php

use yii\helpers\Html;
use yii\grid\GridView;

$status = [
    "" => "请选择",
    "1" => "正常",
    "2" => "禁用",
];
echo '<form action="/agents/bussiness/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("合作商信息", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "bussiness_name", isset($_GET["bussiness_name"]) ? $_GET["bussiness_name"] : "", ["class" => "form-control", "placeholder" => "名称、Appid、Secret key", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("创建日期  ", "", ["style" => "margin-left:27px;"]);
echo Html::input("text", "startdate", isset($_GET["startdate"]) ? $_GET["startdate"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "enddate", isset($_GET["enddate"]) ? $_GET["enddate"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("使用状态  ", "", ["style" => "margin-left:30px;"]);
echo Html::dropDownList("status", isset($_GET["status"]) ? $_GET["status"] : "", $status, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/agents/bussiness/index'"]);
echo Html::tag("span", "新增", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addBussiness()"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '合作商名称',
            'value' => 'name'
        ],[
            'label' => 'APPID',
            'value' => 'bussiness_appid'
        ],[
            'label' => 'SECRET KEY',
            'value' => 'secret_key'
        ],[
            'label' => 'DES KEY',
            'value' => 'des_key'
        ],[
            'label' => '会员编号',
            'value' => 'cust_no'
        ],[
            'label' => '创建时间',
            'value' => 'create_time'
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
                        return '<div class="am-btn-group am-btn-group-xs"><span class="handle pointer" onclick="addUser(' . $model["bussiness_id"] . ')">绑定咕啦会员 |</span><span class="handle pointer" onclick="reviewIp(' . $model["bussiness_id"] . ')"> 分配IP |</span>
                <span class="handle pointer" onclick="reviewRead(' . $model["bussiness_id"] . ')">查看IP |</span><span class="handle pointer" onclick="reviewApi(' . $model["bussiness_id"] . ')"> 分配接口 |</span>'
                 . ($model["status"] == "1" ? '<span class="handle pointer" onclick="edituse(' . $model["bussiness_id"] . ',2)"> 禁用 |</span>' : '<span class="handle pointer" onclick="edituse(' . $model["bussiness_id"] . ',1)"> 启用 |</span>') . '
                <span class="handle pointer" onclick="delBussiness(' . $model["bussiness_id"] . ')"> 删除</span>
            </div>';
                    }
                ]
            ]
        ]);
?>
<script>
    //新增合作商
    function addBussiness(){
        modDisplay({width: 500, height: 250, title: "新增合作商", url: "/agents/bussiness/add-bussiness"});
    }
    //绑定咕啦会员
    function addUser(bussiness_id){
        modDisplay({width: 500, height: 300, title: "绑定咕啦会员", url: "/agents/bussiness/bind-user?bussiness_id=" + bussiness_id});
    }
    //合作商IP分配
    function reviewIp(bussiness_id) {
        modDisplay({width: 500, height: 250, title: "合作商IP分配", url: "/agents/bussiness/add-bussiness-ip?bussiness_id=" + bussiness_id});
    }
     //查看IP信息
    function reviewRead(bussiness_id) {
        location.href="/agents/bussiness/read-bussiness?bussiness_id=" + bussiness_id;
    }
     //分配接口
    function reviewApi(bussiness_id) {
        location.href="/agents/bussiness/allotment-api?bussiness_id=" + bussiness_id;
    }
    //启用禁用状态切换
    function edituse(bussiness_id,sta){
        msgConfirm("提示", "确定修改？", function () {
            $.ajax({
                url: "/agents/bussiness/edituse",
                async: false,
                type: "POST",
                dataType: "json",
                data: {bussiness_id: bussiness_id,sta:sta},
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
    //删除合作商
    function delBussiness(bussiness_id){
        msgConfirm("提示", "确定删除该合作商信息？", function () {
            $.ajax({
                url: "/agents/bussiness/del-bussiness",
                async: false,
                type: "POST",
                dataType: "json",
                data: {bussiness_id: bussiness_id,},
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
</script>