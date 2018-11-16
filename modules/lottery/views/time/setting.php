<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

echo "<form id='lottimeform'>";
echo Html::input('hidden', "lottery_code", $data["lotTime"][0]["lottery_code"]);
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            "label" => '彩票编码',
            "value" => function($model) {
                return $model["lotTime"][0]["lottery_code"];
            }
        ], [
            "label" => "彩种名称",
            "value" => function($model) {
                return $model["lotTime"][0]["lottery_name"];
            }
        ], [
            "label" => '开奖频率<span class="requiredIcon">*</span>',
            "format" => "raw",
            "value" => function($model) {
                return Html::dropDownList("rate", $model["lotTime"][0]["rate"], $model["rates"], ["class" => "form-control need", "id" => "rate"]);
            }
                ], [
                    "label" => '场次<span class="requiredIcon">*</span>',
                    "format" => "raw",
                    "value" => function($model) {
                        return Html::input("text", "changci", $model["lotTime"][0]["changci"], ["class" => "form-control need", "id" => "changci"]);
                    }
                        ], [
                            "label" => '开奖时间<span class="requiredIcon">*</span>',
                            "format" => "raw",
                            "value" => function($model) {
                                $html = "<div class='timecontainer'>";
                                $html .= Html::tag("span", "开奖开始时间", ["class" => "buttomspan"]);
                                $html .= Html::input('text', "start_time", $model["lotTime"][0]["start_time"], ["class" => "form-control inputTime need"]);

                                $html .= Html::tag("span", "开奖结束时间", ["class" => "buttomspan"]);
                                $html .= Html::input('text', "stop_time", $model["lotTime"][0]["stop_time"], ["class" => "form-control inputTime need"]);

                                $html .= Html::tag("span", "截止投注时间", ["class" => "buttomspan"]);
                                $html .= Html::input('text', "limit_time", $model["lotTime"][0]["limit_time"], ["class" => "form-control inputTime need"]);
                                $html .="</div>";
                                $html .= "<div class='weekcontainer'>";
                                $weeks = $model["weeks"];
                                $html .= GridView::widget([
                                            'dataProvider' => $weeks,
                                            'columns' => [
                                                [
                                                    "label" => "星期",
                                                    "value" => "name"
                                                ], [
                                                    "label" => "选择",
                                                    "format" => "raw",
                                                    "value" => function($md) {
                                                        return Html::checkbox("week[]", $md["isSelect"], ["value" => $md["name"]]);
                                                    }
                                                        ]
                                                    ],
                                                    'tableOptions' => [
                                                        "class" => "table table-striped table-bordered"
                                                    ]
                                        ]);
                                        $html .="</div>";
                                        return $html;
                                    }
                                        ], [
                                            "label" => '开奖时间简要描述<span class="requiredIcon">*</span>',
                                            "format" => "raw",
                                            "value" => function($model) {
                                                return Html::textarea("remark", $model["lotTime"][0]["remark"], ["class" => "form-control need", "style" => "height:80px;"]);
                                            }
                                                ], [
                                                    "label" => "操作",
                                                    "format" => "raw",
                                                    "value" => function() {
                                                        $html = Html::button("提交", ["class" => "am-btn am-btn-primary", "onclick" => "submitForm();"]);
                                                        $html .="&nbsp&nbsp&nbsp";
                                                        $html .= Html::button("返回", ["class" => "am-btn am-btn-primary", "onclick" => "returnLast();"]);
                                                        return $html;
                                                    }
                                                        ],
                                                    ]
                                                ]);
                                                echo "</form>";
                                                ?>
<script type="text/javascript">
    function rateChange() {
        var rate = $("#rate").val();
        if (rate == "每周") {
            $(".weekcontainer").show();
            $(".timecontainer").show();
            $("#changci").parents("tr").hide();
        } else if (rate == "每天") {
            $(".weekcontainer").hide();
            $(".timecontainer").show();
            $("#changci").parents("tr").hide();
        } else {
            $(".weekcontainer").show();
            $(".timecontainer").show();
            $("#changci").parents("tr").show();
        }
    }
    function submitForm() {
            err = 0;
            $(".need").each(function(i){
            var text = $(this).val();
            if(text ==""){
                   err++;
                   $(this).focus();
                   $("#msg").empty();
                   h = '<span id="msg" style="color:red;">请填写此字段</span>';
                   $(this).after(h);
                   return false;
            }
            });
            if(err != 0){
                return false;
            }
        var data = $("#lottimeform").serializeArray();
        $.ajax({
            url: "/lottery/time/savedata",
            type: "post",
            data: data,
            dataType: "json",
            success: function (json) {
                if (json["code"] == 1) {
                    msgAlert(json["msg"], function () {
                        location.href = "/lottery/time/";
                    });
                } else {
                    msgAlert(json["msg"]);
                }
            }
        })
//        msgAlert(lotterycode);
    }
    function returnLast() {
        location.href = "/lottery/time/";
    }
    $(function () {
        $("input[name=start_time]").setTime();
        $("input[name=stop_time]").setTime();
        $("input[name=limit_time]").setTime();
        rateChange();
        $("#rate").change(function () {
            rateChange();
        });
    });
</script>

