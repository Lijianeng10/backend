<?php

use yii\grid\GridView;
use yii\helpers\Html;

function updownHtml($data) {
    if ($data == "0") {
        return "";
    } else if ($data == "-1") {
        return Html::tag("i", "", ["class" => "am-icon-long-arrow-down", "style" => "color:green;"]);
    } else if ($data == "1") {
        return Html::tag("i", "", ["class" => "am-icon-long-arrow-up", "style" => "color:red;"]);
    }
}

echo "<div class='divContainer'>";
echo Html::tag("label", "赛程基本信息", ["class" => "title"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);

echo Html::tag("label", "赛事编号:", ["class" => "bold"]);
echo Html::tag("span", $data["schedule"]["schedule_code"], ["class" => "buttomspan"]);

echo Html::tag("label", "比赛开始时间:", ["class" => "bold"]);
echo Html::tag("span", $data["schedule"]["start_time"], ["class" => "buttomspan"]);

echo Html::tag("label", "主队:", ["class" => "bold"]);
echo Html::tag("span", $data["team_long_name"]["0"], ["class" => "buttomspan"]);

echo Html::tag("label", "客队:", ["class" => "bold"]);
echo Html::tag("span", $data["team_long_name"]["1"], ["class" => "buttomspan"]);
echo Html::button('返回', ['class' => 'am-btn am-btn-primary', 'id' => 'reback', 'style' => 'float:right']);
echo "</div>";
echo Html::input("hidden", "schedule_id");

echo Html::input("hidden", "schedule_id", $data["schedule"]["schedule_id"], ["id" => "schedule_id"]);

echo "<div class='divContainer' id='scheduleResult'>";
echo Html::tag("label", "单场开奖结果", ["class" => "title"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);


echo GridView::widget([
    "dataProvider" => $data["schedule_result"],
    "columns" => [
        [
            "label" => "玩法",
            "value" => function() {
                return "开奖结果";
            }
        ], [
            "label" => "胜平负",
            "value" => function($model) {
                return ($model["schedule_result_3010"] == "0" ? "负" : ($model["schedule_result_3010"] == "1" ? "平" : ($model["schedule_result_3010"] == "3" ? "胜" : "未开奖")));
            }
        ], [
            "label" => "让球胜平负",
            "value" => function($model) {
                $vals = explode("|", $model["schedule_result_3006"]);
                if (!is_array($vals) || count($vals) < 2) {
                    return "未开奖";
                }
                return "({$vals[0]})" . ($vals[1] == "0" ? "负" : ($vals[1] == "1" ? "平" : ($vals[1] == "3" ? "胜" : "未开奖")));
            }
        ], [
            "label" => "猜比分",
            "value" => function($model) {
                if (isset($model["schedule_result_3007"]) && $model["schedule_result_3007"] != null) {
                    return $model["schedule_result_3007"];
                } else {
                    return "未开奖";
                }
            }
        ], [
            "label" => "总进球数",
            "value" => function($model) {
                if (isset($model["schedule_result_3008"]) && $model["schedule_result_3008"] != null) {
                    return $model["schedule_result_3008"];
                } else {
                    return "未开奖";
                }
            }
        ], [
            "label" => "半全场胜平负",
            "value" => function($model) {
                if (isset($model["schedule_result_3009"]) && $model["schedule_result_3009"] != null) {
                    $vals = explode("|", $model["schedule_result_3009"]);
                    if (!is_array($vals) || count($vals) < 2) {
                        return "未开奖";
                    }
                    return ($vals[0] == "0" ? "负" : ($vals[0] == "1" ? "平" : ($vals[0] == "3" ? "胜" : "未开奖"))) . ($vals[1] == "0" ? "负" : ($vals[1] == "1" ? "平" : ($vals[1] == "3" ? "胜" : "未开奖")));
                } else {
                    return "未开奖";
                }
            }
        ], 
//                [
//            "label" => "操作",
//            "format" => "raw",
//            "value" => function($model) {
//                return '<div class="am-btn-group am-btn-group-xs">
//                            <span class="handle pointer" onclick="editSpf();">编辑</span>
//                        </div>';
//            }
//        ]
    ]
]);
echo "</div>";



echo "<div class='divContainer' id='spfBonus'>";
echo Html::tag("label", "胜平负过关固定奖金", ["class" => "title"]);
echo Html::tag("span", Html::button("新增", ["class" => "am-btn am-btn-primary", "onclick" => "addSpfBonus($scheduleId);"]), ["class" => "floatRight buttomspan"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);

echo GridView::widget([
    "dataProvider" => $data["3010"],
    "columns" => [
        [
            "label" => "更新时间",
            "value" => "update_time"
        ], [
            "label" => "更新次数",
            "value" => "updates_nums"
        ], [
            "label" => "胜",
            "format" => "raw",
            "value" => function($model) {
                return $model["outcome_wins"] . updownHtml($model["outcome_wins_trend"]);
            }
        ], [
            "label" => "平",
            "format" => "raw",
            "value" => function($model) {
                return $model["outcome_level"] . updownHtml($model["outcome_level_trend"]);
            }
        ], [
            "label" => "负",
            "format" => "raw",
            "value" => function($model) {
                return $model["outcome_negative"] . updownHtml($model["outcome_negative_trend"]);
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editSpfBonus(' . $model["odds_outcome_id"] . ')">编辑</span>
                            <span class="handle pointer" onclick="delSpfBonus(' . $model["odds_outcome_id"] . ')">删除</span>
                        </div>';
            }
        ]
    ]
]);
echo "</div>";



echo "<div class='divContainer' id='rqspfBonus'>";
echo Html::tag("label", "让球胜平负过关固定奖金(所有玩法固定奖金以购买时票面显示奖金为准)", ["class" => "title"]);
echo Html::tag("span", Html::button("新增", ["class" => "am-btn am-btn-primary", "onclick" => "addRqspfBonus();"]), ["class" => "floatRight buttomspan"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);

echo GridView::widget([
    "dataProvider" => $data["3006"],
    "columns" => [
        [
            "label" => "更新时间",
            "value" => "update_time"
        ], [
            "label" => "更新次数",
            "value" => "updates_nums"
        ], [
            "label" => "让球",
            "value" => "let_ball_nums"
        ], [
            "label" => "胜",
            "format" => "raw",
            "value" => function($model) {
                return $model["let_wins"] . updownHtml($model["let_wins_trend"]);
            }
        ], [
            "label" => "平",
            "format" => "raw",
            "value" => function($model) {
                return $model["let_level"] . updownHtml($model["let_level_trend"]);
            }
        ], [
            "label" => "负",
            "format" => "raw",
            "value" => function($model) {
                return $model["let_negative"] . updownHtml($model["let_negative_trend"]);
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editRqspfBonus(' . $model["odds_let_id"] . ')">编辑</span>
                            <span class="handle pointer" onclick="deleteRqspfBonus(' . $model["odds_let_id"] . ')">| 删除</span>
                        </div>';
            }
        ]
    ]
]);
echo "</div>";





echo "<div class='divContainer' id='bfBonus'>";
echo Html::tag("label", "比分固定奖金", ["class" => "title"]);
echo Html::tag("span", Html::button("新增", ["class" => "am-btn am-btn-primary", "onclick" => "addBfBonus($scheduleId);"]), ["class" => "floatRight buttomspan"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo GridView::widget([
    "dataProvider" => $data["3007"],
    'tableOptions' => [
        "class" => "table table-bordered"
    ],
    "columns" => [
        [
            "label" => "更新时间",
            "value" => "update_time"
        ], [
            "label" => "更新次数",
            "value" => "updates_nums"
        ], [
            "label" => "",
            "format" => "raw",
            "value" => function($model) {
                $html = "<table class='tableBorder'>";
                $html .= "<tr><th>1:0</th><th>2:0</th><th>2:1</th><th>3:0</th><th>3:1</th><th>3:2</th><th>4:0</th><th>4:1</th><th>4:2</th><th>5:0</th><th>5:1</th><th>5:2</th><th>胜其他</th></tr>";
                $html .= "<tr><td>{$model['score_wins_10']}</td><td>{$model['score_wins_20']}</td><td>{$model['score_wins_21']}</td><td>{$model['score_wins_30']}</td><td>{$model['score_wins_31']}</td><td>{$model['score_wins_32']}</td><td>{$model['score_wins_40']}</td><td>{$model['score_wins_41']}</td><td>{$model['score_wins_42']}</td><td>{$model['score_wins_50']}</td><td>{$model['score_wins_51']}</td><td>{$model['score_wins_52']}</td><td>{$model['score_wins_90']}</td></tr>";
                $html .= "</table>";

                $html .= "<table class='tableBorder'>";
                $html .= "<tr><th>0:0</th><th>1:1</th><th>2:2</th><th>3:3</th><th>平其他</th></tr>";
                $html .= "<tr><td>{$model['score_level_00']}</td><td>{$model['score_level_11']}</td><td>{$model['score_level_22']}</td><td>{$model['score_level_33']}</td><td>{$model['score_level_99']}</td></tr>";
                $html .= "</table>";

                $html .= "<table class='tableBorder'>";
                $html .= "<tr><th>0:1</th><th>0:2</th><th>1:2</th><th>0:3</th><th>1:3</th><th>2:3</th><th>0:4</th><th>1:4</th><th>2:4</th><th>0:5</th><th>1:5</th><th>2:5</th><th>负其他</th></tr>";
                $html .= "<tr><td>{$model['score_negative_01']}</td><td>{$model['score_negative_02']}</td><td>{$model['score_negative_12']}</td><td>{$model['score_negative_03']}</td><td>{$model['score_negative_13']}</td><td>{$model['score_negative_23']}</td><td>{$model['score_negative_04']}</td><td>{$model['score_negative_14']}</td><td>{$model['score_negative_24']}</td><td>{$model['score_negative_05']}</td><td>{$model['score_negative_15']}</td><td>{$model['score_negative_25']}</td><td>{$model['score_negative_09']}</td></tr>";
                $html .= "</table>";
                return $html;
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editBfBonus(' . $model["odds_score_id"] . ')">编辑</span>
                            <span class="handle pointer" onclick="delBfBonus(' . $model["odds_score_id"] . ')">删除</span>
                        </div>';
            }
        ]
    ]
]);
echo "</div>";



echo "<div class='divContainer' id='zjqsBonus'>";
echo Html::tag("label", "总进球数过关固定奖金", ["class" => "title"]);
echo Html::tag("span", Html::button("新增", ["class" => "am-btn am-btn-primary", "onclick" => "addZjqsBonus();"]), ["class" => "floatRight buttomspan"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);

echo GridView::widget([
    "dataProvider" => $data["3008"],
    "columns" => [
        [
            "label" => "更新时间",
            "value" => "update_time"
        ], [
            "label" => "更新次数",
            "value" => "updates_nums"
        ], [
            "label" => "0",
            "format" => "raw",
            "value" => function($model) {
                return $model["total_gold_0"] . updownHtml($model["total_gold_0_trend"]);
            }
        ], [
            "label" => "1",
            "format" => "raw",
            "value" => function($model) {
                return $model["total_gold_1"] . updownHtml($model["total_gold_1_trend"]);
            }
        ], [
            "label" => "2",
            "format" => "raw",
            "value" => function($model) {
                return $model["total_gold_2"] . updownHtml($model["total_gold_2_trend"]);
            }
        ], [
            "label" => "3",
            "format" => "raw",
            "value" => function($model) {
                return $model["total_gold_3"] . updownHtml($model["total_gold_3_trend"]);
            }
        ], [
            "label" => "4",
            "format" => "raw",
            "value" => function($model) {
                return $model["total_gold_4"] . updownHtml($model["total_gold_4_trend"]);
            }
        ], [
            "label" => "5",
            "format" => "raw",
            "value" => function($model) {
                return $model["total_gold_5"] . updownHtml($model["total_gold_5_trend"]);
            }
        ], [
            "label" => "6",
            "format" => "raw",
            "value" => function($model) {
                return $model["total_gold_6"] . updownHtml($model["total_gold_6_trend"]);
            }
        ], [
            "label" => "7+",
            "format" => "raw",
            "value" => function($model) {
                return $model["total_gold_7"] . updownHtml($model["total_gold_7_trend"]);
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editZjqsBonus(' . $model["odds_3008_id"] . ')">编辑</span>
                            <span class="handle pointer" onclick="deleteZjqsBonus(' . $model["odds_3008_id"] . ')">| 删除</span>
                        </div>';
            }
        ]
    ]
]);
echo "</div>";



echo "<div class='divContainer' id='bqcspfBonus'>";
echo Html::tag("label", "半全场胜平负过关固定奖金", ["class" => "title"]);
echo Html::tag("span", Html::button("新增", ["class" => "am-btn am-btn-primary", "onclick" => "addBqcspfBonus();"]), ["class" => "floatRight buttomspan"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);

echo GridView::widget([
    "dataProvider" => $data["3009"],
    "columns" => [
        [
            "label" => "更新时间",
            "value" => "update_time"
        ], [
            "label" => "更新次数",
            "value" => "updates_nums"
        ], [
            "label" => "胜胜",
            "format" => "raw",
            "value" => function($model) {
                return $model["bqc_33"] . updownHtml($model["bqc_33_trend"]);
            }
        ], [
            "label" => "胜平",
            "format" => "raw",
            "value" => function($model) {
                return $model["bqc_31"] . updownHtml($model["bqc_31_trend"]);
            }
        ], [
            "label" => "胜负",
            "format" => "raw",
            "value" => function($model) {
                return $model["bqc_30"] . updownHtml($model["bqc_30_trend"]);
            }
        ], [
            "label" => "平胜",
            "format" => "raw",
            "value" => function($model) {
                return $model["bqc_13"] . updownHtml($model["bqc_13_trend"]);
            }
        ], [
            "label" => "平平",
            "format" => "raw",
            "value" => function($model) {
                return $model["bqc_11"] . updownHtml($model["bqc_11_trend"]);
            }
        ], [
            "label" => "平负",
            "format" => "raw",
            "value" => function($model) {
                return $model["bqc_10"] . updownHtml($model["bqc_10_trend"]);
            }
        ], [
            "label" => "负胜",
            "format" => "raw",
            "value" => function($model) {
                return $model["bqc_03"] . updownHtml($model["bqc_03_trend"]);
            }
        ], [
            "label" => "负平",
            "format" => "raw",
            "value" => function($model) {
                return $model["bqc_01"] . updownHtml($model["bqc_01_trend"]);
            }
        ], [
            "label" => "负负",
            "format" => "raw",
            "value" => function($model) {
                return $model["bqc_00"] . updownHtml($model["bqc_00_trend"]);
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editBqcspfBonus(' . $model["odds_3009_id"] . ')">编辑</span>
                            <span class="handle pointer" onclick="deleteBqcspfBonus(' . $model["odds_3009_id"] . ')">| 删除</span>
                        </div>';
            }
        ]
    ]
]);
echo "</div>";

echo "</div>";
?>

<script type="text/javascript">
    function addSpfBonus(ocId) {
        var url = '/lottery/schedule/addodds3010?schedule_id=' + ocId;
        window.parent._location = location;
        window.parent.modDisplay({title: "新增胜平负过关固定奖金", width: 400, height: 280, url: url});
    }
    function editSpfBonus(ocId){
        location.href = '/lottery/schedule/editodds3010?oc_id=' + ocId;
    }
    function delSpfBonus(ocId) {
        msgConfirm("提示", "确定删除该项？", function () {
            $.ajax({
                url: "/lottery/schedule/deleteodds3010",
                data: {oc_id: ocId},
                type: "post",
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 0) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
    }

    function addBfBonus(scoreId) {
        location.href = '/lottery/schedule/addodds3007?schedule_id=' + scoreId;
    }

    function editBfBonus(scoreId) {
        location.href = '/lottery/schedule/editodds3007?score_id=' + scoreId;
    }
    function delBfBonus(scoreId) {
        msgConfirm("提示", "确定删除该项？", function () {
            $.ajax({
                url: "/lottery/schedule/deleteodds3007",
                data: {score_id: scoreId},
                type: "post",
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 0) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
    }

    $(function () {
        $("#reback").click(function () {
            location.href = "/lottery/schedule/index";
        });
    });
</script>
<script type="text/javascript">
    var schedule_id =<?php echo $data["schedule"]["schedule_id"]; ?>;
//    function editSpf() {
//        location.href = "/lottery/schedule/editscheduleresult?schedule_id=" + schedule_id;
//    }
    function addRqspfBonus() {
        var url = "/lottery/schedule/addrqspfbonus?schedule_id=" + schedule_id;
        window.parent._location = location;
        window.parent.modDisplay({title: "新增让球胜平负过关固定奖金", width: 400, height: 320, url: url});
//        location.href = "/lottery/schedule/addrqspfbonus?schedule_id=" + schedule_id;
    }
    function addZjqsBonus() {
        var url = "/lottery/schedule/addzjqsbonus?schedule_id=" + schedule_id;
        window.parent._location = location;
        window.parent.modDisplay({title: "新增总进球数过关固定奖金", width: 400, height: 520, url: url});
//        location.href = "/lottery/schedule/addzjqsbonus?schedule_id=" + schedule_id;
    }
    function addBqcspfBonus() {
        var url = "/lottery/schedule/addbqcspfbonus?schedule_id=" + schedule_id;
        window.parent._location = location;
        window.parent.modDisplay({title: "新增半全场胜平负过关固定奖金", width: 400, height: 560, url: url});
//        location.href = "/lottery/schedule/addbqcspfbonus?schedule_id=" + schedule_id;
    }
    function editRqspfBonus(odds_let_id) {
        location.href = "/lottery/schedule/editrqspfbonus?odds_let_id=" + odds_let_id;
    }
    function editZjqsBonus(odds_3008_id) {
        location.href = "/lottery/schedule/editzjqsbonus?odds_3008_id=" + odds_3008_id;
    }
    function editBqcspfBonus(odds_3009_id) {
        location.href = "/lottery/schedule/editbqcspfbonus?odds_3009_id=" + odds_3009_id;
    }

    function deleteRqspfBonus(odds_let_id) {
        msgConfirm("提示", "确定删除该项？", function () {
            $.ajax({
                url: "/lottery/schedule/deleterqspfbonus",
                data: {odds_let_id: odds_let_id},
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


    function deleteZjqsBonus(odds_3008_id) {
        msgConfirm("提示", "确定删除该项？", function () {
            $.ajax({
                url: "/lottery/schedule/deletezjqsbonus",
                data: {odds_3008_id: odds_3008_id},
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

    function deleteBqcspfBonus(odds_3009_id) {
        msgConfirm("提示", "确定删除该项？", function () {
            $.ajax({
                url: "/lottery/schedule/deletebqcspfbonus",
                data: {odds_3008_id: odds_3009_id},
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
</script>
