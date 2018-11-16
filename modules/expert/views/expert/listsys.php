<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\helpers\Constant;
$expertStatus = [
    "" => "全部",
    "1" => "待审核",
    "2" => "认证成功",
    "3" => "审核不通过",
    "4" => "未完成提交",
    "5" => "取消身份"
];
$pactStatus = [
    "" => "全部",
    "1" => "未签",
    "2" => "已签",
    "3" => "失效"
];
$stickType = [
    "" => "全部",
    "1" => "是",
    "999" => "否"
];
//$sourceArr = [
//    "" => "全部",
//    "1" => "咕啦专家",
//    "2" => "唧嗨专家",
//    "3" => "网易专家",
//    "4" => "同行专家",
//    "5" => "全景专家"
//];

echo "<form id='filterForm'>";
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("专家信息", "expertInfo", ["style" => "margin-left:15px;"]);
echo Html::input("input", "expertInfo", isset($get["expertInfo"]) ? $get["expertInfo"] : "", ["id" => "expertInfo", "class" => "form-control", "placeholder" => "编号、昵称、手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("申请日期  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "createTimeStart", isset($get["createTimeStart"]) ? $get["createTimeStart"] : "", ["id" => "createTimeStart", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "createTimeEnd", isset($get["createTimeEnd"]) ? $get["createTimeEnd"] : "", ["id" => "createTimeEnd", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("认证状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("expertStatus", isset($get["expertStatus"]) ? $get["expertStatus"] : "", $expertStatus, ["id" => "expertStatus", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("协议状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("pactStatus", isset($get["pactStatus"]) ? $get["pactStatus"] : "", $pactStatus, ["id" => "pactStatus", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("置顶状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("stick", isset($get["stick"]) ? $get["stick"] : "", $stickType, ["id" => "payType", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo Html::label("专家来源  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("expertSource", isset($get["expertSource"]) ? $get["expertSource"] : "", $sourceArr, ["id" => "pactStatus", "class" => "form-control", "style" => "width:90px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "search();"]);
echo Html::button("重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
echo '</li>';
echo "</ul>";
echo "</form>";
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '专家编号',
            'value' => 'cust_no'
        ], [
            'label' => '专家昵称',
            'value' => 'user_name'
        ],
    	[
    	'label' => '专家类型',
    	'value' => function($model) {
    	if (empty($model["identity"])) {
    		return "--";
    	} else {
    		return Constant::EXPERT_TYPE[$model["identity"]];
    	}
    	}
    	],
    	[
            'label' => '专家身份',
            'value' => function($model) {
                if (empty($model["expert_type_name"])) {
                    return "--";
                } else {
                    return $model["expert_type_name"];
                }
            }
        ], [
            'label' => '专家来源',
            'value' => 'source_name'
            ], [
                    'label' => '手机号',
                    'value' => 'user_tel'
                ], [
                    'label' => '近一月红单',
                    'value' => 'month_red_nums'
                ], [
                    'label' => '最近连红数',
                    'value' => 'even_red_nums'
                ], [
                    'label' => '粉丝',
                    'value' => 'fans_nums'
                ], [
                    'label' => '文章数量',
                    'value' => 'article_nums'
                ], [
                    'label' => '申请时间',
                    'value' => 'create_time'
                ], [
                    'label' => '认证状态',
                    'value' => function($model) {
                        $expertStatus = [
                            "1" => "待审核",
                            "2" => "认证成功",
                            "3" => "审核不通过",
                            "4" => "未完成提交",
                            "5" => "取消身份"
                        ];
                        return isset($expertStatus[$model["expert_status"]]) ? $expertStatus[$model["expert_status"]] : "未知状态";
                    }
                        ], [
                            'label' => '协议状态',
                            'value' => function($model) {
                                $pactStatus = [
                                    "1" => "未签",
                                    "2" => "已签",
                                    "3" => "失效"
                                ];
                                return isset($pactStatus[$model["pact_status"]]) ? $pactStatus[$model["pact_status"]] : "未知状态";
                            }
                                ],[
                                    'label' => '置顶状态',
                                    'value' => function($model) {
                                        if ($model['stick'] == 999) {
                                            $item = "否";
                                        } else {
                                            $item = '是';
                                        }
                                        return $item;
                                    }
                                ],[
                                    'label' => '置顶顺序',
                                    'value' => function($model){
                                        if($model["stick"]==999){
                                            return 0;
                                        }else{
                                            return $model["stick"];
                                        }
                                    }
                                ], [
                                    'label' => '操作',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return '<div class = "am-btn-group am-btn-group-xs">
' . ($model["expert_status"] == 1 ? ('<span class = "handle pointer" onclick = "reviewExpert(\'/expert/expert/review?expert_id=' . $model["expert_id"] . '&expert_source=' . $model["expert_source"] . '\')">审核 |</span>') : "") . '
' . ($model["expert_status"] == 2 ? ('<span class = "handle pointer" onclick="cacelExpertStatus(' . $model["expert_id"] . ')">禁用专家 |</span>') : "") . '
' . ($model["expert_status"] == 5 ? ('<span class = "handle pointer" onclick="enableExpertStatus(' . $model["expert_id"] . ')">启用专家 |</span>') : "") . '
<span class = "handle pointer" onclick = "updatePact(\'/expert/expert/pact-status?expert_id=' . $model["expert_id"] . '\')">协议 |</span>
<span class = "handle pointer" onclick = "readExpert(\'/expert/expert/read-detail-sys?expert_id=' . $model["expert_id"] . '\')">查看</span>
' . ($model["expert_status"] == 2 ? ('<span class = "handle pointer" onclick="editIdentity(' . $model["user_id"] . ')"> | 修改专家类型</span>') : "") .($model["expert_status"] == 2 ? ($model["stick"] == 999 ? ('<span class="handle pointer" onclick="onStick(' . $model["expert_id"] .','.$model["stick"]. ')"> | 置顶</span>') : ('<span class="handle pointer" onclick="offStick(' . $model["expert_id"] . ')"> | 取消置顶</span>')) : "").  
 ($model["expert_status"] == 2 ? ('<span class = "handle pointer" onclick="editType(' . $model["user_id"] . ')"> | 修改专家身份</span>') : "") .
(\Yii::$app->session['type'] == 0 ? ('<span class = "handle pointer" onclick="editSource(' . $model["user_id"] . ')"> | 修改专家来源</span>') : "") .
                                                '</div>';
                                    }
                                ]
                            ]
                        ]);
                        ?>
<script>
    function search() {
        var data = $("#filterForm").serialize();
        location.href = "/expert/expert/list-sys?" + data;
    }
    function goReset() {
        location.href = "/expert/expert/list-sys";
    }
    function readExpert(url) {
        modDisplay({title: '专家详情', url: url, height: 510, width: 800});
    }
    function reviewExpert(url) {
        modDisplay({title: '专家审核', url: url, height: 400, width: 400});
    }
    function updatePact(url) {
        modDisplay({title: '协议状态修改', url: url, height: 150, width: 400});
    }
    function cacelExpertStatus(expertId) {
        if (!confirm("确认禁用身份？")) {
            return false;
        }
        $.ajax({
            url: "/expert/expert/cacel-expert-status",
            async: false,
            dataType: "json",
            data: {expert_id: expertId},
            type: "POST",
            success: function (json) {
                alert(json["msg"]);
                if (json["code"] == "600") {
                    location.reload();
                }
            }
        });
    }
    function enableExpertStatus(expertId) {
        if (!confirm("确认启用身份？")) {
            return false;
        }
        $.ajax({
            url: "/expert/expert/enable-expert-status",
            async: false,
            dataType: "json",
            data: {expert_id: expertId},
            type: "POST",
            success: function (json) {
                alert(json["msg"]);
                if (json["code"] == "600") {
                    location.reload();
                }
            }
        });
    }
    function editIdentity(userId) {
        modDisplay({title: '专家类型', url:' /expert/expert/edit-identity?user_id=' + userId, height: 200, width: 300});
    }
    //置顶

    function onStick(expert_id,value){
        msgPrompt("提示","确定置顶该专家?",value,function(){
            var stick = $("#num").val();
            if(isNaN(parseInt(stick))){
                msgAlert("请输入合法的数字");
                return false;
            }
            if(stick<=0||stick>=999){
                msgAlert("请输入0-999的正整数");
                return false;
            }
            $.ajax({
                url: "/expert/expert/edit-stick",
                type: "POST",
                dataType: "json",
                aysnc: false,
                data: {expert_id: expert_id,stick:stick,type:1},
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
    //取消置顶
    function offStick(expert_id) {
        msgConfirm("提示", "确定取消置顶该专家？", function () {
            $.ajax({
                url: "/expert/expert/edit-stick",
                type: "POST",
                dataType: "json",
                aysnc: false,
                data: {expert_id: expert_id,type:2},
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
    
    function editSource(userId) {
        modDisplay({title: '专家来源', url:' /expert/expert/edit-source?user_id=' + userId, height: 200, width: 300});
    }
    function editType(userId) {
        modDisplay({title: '专家身份', url:' /expert/expert/edit-type?user_id=' + userId, height: 200, width: 300});
    }
</script>