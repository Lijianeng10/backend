<?php

use yii\helpers\Html;
use yii\grid\GridView;
$lotteryNames = [
    '0' => '全部',
    '1001' => '双色球',
    '1002' => '福彩3D',
    '1003' => '七乐彩',
    '2001' => '大乐透',
    '2002' => '排列三',
    '2003' => '排列五',
    '2004' => '七星彩',
    '3000' => '竞彩足球',
    '3100' => '竞彩篮球',
    '3200' => '竞彩冠亚军',
    '4001' => '胜负彩14场',
    '4002' => '胜负彩9场',
    '5000' => '北京单场'
];
$autoType = [
    "" => "全部",
    "1" => "手动",
    "2" => "自动",
];
$dealStatus = [
    "" => "全部",
    "1" => "已对奖",
    "2" => "派奖失败",
];

?>
<div style="font-size:14px;">
    <?php
    echo "<ul class='third_team_ul'>";
    echo '<li>';
    echo Html::label("接单编号", "api_order_code", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "api_order_code", isset($get["api_order_code"]) ? $get["api_order_code"] : "", ["id" => "api_order_code", "class" => "form-control", "placeholder" => "接单编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("第三方订单编号", "third_order_code", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "third_order_code", isset($get["third_order_code"]) ? $get["third_order_code"] : "", ["id" => "third_order_code", "class" => "form-control", "placeholder" => "第三方订单编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("订单编号", "order_code", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "order_code", isset($get["order_code"]) ? $get["order_code"] : "", ["id" => "order_code", "class" => "form-control", "placeholder" => "订单编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("会员信息", "user_info", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["id" => "user_info", "class" => "form-control", "placeholder" => "会员编号、会员手机号、会员名称", "style" => "width:250px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("门店信息", "store_info", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "store_info", isset($get["store_info"]) ? $get["store_info"] : "", ["id" => "store_info", "class" => "form-control", "placeholder" => "门店编号、门店注册手机号、门店名称", "style" => "width:250px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("彩种&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("lottery_code", isset($get["lottery_code"]) ? $get["lottery_code"] : "0", $lotteryNames, ["id" => "lottery_code", "class" => "form-control", "style" => "width:160px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("投注时间  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("text", "startdate", isset($get["startdate"]) ? $get["startdate"] :date("Y-m-d",strtotime("-3 day")), ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("text", "enddate", isset($get["enddate"]) ? $get["enddate"] :date("Y-m-d"), ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("处理状态  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("deal_status", isset($get["deal_status"]) ? $get["deal_status"] : "", $dealStatus, ["id" => "deal_status", "class" => "form-control", "style" => "width:90px;display:inline;margin-left:5px;"]);
    echo '</li>';
     echo '<li>';
    echo Html::label("出票类型  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("auto_type", isset($get["auto_type"]) ? $get["auto_type"] : "", $autoType, ["id" => "auto_type", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;", "onclick" => "search();"]);
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
                'label' => '接单号',
                'value' => 'api_order_code'
            ],[
                'label' => '第三方订单号',
                'value' => 'third_order_code'
            ],[
                'label' => '订单号',
                'value' => 'lottery_order_code'
            ],[
                'label' => '投注时间',
                'value' => 'create_time'
            ], [
                'label' => '彩种',
                'value' => 'lottery_name'
            ], [
                'label' => '倍数',
                'value' => 'bet_double'
            ], [
                'label' => '期号',
                'value' => 'periods'
            ], [
                'label' => '投注金额',
                'value' => 'bet_money'
            ], [
                'label' => '订单状态',
                'value' => function($model) {
                    $orderStatus = [
                        "0" => "所有",
                        "1" => "未支付",
                        "2" => "处理中",
                        "3" => "待开奖",
                        "4" => "中奖",
                        "5" => "未中奖",
                        "6" => "出票失败",
                        '9' => '过点撤销',
                        '10' => '拒绝出票',
                        '11' => '等待出票'
                    ];
                    return isset($orderStatus[$model["status"]]) ? $orderStatus[$model["status"]] : "未知状态";
                }
            ], [
                'label' => '中奖金额',
                'value' => function($model) {
                    return $model["win_amount"] ? $model["win_amount"] : 0;
                }
            ], [
                'label' => '票机中奖金额',
                'value' => function($model) {
                    return $model["zmf_award_money"] != '0.00' ? $model["zmf_award_money"] : ($model['deal_win_amount'] ? $model['deal_win_amount'] : 0);
                }
            ],[
                'label' => '实派金额',
                'value' => function($model) {
                    return $model["award_amount"] ? $model["award_amount"] : "0.00";
                }
            ],[
                'label' => '处理状态',
                'value' => function($model) {
                    $dealStatus = [
                        "0" => "未处理",
                        "1" => "已对奖",
                        "2" => "派奖失败",
                        "3" => "派奖成功",
                        "4" => "退款失败",
                        "5" => "退款成功"
                    ];
                    return isset($dealStatus[$model["deal_status"]]) ? $dealStatus[$model["deal_status"]] : "未知状态";
                }
            ], [
                'label' => '会员编号',
                'value' => 'cust_no'
            ], [
                'label' => '会员手机',
                'value' => 'user_tel'
            ], [
                'label' => '门店名称',
                'value' => 'store_name'
            ], [
                'label' => '来源',
                'value' => function($model) {
                    $source = [
                        "1" => "自购",
                        "2" => "追号",
                        "3" => "赠送",
                        "4" => "合买",
                        "6" => "计划",
                        '7' => '流量单'
                    ];
                    return isset($source[$model["source"]]) ? $source[$model["source"]] : "未知来源";
                }
            ],[
                'label' => '出票',
                'value' => function($model){
                    $autoType = [
                        "1" => "手动",
                        "2" => "自动",
                    ];
                    return isset($autoType[$model["auto_type"]]) ? $autoType[$model["auto_type"]] : "未知";
                }
            ]
        ]
    ]);

?>
<div class="am-modal am-modal-no-btn" tabindex="-1" id="your-modal">
    <div class="am-modal-dialog" style="width: 700px;">
        <div class="am-modal-hd">
            <span class="modalTitle"></span>
            <a href="javascript: void(0)" class="am-close am-close-spin" data-am-modal-close>&times;</a>
        </div>
        <div class="am-modal-bd">
            <p class="modalContent">
                Modal 内容。
            </p>
        </div>
    </div>
</div>
<!--<button
  type="button"
  class="am-btn am-btn-primary"
  data-am-modal="{target: '#your-modal', closeViaDimmer: 0, width: 400, height: 225}">
</button>-->
<script type="text/javascript">
    function search() {
        var lottery_order_code = $("#order_code").val();
        var api_order_code = $("#api_order_code").val();
        var third_order_code = $("#third_order_code").val();
        var lottery_code = $("#lottery_code").val();
        var user_info = $("#user_info").val();
        var store_info = $("#store_info").val();
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var deal_status = $("#deal_status").val();
        var auto_type = $("#auto_type").val();
        var param = '?1=1';
        if (lottery_order_code != "") {
            param += "&order_code=" + lottery_order_code;
        }
        if (api_order_code != "") {
            param += "&api_order_code=" + api_order_code;
        }
        if (third_order_code != "") {
            param += "&third_order_code=" + third_order_code;
        }
        if (lottery_code > 0) {
            param += "&lottery_code=" + lottery_code;
        }
        if (user_info != "") {
            param += "&user_info=" + user_info;
        }
        if (store_info != "") {
            param += "&store_info=" + store_info;
        }
        if (startdate != "") {
            param += "&startdate=" + startdate;
        }
        if (enddate != "") {
            param += "&enddate=" + enddate;
        }
        if (deal_status != "") {
            param += "&deal_status=" + deal_status;
        }
        if (auto_type != "") {
            param += "&auto_type=" + auto_type;
        }
        location.href = '/channel/award/lists' + param;
    }
    //清空投注时间默认值
    function clearTime(){
        $("#startdate").val("");
        $("#enddate").val("");
       
    }
</script>