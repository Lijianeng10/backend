<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\helpers\Constants;

echo '<form action="/lottery/zmforder/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("订单编号", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "order_code", isset($_GET["order_code"]) ? $_GET["order_code"] : "", ["class" => "form-control", "placeholder" => "订单编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("消息流水号  ", "", ["style" => "margin-left:0px;"]);
echo Html::input("input", "messageId", isset($_GET["messageId"]) ? $_GET["messageId"] : "", ["class" => "form-control", "placeholder" => "消息流水号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
//echo '<li>';
//echo Html::label("出票单票号", "", ["style" => "margin-left:0px;"]);
//echo Html::input("input", "ticket_code", isset($_GET["ticket_code"]) ? $_GET["ticket_code"] : "", ["class" => "form-control", "placeholder" => "出票单票号", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
echo '<li>';
echo Html::label("订单状态  ", "", ["style" => "margin-left:13px;"]);
echo Html::dropDownList("status", isset($_GET["status"]) ? $_GET["status"] : "", $status, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:7px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/lottery/zmforder/index'"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '订单编号',
            'value' => 'order_code'
        ], [
            'label' => '版本号',
            'value' => 'version'
        ], [
            'label' => '命令码',
            'value' => 'command'
        ],[
            'label' => '消息流水号',
            'value' => 'messageId' 
        ], [
            'label' => '状态',
            'value' =>function($model){
                $status = Constants::ZMF_STATUS;
                if(isset($model["status"])&&isset($status[$model["status"]])){
                    return $status[$model["status"]];
                }else{
                    return "";
                }
            }
        ], [
            'label' => '投注内容',
            'contentOptions' => [
                'width'=>'21%'
            ],
            'value' => 'bet_val'
        ],[
            'label' => '同步返回',
             'contentOptions' => [
                'width'=>'21%'
            ],
            'value' => 'ret_sync_data'
        ], [
            'label' => '异步返回',
             'contentOptions' => [
                'width'=>'21%'
            ],
            'value' => 'ret_async_data'
        ],
//                [
//            'label' => '订单状态',
//            'value' => function($model){
//                $status = Constants::AUTO_STATUS;
//                if(isset($model["status"])&&!empty($model["status"])){
//                    return $status[$model["status"]];
//                }else{
//                    return "";
//                }
//            }
//        ],[
//            'label' => '创建时间',
//            'value' => 'create_time'
//        ],
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

