<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\modules\lottery\helpers\Constant;

//use yii\web\View;
//$this->params = $data["bet"];
//echo Html::tag("div", Html::tag("span", $data["lotOrder"]["lottery_name"], ["class" => "buttomspan bold", "style" => "font-size:16px;"]));
//echo Html::tag("span", Html::label("方案信息", "", ["style" => "margin-top:10px;font-size: 16px;font-weight:700;color:#6b6b6b"]));
//echo Html::tag("hr", "", ["class" => "resultPage"]);
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
                return $items[$model["status"]] . " ( " . $model["periods"] . " ) ";
            }
                ], [
                    "label" => "投注时间",
                    "value" => function($model) {
                        return $model["create_time"];
                    }
                ], [
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
                    "label" => "订单金额",
                    "value" => function($model) {
                        if ($model["status"] == null) {
                            return "";
                        }
                        $items = [
                            '0' => '未支付',
                            '1' => '已支付',
                            '2' => '支付失败',
                            '3' => '退款成功',
                            '4' => '取消订单',
                        ];
                        return sprintf("%.2f", $model["pay_money"] + $model["discount_money"]) . ("元(实际支付:{$model['pay_money']}元,卡券优惠:{$model['discount_money']}元)") . $items[$model["status"]];
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
                                            "dataProvider" => $model['schedules'],
                                            'options' => [
                                                'class' => 'table table-striped table-bordered modalTable middle',
                                                'style' => 'margin-bottom:0px;'
                                            ],
                                            "columns" => [
                                                [
                                                    'label' => '主队 VS 客队',
                                                    'format' => 'raw',
                                                    'value' => function($val) {
                                                        $num = 0;
                                                        foreach ($val["lottery"] as $v) {
                                                            if ($v["play"] == "3006") {
                                                                $num++;
                                                            }
                                                        }
                                                        if (empty($val["schedule_result_3007"])) {
                                                            return '<span style="display:inline-block;text-align:center;">' . $val["schedule_code"] . '<br />' . $val["home_team_name"] . ($num != 0 ? Html::tag("span", '(' . $val['rq_nums'] . ')', ["style" => "color:red;"]) : "") . '<span style="margin-left:5px;margin-right:5px;">VS</span>' . $val["visit_team_name"] . '</span>';
                                                        }
                                                        return '<span style="display:inline-block;text-align:center;">' . $val["schedule_code"] . '<br />' . $val["home_team_name"] . ($num != 0 ? Html::tag("span", '(' . $val['rq_nums'] . ')', ["style" => "color:red;"]) : "") . '<span style="color:red;margin-left:5px;margin-right:5px;">' . $val["schedule_result_3007"] . '</span>' . $val["visit_team_name"] . '</span>';
                                                    }
                                                        ], [
                                                            'label' => '赛果',
                                                            'format' => 'raw',
                                                            'value' => function($val) {
                                                                $str = "";
                                                                if ($val["status"] == 3) {
                                                                    $str = "比赛取消";
                                                                } else {
                                                                    foreach ($val["lottery"] as $v) {
                                                                        if ($v["play"] == "3006") {
                                                                            $arr = Constant::COMPETING_BET_3006;
                                                                            if (isset($val["schedule_result_3006"]) && $val["schedule_result_3006"] != "") {
                                                                                $str.="让球" . $arr[$val["schedule_result_3006"]] . "<br/>";
                                                                            }
                                                                        }
                                                                        if ($v["play"] == "3007") {
                                                                            if (isset($val["schedule_result_3007"]) && $val["schedule_result_3007"] != "") {
                                                                                $str.=$val["schedule_result_3007"] . "<br/>";
                                                                            }
                                                                        }
                                                                        if ($v["play"] == "3008") {
                                                                            $arr = Constant::COMPETING_BET_3008;
                                                                            if (isset($val["schedule_result_3008"]) && $val["schedule_result_3008"] != "") {
                                                                                $str.=$arr[$val["schedule_result_3008"]] . "<br/>";
                                                                            }
                                                                        }
                                                                        if ($v["play"] == "3009") {
                                                                            $arr = Constant::COMPETING_BET_3009;
                                                                            if (isset($val["schedule_result_3009"]) && $val["schedule_result_3009"] != "") {
                                                                                $str.=$arr[$val["schedule_result_3009"]] . "<br/>";
                                                                            }
                                                                        }
                                                                        if ($v["play"] == "3010") {
                                                                            $arr = Constant::COMPETING_BET_3010;
                                                                            if (isset($val["schedule_result_3010"]) && $val["schedule_result_3010"] != "") {
                                                                                $str.=$arr[$val["schedule_result_3010"]] . "<br/>";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                return $str;
                                                            }
                                                        ], [
                                                            'label' => '投注内容',
                                                            'format' => 'raw',
                                                            'value' => function($val) {
                                                                $html = "";
                                                                $odds = $val["odds"];
                                                                foreach ($val["lottery"] as $key => $betVal) {
                                                                    $str = "COMPETING_BET_" . $betVal["play"];
                                                                    eval('$bet = \app\modules\lottery\helpers\Constant::' . $str . ';');
                                                                    $arr = [];
                                                                    foreach ($betVal["bet"] as $ball) {
                                                                        if ($val["schedule_result_" . $betVal["play"]] == $ball || ($betVal["play"] == "3007" && $val["schedule_result_" . $betVal["play"]] == $bet[$ball])) {
                                                                            $arr[] = Html::tag("span", $bet[$ball] . "(" . (isset($odds[$betVal["play"]][$ball]) ? $odds[$betVal["play"]][$ball] : "赔率") . ")", ["style" => "color:red;"]);
                                                                        } else {
                                                                            $arr[] = Html::tag("span", $bet[$ball] . "(" . (isset($odds[$betVal["play"]][$ball]) ? $odds[$betVal["play"]][$ball] : "赔率") . ")");
                                                                        }
                                                                    }
                                                                    if ($betVal["play"] == "3006") {
                                                                        foreach ($arr as &$v) {
                                                                            $v = '<span class="letball">让</span>' . $v;
                                                                        }
                                                                        $html .= implode(" , ", $arr) . Html::tag("br");
                                                                    } else {
                                                                        $html .= implode(" , ", $arr) . Html::tag("br");
                                                                    }
                                                                }
                                                                return $html;
                                                            }
                                                                ]
                                                            ]
                                                ]);
                                            }
                                                ], [
                                                    "label" => "奖金优化",
                                                    "value" => function($model) {
                                                        $newAry = [
                                                            "0" => "无奖金优化",
                                                            "1" => "平均优化",
                                                            "2" => "博热优化",
                                                            "3" => "博冷优化",
                                                        ];
                                                        return (isset($model["lotOrder"]["major_type"]) ? $newAry[$model["lotOrder"]["major_type"]] : "");
                                                    }
                                                        ], [
                                                            "label" => "中奖金额",
                                                            "value" => function($model) {
                                                                return $model["lotOrder"]["win_amount"] ? $model["lotOrder"]["win_amount"] . " 元" : "";
                                                            }
                                                        ], [
                                                            "label" => "派奖时间",
                                                            "value" => function($model) {
                                                                return $model["lotOrder"]["award_time"] ? $model["lotOrder"]["award_time"] : "";
                                                            }
                                                        ], [
                                                            "label" => "投注期数",
                                                            "value" => function($model) {
                                                                return $model["lotOrder"]["periods"] ? $model["lotOrder"]["periods"] : "";
                                                            }
                                                        ], [
                                                            "label" => "投注信息",
                                                            "value" => function($model) {
                                                                return (!empty($model["lotOrder"]["build_name"]) ? $model["lotOrder"]["build_name"] . " (" . $model["lotOrder"]["play_name"] . ") " . $model["lotOrder"]["count"] . " 注 " . $model["lotOrder"]["bet_double"] . " 倍 " . ("( 投注金额: " . $model["lotOrder"]["bet_money"] . " 元" . " )") : $model["lotOrder"]["play_name"] . " " . $model["lotOrder"]["count"] . " 注 " . $model["lotOrder"]["bet_double"] . " 倍 " . ("( 投注金额: " . $model["lotOrder"]["bet_money"] . " 元" . " )"));
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
                                                                            $html.= "<img class='orderImg' src={$val} />";
                                                                            $html.= "</div>";
                                                                        }
                                                                    }
                                                                }
                                                                return $html;
                                                            }
                                                        ], [
                                                            "label" => "出票时间",
                                                            "format" => "raw",
                                                            "value" => function($model) {
                                                                $html = "";
                                                                if (!empty($model["lotOrder"]["out_time"])) {
                                                                    $html = $model["lotOrder"]["out_time"];
                                                                }
                                                                return $html;
                                                            }
                                                        ]
                                                    ]
                                                ]);
                                                echo Html::tag("span", Html::label("处理明细", "", ["style" => "font-size: 14px;font-weight:700;color:#6b6b6b"]));
                                                echo GridView::widget([
                                                    "dataProvider" => $data['betting_detail'],
                                                    'options' => [
                                                        'class' => 'table table-striped table-bordered modalTable',
                                                        'id' => 'detailList',
                                                        'style' => 'width:92%;margin-bottom:0px;'
                                                    ],
                                                    "columns" => [
                                                        [
                                                            'class' => 'yii\grid\SerialColumn',
                                                            'headerOptions' => [
                                                                'style' => 'width:10px;'
                                                            ],
                                                        ], [
                                                            'label' => '场次',
                                                            'value' => function($model) {
                                                                return $model["bet"];
                                                            }
                                                        ], [
                                                            'label' => '过关方式',
                                                            'value' => function($model) {
                                                                return $model["play_name"];
                                                            }
                                                        ], [
                                                            'label' => '倍数',
                                                            'value' => function($model) {
                                                                return "1 注 " . $model["bet_double"] . " 倍";
                                                            }
                                                        ], [
                                                            'label' => '投注金额',
                                                            'value' => function($model) {
                                                                return $model["bet_money"] . "元";
                                                            }
                                                        ], [
                                                            'label' => '状态',
                                                            'value' => "statusName"
                                                        ], [
                                                            'label' => '中奖金额',
                                                            "format" => "raw",
                                                            'value' => function($model) {
                                                                $html = "";
                                                                if ($model["win_amount"] > 0) {
                                                                    $html.="<span style='color:red'>" . $model["win_amount"] . "</span>";
                                                                } else {
                                                                    $html.="<span>" . $model["win_amount"] . "</span>";
                                                                }
                                                                return $html;
                                                            }
                                                        ]
                                                    ]
                                                ]);
                                                echo "<span id='getDetailMore' style='width:92%;float:left;text-align:center;font-size: 14px;'><a>+加载更多</a></span>";


                                                echo Html::tag("span", Html::label("出票明细", "", ["style" => "font-size: 14px;font-weight:700;color:#6b6b6b"]));
                                                echo GridView::widget([
                                                    "dataProvider" => $data['deal_order'],
                                                    'options' => [
                                                        'class' => 'table table-striped table-bordered modalTable',
                                                        'id' => 'detailList',
                                                        'style' => 'width:92%;margin-bottom:0px;'
                                                    ],
                                                    "columns" => [
                                                        [
                                                            'class' => 'yii\grid\SerialColumn',
                                                            'headerOptions' => [
                                                                'style' => 'width:10px;'
                                                            ],
                                                        ], [
                                                            'label' => '场次',
                                                            "format" => "raw",
                                                            'value' => function($model) {
                                                                return $model['bet'];
                                                            }
                                                        ], [
                                                            'label' => '过关方式',
                                                            'value' => function($model) {
                                                                return $model["play_name"];
                                                            }
                                                        ], [
                                                            'label' => '倍数',
                                                            'value' => function($model) {
                                                                return $model["bet_double"] . " 倍";
                                                            }
                                                        ], [
                                                            'label' => '投注金额',
                                                            'value' => function($model) {
                                                                return $model["bet_money"] . "元";
                                                            }
                                                        ], [
                                                            'label' => '状态',
                                                            'value' => "statusName"
                                                        ], [
                                                            'label' => '中奖金额',
                                                            "format" => "raw",
                                                            'value' => function($model) {
                                                                $html = "";
                                                                if ($model["win_amount"] > 0) {
                                                                    $html.="<span style='color:red'>" . $model["win_amount"] . "</span>";
                                                                } else {
                                                                    $html.="<span>" . $model["win_amount"] . "</span>";
                                                                }
                                                                return $html;
                                                            }
                                                        ]
                                                    ]
                                                ]);

                                                echo Html::button("返回", ["class" => "am-btn am-btn-primary", "onclick" => "closeMask();"]);
                                                ?>
                                                <script>
                                                    $(function () {
                                                        var offset = 10;
                                                        var total =<?php echo $detailCount; ?>;
                                                        var lottery_order_id =<?php echo $_GET["lottery_order_id"]; ?>;
        if (offset >= total) {
            $("#getDetailMore").hide();
        }
        $("#getDetailMore").click(function () {
            if (offset < total) {
                var data = {offset: offset, lottery_order_id: lottery_order_id};
                $.ajax({
                    url: "/subchannel/betlist/get-deatail-list",
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
