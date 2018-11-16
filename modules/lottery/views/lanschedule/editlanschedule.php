<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

echo "<form id='scheduleForm'>";
echo Html::input("hidden", "lan_schedule_id", $data["schedule"]["lan_schedule_id"]);
echo DetailView::widget([
    "model" => $data,
    "attributes" => [
        [
            'label' => '赛程日期<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input("text", "schedule_date", $model["schedule"]["schedule_date"], ["class" => "form-control need"]);
            }
                ],
                [
                    'label' => '赛事编号<span class="requiredIcon">*</span>',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input("text", "schedule_code", $model["schedule"]["schedule_code"], ["class" => "form-control need"]);
                    }
                        ],
                        [
                            'label' => '赛事唯一mid<span class="requiredIcon">*</span>',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input("text", "schedule_mid", $model["schedule"]["schedule_mid"], ["class" => "form-control need"]);
                            }
                                ],
                                [
                                    'label' => '比赛开始时间<span class="requiredIcon">*</span>',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        $html = Html::input('text', "start_time", $model["schedule"]["start_time"], ["class" => "form-control start_time need", "placeholder" => "开始日期时间", "style" => "width:200px;display:inline;margin-left:5px;"]);
                                        return $html;
                                    }
                                        ],
                                        [
                                            'label' => '开售时间<span class="requiredIcon">*</span>',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $html = Html::input('text', "beginsale_time", $model["schedule"]["beginsale_time"], ["class" => "form-control beginsale_time need", "placeholder" => "开始日期时间", "style" => "width:200px;display:inline;margin-left:5px;"]);
                                                return $html;
                                            }
                                                ],
                                                [
                                                    'label' => '停售时间<span class="requiredIcon">*</span>',
                                                    'format' => 'raw',
                                                    'value' => function($model) {
                                                        $html = Html::input('text', "endsale_time", $model["schedule"]["endsale_time"], ["class" => "form-control endsale_time need", "placeholder" => "开始日期时间", "style" => "width:200px;display:inline;margin-left:5px;"]);
                                                        return $html;
                                                    }
                                                        ],
                                                        [
                                                            'label' => '联赛<span class="requiredIcon">*</span>',
                                                            'format' => 'raw',
                                                            'value' => function($model) {
                                                                return Html::dropDownList("league", $model["schedule"]["league_id"], $model["leagues"], ["class" => "form-control need", "id" => "league"]);
                                                            }
                                                                ], [
                                                                    'label' => '客队<span class="requiredIcon">*</span>',
                                                                    'format' => 'raw',
                                                                    'value' => function($model) {
                                                                        return Html::dropDownList("visit", $model["schedule"]["visit_team_id"], $model["team"], ["class" => "form-control need", "id" => "visit"]);
                                                                    }
                                                                        ], [
                                                                            'label' => '主队<span class="requiredIcon">*</span>',
                                                                            'format' => 'raw',
                                                                            'value' => function($model) {
                                                                                return Html::dropDownList("home", $model["schedule"]["home_team_id"], $model["team"], ["class" => "form-control need", "id" => "home"]);
                                                                            }
                                                                                ],
                                                                                [
                                                                                    'label' => '胜负<span class="requiredIcon">*</span>',
                                                                                    'format' => 'raw',
                                                                                    'value' => function($model) {
                                                                                        return Html::dropDownList("schedule_sf", $model["schedule"]["schedule_sf"], $model["items"], ["class" => "form-control need", "id" => "schedule_sf"]);
                                                                                    }
                                                                                        ],
                                                                                        [
                                                                                            'label' => '让分胜负<span class="requiredIcon">*</span>',
                                                                                            'format' => 'raw',
                                                                                            'value' => function($model) {
                                                                                                return Html::dropDownList("schedule_rfsf", $model["schedule"]["schedule_rfsf"], $model["items"], ["class" => "form-control need", "id" => "schedule_rfsf"]);
                                                                                            }
                                                                                                ],
                                                                                                [
                                                                                                    'label' => '大小分<span class="requiredIcon">*</span>',
                                                                                                    'format' => 'raw',
                                                                                                    'value' => function($model) {
                                                                                                        return Html::dropDownList("schedule_dxf", $model["schedule"]["schedule_dxf"], $model["items"], ["class" => "form-control need", "id" => "schedule_dxf"]);
                                                                                                    }
                                                                                                        ],
                                                                                                        [
                                                                                                            'label' => '胜负差<span class="requiredIcon">*</span>',
                                                                                                            'format' => 'raw',
                                                                                                            'value' => function($model) {
                                                                                                                return Html::dropDownList("schedule_sfc", $model["schedule"]["schedule_sfc"], $model["items"], ["class" => "form-control need", "id" => "schedule_sfc"]);
                                                                                                            }
                                                                                                                ],
                                                                                                                [
                                                                                                                    'label' => '是否热门<span class="requiredIcon">*</span>',
                                                                                                                    'format' => 'raw',
                                                                                                                    'value' => function($model) {
                                                                                                                        return Html::radioList('hot_status', $model["schedule"]['hot_status'], ['1' => '是', '0' => '否']);
                                                                                                                    }
                                                                                                                        ],
                                                                                                                        [
                                                                                                                            'label' => '是否高中奖<span class="requiredIcon">*</span>',
                                                                                                                            'format' => 'raw',
                                                                                                                            'value' => function($model) {
                                                                                                                                return Html::radioList('high_win_status', $model["schedule"]['high_win_status'], ['1' => '是', '0' => '否']);
                                                                                                                            }
                                                                                                                                ],
                                                                                                                                [
                                                                                                                                    'label' => '操作',
                                                                                                                                    'format' => 'raw',
                                                                                                                                    'value' => function($model) {
                                                                                                                                        $html = Html::button("保存", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "submitschedule();"]);
//                $html .= Html::button("保存并设置固定奖金", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "submitschedule(true);"]);
                                                                                                                                        $html .= Html::button("取消", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "history.go(-1)"]);
                                                                                                                                        return $html;
                                                                                                                                    }
                                                                                                                                        ]
                                                                                                                                    ]
                                                                                                                                ]);
                                                                                                                                echo "</form>";
                                                                                                                                ?>
<script>
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
    });
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
            url: "/lottery/lanschedule/save-lan-schedule",
            type: "post",
            data: data,
            dataType: "json",
            success: function (json) {
                if (json["code"] == 600) {
                    msgAlert(json["msg"], function () {
                         closeMask();
                         window._location.reload();
//                        if (goBonus == false) {
//                            location.href = "/lottery/lanschedule/";
//                        } else {
//                            location.href = "/lottery/lanschedule/readlanbonus?schedule_mid=" + json["result"];
//                        }
                    });
                } else {
                    msgAlert(json["msg"]);
                }
            }
        });
    }
</script>

