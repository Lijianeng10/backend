<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\helpers\Constants;

echo '<form action="/channel/apiorder/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("接单编号", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "api_order_code", isset($_GET["api_order_code"]) ? $_GET["api_order_code"] : "", ["class" => "form-control", "placeholder" => "接单编号", "style" => "width:150px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("商户订单号  ", "", ["style" => "margin-left:0px;"]);
echo Html::input("input", "third_order_code", isset($_GET["third_order_code"]) ? $_GET["third_order_code"] : "", ["class" => "form-control", "placeholder" => "商户订单号", "style" => "width:150px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("渠道会员", "", ["style" => "margin-left:0px;"]);
echo Html::input("input", "user_info", isset($_GET["user_info"]) ? $_GET["user_info"] : "", ["class" => "form-control", "placeholder" => "会员编号、昵称、手机号", "style" => "width:150px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("彩种  ", "", ["style" => "margin-left:44px;"]);
echo Html::dropDownList("lottery_code", isset($_GET["lottery_code"]) ? $_GET["lottery_code"] : "0", $lotteryNames, ["id" => "lottery_code", "class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("接单时间  ", "", ["style" => "margin-left:12px;"]);
echo Html::input("text", "startdate", isset($_GET["startdate"]) ? $_GET["startdate"] :date("Y-m-d",strtotime("-3 day")), ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "enddate", isset($_GET["enddate"]) ? $_GET["enddate"] :date("Y-m-d"), ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("订单状态  ", "", ["style" => "margin-left:0px;"]);
echo Html::dropDownList("status", isset($_GET["status"]) ? $_GET["status"] : "", $status, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:7px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/channel/apiorder/index'"]);
//echo Html::tag("span", "新增", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addApi()"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '接单编号',
            'value' => 'api_order_code'
        ], [
            'label' => '商户订单号',
            'value' => 'third_order_code'
        ],  [
            'label' => '投注单编号',
            'value' => 'lottery_order_code'
        ],[
            'label' => '接单时间',
            'value' => 'create_time'
        ],[
            'label' => '下单时间',
            'value' => 'jtime'
        ],[
            'label' => '截止时间',
            'value' => 'end_time'
        ],[
            'label' => '彩种',
            'value' => function($model){
                $html="";
                $plays = Constants::LOTTERY;
                $zq = Constants ::ZQ_PLAY;
                $lq = Constants :: LQ_PLAY;
                if(isset($plays[$model["lottery_code"]])&&!empty($plays[$model["lottery_code"]])){
                    $html.= $plays[$model["lottery_code"]];
                }
                if(in_array($model["lottery_code"], $zq)){
                    $html.="(竞足)";
                }elseif(in_array($model["lottery_code"], $lq)){
                    $html.="(竞篮)";
                }
                return $html;
            }
        ],[
            'label' => '倍数',
            'value' => 'multiple'
        ], [
            'label' => '期号',
            'value' => 'periods'
        ], [
            'label' => '投注金额',
            'value' => 'bet_money'
        ],[
            'label' => '订单状态',
            'value' => function($model){
                $status = Constants::THIRD_STATUS;
                if(isset($model["status"])&&!empty($model["status"])){
                    return $status[$model["status"]];
                }else{
                    return "";
                }
            }
        ],[
            'label' => '渠道会员',
            'value' => 'user_name'
        ],[
            'label' => '手机号',
            'value' => 'user_tel'
        ],
//                        [
//                    'label' => '操作',
//                    'format' => 'raw',
//                    'value' => function( $model) {
//                        return '<div class="am-btn-group am-btn-group-xs"><span class="handle pointer" onclick="readOrder(' . $model["api_order_id"] . ')"> 查看</span></div>';
//                    }
//                ]
            ]
        ]);
?>

<script>

</script>
