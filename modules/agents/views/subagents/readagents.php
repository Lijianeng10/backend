<?php

use yii\helpers\Html;
use yii\grid\GridView;

$agents_type = [
    "1" => "总部",
    "2" => "地推",
    "3" => "体彩店",
    "4" => "福彩店",
    "5" => "便利店",
    "6" => "个人",
];
$pass_status = [
    "1" => "未认证",
    "2" => "审核中",
    "3" => "已通过",
    "4" => "未通过",
];
$use_status = [
    "1" => "使用",
    "2" => "禁用",
];
echo '<form>';
echo "<ul class='double_team_ul'>";
echo '<li style="width:100%;">';
echo Html::label("代理商基本信息", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo Html::input("hidden", "agents_id", $data["agents_id"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商账户  ", "", ["style" => "margin-left:45px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "agents_account", $data['agents_account'], ["class" => "form-control need", "placeholder" => "代理商账户", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("上级代理商编号  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "upagents_code", $data['upagents_code'], ["class" => "form-control need", "placeholder" => "上级代理商编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商名称  ", "", ["style" => "margin-left:42px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "agents_name", $data['agents_name'], ["class" => "form-control need", "placeholder" => "代理商名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("上级代理商名称  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "upagents_name", $data['upagents_name'], ["class" => "form-control need", "placeholder" => "上级代理商名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商APPID  ", "", ["style" => "margin-left:33px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "agents_appid", $data['agents_appid'], ["class" => "form-control need", "placeholder" => "代理商APPID", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商类型", "", ["style" => "margin-left:43px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("agents_type", $data['agents_type'], $agents_type, ["class" => "form-control need", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商简码  ", "", ["style" => "margin-left:42px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "agents_code", $data['agents_code'], ["class" => "form-control need","id"=>"agents_code", "placeholder" => "代理商简码", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("跳转URL  ", "", ["style" => "margin-left:61px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "to_url", $data['to_url'], ["class" => "form-control need","id"=>"to_url", "placeholder" => "跳转URL", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("认证状态", "", ["style" => "margin-left:55px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("pass_status", $data['pass_status'], $pass_status, ["class" => "form-control need", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("使用状态", "", ["style" => "margin-left:57px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("use_status", $data['use_status'], $use_status, ["class" => "form-control need", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("开户时间  ", "", ["style" => "margin-left:54px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "check_time", $data['check_time'], ["class" => "form-control need", "placeholder" => "开户时间", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商备注  ", "", ["style" => "margin-left:43px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("text", "agents_remark", $data['agents_remark'], ["class" => "form-control need", "placeholder" => "代理商备注", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';

echo '<form>';
echo "<ul class='double_team_ul'>";
echo '<li style="width:100%;">';
echo Html::label("代理商Ip地址信息", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo Html::input("hidden", "agents_id", $data["agents_id"]);
echo '</li>';
echo GridView::widget([
    "dataProvider" => $ipInfo,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '代理商名称',
            'value' => 'agents_name'
        ],
        [
            'label' => 'IP地址',
            'value' => 'ip_address'
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
                                . ($model["status"] == "1" ? '<span class="handle pointer" onclick="editsta(' . $model["agents_ip_id"] . ',2)">禁用 |</span>' : '<span class="handle pointer" onclick="editsta(' . $model["agents_ip_id"] . ',1)">启用 |</span>') .
                                '<span class="handle pointer" onclick="deleteIp(' . $model["agents_ip_id"] . ')"> 删除</span></div>';
                    }
                ]
            ]
        ]);
echo Html::tag("span", "返回", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:30px;", "onclick" => "closeMask()"]);
        ?>
<script>
    $(function () {
        $("input").attr("disabled", true);
        $("textarea").attr("disabled", true);
        $.each($("select"), function () {
            $(this).attr("disabled", true);
        });
    });
     //启用禁用状态切换
    function editsta(agents_ip_id,sta){
        msgConfirm("提示", "确定修改？", function () {
            $.ajax({
                url: "/agents/subagents/edit-ip-sta",
                async: false,
                type: "POST",
                dataType: "json",
                data: {agents_ip_id: agents_ip_id, sta: sta},
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
    //删除IP地址
    
</script>


