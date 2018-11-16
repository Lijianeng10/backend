<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\modules\lottery\helpers\Constant;

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
        ], [
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
            "label" => "订单状态",
            "value" => function($model) {
                $items = [
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
                return $items[$model["bet_status"]] . " ( " . $model["periods"] . " ) ";
            }
        ], [
            "label" => "投注时间",
            "value" => function($model) {
                return $model["bet_time"];
            }
        ], 
    ]
]);
echo DetailView::widget([
    'model' => $data,
    'options' => [
        'class' => 'table table-striped table-bordered modalTable',
        'style' => 'width:45%;float:left; margin-left:10px;'
    ],
    'attributes' => [
        [
            "label" => "交易流水号",
            "value" => function($model) {
                return $model["pay_no"];
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
                return sprintf("%.2f", $model["pay_money"] + $model["discount_money"]) . ("元(实际支付:{$model['pay_money']}元,卡券优惠:{$model['discount_money']}元)");
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
        'class' => 'table table-striped table-bordered modalTable tableTh100',
        'style' => 'width:92%;'
    ],
    'attributes' => [
        [
            "label" => "投注内容",
            "format" => "raw",
            "value" => function($model) {
                $model["bet_val"] = trim($model["bet_val"], "^");
                $betVals = explode("^", $model["bet_val"]);
                $playCodes = explode(",", $model["play_code"]);
                $playNames = explode(",", $model["play_name"]);
                $ret = "";
                if (empty($model["lottery_numbers"])) {
                    foreach ($betVals as $key=> $val) {
                        $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' .$playNames[$key] .'</span>'.$val;
                    }
                    return $ret;
                } else {
                    if (in_array($model["lottery_id"], ['2001', '1001', '1002', '1003', '2002', '2003', '2004'])) {
                        foreach ($betVals as $k => $v) {
                            if ($model["lottery_id"] == "2001" || $model["lottery_id"] == "1001") {
                                $resAry = explode("|", $model["lottery_numbers"]);
                                $resRed = explode(",", $resAry[0]);
                                $resBlue = explode(",", $resAry[1]);
                                $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$k] . '</span>';
                                $bet = explode("|", $v);
                                $betRed = explode(",", $bet[0]);
                                $betBlue = explode(",", $bet[1]);
                                foreach ($betRed as $key => $val) {
                                    if (in_array($val, $resRed)) {
                                        $ret.="<span class='yuan_0'>" . $val . "</span> ";
                                    } else {
                                        $ret.="<span class='yuan_3'>" . $val . "</span> ";
                                    }
                                }
                                foreach ($betBlue as $key => $val) {
                                    if (in_array($val, $resBlue)) {
                                        $ret.="<span class='yuan_1'>" . $val . "</span> ";
                                    } else {
                                        $ret.="<span class='yuan_4'>" . $val . "</span> ";
                                    }
                                }
                                    $ret .= '</p>';
                                }
                                if (in_array($playCodes[$k], ['100201', '100211','200201', '200211', '200301', '200302', '200401', '200402'])) {
                                    $resAry = explode(",", $model["lottery_numbers"]);
                                    $bet = explode("|", $v);
                                    $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$k] . '</span>';
                                    foreach ($bet as $key => $val) {
                                        if (strlen($val) > 1) {
                                            $ball = explode(",", $val);
                                            foreach ($ball as $n => $m) {
                                            if ($resAry[$key] != "undefined" && $resAry[$key] == $m) {
                                                $ret.="<span class='yuan_0'>" . $m . "</span> ";
                                            } else {
                                                $ret.="<span class='yuan_3'>" . $m . "</span> ";
                                            }
                                        }
                                        $ret .= ' |  ';
                                    } else {
                                        if ($resAry[$key] != "undefined" && $resAry[$key] == $val) {
                                            $ret.="<span class='yuan_0'>" . $val . "</span> ";
                                        } else {
                                            $ret.="<span class='yuan_3'>" . $val . "</span> ";
                                        }
                                    }
                                }
                                $ret .= '</p>';
                            } elseif(in_array($playCodes[$k], ['100301', '100302'])){
                                $resAry = explode("|", $model["lottery_numbers"]);
                                $resOne= explode(",",$resAry[0]);
                                $bet = explode(",", $v);
                                $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$k] . '</span>';
                                foreach ($bet as $val){
                                    if(in_array($val,$resOne)){
                                        $ret.="<span class='yuan_0'>" . $val . "</span> "; 
                                    }elseif($val==$resAry[1]){
                                        $ret.="<span class='yuan_1'>" . $val . "</span> ";
                                    }else{
                                        $ret.="<span class='yuan_3'>" . $val . "</span> ";
                                    }
                                }
                                $ret .= '</p>';
                            }elseif (in_array($playCodes[$k], ['100202', '100212', '100203', '100213', '200202', '200212', '200203', '200213'])) {
                                $resAry = explode(",", $model["lottery_numbers"]);
                                $bet = explode(",", $v);
                                $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$k] . '</span>';
                                foreach ($bet as $key => $val) {
                                    if (in_array($val, $resAry)) {
                                        $ret.="<span class='yuan_0'>" . $val . "</span> ";
                                    } else {
                                        $ret.="<span class='yuan_3'>" . $val . "</span> ";
                                    }
                                }
                                $ret .= '</p>';
                            }
                        }
                    } elseif (in_array($model["lottery_id"], ['2005', '2006', '2007', '2008','2011'])) {
                        $resultBalls = explode(",", $model["lottery_numbers"]);
                        foreach ($betVals as $key => $val) {
                                            //前一单式复式
                            if (in_array($playCodes[$key], ['200531', '200541', '200631', '200641', '200731', '200741', '200831', '200841','201131','201141'])) {
                                $areas = explode(",", $val);
                                $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                foreach ($areas as $v) {
                                    if ($resultBalls[0] == $v) {
                                        $ret .= '<span class="yuan_0">' . $v . '</span>';
                                    } else {
                                        $ret .= '<span class="yuan_3">' . $v . '</span>';
                                    }
                                }
                                 $ret .= '</p>';
                            } elseif (in_array($playCodes[$key], ['200532', '200533', '200542', '200543', '200632', '200633', '200642', '200643', '200732', '200733', '200742', '200743', '200832', '200833', '200842', '200843','201132','201133','201142','201143'])) {
                                if (strstr($val, ";")) {
                                    $areas = explode(";", $val);
                                    $num = explode(",", $areas[0]);
                                    $num2 = explode(",", $areas[1]);
                                    $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                    foreach ($num as $v) {
                                        if ($resultBalls[0] == $v) {
                                            $ret .= '<span class="yuan_0">' . $v . '</span>';
                                        } else {
                                            $ret .= '<span class="yuan_3">' . $v . '</span>';
                                        }
                                    }
                                    $ret .= ' | ';
                                    foreach ($num2 as $v) {
                                        if ($resultBalls[1] == $v) {
                                            $ret .= '<span class="yuan_0">' . $v . '</span>';
                                        } else {
                                            $ret .= '<span class="yuan_3">' . $v . '</span>';
                                        }
                                    }
                                    if (count($areas) == 3) {
                                        $num3 = explode(",", $areas[2]);
                                        $ret .= ' | ';
                                        foreach ($num3 as $v) {
                                            if ($resultBalls[2] == $v) {
                                                $ret .= '<span class="yuan_0">' . $v . '</span>';
                                            } else {
                                                $ret .= '<span class="yuan_3">' . $v . '</span>';
                                            }
                                        }
                                    }
                                    $ret .="</p>";
                                } else {
                                    $areas = explode(";", $val);
                                    $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                    if ($resultBalls[0] == $areas[0]) {
                                        $ret .= '<span class="yuan_0">' . $areas[0] . '</span>';
                                    } else {
                                        $ret .= '<span class="yuan_3">' . $areas[0] . '</span>';
                                    }
                                    if ($resultBalls[1] == $areas[1]) {
                                        $ret .= '<span class="yuan_0">' . $areas[1] . '</span>';
                                    } else {
                                        $ret .= '<span class="yuan_3">' . $areas[1] . '</span>';
                                    }
                                    if (count($areas) == 3) {
                                        if ($resultBalls[2] == $areas[2]) {
                                            $ret .= '<span class="yuan_0">' . $areas[2] . '</span>';
                                        } else {
                                            $ret .= '<span class="yuan_3">' . $areas[2] . '</span>';
                                        }
                                    }
                                    $ret .="</p>";
                                }
                            } elseif (in_array($playCodes[$key], ['200534', '200544', '200634', '200644', '200734', '200744', '200834', '200844','201134','201144'])) {
                                $areas = explode(",", $val);
                                $newAry = [$resultBalls[0], $resultBalls[1]];
                                $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                foreach ($areas as $v) {
                                    if (in_array($v, $newAry)) {
                                        $ret .= '<span class="yuan_0">' . $v . '</span>';
                                    } else {
                                         $ret .= '<span class="yuan_3">' . $v . '</span>';
                                    }
                                }
                                $ret .="</p>";
                                                //前三组选单式复式
                            } elseif (in_array($playCodes[$key], ['200535', '200545', '200635', '200645', '200735', '200745', '200835', '200845','201135','201145'])) {
                                $areas = explode(",", $val);
                                $newAry = [$resultBalls[0], $resultBalls[1], $resultBalls[2]];
                                $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                foreach ($areas as $v) {
                                    if (in_array($v, $newAry)) {
                                        $ret .= '<span class="yuan_0">' . $v . '</span>';
                                    } else {
                                        $ret .= '<span class="yuan_3">' . $v . '</span>';
                                    }
                                }
                                $ret .="</p>";
                           } elseif (in_array($playCodes[$key], ['200554', '200654', '200754', '200854','201154'])) {
                                $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                $areas = explode("#", $val);
                                $danBall = $areas[0];
                                $tuoBall = explode(",", $areas[1]);
                                $newAry = [$resultBalls[0], $resultBalls[1]];
                                $ret.="<span>胆：</span>";
                                if (in_array($danBall, $newAry)) {
                                    $ret .= '<span class="yuan_0">' . $danBall . '</span>';
                                } else {
                                    $ret .= '<span class="yuan_3">' . $danBall . '</span>';
                                }
                                    $ret.="<span>拖：</span>";
                                foreach ($tuoBall as $v) {
                                    if (in_array($v, $newAry)) {
                                        $ret .= '<span class="yuan_0">' . $v . '</span>';
                                    } else {
                                        $ret .= '<span class="yuan_3">' . $v . '</span>';
                                    }
                                }
                                $ret .="</p>";
                            } elseif (in_array($playCodes[$key], ['200555', '200655', '200755', '200855','201155'])) {
                                $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                $areas = explode("#", $val);
                                $danBall = explode(",", $areas[0]);
                                $tuoBall = explode(",", $areas[1]);
                                $newAry = [$resultBalls[0], $resultBalls[1], $resultBalls[2]];
                                $ret.="<span>胆：</span>";
                                foreach ($danBall as $v) {
                                    if (in_array($v, $newAry)) {
                                        $ret .= '<span class="yuan_0">' . $v . '</span>';
                                    } else {
                                        $ret .= '<span class="yuan_3">' . $v . '</span>';
                                    }
                                }
                                $ret.="<span>拖：</span>";
                                foreach ($tuoBall as $v) {
                                    if (in_array($v, $newAry)) {
                                        $ret .= '<span class="yuan_0">' . $v . '</span>';
                                    } else {
                                        $ret .= '<span class="yuan_3">' . $v . '</span>';
                                    }
                                }
                                $ret .="</p>";
                            }else if(in_array($playCodes[$key], ['201163', '201164', '201165','200763', '200764', '200765'])){
                                                 //乐选单式
                                $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                $areas = explode(";", $val);
                                foreach ($areas as $v) {
                                    if (in_array($v, $resultBalls)) {
                                        $ret .= '<span class="yuan_0">' . $v . '</span>';
                                    } else {
                                        $ret .= '<span class="yuan_3">' . $v . '</span>';
                                    }
                                }
                                 $ret .="</p>";
                            }else if(in_array($playCodes[$key], ['201166','201167','201168','200766','200767','200768'])){
                                                 //乐选复式
                                $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                $areas = explode(";", $val);
                                foreach ($areas as $v) {
                                    $str="";
                                    $lNum = explode(",", $v);
                                    foreach ($lNum as $m){
                                        if (in_array($m, $resultBalls)) {
                                            $str .= '<span class="yuan_0">' . $m . '</span>';
                                        } else {
                                            $str .= '<span class="yuan_3">' . $m . '</span>';
                                        }
                                    }
                                    $str.=" | ";
                                    $ret.=$str ;
                                }
                                $ret .="</p>";
                            }else {
                                //任选胆拖
                                if (strstr($val,"#")) {
                                    $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                    $areas = explode("#", $val);
                                    $danBall = explode(",", $areas[0]);
                                    $tuoBall = explode(",", $areas[1]);
                                    $ret.="<span>胆：</span>";
                                    foreach ($danBall as $v) {
                                        if (in_array($v, $resultBalls)) {
                                            $ret .= '<span class="yuan_0">' . $v . '</span>';
                                        } else {
                                            $ret .= '<span class="yuan_3">' . $v . '</span>';
                                        }
                                    }
                                    $ret.="<span>拖：</span>";
                                    foreach ($tuoBall as $v) {
                                        if (in_array($v, $resultBalls)) {
                                            $ret .= '<span class="yuan_0">' . $v . '</span>';
                                        } else {
                                            $ret .= '<span class="yuan_3">' . $v . '</span>';
                                        }
                                    }
                                    $ret .="</p>";
                                } else {
                                    //任选
                                    $ret .= '<p style="margin:0;padding:0"><span class="circlePrompt">' . $playNames[$key] . '</span>';
                                    $areas = explode(",", $val);
                                    foreach ($areas as $v) {
                                        if (in_array($v, $resultBalls)) {
                                            $ret .= '<span class="yuan_0">' . $v . '</span>';
                                        } else {
                                            $ret .= '<span class="yuan_3">' . $v . '</span>';
                                        }
                                    }
                                    $ret .="</p>";
                                }
                            }
                        }
                    }
                    return $ret;
                }
            }
        ], [
            "label" => "开奖结果",
            "value" => function($model) {
                return $model["lottery_numbers"];
            }
        ], [
            "label" => "开奖时间",
            "value" => function($model) {
                 return $model["lottery_time"];
            }
        ], [
            "label" => "中奖金额",
            "value" => function($model) {
                return $model["win_amount"] ? $model["win_amount"] . " 元" : "";
            }
        ], [
            "label" => "派奖时间",
            "value" => function($model) {
                return $model["award_time"] ? $model["award_time"]: "";
            }
        ],[
            "label" => "投注信息",
            "value" => function($model) {
                return $model["count"] . "注" . $model["bet_double"] . "倍 " . ($model["is_bet_add"] == 1 ? "追加 " : "") . ("( 投注金额: " . $model["bet_money"] . " 元" . " )");
            }
        ], [
            "label" => "出票时间",
            "format" => "raw",
            "value" => function($model) {
                $html = "";
                if (!empty($model["out_time"])) {
                    $html=$model["out_time"];
                }
                return $html;
            }
        ]
    ]
]);
echo Html::button("返回", ["class" => "am-btn am-btn-primary", "onclick" => "closeMask();"]);
?>