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

echo Html::tag("label", "客队:", ["class" => "bold"]);
echo Html::tag("span", $data["schedule"]["visit_short_name"], ["class" => "buttomspan"]);

echo Html::tag("label", "主队:", ["class" => "bold"]);
echo Html::tag("span", $data["schedule"]["home_short_name"], ["class" => "buttomspan"]);
echo Html::button('返回', ['class' => 'am-btn am-btn-primary', 'id' => 'reback', 'style' => 'float:right']);
echo "</div>";
echo Html::input("hidden", "schedule_mid");

echo Html::input("hidden", "schedule_mid", $data["schedule"]["schedule_mid"], ["id" => "schedule_mid"]);

echo "<div class='divContainer' id='scheduleResult'>";
echo Html::tag("label", "单场开奖结果", ["class" => "title"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);

echo GridView::widget([
    "dataProvider" => $data["lan_schedule_result"],
    "columns" => [
        [
            "label" => "玩法",
            "value" => function(){
                return "开奖结果";
            }
        ], [
            "label" => "胜负",
            "value" => function($model) {
                return ($model["result_3001"] == "0" ? "主负" : ($model["result_3001"] == "3" ? "主胜" : "未开奖"));
            }
        ], [
            "label" => "让分胜负",
            "value" => function($model) {
               return ($model["result_3002"] == "0" ? "让分主负" : ($model["result_3002"] == "3" ? "让分主胜" : "未开奖"));
            }
        ], [
            "label" => "胜分差",
            "value" => function($model) {
                if (isset($model["result_3003"]) && $model["result_3003"] != null) {
                    switch ($model["result_3003"]) {
                                case "01":
                                    return "主胜1-5";
                                    break;
                                case "02":
                                    return "主胜6-10";
                                    break;
                                case "03":
                                    return "主胜11-15";
                                    break;
                                case "04":
                                    return "主胜16-20";
                                    break;
                                case "05":
                                    return "主胜21-25";
                                    break;
                                case "06":
                                    return "主胜26+";
                                    break;
                                case "11":
                                    return "客胜1-5";
                                    break;
                                case "12":
                                    return "客胜6-10";
                                    break;
                                case "13":
                                    return "客胜11-15";
                                    break;
                                case "14":
                                    return "客胜16-20";
                                    break;
                                case "15":
                                    return "客胜21-25";
                                    break;
                                case "16":
                                    return "客胜26+";
                                    break;
                        } 
                }else {
                    return "未开奖";
                }
            }
        ], [
            "label" => "大小分",
            "value" => function($model) {
                if (isset($model["result_3004"]) && $model["result_3004"] != null) {
                    return ($model["result_3004"]==1?"大分":"小分");
                } else {
                    return "未开奖";
                }
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function($model) {
                if($model["result_3001"]==""&&$model["result_3002"]==""&&$model["result_3003"]==""&&$model["result_3004"]==""){
                    return "";
                }else{
                     return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editResult();">编辑</span>
                        </div>';
                }
               
            }
        ]
    ]
]);
echo "</div>";

echo "<div class='divContainer' id='sfBonus'>";
echo Html::tag("label", "胜负过关固定奖金", ["class" => "title"]);
echo Html::tag("span", Html::button("新增", ["class" => "am-btn am-btn-primary", "onclick" => "addSfBonus();"]), ["class" => "floatRight buttomspan"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);

echo GridView::widget([
    "dataProvider" => $data["3001"],
    "columns" => [
        [
            "label" => "更新时间",
            "value" => "update_time"
        ], [
            "label" => "更新次数",
            "value" => "update_nums"
        ], [
            "label" => "主胜",
            "format" => "raw",
            "value" => function($model) {
                return $model["wins_3001"] . updownHtml($model["wins_trend"]);
            }
        ],[
            "label" => "主负",
            "format" => "raw",
            "value" => function($model) {
                return $model["lose_3001"] . updownHtml($model["lose_trend"]);
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editSfBonus(' . $model["odds_3001_id"] . ')">编辑</span>
                            <span class="handle pointer" onclick="delSfBonus(' . $model["odds_3001_id"] . ')">删除</span>
                        </div>';
            }
        ]
    ]
]);
echo "</div>";


echo "<div class='divContainer' id='rfsfBonus'>";
echo Html::tag("label", "让分胜负过关固定奖金(所有玩法固定奖金以购买时票面显示奖金为准)", ["class" => "title"]);
echo Html::tag("span", Html::button("新增", ["class" => "am-btn am-btn-primary", "onclick" => "addRfsfBonus();"]), ["class" => "floatRight buttomspan"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);

echo GridView::widget([
    "dataProvider" => $data["3002"],
    "columns" => [
        [
            "label" => "更新时间",
            "value" => "update_time"
        ], [
            "label" => "更新次数",
            "value" => "update_nums"
        ], [
            "label" => "让分数",
            "value" => "rf_nums"
        ], [
            "label" => "让分主胜",
            "format" => "raw",
            "value" => function($model) {
                return $model["wins_3002"] . updownHtml($model["wins_trend"]);
            }
        ], [
            "label" => "让分主负",
            "format" => "raw",
            "value" => function($model) {
                return $model["lose_3002"] . updownHtml($model["lose_trend"]);
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editRfsfBonus(' . $model["odds_3002_id"] . ')">编辑</span>
                            <span class="handle pointer" onclick="delRfsfBonus(' . $model["odds_3002_id"] . ')">| 删除</span>
                        </div>';
            }
        ]
    ]
]);
echo "</div>";

echo "<div class='divContainer' id='sfcBonus'>";
echo Html::tag("label", "胜分差过关固定奖金", ["class" => "title"]);
echo Html::tag("span", Html::button("新增", ["class" => "am-btn am-btn-primary", "onclick" => "addSfcBonus();"]), ["class" => "floatRight buttomspan"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo GridView::widget([
    "dataProvider" => $data["3003"],
    'tableOptions' => [
        "class" => "table table-bordered"
    ],
    "columns" => [
        [
            "label" => "更新时间",
            "value" => "update_time"
        ], [
            "label" => "更新次数",
            "value" => "update_nums"
        ], [
            "label" => "",
            "format" => "raw",
            "value" => function($model) {
                $html = "<table class='tableBorder'>";
                $html .= "<tr><th>主胜1-5</th><th>主胜6-10</th><th>主胜11-15</th><th>主胜16-20</th><th>主胜21-25</th><th>主胜26+</th><th>主负1-5</th><th>主负6-10</th><th>主负11-15</th><th>主负16-20</th><th>主负21-25</th><th>主负26+</th></tr>";
                $html .= "<tr><td>{$model['cha_01']}".updownHtml($model['cha_01_trend'])."</td><td>{$model['cha_02']}".updownHtml($model['cha_02_trend'])."</td><td>{$model['cha_03']}".updownHtml($model['cha_03_trend'])."</td><td>{$model['cha_04']}".updownHtml($model['cha_04_trend'])."</td><td>{$model['cha_05']}".updownHtml($model['cha_05_trend'])."</td><td>{$model['cha_06']}".updownHtml($model['cha_06_trend'])."</td><td>{$model['cha_11']}".updownHtml($model['cha_11_trend'])."</td><td>{$model['cha_12']}".updownHtml($model['cha_12_trend'])."</td><td>{$model['cha_13']}".updownHtml($model['cha_13_trend'])."</td><td>{$model['cha_14']}".updownHtml($model['cha_14_trend'])."</td><td>{$model['cha_15']}".updownHtml($model['cha_15_trend'])."</td><td>{$model['cha_16']}".updownHtml($model['cha_16_trend'])."</td></tr>";
                $html .= "</table>";
                return $html;
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editSfcBonus(' . $model["odds_3003_id"] . ')">编辑</span>
                            <span class="handle pointer" onclick="delSfcBonus(' . $model["odds_3003_id"] . ')">删除</span>
                        </div>';
            }
        ]
    ]
]);
echo "</div>";

echo "<div class='divContainer' id='zjqsBonus'>";
echo Html::tag("label", "大小分过关固定奖金", ["class" => "title"]);
echo Html::tag("span", Html::button("新增", ["class" => "am-btn am-btn-primary", "onclick" => "addDxfBonus();"]), ["class" => "floatRight buttomspan"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);

echo GridView::widget([
    "dataProvider" => $data["3004"],
    "columns" => [
        [
            "label" => "更新时间",
            "value" => "update_time"
        ], [
            "label" => "更新次数",
            "value" => "update_nums"
        ],  [
            "label" => "大小分切割点",
            "value" => "fen_cutoff"
        ],[
            "label" => "大分",
            "format" => "raw",
            "value" => function($model) {
                return $model["da_3004"] . updownHtml($model["da_3004_trend"]);
            }
        ], [
            "label" => "小分",
            "format" => "raw",
            "value" => function($model) {
                return $model["xiao_3004"] . updownHtml($model["xiao_3004_trend"]);
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editDxfBonus(' . $model["odds_3004_id"] . ')">编辑</span>
                            <span class="handle pointer" onclick="delDxfBonus(' . $model["odds_3004_id"] . ')">| 删除</span>
                        </div>';
            }
        ]
    ]
]);
echo "</div>";

echo "</div>";
?>

<script>
    //返回上一页
    $("#reback").click(function () {
         history.go(-1);
    });
    //编辑修改赛果
    function editResult(){
        var mid =$("#schedule_mid").val();
        var url = '/lottery/lanschedule/editlanresult?schedule_mid=' + mid;
        window.parent._location = location;
        window.parent.modDisplay({title: "修改赛程结果", width: 400, height: 350, url: url});
    }
    //胜负赔率操作
    function addSfBonus(){
        var mid =$("#schedule_mid").val();
        var url = '/lottery/lanschedule/addodds3001?schedule_mid=' + mid;
        window.parent._location = location;
        window.parent.modDisplay({title: "新增胜负过关固定奖金", width: 400, height: 280, url: url});
    }
    function editSfBonus(oid) {
        var url = '/lottery/lanschedule/editodds3001?odds_3001_id=' + oid;
        window.parent._location = location;
        window.parent.modDisplay({title: "修改胜负过关固定奖金", width: 400, height: 280, url: url});
    }
    function delSfBonus(oid) {
        msgConfirm("提示", "确定删除该项？", function () {
            $.ajax({
                url: "/lottery/lanschedule/deleteodds3001",
                data: {odds_3001_id: oid},
                type: "post",
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            closeMask();
                            window.location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
    }
    //让分胜负赔率操作
    function addRfsfBonus(){
        var mid =$("#schedule_mid").val();
        var url = '/lottery/lanschedule/addodds3002?schedule_mid=' + mid;
        window.parent._location = location;
        window.parent.modDisplay({title: "新增让分胜负过关固定奖金", width: 400, height: 280, url: url});
    }
    function editRfsfBonus(oid) {
        var url = '/lottery/lanschedule/editodds3002?odds_3002_id=' + oid;
        window.parent._location = location;
        window.parent.modDisplay({title: "修改让分胜负过关固定奖金", width: 400, height: 280, url: url});
    }
    function delRfsfBonus(oid) {
        msgConfirm("提示", "确定删除该项？", function () {
            $.ajax({
                url: "/lottery/lanschedule/deleteodds3002",
                data: {odds_3002_id: oid},
                type: "post",
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            closeMask();
                            window.location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
    }
    //胜分差赔率操作
    function addSfcBonus(){
        var mid =$("#schedule_mid").val();
        var url = '/lottery/lanschedule/addodds3003?schedule_mid=' + mid;
        window.parent._location = location;
        window.parent.modDisplay({title: "新增胜分差过关固定奖金", width: 400, height: 500, url: url});
    }
    function editSfcBonus(oid) {
        var url = '/lottery/lanschedule/editodds3003?odds_3003_id=' + oid;
        window.parent._location = location;
        window.parent.modDisplay({title: "修改胜分差过关固定奖金", width: 400, height: 500, url: url});
    }
    function delSfcBonus(oid) {
        msgConfirm("提示", "确定删除该项？", function () {
            $.ajax({
                url: "/lottery/lanschedule/deleteodds3003",
                data: {odds_3003_id: oid},
                type: "post",
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            closeMask();
                            window.location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
    }
     //大小分赔率操作
    function addDxfBonus(){
        var mid =$("#schedule_mid").val();
        var url = '/lottery/lanschedule/addodds3004?schedule_mid=' + mid;
        window.parent._location = location;
        window.parent.modDisplay({title: "新增让分胜负过关固定奖金", width: 400, height: 280, url: url});
    }
    function editDxfBonus(oid) {
        var url = '/lottery/lanschedule/editodds3004?odds_3004_id=' + oid;
        window.parent._location = location;
        window.parent.modDisplay({title: "修改让分胜负过关固定奖金", width: 400, height: 280, url: url});
    }
    function delDxfBonus(oid) {
        msgConfirm("提示", "确定删除该项？", function () {
            $.ajax({
                url: "/lottery/lanschedule/deleteodds3004",
                data: {odds_3004_id: oid},
                type: "post",
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            closeMask();
                            window.location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
    }
</script>