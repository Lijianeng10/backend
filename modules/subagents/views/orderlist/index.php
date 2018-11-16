<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\lottery\helpers\Constant;

$dealStatus = [
    "" => "全部",
    "0" => "未处理",
    "1" => "已对奖",
    "2" => "派奖失败",
    "3" => "派奖成功",
    "4" => "退款失败",
    "5" => "退款成功"
];

echo '<ul class="nav nav-tabs" style="margin-bottom:10px;">
  <li role="presentation"' . ((!isset($_GET['lottery_category_id']) || $_GET['lottery_category_id'] == '4,5,6') ? ' class="active"' : '') . '><a href="/subagents/orderlist?lottery_category_id=4,5,6">数字彩</a></li>
  <li role="presentation"' . ((isset($_GET['lottery_category_id']) && $_GET['lottery_category_id'] == '11') ? ' class="active"' : '') . '><a href="/subagents/orderlist?lottery_category_id=11">足球竞彩</a></li>
  <li role="presentation"' . ((isset($_GET['lottery_category_id']) && $_GET['lottery_category_id'] == '12') ? ' class="active"' : '') . '><a href="/subagents/orderlist?lottery_category_id=12">篮球竞彩</a></li>
</ul>';
?>
<div style="font-size:14px;">
    <?php
    echo "<ul class='third_team_ul'>";
    echo '<li>';
    echo Html::label("订单号", "order_code", ["style" => "margin-left:32px;"]);
    echo Html::input("input", "lottery_order_code", isset($get["lottery_order_code"]) ? $get["lottery_order_code"] : "", ["id" => "lottery_order_code", "class" => "form-control", "placeholder" => "订单号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("会员信息", "user_info", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["id" => "user_info", "class" => "form-control", "placeholder" => "会员编号、会员手机号、会员名称", "style" => "width:250px;display:inline;margin-left:5px;"]);
    echo '</li>';
//    echo '<li>';
//    echo Html::label("门店信息", "store_info", ["style" => "margin-left:15px;"]);
//    echo Html::input("input", "store_info", isset($get["store_info"]) ? $get["store_info"] : "", ["id" => "store_info", "class" => "form-control", "placeholder" => "门店编号、门店注册手机号、门店名称", "style" => "width:250px;display:inline;margin-left:5px;"]);
//    echo '</li>';
    echo '<li>';
    echo Html::label("彩种  ", "", ["style" => "margin-left:44px;"]);
    echo Html::dropDownList("lottery_code", isset($get["lottery_code"]) ? $get["lottery_code"] : "0", $lotteryNames, ["id" => "lottery_code", "class" => "form-control", "style" => "width:160px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("投注时间  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("text", "startdate", isset($get["startdate"]) ? $get["startdate"] :date("Y-m-d",strtotime("-3 day")), ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("text", "enddate", isset($get["enddate"]) ? $get["enddate"] :date("Y-m-d"), ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("截止时间  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("text", "end_time_start", isset($get["end_time_start"]) ? $get["end_time_start"] : "", ["id" => "end_time_start", "class" => "form-control",  "placeholder" => "开始日期", "style" => "width:130px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("text", "end_time_end", isset($get["end_time_end"]) ? $get["end_time_end"] : "", ["id" => "end_time_end", "class" => "form-control", "placeholder" => "结束日期", "style" => "width:130px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("订单状态  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $orderStatus, ["id" => "status", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo "<lable style='margin-left:25px;margin-right:6px;padding-left: 8px;font-size:15px;font-weight: normal;'>未支付</lable>";
    echo Html::checkbox("choose", isset($get["choose"]) && $get["choose"] == "1" ? "true" : "", ["style" => "height:20px;width:20px;vertical-align:top;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("处理状态  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("deal_status", isset($get["deal_status"]) ? $get["deal_status"] : "", $dealStatus, ["id" => "deal_status", "class" => "form-control", "style" => "width:90px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;", "onclick" => "search();"]);
//    echo Html::button("清空投注时间", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "clearTime();"]);
    echo Html::button("重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
//    echo Html::button("导出", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "getReport();"]);
    echo '</li>';
    echo "</ul>";
    ?>
</div>
<?php
if (!isset($_GET['lottery_category_id']) || $_GET['lottery_category_id'] == '4,5,6') {
    echo GridView::widget([
        "dataProvider" => $data,
        "columns" => [
            [ 'class' => 'yii\grid\SerialColumn'],
            [
                'label' => '订单号',
                'value' => 'lottery_order_code'
            ],
            [
                'label' => '投注时间',
                'value' => 'create_time'
            ], [
                'label' => '截止时间',
                'value' => 'end_time'
            ],
//            [
//                'label' => '投注内容',
//                'format' => 'raw',
//                'value' => function($model) {
//            $vals = explode("^", trim($model["bet_val"], "^"));
//            return "<span data-am-popover=\"{content: '" . implode("<br \>", $vals) . "', trigger: 'hover focus'}\" style='color:#55acee;'>查看</span>";
//        }
//            ], 
                    [
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
            $orderStatus = Constant::ORDER_STATUS;
            return isset($orderStatus[$model["status"]]) ? $orderStatus[$model["status"]] : "未知来源";
        }
            ], 
//                    [
//                'label' => '出票手续费',
//                'value' => function($model) {
//            return $model["pay_pre_money"] ? $model["pay_pre_money"] : 0;
//        }
//            ],
            [
                'label' => '中奖金额',
                'value' => function($model) {
            return $model["win_amount"] ? $model["win_amount"] : 0;
        }
            ],  [
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
                'label' => '实兑金额',
                'value' => function($model) {
            return $model["award_amount"] ? $model["award_amount"] : 0;
        }
            ],[
                'label' => '会员编号',
                'value' => 'cust_no'
            ], [
                'label' => '会员手机',
                'value' => 'user_tel'
            ], [
                'label' => '子代理商名称',
                'value' => 'user_remark'
            ],
//                    [
//                'label' => '门店名称',
//                'value' => 'store_name'
//            ], [
//                'label' => '门店店主',
//                'value' => 'consignee_name'
//            ], [
//                'label' => '门店注册手机号',
//                'value' => 'phone_num'
//            ], 
//                    [
//                'label' => '来源',
//                'value' => function($model) {
//            $source = [
//                "1" => "自购",
//                "2" => "追号",
//                "3" => "赠送",
//                "4" => "合买",
//                "6" => "计划"
//            ];
//            return isset($source[$model["source"]]) ? $source[$model["source"]] : "未知来源";
////                    return ($model["source"] == 1 ? "自购" : ($model["source"] == 2 ? "追号" : ($model["source"] == 3 ? "赠送" : "未知来源")));
//        }
//            ],
            [
                'label' => '操作',
                'format' => 'raw',
                'value' => function($model) {
            return '<div class="am-btn-group am-btn-group-xs">
                     <span class="handle pointer" onclick="readDeatail(\'/subagents/orderlist/readdetail12?lottery_order_id=' . $model['lottery_order_id'] . '\')">查看</span></div>';
        }
            ]
        ]
    ]);
} else if (isset($_GET['lottery_category_id']) && $_GET['lottery_category_id'] == '11') {

    echo GridView::widget([
        "dataProvider" => $data,
        "columns" => [
            [ 'class' => 'yii\grid\SerialColumn'],
            [
                'label' => '订单号',
                'value' => 'lottery_order_code'
            ],
//             [
//                'label' => '期数',
//                'value' => 'periods'
//            ],
            [
                'label' => '投注时间',
                'value' => 'create_time'
            ], [
                'label' => '截止时间',
                'value' => 'end_time'
            ],
//            [
//                'label' => '投注内容',
//                'format' => 'raw',
//                'value' => function($model) {
//            $vals = explode("^", trim($model["bet_val"], "^"));
//            return "<span data-am-popover=\"{content: '" . implode("<br \>", $vals) . "', trigger: 'hover focus'}\" style='color:#55acee;'>查看</span>";
//        }
//            ],
                    [
                'label' => '过关方式',
                'value' => function($model) {
            return $model["play_name"];
        }
            ], [
                'label' => '彩种玩法',
                'value' => 'lottery_name'
            ], [
                'label' => '倍数',
                'value' => 'bet_double'
            ], [
                'label' => '投注金额',
                'value' => 'bet_money'
            ], [
                'label' => '订单状态',
                'value' => function($model) {
            $orderStatus = Constant::ORDER_STATUS;
            return isset($orderStatus[$model["status"]]) ? $orderStatus[$model["status"]] : "未知来源";
        }
            ],
//                    [
//                'label' => '出票手续费',
//                'value' => function($model) {
//            return $model["pay_pre_money"] ? $model["pay_pre_money"] : 0;
//        }
//            ],
                    [
                'label' => '中奖金额',
                'value' => function($model) {
            return $model["win_amount"] ? $model["win_amount"] : 0;
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
                'label' => '实兑金额',
                'value' => function($model) {
            return $model["award_amount"] ? $model["award_amount"] : 0;
        }
            ],[
                'label' => '会员编号',
                'value' => 'cust_no'
            ], [
                'label' => '会员手机',
                'value' => 'user_tel'
            ], [
                'label' => '子代理商名称',
                'value' => 'user_remark'
            ],
//                    [
//                'label' => '门店名称',
//                'value' => 'store_name'
//            ], [
//                'label' => '门店店主',
//                'value' => 'consignee_name'
//            ], [
//                'label' => '门店注册手机号',
//                'value' => 'phone_num'
//            ],  
                    [
                'label' => '操作',
                'format' => 'raw',
                'value' => function($model) {
            return '<div class="am-btn-group am-btn-group-xs">'
                . '<span class="handle pointer" onclick="readDeatail(\'/subagents/orderlist/readdetail3?lottery_order_id=' . $model['lottery_order_id'] . '\')">查看</span></div>';
        }
            ]
        ]
    ]);
} else {
    echo GridView::widget([
        "dataProvider" => $data,
        "columns" => [
            [ 'class' => 'yii\grid\SerialColumn'],
            [
                'label' => '订单号',
                'value' => 'lottery_order_code'
            ],
            [
                'label' => '投注时间',
                'value' => 'create_time'
            ], [
                'label' => '截止时间',
                'value' => 'end_time'
            ],
//            [
//                'label' => '投注内容',
//                'format' => 'raw',
//                'value' => function($model) {
//            $vals = explode("^", trim($model["bet_val"], "^"));
//            return "<span data-am-popover=\"{content: '" . implode("<br \>", $vals) . "', trigger: 'hover focus'}\" style='color:#55acee;'>查看</span>";
//        }
//            ], 
                    [
                'label' => '过关方式',
                'value' => function($model) {
            return $model["play_name"];
        }
            ], [
                'label' => '彩种玩法',
                'value' => 'lottery_name'
            ], [
                'label' => '倍数',
                'value' => 'bet_double'
            ], [
                'label' => '投注金额',
                'value' => 'bet_money'
            ], [
                'label' => '订单状态',
                'value' => function($model) {
            $orderStatus = Constant::ORDER_STATUS;
            return isset($orderStatus[$model["status"]]) ? $orderStatus[$model["status"]] : "未知来源";
        }
            ], 
//                    [
//                'label' => '出票手续费',
//                'value' => function($model) {
//            return $model["pay_pre_money"] ? $model["pay_pre_money"] : 0;
//        }
//            ],
                    [
                'label' => '中奖金额',
                'value' => function($model) {
            return $model["win_amount"] ? $model["win_amount"] : 0;
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
                'label' => '实兑金额',
                'value' => function($model) {
            return $model["award_amount"] ? $model["award_amount"] : 0;
        }
            ],[
                'label' => '会员编号',
                'value' => 'cust_no'
            ], [
                'label' => '会员手机',
                'value' => 'user_tel'
            ], [
                'label' => '子代理商名称',
                'value' => 'user_remark'
            ],
//                    [
//                'label' => '门店名称',
//                'value' => 'store_name'
//            ], [
//                'label' => '门店店主',
//                'value' => 'consignee_name'
//            ], [
//                'label' => '门店注册手机号',
//                'value' => 'phone_num'
//            ], 
                    [
                'label' => '操作',
                'format' => 'raw',
                'value' => function($model) {
            return '<div class="am-btn-group am-btn-group-xs bigRead">' . '<span class="handle pointer readLan" onclick="readDeatail(\'/subagents/orderlist/readdetail4?lotteryOrderCode=' . $model['lottery_order_code'] . '\')">查看</span>
                                ' . '</div>';
        }
            ]
        ]
    ]);
}
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
    //日期插件
    laydate.render({
        elem: '#end_time_start',//指定元素
        type: 'datetime'
    });
    laydate.render({
        elem: '#end_time_end',//指定元素
        type: 'datetime'
    });
    function goReset() {
        location.href = '/subagents/orderlist/index<?php echo isset($_GET['lottery_category_id']) ? ("?lottery_category_id=" . $_GET['lottery_category_id']) : "" ?>';
    }
    function search() {
        var lottery_order_code = $("#lottery_order_code").val();
        var lottery_additional_code = $("#lottery_additional_code").val();
        var lottery_code = $("#lottery_code").val();
        var user_info = $("#user_info").val();
        var store_info = $("#store_info").val();
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var status = $("#status").val();
        var end_time_start = $("#end_time_start").val();
        var end_time_end = $("#end_time_end").val();
        var deal_status = $("#deal_status").val();
        var chooseSta = "1";
        var param = '<?php echo isset($_GET['lottery_category_id']) ? ("?lottery_category_id=" . $_GET['lottery_category_id']) : "?1=1" ?>';
        if (lottery_order_code != "") {
            param += "&lottery_order_code=" + lottery_order_code;
        }
        if (lottery_additional_code != "") {
            param += "&lottery_additional_code=" + lottery_additional_code;
        }
        if (lottery_code > 0) {
            param += "&lottery_code=" + lottery_code;
        }
        if (user_info != "") {
            param += "&user_info=" + user_info;
        }
//        if (store_info != "") {
//            param += "&store_info=" + store_info;
//        }
        if (startdate != "") {
            param += "&startdate=" + startdate;
        }
        if (enddate != "") {
            param += "&enddate=" + enddate;
        }

        if (end_time_start != "") {
            param += "&end_time_start=" + end_time_start;
        }
        if (end_time_end != "") {
            param += "&end_time_end=" + end_time_end;
        }

        if (status != 0 && status != "") {
            param += "&status=" + status;
        }
        //判断未支付是否是选中状态
        if ($("input[type='checkbox']").is(':checked')) {
            param += "&choose=" + chooseSta;
        }
        if (deal_status != "") {
            param += "&deal_status=" + deal_status;
        }
        location.href = '/subagents/orderlist/index' + param;
    }
    function readDeatail(url) {
        modDisplay({title: '订单详情', url: url, height: 600, width: 800});
    }
    function subOrder(orderId) {//order_id
        if (confirm("确定生成子单?")) {
            $.ajax({
                url: "<?php echo \Yii::$app->params['userDomain']; ?>/api/cron/cron/sub-order",
                data: {order_id: orderId},
                type: "GET",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        alert(json["msg"]);
                        location.reload();
                    } else {
                        alert(json["msg"]);
                    }
                }
            });
        }
    }

    function awardSubmit(orderId) {
        msgConfirm ('提醒','确定对此单相关投注内容进行对奖？',function() {
            $.ajax({
                url:"/lottery/betting/do-award",
                async: false,
                type: 'POST',
                data: {order_id:orderId},
                dataType: 'json',
                success: function (data) {
//                    console.log(data);
                    if (600 != data['code']) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            })
        })
    }
    //清空投注时间默认值
    function clearTime(){
        $("#startdate").val("");
        $("#enddate").val("");
       
    }
    //打印报表
    function getReport() {
        var lottery_order_code = $("#lottery_order_code").val();
        var lottery_code = $("#lottery_code").val();
        var user_info = $("#user_info").val();
        var store_info = $("#store_info").val();
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var status = $("#status").val();
        var end_time_start = $("#end_time_start").val();
        var end_time_end = $("#end_time_end").val();
        var deal_status = $("#deal_status").val();
        var chooseSta = "1";
        var param = '<?php echo isset($_GET['lottery_category_id']) ? ("?lottery_category_id=" . $_GET['lottery_category_id']) : "?1=1" ?>';
        if (lottery_order_code != "") {
            param += "&lottery_order_code=" + lottery_order_code;
        }
        if (lottery_code > 0) {
            param += "&lottery_code=" + lottery_code;
        }
        if (user_info != "") {
            param += "&user_info=" + user_info;
        }
        if (startdate != "") {
            param += "&startdate=" + startdate;
        }
        if (enddate != "") {
            param += "&enddate=" + enddate;
        }

        if (end_time_start != "") {
            param += "&end_time_start=" + end_time_start;
        }
        if (end_time_end != "") {
            param += "&end_time_end=" + end_time_end;
        }

        if (status != 0 && status != "") {
            param += "&status=" + status;
        }
        //判断未支付是否是选中状态
        if ($("input[type='checkbox']").is(':checked')) {
            param += "&choose=" + chooseSta;
        }
        if (deal_status != "") {
            param += "&deal_status=" + deal_status;
        }
        msgConfirm ('提醒','确定需要打印该报表吗？',function() {
            var s1 = startdate.replace(/\-/g, "/");
            var e1 = enddate.replace(/\-/g, "/");
            var star1 = new Date(s1);
            var end1 = new Date(e1);
            var difTime = parseInt(end1-star1)/1000/3600/24;
            if(difTime>31){
                msgAlert("投注时间间隔不能超过31天，请缩短筛选条件")
               return false;
            }
            location.href = '/subagents/orderlist/print-report' + param;
        })

    }
    //打印报表
    //function getReport() {
    //    var lottery_order_code = $("#lottery_order_code").val();
    //    var lottery_code = $("#lottery_code").val();
    //    var user_info = $("#user_info").val();
    //    var startdate = $("#startdate").val();
    //    var enddate = $("#enddate").val();
    //    var status = $("#status").val();
    //    var end_time_start = $("#end_time_start").val();
    //    var end_time_end = $("#end_time_end").val();
    //    var deal_status = $("#deal_status").val();
    //    if ($("input[type='checkbox']").is(':checked')) {
    //        var choose = "1";
    //    }else{
    //        var choose = "";
    //    }
    //    var lottery_category_id = '<?php //echo isset($_GET['lottery_category_id']) ?$_GET['lottery_category_id']: "" ?>//';
    //    msgConfirm ('提醒','确定需要打印该报表吗？',function() {
    //        $.ajax({
    //            url:"/subagents/orderlist/print-report",
    //            async: false,
    //            type: 'GET',
    //            data: {lottery_category_id:lottery_category_id,lottery_order_code:lottery_order_code,lottery_code:lottery_code,user_info:user_info,startdate:startdate,enddate:enddate,status:status,end_time_start:end_time_start,end_time_end:end_time_end,deal_status:deal_status,choose:choose},
    //            dataType: 'json',
    //            success: function (data) {
    //                if (600 != data['code']) {
    //                    msgAlert(data['msg']);
    //                } else {
    //                    msgAlert(data['msg'], function () {
    //                        location.reload();
    //                    });
    //                }
    //            }
    //        })
    //    })
        // if (lottery_order_code != "") {
        //     param += "&lottery_order_code=" + lottery_order_code;
        // }
        // if (lottery_code > 0) {
        //     param += "&lottery_code=" + lottery_code;
        // }
        // if (user_info != "") {
        //     param += "&user_info=" + user_info;
        // }
        // if (startdate != "") {
        //     param += "&startdate=" + startdate;
        // }
        // if (enddate != "") {
        //     param += "&enddate=" + enddate;
        // }
        //
        // if (end_time_start != "") {
        //     param += "&end_time_start=" + end_time_start;
        // }
        // if (end_time_end != "") {
        //     param += "&end_time_end=" + end_time_end;
        // }
        //
        // if (status != 0 && status != "") {
        //     param += "&status=" + status;
        // }
        // //判断未支付是否是选中状态
        // if ($("input[type='checkbox']").is(':checked')) {
        //     param += "&choose=" + chooseSta;
        // }
        // if (deal_status != "") {
        //     param += "&deal_status=" + deal_status;
        // }
        // location.href = '/subagents/orderlist/print-report' + param;
    // }
</script>
