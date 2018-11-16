<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\modules\lottery\helpers\Constant;
use app\modules\common\helpers\PublicHelpers;
echo DetailView::widget([
    'model' => $data,
    'options' => [
        'class' => 'table table-striped table-bordered modalTable',
        'style' => 'width:45%;float:left;'
    ],
    'attributes' => [
        [
            "label" => "方案信息",
            "value" => function($model) {
                return $model["lottery_name"];
            }
        ],
        [
            "label" => "订单号",
            "value" => function($model) {
                return $model["lottery_order_code"];
            }
        ], [
            "label" => "会员编号",
            "value" => function($model) {
                return $model["cust_no"];
            }
        ], [
            "label" => "接单门店",
            "value" => function($model) {
                return $model["store_name"] . " ( {$model['phone_num']} )";
            }
        ], [
            "label" => "订单状态",
            "value" => function($model) {
                 return $model["status_name"];

            }
                ], [
                    "label" => "投注时间",
                    "value" => function($model) {
                        return $model["create_time"];
                    }
                ],[
                    "label" => "出票手续费",
                    "value" => function($model) {
                        return $model["pay_pre_money"];
                    }
                ]
            ]
        ]);
        echo DetailView::widget([
            'model' => $payRecord,
            'options' => [
                'class' => 'table table-striped table-bordered modalTable',
                'style' => 'width:45%;float:left; margin-left:10px;'
            ],
            'attributes' => [
                [
                    "label" => "交易流水号",
                    "value" => function($model) {
                        return $model["pay_no"];
                        ;
                    }
                ], [
                    "label" => "第三方交易号",
                    "value" => function($model) {
                        return $model["outer_no"];
                    }
                ], [
                    "label" => "退款号",
                    "value" => function($model) {
                        return $model["refund_no"];
                    }
                ], [
                    "label" => "支付金额",
                    "value" => function($model) {
                        if ($model["status"] == null) {
                            return "";
                        }
                        $items = [
                            '0' => '未支付',
                            '1' => '已支付',
                            '2' => '支付失败',
                            '3' => '退款成功',
                            '4' => '取消订单'
                        ];
                        return sprintf("%.2f", $model["pay_money"]+$model["discount_money"]).("元(实际支付:{$model['pay_money']}元,卡券优惠:{$model['discount_money']}元)") . $items[$model["status"]];
                    }
                        ], [
                            "label" => "支付方式",
                            "value" => function($model) {
                                return $model["pay_name"] . "支付 ( 交易渠道: {$model["way_name"]} )";
                            }
                        ], [
                            "label" => "支付时间",
                            "value" => function($model) {
                                return $model["pay_time"];
                            }
                        ]
                    ]
                ]);
                echo DetailView::widget([
                    'model' => $data,
                    'options' => [
                        'class' => 'table table-striped table-bordered modalTable',
                        'style' => 'width:92%;'
                    ],
                    'attributes' => [
                        [
                            "label" => "投注内容",
                            "format" => "raw",
                            "value" => function($model) {
                                return GridView::widget([
                                            "dataProvider" => $model["betval_arr"],
                                            'options' => [
                                                'class' => 'table table-striped table-bordered modalTable middle',
                                            ],
                                            "columns" => [
                                                [
                                                    'label' => '投注球队',
                                                    'format' => 'raw',
                                                    'value' => function($val) {
                                                        return $val["open_mid"]."<br/>".$val["team_name"];
                                                    }
                                                ],[
                                                    'label' => '赛果',
                                                    'value' => function($val) {
                                                        if($val["result"]==1){
                                                           return ""; 
                                                        } 
                                                    }
                                                ], [
                                                    'label' => '赔率',
                                                    'value' => function($val) {
                                                       return $val["odds"];
                                                    }
                                                ], 
                                            ]
                                ]);
                            }
                                ], 
                                        [
                                    "label" => "中奖金额",
                                    "value" => function($model) {
                                        return $model["win_amount"] ? $model["win_amount"] . " 元" : "";
                                    }
                                ],  [
                                    "label" => "派奖时间",
                                    "value" => function($model) {
                                        return $model["award_time"] ? $model["award_time"] : "";
                                    }
                                ],[
                                    "label" => "投注信息",
                                    "value" => function($model) {
                                        return $model["play_name"] . " " . $model["count"] . " 注 " . $model["bet_double"] . " 倍 " . ("( 投注金额: " . $model["bet_money"] . " 元" . " )");
                                    }
                                ], [
                                    "label" => "出票人员",
                                    "value" => function($model) {
                                        return $model["optInfo"];
                                    }
                                ], [
                                    "label" => "出票照片",
                                    "format" => "raw",
                                    "value" => function($model) {
                                        $html = "";
                                        if (!empty($model["out_pic"])) {
                                            foreach ($model["out_pic"] as $val) {
                                                if (!empty($val)) {
                                                    $html .= "<div style='padding-left:4px;display:inline-block' data-magnify='gallery' href={$val} data-caption='出票照片'}>";
                                                    $html.= "<img class='orderImg' src='{$val}' />";
                                                    $html.= "</div>";
                                                }
                                            }
                                        }
                                        return $html;
                                    }
                                ],[
                                    "label" => "出票时间",
                                    "format" => "raw",
                                    "value" => function($model) {
                                        $html = "";
                                        if (!empty($model["out_time"])) {
                                             $html=$model["out_time"];
                                        }
                                        return $html;
                                    }
                                ],[
                                    "label" => "拒绝理由",
                                    "value" => function($model) {
                                        return $model["refuse_reason"]??"";
                                    }
                                ]
                            ]
                        ]);
//订单接单详情
echo Html::tag("span", Html::label("接单详情", "", ["style" => "font-size: 14px;font-weight:700;color:#6b6b6b"]));
echo GridView::widget([
    "dataProvider" => $takingRecord,
    'options' => [
        'class' => 'table table-striped table-bordered modalTable',
        'style' => 'width:92%;margin-bottom:0px;'
    ],
    "columns" => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => [
                'style' => 'width:10px;'
            ],
        ], [
            'label' => '门店名称',
            'value' => "store_name"

        ], [
            'label' => '门店编号',
            'value' => "store_code"
        ], [
            'label' => '接单状态',
            'value' => function($model) {
                $status = PublicHelpers::TAKING_STATUS;
                return $status[$model["status"]]??"";
            }
        ], [
            'label' => '创建时间',
            'value' => "create_time"
        ],[
            'label' => '更新时间',
            'value' => "modify_time"
        ],
    ]
]);
echo Html::button("返回", ["class" => "am-btn am-btn-primary", "onclick" => "closeMask();"]);
?>
