<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\helpers\Constants;

echo '<form action="/lottery/autoticket/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("出票编号", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "out_order_code", isset($_GET["out_order_code"]) ? $_GET["out_order_code"] : "", ["class" => "form-control", "placeholder" => "出票编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("订单编号  ", "", ["style" => "margin-left:0px;"]);
echo Html::input("input", "order_code", isset($_GET["order_code"]) ? $_GET["order_code"] : "", ["class" => "form-control", "placeholder" => "订单编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("出票单票号", "", ["style" => "margin-left:0px;"]);
echo Html::input("input", "ticket_code", isset($_GET["ticket_code"]) ? $_GET["ticket_code"] : "", ["class" => "form-control", "placeholder" => "出票单票号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("订单状态  ", "", ["style" => "margin-left:13px;"]);
echo Html::dropDownList("status", isset($_GET["status"]) ? $_GET["status"] : "", $status, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("彩种  ", "", ["style" => "margin-left:25px;"]);
echo Html::dropDownList("play", isset($_GET["play"]) ? $_GET["play"] : "", $play, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:7px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/lottery/autoticket/index'"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '出票编号',
            'value' => 'out_order_code'
        ], [
            'label' => '订单编号',
            'value' => 'order_code'
        ], [
            'label' => '出票单票号',
            'value' => 'ticket_code'
        ],[
            'label' => '串关方式',
            'value' => function($model){
                $free = Constants::FREE_TYPE;
                if(isset($model["free_type"])&&isset($free[$model["free_type"]])){
                    return $free[$model["free_type"]];
                }else{
                    return $model["free_type"];
                }
            }
        ],[
            'label' => '彩种',
            'value' => function($model){
                $lottery = Constants::AUTO_PLAY;
                $lottery2 = Constants::LOTTERY;
                if(isset($lottery[$model["lottery_code"]])){
                    return $lottery[$model["lottery_code"]];
                }elseif(isset($lottery2[$model["lottery_code"]])){
                    return $lottery2[$model["lottery_code"]];
                }else{
                    return "";
                }
            }
        ],[
            'label' => '期数',
            'value' => 'periods'
        ], [
            'label' => '倍数',
            'value' => 'multiple'
        ],[
            'label' => '投注金额',
            'value' => 'amount'
        ],[
            'label' => '注数',
            'value' =>'count'
        ],[
            'label' => '订单状态',
            'value' => function($model){
                $status = Constants::AUTO_STATUS;
                if(isset($model["status"])&&!empty($status[$model["status"]])){
                    return $status[$model["status"]];
                }else{
                    return "";
                }
            }
        ],[
            'label' => '中奖金额',
            'value' => 'zmf_award_money'
        ],[
            'label' => '出票方',
            'value' => 'source'
        ],[
            'label' => '创建时间',
            'value' => 'create_time'
        ],[
            'label' => '操作',
            'format' => 'raw',
            'value' => function( $model) {
                return '<div class="am-btn-group am-btn-group-xs">'
                    . ($model['status'] == 3 ?  ( '<span class="handle pointer" onclick="editAutoOrder(' . $model["out_order_id"] . ')"> 编辑</span>') :( '<span class="handle pointer" onclick="readAutoOrder(' . $model["out_order_id"] . ')"> 查看</span>')  ) . '</div>';
            }
        ]
    ]
]);
?>


<script type="text/javascript">
    function readAutoOrder(outId) {
        modDisplay({width: 450, height: 350, title: "详情", url: "/lottery/autoticket/auto-order-read?outId=" + outId});
    }
    
    function editAutoOrder(outId) {
        modDisplay({width: 400, height: 330, title: "编辑", url: "/lottery/autoticket/auto-order-edit?outId=" + outId});
    }
    
</script>
