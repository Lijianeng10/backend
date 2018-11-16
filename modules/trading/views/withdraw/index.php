<?php

use yii\helpers\Html;
use yii\grid\GridView;

$status = [
    '' => '请选择',
    '0' => '未处理',
    '1' => '处理中',
    '2' => '提现成功',
    '3' => '提现失败',
    '4' => 'JAVA订单',
    '5' => '异常订单'
];
$custType = [
    '0' => '全部',
    '1' => '会员',
    '2' => '门店'
];

echo '<form action="/trading/withdraw/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label('商户订单号', 'order_code', ['style' => 'margin-left:15px;']);
echo Html::input('input', 'outer_no', isset($get['outer_no']) ? $get['outer_no'] : '', ['class' => 'form-control', 'placeholder' => '商户订单号', 'style' => 'width:200px;display:inline;margin-left:5px;']);
echo '</li>';
echo '<li>';
echo Html::label('提现订单号  ', '', ['style' => 'margin-left:15px;']);
echo Html::input('input', 'order_code', isset($get['order_code']) ? $get['order_code'] : '', ['class' => 'form-control', 'placeholder' => '提现订单号', 'style' => 'width:200px;display:inline;margin-left:5px;']);
echo '</li>';
echo '<li>';
echo Html::label('会员信息', '', ['style' => 'margin-left:15px;']);
echo Html::input('input', 'cust_no', isset($get['cust_no']) ? $get['cust_no'] : '', ['class' => 'form-control', 'placeholder' => '会员编号、手机号', 'style' => 'width:200px;display:inline;margin-left:5px;']);
echo '</li>';
//echo '<li>';
//echo Html::label('会员手机号', '', ['style' => 'margin-left:15px;']);
//echo Html::input('input', 'user_tel', isset($get['user_tel']) ? $get['user_tel'] : '', ['class' => 'form-control', 'placeholder' => '会员手机号', 'style' => 'width:200px;display:inline;margin-left:5px;']);
//echo '</li>';
echo '<li>';
echo Html::label('交易时间&nbsp&nbsp&nbsp&nbsp', '', ['style' => 'margin-left:15px;']);
echo Html::input('text', 'startdate', isset($get['startdate']) ? $get['startdate'] :date("Y-m-d",strtotime("-1 weeks")), ['class' => 'form-control', 'data-am-datepicker' => '', 'placeholder' => '开始日期', 'style' => 'width:80px;display:inline;margin-left:5px;']);
echo "-";
echo Html::input('text', 'enddate', isset($get['enddate']) ? $get['enddate'] :date("Y-m-d"), ['class' => 'form-control', 'data-am-datepicker' => '', 'placeholder' => '结束日期', 'style' => 'width:80px;display:inline;margin-left:5px;']);
echo '</li>';
echo '<li>';
echo Html::label('交易状态&nbsp&nbsp&nbsp&nbsp', '', ['style' => 'margin-left:15px;']);
echo Html::dropDownList('status', isset($get['status']) ? $get['status'] : '', $status, [ 'class' => 'form-control', 'style' => 'width:85px;display:inline;margin-left:5px;']);
echo '</li>';
echo '<li>';
echo Html::label('会员类型', '', ['style' => 'margin-left:15px;']);
echo Html::dropDownList('cust_type', isset($get['cust_type']) ? $get['cust_type'] : '', $custType, [ 'class' => 'form-control', 'style' => 'width:85px;display:inline;margin-left:5px;']);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:22px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href='/trading/withdraw/index'"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '会员编号',
            'value' => 'cust_no'
        ], [
            'label' => '会员手机号',
            'value' => 'user_tel'
        ], [
            'label' => '提现订单号',
            'value' => 'withdraw_code'
        ], [
            'label' => '商户订单号',
            'value' => 'outer_no'
        ], [
            'label' => '交易时间',
            'value' => 'create_time'
        ], [
            'label' => '到账时间',
            'value' => 'toaccount_time'
        ], [
            'label' => '账户信息',
            'value' => function($model){
                if(isset($model['bank_info'])&&isset($model['bank_name'])&&isset($model['cardholder'])){
                    $str = substr($model['bank_info'], strlen($model['bank_info'])-4);
                    return $model['bank_name'] . '(' . $str . ')' . $model['cardholder'];
                }else{
                    return "";
                }
            }
        ], [
            'label' => '提现金额',
            'value' => 'withdraw_money'
        ], [
            'label' => '实际到账金额',
            'value' => 'actual_money'
        ], [
            'label' => '提现费用',
            'value' => 'fee_money'
        ], [
            'label' => '状态',
            'value' => function($model) {
                $status = [
                    '0' => '未处理',
                    '1' => '处理中',
                    '2' => '提现成功',
                    '3' => '提现失败',
                    '4' => '异常订单'
                ];
                return $status[$model['status']];
            }
        ], [
            'label' => '备注',
            'value' => 'remark'
        ], 
//                [
//            'label' => '操作',
//            'format' => 'raw',
//            'value' => function($model) {
//                return '<div class="am-btn-group am-btn-group-xs">
//                            <span class="handle pointer">查看</span>
//                        </div>';
//            }
//        ]
    ]
]);

        