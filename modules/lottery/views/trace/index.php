<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>
<div style="font-size:14px;">
    <?php
     echo "<ul class='third_team_ul'>";
    echo '<li>';
    echo Html::label("订单号", "order_code", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "lottery_additional_code", (isset($_GET["lottery_additional_code"]) ? $_GET["lottery_additional_code"] : ""), ["id" => "lottery_additional_code", "class" => "form-control", "placeholder" => "订单号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
     echo '<li>';
    echo Html::label("会员信息", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "user_info", (isset($_GET["user_info"]) ? $_GET["user_info"] : ""), ["id" => "user_info", "class" => "form-control", "placeholder" => "会员手机号、会员编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("彩种  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("lottery_code", (isset($_GET["lottery_code"]) ? $_GET["lottery_code"] : "0"), $lotteryNames, ["id" => "lottery_code", "class" => "form-control", "style" => "width:160px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("投注时间  ", "", ["style" => "margin-left:0px;"]);
    echo Html::input("text", "startdate", (isset($_GET["startdate"]) ? $_GET["startdate"] : ""), ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("text", "enddate", (isset($_GET["enddate"]) ? $_GET["enddate"] : ""), ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("状态  ", "", ["style" => "margin-left:42px;"]);
    echo Html::dropDownList("status", (isset($_GET["status"]) ? $_GET["status"] : ""), $orderStatus, ["id" => "status", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:25px;", "onclick" => "search();"]);
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
            'value' => 'lottery_additional_code'
        ], [
            'label' => '投注时间',
            'value' => 'create_time'
        ], [
            'label' => '彩种',
            'value' => 'lottery_name'
        ], [
            'label' => '已追/总期数',
            'value' => function($model) {
                return $model["chased_num"] . "期/共" . $model["periods_total"] . "期";
            }
        ], [
            'label' => '是否随机追期',
            'value' => function($model) {
                return $model["is_random"] == 1 ? "是" : "否";
            }
        ], [
            'label' => '当期投注金额',
            'value' => 'bet_money'
        ], [
            'label' => '会员编号',
            'value' => 'cust_no'
        ], [
            'label' => '会员手机号',
            'value' => 'user_tel'
        ], [
            'label' => '状态',
            'value' => function($model) {
                return ($model["status"] == 1 ? "未追" : ($model["status"] == 2 ? "正在追" : ($model["status"] == 3 ? "完成" : ($model["status"] == 0 ? "停止" : "未知来源"))));
            }
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="location.href=\'/lottery/trace/readdetail?lottery_additional_id=' . $model['lottery_additional_id'] . '\'">查看</span>
                        </div>';
            }
        ]
    ]
]);
?>

<script type="text/javascript">
    function goReset() {
        location.href = '/lottery/trace/index';
    }
    function search() {
        var lottery_additional_code = $("#lottery_additional_code").val();
        var lottery_code = $("#lottery_code").val();
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var status = $("#status").val();
        var param = "?1=1";
        var userInfo = $("#user_info").val();
        if (lottery_additional_code != "") {
            param += "&lottery_additional_code=" + lottery_additional_code;
        }
        if (lottery_code > 0) {
            param += "&lottery_code=" + lottery_code;
        }
        if (startdate != "") {
            param += "&startdate=" + startdate;
        }
        if (enddate != "") {
            param += "&enddate=" + enddate;
        }

        if (status >= 0) {
            param += "&status=" + status;
        }
        if (userInfo != "") {
            param += "&user_info=" + userInfo;
        }
        location.href = '/lottery/trace/index' + param;
    }
</script>