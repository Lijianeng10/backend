<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            "label" => "订单号",
            "value" => function($model) {
                return $model["lotAdditional"]["lottery_additional_code"];
            }
        ], [
            "label" => "彩种",
            "value" => function($model) {
                return $model["lotAdditional"]["lottery_name"];
            }
        ], [
            "label" => "追号情况",
            "value" => function($model) {
                return $model['orderStatus'][$model["lotAdditional"]["status"]];
            }
        ], [
            "label" => "投注金额",
            "value" => function($model) {
                return $model["lotAdditional"]["total_money"];
            }
        ], [
            "label" => "中奖金额",
            "value" => function($model) {
                $win_amount = 0;
                foreach ($model["lotOrders"] as $order) {
                    if ($order["status"] == 4) {
                        $win_amount+=$order['win_amount'];
                    }
                }
                return $win_amount;
            }
        ], [
            "label" => "已追期数/共追期数",
            "value" => function($model) {
                return "已追" . $model["lotAdditional"]["chased_num"] . "期/共" . $model["lotAdditional"]["periods_total"] . "期";
            }
        ], [
            "label" => "下单时间",
            "value" => function($model) {
                return $model["lotAdditional"]["create_time"];
            }
        ], [
            "label" => "出票门店",
            "value" => function($model) {
                return $model["lotAdditional"]["store_name"]."(".$model["lotAdditional"]["store_no"].")";
            }
        ],[
            "label" => "投注详情",
            "format" => "raw",
            "value" => function($model) {
                $html = "";
                $items = [
                    '1' => '未支付',
                    '2' => '处理中',
                    '3' => '待开奖',
                    '4' => '中奖',
                    '5' => '未中奖',
                    '6'=>'出票失败',
                    '9'=>'过点撤销',
                    '10'=>'拒绝出票',
                ];
                foreach ($model["lotOrders"] as $order) {
                        $order["bet_val"] = trim($order["bet_val"], "^");
                        $betVals = explode("^", $order["bet_val"]);
                        $ret = "";
                        foreach ($betVals as $v) {
                            $ret.=$v . "&nbsp;&nbsp;&nbsp;";
                        }
                        $html.='<div class="tableBorder marginRight5">';
                        $html.=Html::tag("div", '投注期数：第' . $order['periods'] . '期', ["class" => ""]);
                        $html.=Html::tag("div", '投注号码：' . $ret);
                        $html.=Html::tag("div", '投注金额：' . $order['bet_money'] . '元');
                        $html.=Html::tag("div", '开奖时间：' . $order['lottery_time']);
                        $html.=Html::tag("div", '开奖状态：'.$items[$order["status"]], ["class" => ""]);
                        if ($order["status"] == 4) {
                            $html.=Html::tag("div", '中奖金额：' . $order['win_amount'] . '元', ["class" => ""]);
                        } 
                        $html.='</div>';
                        $html.=Html::tag("hr", "", ["class" => "resultPage marginRight5"]);
                }
                return $html;
            }
                ],
//                        [
//                    "label" => "支付信息",
//                    "value" => function($model) {
//                        return "";
//                    }
//                ], 
                        [
                    "label" => "操作",
                    "format" => "raw",
                    "value" => function() {
                        return Html::button("返回", ["class" => "am-btn am-btn-primary", "onclick" => "window.history.go(-1);"]);
                    }
                        ]
                    ]
                ]);
                