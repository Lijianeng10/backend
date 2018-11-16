<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\helpers\Constants;

echo '<ul class="nav nav-tabs" style="margin-bottom:10px;">
  <li flag=1 role="presentation"' . ((!isset($_GET['code']) || $_GET['code'] == 1) ? ' class="active"' : '') . '><a href="/lottery/schresult?code=1">竞彩足球</a></li>
  <li flag=2 role="presentation"' . ((isset($_GET['code']) && $_GET['code'] == 2) ? ' class="active"' : '') . '><a href="/lottery/schresult?code=2">竞彩篮球</a></li>
  <li flag=3 role="presentation"' . ((isset($_GET['code']) && $_GET['code'] == 3) ? ' class="active"' : '') . '><a href="/lottery/schresult?code=3">北京单场</a></li>
</ul>';
?>
<div style="font-size:14px;">
    <?php
    $result_status = ["0" => "请选择", "1" => "比赛中", "2" => "完结", "3" => "取消", "4" => "延迟", "5" => "比赛结果不正确", "6" => "比赛未出结果", "7" => "腰斩"];
    echo '<form>';
    echo "<ul>";
    echo '<li style="display:inline-block">';
    echo Html::label("赛事信息", "schedule_mid", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "schedule_mid", isset($get["schedule_mid"]) ? $get["schedule_mid"] : "", ["id" => "schedule_mid", "class" => "form-control", "placeholder" => "赛事MID、赛程编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li style="display:inline-block">';
    echo Html::label("比赛时间  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("text", "schedule_date", isset($get["schedule_date"]) ? $get["schedule_date"] : "", ["id" => "schedule_date", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "比赛时间", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li style="display:inline-block">';
    echo Html::label("是否开奖  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("result_status", isset($get["result_status"]) ? $get["result_status"] : "", $result_status, ["id" => "result_status", "class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li style="display:inline-block">';
    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;", "onclick" => "search();"]);
    echo Html::input("reset", "", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goIndex();"]);
    echo '</li>';
    echo "</ul>";
    echo '</form>';
    ?>
</div>

<div style="font-size:14px;">
    <?php
    if (!isset($_GET['code']) || $_GET['code'] == 1) {
        echo GridView::widget([
            "dataProvider" => $data,
            "columns" => [
                [ 'class' => 'yii\grid\SerialColumn'],
                [
                    'label' => '赛事MID',
                    'value' => 'schedule_mid'
                ],
                [
                    'label' => '赛程日期',
                    'value' => 'schedule_date'
                ], [
                    'label' => '赛程编号',
                    'value' => 'schedule_code'
                ], [
                    'label' => '联赛名称',
                    'value' => 'league_name'
                ],
                [
                    'label' => '主队',
                    'value' => "home_short_name"
                ], [
                    'label' => '客队',
                    'value' => 'visit_short_name'
                ], [
                    'label' => '胜平负',
                    'value' => function($model) {
                        $res = ["0" => "负", "1" => "平", "3" => "胜"];
                        return isset($model['schedule_result_3010']) ? $res[$model['schedule_result_3010']] : "";
                    }
                        ], [
                            'label' => '让球胜平负',
                            'value' => function($model) {
                                $res = ["0" => "负", "1" => "平", "3" => "胜"];
                                return isset($model['schedule_result_3006']) && $model['schedule_result_3006'] != "" ? "让球" . $res[$model['schedule_result_3006']] : "";
                            }
                                ], [
                                    'label' => '比分',
                                    'value' => 'schedule_result_3007'
                                ], [
                                    'label' => '总进球数',
                                    'value' => 'schedule_result_3008'
                                ], [
                                    'label' => '半全场胜平负',
                                    'value' => function($model) {
                                        $res = ["0" => "负", "1" => "平", "3" => "胜"];
                                        $str = "";
                                        for ($i = 0; $i < strlen($model['schedule_result_3009']); $i++) {
                                            $str.=$res[$model['schedule_result_3009'][$i]];
                                        }
                                        return isset($model['schedule_result_3009']) ? $str : "";
                                    }
                                        ], [
                                            'label' => '上半场比分',
                                            'value' => 'schedule_result_sbbf'
                                        ], [
                                            'label' => '是否开奖',
                                            'value' => function($model) {
                                                $res = ["1" => "比赛中", "2" => "完结", "3" => "取消", "4" => "延迟", "5" => "比赛结果不正确", "6" => "比赛未出结果", "7" => "腰斩"];
                                                return $res[$model['status']];
                                            }
                                                ],
//            [
//                'label' => '是否已对奖',
//                'value' =>function($model){
//                    $res=["0"=>"未对奖","1"=>"已对奖"];
//                    return $res[$model['deal_status']];
//                }
//            ],
                                                [
                                                    'label' => '操作',
                                                    'format' => 'raw',
                                                    'value' => function($model) {
                                                        return ($model['status'] == 2 ? '<span class="handle pointer" onclick="deal(' . $model['schedule_mid'] . ',' . 3000 . ')">延迟出票对奖</span><span class="handle pointer" onclick="errorAward(' . $model["schedule_mid"] . ',' . 3000 . ')"> | 赛果错误重新对奖 </span>' : ($model['status'] == 3 ? '<span class="handle pointer" id="deal_' . $model['schedule_mid'] . '" onclick="dealOrder(' . $model["schedule_mid"] . ',' . 3000 . ')">相关订单修改赔率 </span><span class="handle pointer" id="award_' . $model['schedule_mid'] . '" style="display:none" onclick="dealAward(' . $model["schedule_mid"] . ',' . 3000 . ')">取消赛程相关订单对奖 </span>' : ''));
                                                    }
                                                ],
                                            ]
                                        ]);
                                    } else if (isset($_GET['code']) && $_GET['code'] == 2) {
                                        echo GridView::widget([
                                            "dataProvider" => $data,
                                            "columns" => [
                                                [ 'class' => 'yii\grid\SerialColumn'],
                                                [
                                                    'label' => '赛事MID',
                                                    'value' => 'schedule_mid'
                                                ],
                                                [
                                                    'label' => '赛程日期',
                                                    'value' => 'schedule_date'
                                                ], [
                                                    'label' => '赛程编号',
                                                    'value' => 'schedule_code'
                                                ], [
                                                    'label' => '联赛名称',
                                                    'value' => 'league_name'
                                                ],
                                                [
                                                    'label' => '客队',
                                                    'value' => 'visit_short_name'
                                                ], [
                                                    'label' => '主队',
                                                    'value' => "home_short_name"
                                                ], [
                                                    'label' => '全场比分',
                                                    'value' => "result_qcbf"
                                                ], [
                                                    'label' => '胜负',
                                                    'value' => function($model) {
                                                        if (!empty($model["result_qcbf"])) {
                                                            $bfArr = explode(':', $model['result_qcbf']);
                                                            if ((int) $bfArr[1] > (int) $bfArr[0]) {
                                                                return "胜";
                                                            } else {
                                                                return "负";
                                                            }
                                                        }
                                                    }
                                                ], [
                                                    'label' => '让分',
                                                    'value' => "rf_nums"
                                                ], [
                                                    'label' => '让分胜负',
                                                    'value' => function($model) {
                                                        if (!empty($model["result_qcbf"]) && !empty($model["rf_nums"])) {
                                                            $bfArr = explode(':', $model['result_qcbf']);
                                                            if ((int) $bfArr[1] + (float) $model['rf_nums'] > (int) $bfArr[0]) {
                                                                return "让分主胜";
                                                            } else {
                                                                return "让分主负";
                                                            }
                                                        }
                                                    }
                                                ], [
                                                    'label' => '胜分差',
                                                    'value' => function($model) {
                                                        $str = "";
                                                        switch ($model["result_3003"]) {
                                                            case "01":
                                                                $str = "主胜1-5";
                                                                break;
                                                            case "02":
                                                                $str = "主胜6-10";
                                                                break;
                                                            case "03":
                                                                $str = "主胜11-15";
                                                                break;
                                                            case "04":
                                                                $str = "主胜16-20";
                                                                break;
                                                            case "05":
                                                                $str = "主胜21-25";
                                                                break;
                                                            case "06":
                                                                $str = "主胜26+";
                                                                break;
                                                            case "11":
                                                                $str = "客胜1-5";
                                                                break;
                                                            case "12":
                                                                $str = "客胜6-10";
                                                                break;
                                                            case "13":
                                                                $str = "客胜11-15";
                                                                break;
                                                            case "14":
                                                                $str = "客胜16-20";
                                                                break;
                                                            case "15":
                                                                $str = "客胜21-25";
                                                                break;
                                                            case "16":
                                                                $str = "客胜26+";
                                                                break;
                                                        }
                                                        return $str;
                                                    }
                                                ], [
                                                    'label' => '大小分',
                                                    'value' => function($model) {
                                                        if (!empty($model["result_qcbf"]) && !empty($model["schedule_zf"])) {
                                                            $bfArr = explode(':', $model['result_qcbf']);
                                                            $total = (int) $bfArr[1] + (int) $bfArr[0];
                                                            if ($total > $model["schedule_zf"]) {
                                                                return "大分";
                                                            } else {
                                                                return "小分";
                                                            }
                                                        }
                                                    }
                                                ], [
                                                    'label' => '是否开奖',
                                                    'value' => function($model) {
                                                        $res = ["1" => "比赛中", "2" => "完结", "3" => "取消", "4" => "延迟", "5" => "比赛结果不正确", "6" => "比赛未出结果", "7" => "腰斩"];
                                                        return $res[$model['result_status']];
                                                    }
                                                        ], [
                                                            'label' => '操作',
                                                            'format' => 'raw',
                                                            'value' => function($model) {
                                                                return ($model['result_status'] == 2 ? '<span class="handle pointer" onclick="deal(' . $model['schedule_mid'] . ',' . 3100 . ')">延迟出票对奖</span><span class="handle pointer" onclick="errorAward(' . $model["schedule_mid"] . ',' . 3100 . ')"> | 赛果错误重新对奖 </span>' : ($model['status'] == 3 ? '<span class="handle pointer" id="deal_' . $model['schedule_mid'] . '" onclick="dealOrder(' . $model["schedule_mid"] . ',' . 3100 . ')">相关订单修改赔率 </span><span class="handle pointer" id="award_' . $model['schedule_mid'] . '" style="display:none" onclick="dealAward(' . $model["schedule_mid"] . ',' . 3100 . ')">取消赛程相关订单对奖 </span>' : ''));
                                                            }
                                                        ],
                                                    ]
                                                ]);
                                            } else {
                                                echo GridView::widget([
                                                    "dataProvider" => $data,
                                                    "columns" => [
                                                        [ 'class' => 'yii\grid\SerialColumn'],
                                                        [
                                                            'label' => '赛事MID',
                                                            'value' => 'open_mid'
                                                        ],
                                                        [
                                                            'label' => '赛程日期',
                                                            'value' => 'schedule_date'
                                                        ],[
                                                            'label' => '联赛名称',
                                                            'value' => 'league_name'
                                                        ],
                                                        [
                                                            'label' => '主队',
                                                            'value' => 'home_name'
                                                        ], [
                                                            'label' => '客队',
                                                            'value' => 'visit_name'
                                                        ], [ 
                                                            'label' => '胜平负',
                                                            'value' => function($model){
                                                               $result = Constants::BDSCHEDULE_PLAY;
                                                               if(isset($model["result_5001"])&&$model["result_5001"]!=""){
                                                                   return $result["5001"][$model["result_5001"]];
                                                               }else{
                                                                   return "";
                                                               }
                                                            }
                                                        ],[ 
                                                            'label' => '总进球',
                                                            'value' => function($model){
                                                               $result = Constants::BDSCHEDULE_PLAY;
                                                               if(isset($model["result_5002"])&&$model["result_5002"]!=""){
                                                                   if($model["result_5002"]>=7){
                                                                        return $result["5002"][7];
                                                                   }else{
                                                                        return $result["5002"][$model["result_5002"]];  
                                                                   }
                                                               }else{
                                                                   return "";
                                                               }
                                                            }
                                                        ], [ 
                                                            'label' => '半全场',
                                                            'value' => function($model){
                                                               $result = Constants::BDSCHEDULE_PLAY;
                                                               if(isset($model["result_5003"])&&$model["result_5003"]!=""){
                                                                   return $result["5003"][$model["result_5003"]];
                                                               }else{
                                                                   return "";
                                                               }
                                                            }
                                                        ],[ 
                                                            'label' => '上下盘单双',
                                                            'value' => function($model){
                                                               $result = Constants::BDSCHEDULE_PLAY;
                                                               if(isset($model["result_5004"])&&$model["result_5004"]!=""){
                                                                   return $result["5004"][$model["result_5004"]];
                                                               }else{
                                                                   return "";
                                                               }
                                                            }
                                                        ],[ 
                                                            'label' => '比分结果',
                                                            'value' => function($model){
                                                               if(isset($model["result_5005"])&&$model["result_5005"]!=""){
                                                                   return $model["result_5005"];
                                                               }else{
                                                                   return "";
                                                               }
                                                            }
                                                        ],[ 
                                                            'label' => '胜负过关',
                                                            'value' => function($model){
                                                               $result = Constants::BDSCHEDULE_PLAY;
                                                               if(isset($model["result_5006"])&&$model["result_5006"]!=""){
                                                                   return $result["5006"][$model["result_5006"]];
                                                               }else{
                                                                   return "";
                                                               }
                                                            }
                                                        ], [
                                                            'label' => '是否开奖',
                                                            'value' => function($model) {
                                                                $res = ["1" => "比赛中", "2" => "完结", "3" => "取消", "4" => "延迟", "5" => "比赛结果不正确", "6" => "比赛未出结果", "7" => "腰斩"];
                                                                return $res[$model['status']];
                                                            }
                                                                ], 
//                                                                        [
//                                                                    'label' => '操作',
//                                                                    'format' => 'raw',
//                                                                    'value' => function($model) {
//                                                                        return ($model['status'] == 2 ? '<span class="handle pointer" onclick="deal(' . $model['schedule_mid'] . ',' . 3100 . ')">延迟出票对奖</span><span class="handle pointer" onclick="errorAward(' . $model["schedule_mid"] . ',' . 3100 . ')"> | 赛果错误重新对奖 </span>' : ($model['status'] == 3 ? '<span class="handle pointer" id="deal" onclick="dealOrder(' . $model["schedule_mid"] . ',' . 3100 . ')">相关订单修改赔率 </span><span class="handle pointer" id="award" style="display:none" onclick="dealAward(' . $model["schedule_mid"] . ',' . 3100 . ')">取消赛程相关订单对奖 </span>' : ''));
//                                                                    }
//                                                                ],
                                                            ]
                                                        ]);
                                                    }
                                                    ?>
</div>
<script>
    function deal(schedule_mid, code) {
        msgConfirm('提醒', '确定要进行手动对奖吗？', function () {
            $.ajax({
                url: "<?php echo \Yii::$app->params['userDomain']; ?>/api/cron/time/cash-compting-detail",
                type: "get",
                data: {mid: schedule_mid, code: code},
                dataType: "json",
                success: function (json) {
                    if (json.code == 600) {
                        msgAlert(json.msg, function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json.msg);
                    }
                }
            });
        })
    }
    //条件搜索
    function search() {
        var code = "";
        for (var i = 0; i < $(".nav li").length; i++) {
            if ($($(".nav li")[i]).hasClass("active")) {
                code = $($(".nav li")[i]).attr("flag");
            }
        }
        var schedule_mid = $("#schedule_mid").val();
        var schedule_date = $("#schedule_date").val();
        var result_status = $("#result_status").val();
        if (schedule_mid == "" && schedule_date == "" && result_status == "") {
            msgAlert("请输入搜索条件");
        } else {
            location.href = '/lottery/schresult/index?code=' + code + '&schedule_mid=' + schedule_mid + '&schedule_date=' + schedule_date + '&result_status=' + result_status;
        }

    }
    //重置
    function goIndex() {
        location.href = '/lottery/schresult/index';
    }

    function dealOrder(mid, code) {
        msgConfirm('提醒', '确定要重新修改相关订单的赔率？', function () {
            $.ajax({
                url: '/lottery/schresult/deal-delay-order',
                async: false,
                type: 'POST',
                data: {mid: mid, code: code},
                dataType: 'json',
                success: function (data) {
                    if (data.code == 600) {
                        msgAlert(data.msg, function () {
                            console.log(data)
                            $('#deal_' + mid).hide();
                            $('#award_' + mid).show();
//                            location.reload();
                        });
                    } else {
                        msgAlert(data.msg);
                    }
                }
            });
        })
    }

    function dealAward(mid, code) {
        msgConfirm('提醒', '确定要对该赛程相关订单对奖？', function () {
            $.ajax({
                url: '/lottery/schresult/deal-delay-award',
                async: false,
                type: 'POST',
                data: {mid: mid, code: code},
                dataType: 'json',
                success: function (data) {
                    if (data.code == 600) {
                        msgAlert(data.msg, function () {
                            console.log(data)
                            $('#deal_' + mid).hide();
                            $('#award_' + mid).hide();
//                            location.reload();
                        });
                    } else {
                        msgAlert(data.msg);
                    }
                }
            });
        })
    }
    function errorAward(mid, code) {
        msgConfirm('提醒', '确定要对该赛程相关订单重新对奖？', function () {
            $.ajax({
                url: '/lottery/schresult/deal-error-award',
                async: false,
                type: 'POST',
                data: {mid: mid, code: code},
                dataType: 'json',
                success: function (data) {
                    if (data.code == 600) {
                        msgAlert(data.msg, function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(data.msg);
                    }
                }
            });
        })
    }
</script>
