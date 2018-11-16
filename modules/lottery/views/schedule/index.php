<?php

use yii\grid\GridView;
use yii\helpers\Html;

echo Html::label("所属联赛");
echo Html::dropDownList("league", (isset($_GET["league"]) ? $_GET["league"] : ""), $data["leagues"], ["class" => "form-control inputLimit", "id" => "league"]);

echo Html::label("联赛ID");
echo Html::input("input","league_id",isset($get["league_id"]) ? $get["league_id"] : "", ["class" => "form-control inputLimit", "id" => "league_id","style"=>"width:80px"]);

echo Html::label("球队名称");
echo Html::input("input","ball_name",isset($get["ball_name"]) ? $get["ball_name"] : "", ["class" => "form-control inputLimit", "id" => "ball_name","style"=>"width:150px"]);

echo Html::label("比赛开始时间  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "startdate", isset($get["startdate"]) ? $get["startdate"] :date("Y-m-d",strtotime("-4 days")), ["id" => "startdate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("input", "enddate", isset($get["enddate"]) ? $get["enddate"] :date("Y-m-d",strtotime("+3 days")), ["id" => "enddate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo Html::button("搜索", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "limitsubmit();"]);
echo Html::button("重置", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "limitreset();"]);

echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "location.href='/lottery/schedule/addschedule'"]);
echo Html::tag("div", Html::label("注 ：1、本页面所示赛程未包含让球信息，查看让球数请点击“过关固定奖金－胜平负玩法页面”。") . "<br />"
        . "<label>2、<span class='swicth_1'></span>：仅开售过关方式 <span class='swicth_2'></span>：开售单关方式和过关方式 <span class='swicth_3'></span>：未开售此玩法 空白：待开售此玩法</label>", ["class" => "buttomspan", "style" => "color:#898989;"]);

echo GridView::widget([
    "dataProvider" => $data["list"],
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '期号',
            'value' => 'periods'
        ], [
            'label' => '赛事编号',
            'format' => 'raw',
            'value' => function($model) {
                return $model["schedule_code"] . Html::input("hidden", "schedule_id", $model["schedule_id"], ["class" => "schedule_id"]);
            }
                ], [
                    'label' => '联赛ID',
                    'value' => 'league_id'
                ],[
                    'label' => '联赛',
                    'value' => 'league_name'
                ], [
                    'label' => '主队vs客队',
                    'value' => function($model){
                        if(isset($model["visit_short_name"])&&isset($model["home_short_name"])){
                               return $model["home_short_name"]."VS".$model["visit_short_name"];
                           }else{
                               return "";
                           }
                    }
                ], [
                    'label' => '比赛开始时间',
                    'value' => 'start_time'
                ], [
                    'label' => '开售时间',
                    'value' => 'beginsale_time'
                ],[
                    'label' => '停售时间',
                    'value' => 'endsale_time'
                ],[
                    'label' => '固定奖金',
                    'format' => 'raw',
                    'value' => function($model) {
                        return '<span class="handle pointer" onclick="readBonus(' . $model["schedule_id"] . ')">查看</span>';
                    }
                ], [
                    'label' => '开售状态',
                    'value' => function($model) {
                        return ($model["schedule_status"] == 0) ? "等待开售" : ($model["schedule_status"] == 1 ? "销售中" : "停售");
                    }
                ], [
                    'label' => '胜平负',
                    'format' => 'raw',
                    'value' => function($model) {
                        $html = Html::tag("span", "", ["class" => "swicthplay swicth_" . $model["schedule_spf"], "data-name" => "spf"]);
                        return $html;
                    }
                        ], [
                            'label' => '让球胜平负',
                            'format' => 'raw',
                            'value' => function($model) {
                                $html = Html::tag("span", "", ["class" => "swicthplay  swicth_" . $model["schedule_rqspf"], "data-name" => "rqspf"]);
                                return $html;
                            }
                                ], [
                                    'label' => '比分',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        $html = Html::tag("span", "", ["class" => "swicthplay swicth_" . $model["schedule_bf"], "data-name" => "bf"]);
                                        return $html;
                                    }
                                        ], [
                                            'label' => '总进球数',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $html = Html::tag("span", "", ["class" => "swicthplay swicth_" . $model["schedule_zjqs"], "data-name" => "zjqs"]);
                                                return $html;
                                            }
                                                ], [
                                                    'label' => '半全场胜平负',
                                                    'format' => 'raw',
                                                    'value' => function($model) {
                                                        $html = Html::tag("span", "", ["class" => "swicthplay swicth_" . $model["schedule_bqcspf"], "data-name" => "bqcspf"]);
                                                        return $html;
                                                    }
                                                        ], 
//                                                        [
//                                                            'label' => '任选14/9',
//                                                            'format' => 'raw',
//                                                            'value' => function($model) {
//                                                                $res=[
//                                                                    "0"=>"不是",
//                                                                    "1"=>"是",
//                                                                ];
//                                                                return isset($model["is_optional"])?$res[$model["is_optional"]]:"";
//                                                            }
//                                                        ],
                                                        [
                                                            'label' => '实力对比(主:客)',
                                                            'format' => 'raw',
                                                            'value' => function($model) {
                                                                if(!empty($model["json_data"])){
                                                                    $jsonData=json_decode($model["json_data"]);
                                                                    return $jsonData->avg_home_per."  ： " .$jsonData->avg_visit_per;
                                                                }else{
                                                                    return "";
                                                                }
                                                            }
                                                        ],[
                                                            'label' => '操作',
                                                            'format' => 'raw',
                                                            'value' => function($model) {
                                                                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editSchedule(' . $model["schedule_id"] . ');">编辑</span>
                            <span class="handle pointer" onclick="releaseSchedule(' . $model["schedule_id"] . ');">' . (($model["schedule_status"] == 0) ? "| 开售" : ($model["schedule_status"] == 1 ? "| 停售" : "")) . '</span>
                            <span class="handle pointer" onclick="deleteSchedule(' . $model["schedule_id"] . ');" style="display:none">| 删除</span>
                        </div>';
                                                            }
                                                        ]
                                                    ]
                                                ]);
                                                ?>
<script type="text/javascript">
    function limitsubmit() {
        var league = $("#league").val();
        var league_id = $("#league_id").val();
        var ball_name = $("#ball_name").val();
        var  startdate = $("#startdate").val();
        var  enddate = $("#enddate").val();
        location.href = "?league=" + league+"&league_id="+league_id+"&ball_name="+ball_name+"&startdate="+startdate+"&enddate="+enddate;
    }
    function limitreset() {
        location.href = "/lottery/schedule/";
    }
    function editSchedule(scheduleId) {
        location.href = "/lottery/schedule/editschedule?schedule_id=" + scheduleId;
    }
    function releaseSchedule(scheduleId) {
        $.ajax({
            url: "/lottery/schedule/releaseschedule",
            data: {schedule_id: scheduleId},
            type: "post",
            dataType: "json",
            success: function (json) {
                if (json["code"] == "0") {
                    msgAlert(json["msg"], function () {
                        location.reload();
                    });
                } else {
                    console.log(json);
                    msgAlert(json["msg"]);
                }
            }
        });
    }
    function readBonus(scheduleId) {
        location.href = "/lottery/schedule/readbonus?schedule_id=" + scheduleId;
    }
    function deleteSchedule(scheduleId) {
        msgConfirm("提示", "确定删除该项？", function () {
            $.ajax({
                url: "/lottery/schedule/deleteschedule",
                data: {schedule_id: scheduleId},
                type: "post",
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 0) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        console.log(json);
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
    }
//    function switchplay(_this) {
//        if (_this.hasClass("checkedYuan")) {
//            _this.removeClass("checkedYuan");
//            _this.addClass("uncheckYuan");
//        } else {
//            _this.removeClass("uncheckYuan");
//            _this.addClass("checkedYuan");
//        }
//    }
//    $(function () {
//        $(".swicthplay").parent("td").click(function () {
//            var _this = $(this).children(".swicthplay");
//            var status = 1;
//            var play = _this.data("name");
//            var schedule_id = $(this).parent("tr").find(".schedule_id").val();
//            if (_this.hasClass("checkedYuan")) {
//                status = 0;
//            }
//            $.ajax({
//                url: "/lottery/schedule/swicthplay",
//                data: {status: status, play: play, schedule_id: schedule_id},
//                type: "post",
//                dataType: "json",
//                success: function (json) {
//                    if (json["code"] == 0) {
//                        msgAlert(json["msg"], function () {
//                            switchplay(_this);
//                        });
//                    } else {
//                        console.log(json);
//                        msgAlert(json["msg"]);
//                    }
//                }
//            });
//        });
//    });
</script>
