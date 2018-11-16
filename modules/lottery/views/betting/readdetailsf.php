<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\modules\lottery\helpers\Constant;
use app\modules\common\helpers\PublicHelpers;

//use yii\web\View;
//$this->params = $data["bet"];
//echo Html::tag("div", Html::tag("span", $data["lotOrder"]["lottery_name"], ["class" => "buttomspan bold", "style" => "font-size:16px;"]));
//echo Html::tag("span", Html::label("方案信息", "", ["style" => "margin-top:10px;font-size: 16px;font-weight:700;color:#6b6b6b"]));
//echo Html::tag("hr", "", ["class" => "resultPage"]);
//print_r($data);
//die;
echo DetailView::widget([
    'model' => $data["lotOrder"],
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
                $items = Constant::ORDER_STATUS;
                 return $items[$model["status"]]." ( ".$model["periods"] ." ) ";

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
                                            "dataProvider" => $model["schedules"],
                                            'options' => [
                                                'class' => 'table table-striped table-bordered modalTable middle',
                                                'style' => 'margin-bottom:0px;'
                                            ],
                                            "columns" => [
                                                [
                                                    'label' => '主队 VS 客队',
                                                    'format' => 'raw',
                                                    'value' => function($val) {
                                                        return '<span style="display:inline-block;text-align:center;">' . $val["sid"] . '<br />' . $val["home_team"] . 'VS' . $val["visit_team"] . '</span>';
                                                    }
                                                ], [
                                                    'label' => '赛果',
                                                    'value' => function($val) {
                                                        if ($val["result"] == "0") {
                                                            return "负";
                                                        } else if ($val["result"] == "1") {
                                                            return "平";
                                                        }else if ($val["result"] == "3") {
                                                            return "胜";
                                                        } else {
                                                            return "";
                                                        }
                                                    }
                                                ], [
                                                    'label' => '投注内容',
                                                    'format' => 'raw',
                                                    'value' => function($val) {
                                                        $str="";
                                                        $con = "";
                                                        for ($i = 0; $i < strlen($val["bet_val"]); $i++) {
                                                            if ($val["bet_val"][$i] == "0") {
                                                                $con = "负";
                                                            } else if ($val["bet_val"][$i] == "1") {
                                                                $con = "平";
                                                            } else if ($val["bet_val"][$i] == "3") {
                                                                $con = "胜";
                                                            } else if ($val["bet_val"][$i] == "_") {
                                                                $con = "_";
                                                            }
                                                            if ($val["result"] ==$val["bet_val"][$i]) {
                                                               $str.="<span style='display:inline-block;text-align:center;color:red;'>" . $con . "</span>";
                                                            } else {
                                                               $str.= "<span style='display:inline-block;text-align:center;'>" . $con . "</span>";
                                                            }
                                                        }
                                                        return $str;
                                                    }
                                                ]
                                            ]
                                ]);
                            }
                                ], [
                                    "label" => "中奖金额",
                                    "value" => function($model) {
                                        return $model["lotOrder"]["win_amount"] ? $model["lotOrder"]["win_amount"] . " 元" : "";
                                    }
                                ],  [
                                    "label" => "派奖时间",
                                    "value" => function($model) {
                                        return $model["lotOrder"]["award_time"] ? $model["lotOrder"]["award_time"] : "";
                                    }
                                ],[
                                    "label" => "投注信息",
                                    "value" => function($model) {
                                        return $model["lotOrder"]["play_name"] . " " . $model["lotOrder"]["count"] . " 注 " . $model["lotOrder"]["bet_double"] . " 倍 " . ("( 投注金额: " . $model["lotOrder"]["bet_money"] . " 元" . " )");
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
                                        if (!empty($model["lotOrder"]["pic"])) {
                                            foreach ($model["lotOrder"]["pic"] as $val) {
                                                if (!empty($val)) {
                                                    $html .= "<div style='display:inline-block' data-magnify='gallery' href={$val} data-caption='出票照片'>";
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
                                        if (!empty($model["lotOrder"]["out_time"])) {
                                             $html=$model["lotOrder"]["out_time"];
                                        }
                                        return $html;
                                    }
                                ],[
                                    "label" => "拒绝理由",
                                    "value" => function($model) {
                                        return $model["lotOrder"]["refuse_reason"]??"";
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
//                                        echo Html::tag("span", Html::label("处理明细", "", ["style" => "font-size: 14px;font-weight:700;color:#6b6b6b"]));
//                                        echo GridView::widget([
//                                            "dataProvider" => $data['betting_detail'],
//                                            'options' => [
//                                                'class' => 'table table-striped table-bordered modalTable',
//                                                'id' => 'detailList',
//                                                'style' => 'width:92%;margin-bottom:0px;'
//                                            ],
//                                            "columns" => [
//                                                [
//                                                    'class' => 'yii\grid\SerialColumn',
//                                                    'headerOptions' => [
//                                                        'style' => 'width:10px;'
//                                                    ],
//                                                ], [
//                                                    'label' => '场次',
//                                                    'value' => function($model) {
//                                                        return $model["bet"];
//                                                    }
//                                                ], [
//                                                    'label' => '过关方式',
//                                                    'value' => function($model) {
//                                                        return $model["play_name"];
//                                                    }
//                                                ], [
//                                                    'label' => '倍数',
//                                                    'value' => function($model) {
//                                                        return "1 注 " . $model["bet_double"] . " 倍";
//                                                    }
//                                                ], [
//                                                    'label' => '投注金额',
//                                                    'value' => function($model) {
//                                                        return $model["bet_money"] . "元";
//                                                    }
//                                                ], [
//                                                    'label' => '状态',
//                                                    'value' => "statusName"
//                                                ], [
//                                                    'label' => '中奖金额',
//                                                    'value' => 'win_amount'
//                                                ]
//                                            ]
//                                        ]);
//                        echo "<span id='getDetailMore' style='width:92%;float:left;text-align:center;font-size: 14px;'><a>+加载更多</a></span>";

                        echo Html::button("返回", ["class" => "am-btn am-btn-primary", "onclick" => "closeMask();"]);
                        ?>
                        <script>
                            $(function () {
                                var offset = 10;

                                var lottery_order_id =<?php echo $_GET["lottery_order_id"]; ?>;
//        if (offset >= total) {
//            $("#getDetailMore").hide();
//        }
        $("#getDetailMore").click(function () {
            if (offset < total) {
                var data = {offset: offset, lottery_order_id: lottery_order_id};
                $.ajax({
                    url: "/lottery/betting/get-deatail-list",
                    async: false,
                    type: 'POST',
                    data: data,
                    dataType: "json",
                    success: function (json) {
                        if (json["code"] == 600) {
                            var html = "";
                            $.each(json["result"], function (k, v) {
                                html += '<tr data-key="' + offset + '" ' + (v["status"] == 4 ? ('style="color:red;"') : "") + '><td>' + (++offset) + '</td><td>' + v["bet"] + '</td><td>' + v["play_name"] + '</td><td>1 注 ' + v["bet_double"] + ' 倍</td><td>' + v["bet_money"] + ' 元</td><td>' + v["statusName"] + ' </td><td>' + v["win_amount"] + '</td></tr>';
                            });
                            $("#detailList tbody").append(html);
                            if (offset >= total) {
                                $("#getDetailMore").hide();
                            }
                        } else {
                            alert(json["msg"]);
                        }
                    }
                });
            }
        });
    });
</script>
