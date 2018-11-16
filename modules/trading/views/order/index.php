<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\helpers\Constants;

$pay_type = [
    "" => "请选择",
    '1|5' => '购彩',
    '2' => '转账',
    '3' => '充值',
    '4' => '提现',
    '6|18' => '退款',
    '7|8|12|13' => '定投计划',
    '9' => '门店出票',
    '10|16' => '服务费',
    '11' => '奖金发放',
    '15' => '奖金',
    '12' => '提成',
    '17' => '文章购买',
    '20' => '活动奖金发放',
    '21' => '不可提现金额转可提现金额',
    '22' => '可提现金额转入',
    '23' => '推广提成发放',
    '25' => '咕币充值'
];
//1、购彩 2、转账 3、充值 4、提现  5、购彩-合买  6、退款 7、定投计划-认购  8、定投计划-收款 9、门店出票 10、服务费  11、奖金发放 12、定投计划-结算收款  13、定投计划-结算付款 14、合买-提成
$way_type = [
    "" => "请选择",
    "2_NATIVE" => "微信扫码",
    "2_JSAPI" => "微信公众号",
    "2_APP" => "微信APP",
    "1_PAGE" => "支付宝扫码",
    "1_WAP" => "支付宝H5",
    "1_APP" => "支付宝APP",
    "4_GLC" => '钱包二维码',
    "3_YE" => "余额"
];
$status = [
    "" => "请选择",
    '0' => '未支付',
    '1' => '已支付',
    '2' => '支付失败',
    '3' => '退款成功'
];
$flow = [
    "" => "请选择",
    "1" => "收入",
    "2" => "支出",
];
echo '<form action="/trading/order/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("交易流水号  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "pay_no", isset($_GET["pay_no"]) ? $_GET["pay_no"] : "", ["class" => "form-control", "placeholder" => "交易流水号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("第三方交易号", "order_code", ["style" => "margin-left:15px;"]);
echo Html::input("input", "outer_no", isset($_GET["outer_no"]) ? $_GET["outer_no"] : "", ["class" => "form-control", "placeholder" => "第三方交易号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("商户订单号  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "order_code", isset($_GET["order_code"]) ? $_GET["order_code"] : "", ["class" => "form-control", "placeholder" => "商户订单号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("交易金额  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "pay_money_min", isset($_GET["pay_money_min"]) ? $_GET["pay_money_min"] : "", ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("input", "pay_money_max", isset($_GET["pay_money_max"]) ? $_GET["pay_money_max"] : "", ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("会员编号  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "cust_no", isset($_GET["cust_no"]) ? $_GET["cust_no"] : "", ["class" => "form-control", "placeholder" => "会员编号,手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("交易时间  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "startdate", isset($_GET["startdate"]) ? $_GET["startdate"] :date("Y-m-d",strtotime("-1 weeks")), ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "enddate", isset($_GET["enddate"]) ? $_GET["enddate"] :date("Y-m-d"), ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("交易分类  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("pay_type", isset($_GET["pay_type"]) ? $_GET["pay_type"] : "", $pay_type, ["class" => "form-control", "style" => "width:195px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("支付场景  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("way_type", isset($_GET["way_type"]) ? $_GET["way_type"] : "", $way_type, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("交易状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("status", isset($_GET["status"]) ? $_GET["status"] : "", $status, [ "class" => "form-control", "style" => "width:85px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("资金流向  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("flow", isset($_GET["flow"]) ? $_GET["flow"] : "", $flow, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href='/trading/order/index'"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '交易时间',
            'value' => 'create_time'
        ], [
            'label' => '交易说明',
            'value' => 'body'
        ], [
            'label' => '交易流水号',
            'value' => 'pay_no'
        ], [
            'label' => '商户订单号',
            'value' => 'order_code'
        ], [
            'label' => '第三方交易号',
            'value' => function($model) {
                return $model["outer_no"] ? $model["outer_no"] : "";
            }
        ], [
            'label' => '交易场景',
            'value' => 'way_name'
        ], [
            'label' => '状态',
            'value' => function($model) {
                $status = [
                    '0' => '未支付',
                    '1' => '已支付',
                    '2' => '支付失败',
                    '3' => '退款成功',
                    '4' => '已取消'
                ];
                return $status[$model['status']];
            }
                ],
                [
                'label' => '订单总金额',
                'value' => function($model) {
                return $model["total_money"]>0 ? $model["total_money"] : $model["pay_money"] ;
                }
                ],
                [
                'label' => '需付金额',
                'value' => function($model) {
                return $model["pay_pre_money"] ? $model["pay_pre_money"] : "";
                }
                ],
                [
                    'label' => '实付金额',
                    'value' => function($model) {
                        $payType = Constants::PAY_TYPE_SPEND;
                        if(in_array($model["pay_type"], $payType)){
                            return $model["pay_money"] ?"-".$model["pay_money"] : "";
                        }else{
                            return $model["pay_money"] ?$model["pay_money"] : "";
                        }
                        
                    }
                ],   [
                    'label' => '当前余额',
                    'value' => function($model) {
                        return $model["balance"] && $model['pay_type'] != 25 ? $model["balance"] : "";
                    }
                ], 
                        
                [
                'label' => '优惠券',
                'value' => function($model) {
                $disData=json_decode($model["discount_detail"],true);
                return -(isset($disData['coupons']) ? $disData['coupons']['money'] : "0");
                }
                ],
                [
                'label' => '咕币',
                'value' => function($model) {
                $disData=json_decode($model["discount_detail"],true);
                return -(isset($disData['coin']) ? $disData['coin']['money'] : "0");
                }
                ],
                [
                    'label' => '交易分类',
                    'value' => 'pay_type_name'
                ], [
                    'label' => '会员编号',
                    'value' => 'cust_no'
                ], [
                    'label' => '会员手机号',
                    'value' => "user_tel"
                ],
//                        [
//                    'label' => '出单门店',
//                    'value' => "store_code"
//                ],[
//                    'label' => '门店手机号',
//                    'value' => "phone_num"
//                ],
//                        [
//                    'label' => '操作',
//                    'format' => 'raw',
//                    'value' => function($model) {
//                        return '<div class="am-btn-group am-btn-group-xs">
//                            <span class="handle pointer">查看</span>
//                            <span class="handle pointer">| 退款</span>
//                        </div>';
//                    }
//                ]
            ]
        ]);

        