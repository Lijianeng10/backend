<?php

use yii\helpers\Html;
use yii\grid\GridView;

$status = [
    "" => "请选择",
    "1" => "正常",
    "2" => "禁用",
];
echo '<form action="/agents/apilist/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("接口名称", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "api_name", isset($_GET["api_name"]) ? $_GET["api_name"] : "", ["class" => "form-control", "placeholder" => "接口名称、URL", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
//echo '<li>';
//echo Html::label("创建日期  ", "", ["style" => "margin-left:27px;"]);
//echo Html::input("text", "startdate", isset($_GET["startdate"]) ? $_GET["startdate"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
//echo "-";
//echo Html::input("text", "enddate", isset($_GET["enddate"]) ? $_GET["enddate"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
//echo '</li>';
echo '<li>';
echo Html::label("使用状态  ", "", ["style" => "margin-left:30px;"]);
echo Html::dropDownList("status", isset($_GET["status"]) ? $_GET["status"] : "", $status, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/agents/apilist/index'"]);
echo Html::tag("span", "新增", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addApi()"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '接口名称',
            'value' => 'api_name'
        ], [
            'label' => 'URL',
            'value' => 'api_url'
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
                 . ($model["status"] == "1" ? '<span class="handle pointer" onclick="edituse(' . $model["api_list_id"] . ',2)"> 禁用 |</span>' : '<span class="handle pointer" onclick="edituse(' . $model["api_list_id"] . ',1)"> 启用 |</span>') . '
                <span class="handle pointer" onclick="editApi(' . $model["api_list_id"] . ')"> 编辑 |</span><span class="handle pointer" onclick="deleteApi(' . $model["api_list_id"] . ')"> 删除</span>
            </div>';
                    }
                ]
            ]
        ]);
?>
<script>
    //新增api接口
    function addApi(){
        modDisplay({width: 500, height: 300, title: "新增接口", url: "/agents/apilist/add-api"});
    }
    //编辑api接口信息
    function editApi(api_list_id){
        modDisplay({width: 500, height: 300, title: "编辑接口", url: "/agents/apilist/edit-api?api_list_id="+api_list_id});
    }
    //删除接口
    function deleteApi(api_list_id){
        msgConfirm("提示", "确定删除？", function () {
            $.ajax({
                url: "/agents/apilist/delete-api",
                async: false,
                type: "POST",
                dataType: "json",
                data: {api_list_id: api_list_id},
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
    //启用禁用状态切换
    function edituse(api_list_id,sta){
        msgConfirm("提示", "确定修改？", function () {
            $.ajax({
                url: "/agents/apilist/edituse",
                async: false,
                type: "POST",
                dataType: "json",
                data: {api_list_id: api_list_id,sta:sta},
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
