<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

echo "<form id='scheduleForm'>";
echo Html::input("hidden", "schedule_id", $data["schedule"]["schedule_id"]);
echo DetailView::widget([
    "model" => $data,
    "attributes" => [
        [
            'label' => '期号<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input("text", "periods", $model["schedule"]["periods"], ["class" => "form-control need"]);
            }
                ], [
                    'label' => '赛事编码<span class="requiredIcon">*</span>',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input("text", "schedule_code", $model["schedule"]["schedule_code"], ["class" => "form-control need"]);
                    }
                        ], [
                            'label' => '赛事唯一mid<span class="requiredIcon">*</span>',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input("text", "schedule_mid", $model["schedule"]["schedule_mid"], ["class" => "form-control need"]);
                            }
                                ], [
                                    'label' => '比赛开始时间<span class="requiredIcon">*</span>',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        $html = Html::input('text', "start_time", $model["schedule"]["start_time"], ["class" => "form-control start_time need", "placeholder" => "开始日期时间", "style" => "width:200px;display:inline;margin-left:5px;"]);
                                        return $html;
                                    }
                                        ], [
                                            'label' => '开售时间<span class="requiredIcon">*</span>',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $html = Html::input('text', "beginsale_time", $model["schedule"]["beginsale_time"], ["class" => "form-control beginsale_time need", "placeholder" => "开始日期时间", "style" => "width:200px;display:inline;margin-left:5px;"]);
                                                return $html;
                                            }
                                                ], [
                                                    'label' => '停售时间<span class="requiredIcon">*</span>',
                                                    'format' => 'raw',
                                                    'value' => function($model) {
                                                        $html = Html::input('text', "endsale_time", $model["schedule"]["endsale_time"], ["class" => "form-control endsale_time need", "placeholder" => "开始日期时间", "style" => "width:200px;display:inline;margin-left:5px;"]);
                                                        return $html;
                                                    }
                                                        ], [
                                                            'label' => '联赛<span class="requiredIcon">*</span>',
                                                            'format' => 'raw',
                                                            'value' => function($model) {
                                                                return Html::dropDownList("league", $model["schedule"]["league_id"], $model["leagues"], ["class" => "form-control need", "id" => "league"]);
                                                            }
                                                                ], [
                                                                    'label' => '主队<span class="requiredIcon">*</span>',
                                                                    'format' => 'raw',
                                                                    'value' => function($model) {
                                                                        return Html::dropDownList("team_1", $model["schedule"]["home_team_id"], $model["team"], ["class" => "form-control need", "id" => "team_1"]);
                                                                    }
                                                                        ], [
                                                                            'label' => '客队<span class="requiredIcon">*</span>',
                                                                            'format' => 'raw',
                                                                            'value' => function($model) {
                                                                                return Html::dropDownList("team_2", $model["schedule"]["visit_team_id"], $model["team"], ["class" => "form-control need", "id" => "team_2"]);
                                                                            }
                                                                                ], [
                                                                                    'label' => '让球数<span class="requiredIcon">*</span>',
                                                                                    'format' => 'raw',
                                                                                    'value' => function($model) {
                                                                                        return Html::input("text", "rq_nums", $model["schedule"]["rq_nums"], ["class" => "form-control need", "id" => "rq_nums"]);
                                                                                    }
                                                                                        ], [
                                                                                            'label' => '胜平负<span class="requiredIcon">*</span>',
                                                                                            'format' => 'raw',
                                                                                            'value' => function($model) {
                                                                                                return Html::dropDownList("schedule_spf", $model["schedule"]["schedule_spf"], $model["items"], ["class" => "form-control need", "id" => "team_2"]);
                                                                                            }
                                                                                                ], [
                                                                                                    'label' => '让球胜平负<span class="requiredIcon">*</span>',
                                                                                                    'format' => 'raw',
                                                                                                    'value' => function($model) {
                                                                                                        return Html::dropDownList("schedule_rqspf", $model["schedule"]["schedule_rqspf"], $model["items"], ["class" => "form-control need", "id" => "team_2"]);
                                                                                                    }
                                                                                                        ], [
                                                                                                            'label' => '比分<span class="requiredIcon">*</span>',
                                                                                                            'format' => 'raw',
                                                                                                            'value' => function($model) {
                                                                                                                return Html::dropDownList("schedule_bf", $model["schedule"]["schedule_bf"], $model["items"], ["class" => "form-control need", "id" => "team_2"]);
                                                                                                            }
                                                                                                                ], [
                                                                                                                    'label' => '总进球数<span class="requiredIcon">*</span>',
                                                                                                                    'format' => 'raw',
                                                                                                                    'value' => function($model) {
                                                                                                                        return Html::dropDownList("schedule_zjqs", $model["schedule"]["schedule_zjqs"], $model["items"], ["class" => "form-control need", "id" => "team_2"]);
                                                                                                                    }
                                                                                                                        ], [
                                                                                                                            'label' => '半全场胜平负<span class="requiredIcon">*</span>',
                                                                                                                            'format' => 'raw',
                                                                                                                            'value' => function($model) {
                                                                                                                                return Html::dropDownList("schedule_bqcspf", $model["schedule"]["schedule_bqcspf"], $model["items"], ["class" => "form-control need", "id" => "team_2"]);
                                                                                                                            }
                                                                                                                                ], [
                                                                                                                                    'label' => '是否热门<span class="requiredIcon">*</span>',
                                                                                                                                    'format' => 'raw',
                                                                                                                                    'value' => function($model) {
                                                                                                                                        return Html::radioList('hot_status', $model["schedule"]['hot_status'], ['1' => '是', '0' => '否']);
                                                                                                                                    }
                                                                                                                                        ], [
                                                                                                                                            'label' => '是否高中奖<span class="requiredIcon">*</span>',
                                                                                                                                            'format' => 'raw',
                                                                                                                                            'value' => function($model) {
                                                                                                                                                return Html::radioList('high_win_status', $model["schedule"]['high_win_status'], ['1' => '是', '0' => '否']);
                                                                                                                                            }
                                                                                                                                                ], [
                                                                                                                                                    'label' => '操作',
                                                                                                                                                    'format' => 'raw',
                                                                                                                                                    'value' => function($model) {
                                                                                                                                                        $html = Html::button("保存", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "submitschedule();"]);
                                                                                                                                                        $html .= Html::button("保存并设置固定奖金", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "submitschedule(true);"]);
                                                                                                                                                        $html .= Html::button("取消", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "location.href='/lottery/schedule/'"]);
                                                                                                                                                        return $html;
                                                                                                                                                    }
                                                                                                                                                        ]
                                                                                                                                                    ]
                                                                                                                                                ]);
                                                                                                                                                echo "</form>";
                                                                                                                                                ?>
<script type="text/javascript">
    function submitschedule(goBonus) {
        err = 0;
        $(".need").each(function (i) {
                        var text = $(this).val();
                        if (text == "") {
                err++;
                $(this).focus();
                $("#msg").empty();
                h = '<span id="msg" style="color:red;">请填写此字段</span>';
                $(this).after(h);
                return false;
                        }
        });
        if (err != 0) {
            return false;
        }
        goBonus = goBonus || false;
        var data = $("#scheduleForm").serialize();
        $.ajax({
            url: "/lottery/schedule/saveschedule",
            type: "post",
            data: data,
            dataType: "json",
            success: function (json) {
                if (json["code"] == 0) {
                    msgAlert(json["msg"], function () {
                        if (goBonus == false) {
                            location.href = "/lottery/schedule/";
                        } else {
                            location.href = "/lottery/schedule/readbonus?schedule_id=" + json["result"];
                        }
                    });
                } else {
                    console.log(json);
                    msgAlert(json["msg"]);
                }
            }
        });
    }
    function proOption(arr) {
        var html = "";
        $.each(arr, function (key, val) {
            html += "<option value='" + key + "'>" + val + "</option>";
        })
        return html;
    }
    $(function () {
        jeDate({
            dateCell: ".start_time",
            format: "YYYY-MM-DD hh:mm:ss",
            isTime: false,
            minDate: "2000-09-19 00:00:00"
        });
        jeDate({
            dateCell: ".endsale_time",
            format: "YYYY-MM-DD hh:mm:ss",
            isinitVal: false,
            isTime: true,
            minDate: "2000-09-19 00:00:00"
        });
        jeDate({
            dateCell: ".beginsale_time",
            format: "YYYY-MM-DD hh:mm:ss",
            isinitVal: false,
            isTime: true,
            minDate: "2000-09-19 00:00:00"
        });
        $("#league").change(function () {
            var league = $(this).val();
            if (league == 0) {
                return false;
            }
            $.ajax({
                url: "/lottery/schedule/getleague_team",
                type: "post",
                data: {league: league},
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 0) {
                        $("#team_1").attr("disabled", false);
                        $("#team_2").attr("disabled", false);
                        $("#team_1").html(proOption(json["result"]));
                        $("#team_2").html(proOption(json["result"]));
                    } else {
                        console.log(json);
                    }
                }
            });
        });
    });
</script>

