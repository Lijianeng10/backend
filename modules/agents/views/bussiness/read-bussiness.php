<?php

use yii\helpers\Html;
use yii\grid\GridView;

$status = [
    "1" => "正常",
    "2" => "禁用",
];
echo '<form>';
echo "<ul class='double_team_ul'>";
echo '<li style="width:100%;">';
echo Html::label("合作商基本信息", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo Html::input("hidden", "bussiness_id", $data["bussiness_id"]);
echo '</li>';
echo '<li style="margin-top:10px;">';
echo Html::label("合作商名称  ", "", ["style" => "margin-left:10px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "name", $data['name'], ["class" => "form-control need", "placeholder" => "合作商名称", "style" => "width:150px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:10px;">';
echo Html::label("Appid  ", "", ["style" => "margin-left:20px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "bussiness_appid", $data['bussiness_appid'], ["class" => "form-control need", "placeholder" => "Appid", "style" => "width:150px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:10px;">';
echo Html::label("Secret_key  ", "", ["style" => "margin-left:13px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "secret_key", $data['secret_key'], ["class" => "form-control need", "placeholder" => "SECRET KEY", "style" => "width:240px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:10px;">';
echo Html::label("Cust_no  ", "", ["style" => "margin-left:10px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "cust_no", $data['cust_no'], ["class" => "form-control need", "placeholder" => "cust_no", "style" => "width:150px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:10px;">';
echo Html::label("使用状态", "", ["style" => "margin-left:20px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("status", $data['status'], $status, ["class" => "form-control need", "style" => "width:100px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:10px;">';
echo Html::label("创建时间  ", "", ["style" => "margin-left:4px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "create_time", $data['create_time'], ["class" => "form-control need", "placeholder" => "创建时间", "style" => "width:150px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '</ul>';
echo '</form>';
echo '<form>';
echo "<ul class='double_team_ul'>";
echo '<li style="width:100%;">';
echo Html::label("合作商Ip地址信息", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo Html::input("hidden", "bussiness_id", $data["bussiness_id"]);
echo '</li>';
echo GridView::widget([
    "dataProvider" => $ipInfo,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '合作商名称',
            'value' => 'name'
        ],
        [
            'label' => 'IP地址',
            'value' => 'ip'
        ],
        [
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
                ],
                [
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => function( $model) {
                        return '<div class="am-btn-group am-btn-group-xs">'
                                . ($model["status"] == "1" ? '<span class="handle pointer" onclick="editsta(' . $model["bussiness_ip_white_id"] . ',2)"> 禁用 |</span>' : '<span class="handle pointer" onclick="editsta(' . $model["bussiness_ip_white_id"] . ',1)">启用 |</span>') .
                                '<span class="handle pointer" onclick="deleteIp(' . $model["bussiness_ip_white_id"] . ')"> 删除</span></div>';
                    }
                ]
            ]
        ]);
echo Html::tag("span", "返回", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:30px;", "onclick" => "back()"]);
        ?>
<script>
    $(function () {
        $("input").attr("disabled", true);
        $.each($("select"), function () {
            $(this).attr("disabled", true);
        });
    });
     //启用禁用状态切换
    function editsta(bussiness_ip_white_id,sta){
        msgConfirm("提示", "确定修改？", function () {
            $.ajax({
                url: "/agents/bussiness/edit-ip-sta",
                async: false,
                type: "POST",
                dataType: "json",
                data: {bussiness_ip_white_id: bussiness_ip_white_id, sta: sta},
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
    //返回
    function back(){
        location.href="/agents/bussiness/index"
    }
    //删除IP地址
    function deleteIp(bussiness_ip_white_id){
        msgConfirm("提示", "确定删除该IP地址？", function () {
            $.ajax({
                url: "/agents/bussiness/del-ip",
                async: false,
                type: "POST",
                dataType: "json",
                data: {bussiness_ip_white_id: bussiness_ip_white_id,},
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

