<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>
<div style="font-size:14px;">
    <?php
    echo "<ul class='third_team_ul'>";
    echo '<li>';
    echo Html::label("合买订单号", "programme_code", ["style" => "margin-left:32px;"]);
    echo Html::input("input", "programme_code", isset($get["programme_code"]) ? $get["programme_code"] : "", ["id" => "programme_code", "class" => "form-control", "placeholder" => "订单号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("专家信息", "user_info", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["id" => "user_info", "class" => "form-control", "placeholder" => "编号，手机号，名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("彩种  ", "", ["style" => "margin-left:44px;"]);
    echo Html::dropDownList("lottery_code", isset($get["lottery_code"]) ? $get["lottery_code"] : "0", $lotteryNames, ["id" => "lottery_code", "class" => "form-control", "style" => "width:160px;display:inline;margin-left:5px;"]);
    echo '</li>';
//    echo '<li>';
//    echo Html::label("投注时间  ", "", ["style" => "margin-left:15px;"]);
//    echo Html::input("text", "startdate", isset($get["startdate"]) ? $get["startdate"] : "", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
//    echo "-";
//    echo Html::input("text", "enddate", isset($get["enddate"]) ? $get["enddate"] : "", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
//    echo '</li>';
//    echo '<li>';
//    echo Html::label("截止时间  ", "", ["style" => "margin-left:15px;"]);
//    echo Html::input("text", "end_time_start", isset($get["end_time_start"]) ? $get["end_time_start"] : "", ["id" => "end_time_start", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
//    echo "-";
//    echo Html::input("text", "end_time_end", isset($get["end_time_end"]) ? $get["end_time_end"] : "", ["id" => "end_time_end", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
//    echo '</li>';
    echo '<li>';
    echo Html::label("方案状态  ", "", ["style" => "margin-left:45px;"]);
    echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $orderStatus, ["id" => "status", "class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("保密状态  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("security", isset($get["security"]) ? $get["security"] : "", $security, ["id" => "security", "class" => "form-control", "style" => "width:90px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:56px;", "onclick" => "search();"]);
    echo Html::button("重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
    echo '</li>';
    echo "</ul>";
    ?>
</div>
<?php
echo GridView::widget([
        "dataProvider" => $data,
        "columns" => [
            [ 'class' => 'yii\grid\SerialColumn'],
            [
                'label' => '订单号',
                'value' => 'programme_code'
            ],
            [
                'label' => '创建时间',
                'value' => 'create_time'
            ],[
                'label' => '截止时间',
                'value' => 'programme_end_time'
            ], [
                'label' => '彩种',
                'value' => 'lottery_name'
            ],
//            [
//                'label' => '投注内容',
//                'format' => 'raw',
//                'value' => function($model) {
//            $vals = explode("^", trim($model["bet_val"], "^"));
//            return "<span data-am-popover=\"{content: '" . implode("<br \>", $vals) . "', trigger: 'hover focus'}\" style='color:#55acee;'>查看</span>";
//        }
//            ],
//            [
//                'label' => '专家编号',
//                'value' => 'expert_no'
//            ], 
            [
                'label' => '专家名称',
                'value' => 'user_name'
            ], 
//            [
//                'label' => '期号',
//                'value' => 'periods'
//            ],
                    [
                'label' => '保密设置',
                'value' => function($model) {
                    $orderStatus = [
                        "1" => "完全公开",
                        "2" => "跟单公开",
                        "3" => "截止后公开",
                    ];
                    return isset($orderStatus[$model["security"]]) ? $orderStatus[$model["security"]] : "未知来源";
                }
            ], [
                'label' => '总金额',
                'value' => 'bet_money'
            ], [
                'label' => '剩余金额',
                'value' => 'programme_last_amount'
            ],[
                'label' => '跟单人数',
                'value' => 'programme_peoples'
            ], [
                'label' => '方案进度(%)',
                'value' => function($model){
                    return $model['programme_speed'].'%';
                }
            ],
//                    [
//                'label' => '已预购',
//                'value' => function($model){
//                    $str="";
//                    $str=sprintf("%.2f",$model['owner_buy_number']/$model['programme_all_number']).'%';
//                    return $str;
//                }
//            ], 
//            [
//                'label' => '总份数',
//                'value' => 'programme_all_number'
//            ], [
//                'label' => '被购份数',
//                'value' => 'programme_buy_number'
//            ],  [
//                'label' => '剩余份数',
//                'value' => 'programme_last_number'
//            ], [
//                'label' => '每份金额',
//                'value' => 'programme_univalent'
//            ],[
//                'label' => '跟单人数',
//                'value' => 'programme_peoples'
//            ],  [
//                'label' => '方案进度(%)',
//                'value' => 'programme_speed'
//            ], [
//                'label' => '保底操作',
//                'value' =>function($model){
//                     $action = [
//                        "1" => "未操作",
//                        "2" => "参加保底",
//                        "3" => "未参与保底",
//                    ];
//                    return isset($action[$model["guarantee_status"]]) ? $action[$model["guarantee_status"]] : "未知";
//                }
//            ], 
            [
                'label' => '方案状态',
                'value' =>function($model){
                     $action = [
                        "1" => "未发布",
                        "2" => "招募中",
                        "3" => "处理中",
                        "4" => "待开奖",
                        "5" => "未中奖",
                        "6" => "中奖",
                        "7" => "未满员撤单",
                        "8" => "方案失败",
                        "9" => "过点撤销",
                        "10" => "拒绝出票",
                        "11" => "未上传方案撤单"
                    ];
                    return isset($action[$model["status"]]) ? $action[$model["status"]] : "未知";
                }
            ], 
//            [
//                'label' => '接单门店',
//                'value' => 'store_name'
//            ], 
//            [
//                'label' => '门店店主',
//                'value' => 'consignee_name'
//            ], 
//            [
//                'label' => '门店电话',
//                'value' => 'phone_num'
//            ], 
            [
                'label' => '操作',
                'format' => 'raw',
                'value' => function($model){
                    return '<div class="am-btn-group am-btn-group-xs"> <span class="handle pointer" onclick="readDeatail(\'/subagents/chipped/readdetail?programme_code=' . $model['programme_code'] . '\')">查看</span>
                                </div>';
                }
            ]
        ]
    ]);
?>
<script>
    function goReset() {
        location.href = '/subagents/chipped/index';
    }
    function search(){
        var programme_code=$("#programme_code").val();
        var user_info = $("#user_info").val();
        var lottery_code=$("#lottery_code").val();
        var status= $("#status").val();
        var security= $("#security").val();
        location.href = '/subagents/chipped/index?programme_code='+programme_code+'&user_info='+user_info+'&lottery_code='+lottery_code+'&status='+status+'&security='+security;
    }
    function readDeatail(url) {
        modDisplay({title: '合买详情', url: url, height: 600, width: 800});
    }
</script>

