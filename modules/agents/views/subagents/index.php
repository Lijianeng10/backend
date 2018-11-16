<?php

use yii\helpers\Html;
use yii\grid\GridView;

$pass_status = [
    "" => "请选择",
    "1" => "未认证",
    "2" => "审核中",
    "3" => "已通过",
    "4" => "未通过"
];
$agents_type = [
    "" => "请选择",
    "1" => "总部",
    "2" => "地推",
    "3" => "体彩店",
    "4" => "福彩店",
    "5" => "便利店",
    "6" => "个人",
];
$use_status = [
    "" => "请选择",
    "1" => "使用",
    "2" => "锁定",
];

echo '<form action="/agents/subagents/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("代理商编号", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "agents_account", isset($_GET["agents_account"]) ? $_GET["agents_account"] : "", ["class" => "form-control", "placeholder" => "代理商编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商名称", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "agents_name", isset($_GET["agents_name"]) ? $_GET["agents_name"] : "", ["class" => "form-control", "placeholder" => "代理商名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商类型  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("agents_type", isset($_GET["agents_type"]) ? $_GET["agents_type"] : "", $agents_type, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("上级代理商", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "upagents_info", isset($_GET["upagents_info"]) ? $_GET["upagents_info"] : "", ["class" => "form-control", "placeholder" => "上级代理商编号，名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("开户日期  ", "", ["style" => "margin-left:27px;"]);
echo Html::input("text", "startdate", isset($_GET["startdate"]) ? $_GET["startdate"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "enddate", isset($_GET["enddate"]) ? $_GET["enddate"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("认证状态  ", "", ["style" => "margin-left:30px;"]);
echo Html::dropDownList("pass_status", isset($_GET["pass_status"]) ? $_GET["pass_status"] : "", $pass_status, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/agents/subagents/index'"]);
echo Html::tag("span", "新增", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/agents/subagents/addagents'"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '代理商账户',
            'value' => 'agents_account'
        ],
        [
            'label' => '代理商ID',
            'value' => 'agents_id'
        ],
        [
            'label' => '代理商名称',
            'value' => 'agents_name'
        ],
        [
            'label' => '代理商简码',
            'value' => 'agents_code'
        ],
        [
            'label' => '代理商APPID',
            'value' => 'agents_appid'
        ],
        [
            'label' => '代理商SECRET_KEY',
            'value' => 'secret_key'
        ], 
        [
            'label' => 'URL',
            'value' => 'to_url'
        ], 
        [
            'label' => '上级代理商编号',
            'value' => 'upagents_code'
        ],
        [
            'label' => '上级代理商名称',
            'value' => 'upagents_name'
        ],
        [
            'label' => '代理商类型',
            'value' => function($model) {
                if ($model["agents_type"] == null) {
                    return "";
                }
                $agents_type = [
                    "1" => "总部",
                    "2" => "地推",
                    "3" => "体彩店",
                    "4" => "福彩店",
                    "5" => "便利店",
                    "6" => "个人",
                ];
                return $agents_type[$model["agents_type"]];
            }
                ],
                [
                    'label' => '开户时间',
                    'value' => 'check_time'
                ],
                [
                    'label' => '认证状态',
                    'value' => function($model) {
                        if ($model["pass_status"] == null) {
                            return "";
                        }
                        $pass_status = [
                            "1" => "未认证",
                            "2" => "审核中",
                            "3" => "已通过",
                            "4" => "未通过"
                        ];
                        return $pass_status[$model["pass_status"]];
                    }
                        ],
                        [
                            'label' => '使用状态',
                            'value' => function($model) {
                                if ($model["use_status"] == null) {
                                    return "";
                                }
                                $status = [
                                    "1" => "正常",
                                    "2" => "禁用"
                                ];
                                return $status[$model["use_status"]];
                            }
                                ],
                                [
                                    'label' => '操作',
                                    'format' => 'raw',
                                    'value' => function( $model) {
//                                    <span class="handle pointer" onclick="deleteAgents(' . $model["agents_id"] . ')"> 删除</span>
                                        return '<div class="am-btn-group am-btn-group-xs">' . ($model["pass_status"] == "2" ? '<span class="handle pointer" onclick="review(' . $model["agents_id"] . ')">审核 |</span>' : "") .($model["pass_status"] == "3" ? '<span class="handle pointer" onclick="reviewIp(' . $model["agents_id"] . ')">分配IP |</span>' : "") . '
                <span class="handle pointer" onclick="reviewRead(' . $model["agents_id"] . ')">查看 |</span>
                <span class="handle pointer" onclick="reviewEdit(' . $model["agents_id"] . ')">编辑 |</span>'.($model["use_status"] == "1" ? '<span class="handle pointer" onclick="edituse(' . $model["agents_id"] . ',2)"> 禁用 |</span>' :'<span class="handle pointer" onclick="edituse(' . $model["agents_id"] . ',1)"> 启用 |</span>') .'
                
            </div>';
                                    }
                                ]
                            ]
                        ]); 
                        ?>
<script>
    //审核代理商
    function review(agents_id) {
        modDisplay({width: 500, height: 240, title: "审核", url: "/agents/subagents/review-agents?agents_id=" + agents_id});
    }
    //代理商IP分配
    function reviewIp(agents_id) {
        modDisplay({width: 500, height: 240, title: "代理商IP分配", url: "/agents/subagents/add-agents-ip?agents_id=" + agents_id});
    }
     //查看代理商信息
    function reviewRead(agents_id) {
        modDisplay({width: 1000, height: 700, title: "", url: "/agents/subagents/readagents?agents_id=" + agents_id});
    }
    //编辑代理商信息
    function reviewEdit(agents_id) {
        modDisplay({width: 1000, height: 500, title: "", url: "/agents/subagents/editinfo?agents_id=" + agents_id});
    }
    //启用禁用状态切换
    function edituse(agents_id,sta){
        msgConfirm("提示", "确定修改？", function () {
            $.ajax({
                url: "/agents/subagents/edituse",
                async: false,
                type: "POST",
                dataType: "json",
                data: {agents_id: agents_id,sta:sta},
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
//    function deleteAgents(agentsAccount){
//        msgConfirm("提示", "确定删除？", function () {
//            $.ajax({
//                url: "/agents/subagents/delete-agents",
//                async: false,
//                type: "POST",
//                dataType: "json",
//                data: {agentsAccount: agentsAccount},
//                success: function (json) {
//                    if (json["code"] == 600){
//                        msgAlert(json["msg"], function () {
//                            location.reload();
//                        });
//                    }else{
//                        msgAlert(json["msg"]);
//                    }
//                }
//            });
//        })
//    }
</script>
