<?php

use yii\helpers\Html;
use yii\grid\GridView;

$status = [
    '' => '全部',
    '1' => '审核中',
    '2' => '通过',
    '3' => '失败'
];

echo '<form action="/trading/apply-withdraw/apply-list" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label('渠道商', '', ['style' => 'margin-left:15px;']);
echo Html::dropDownList('bussiness_id', isset($get['bussiness_id']) ? $get['bussiness_id'] : '', $channel, [ 'class' => 'form-control', 'style' => 'width:85px;display:inline;margin-left:5px;']);
echo '</li>';
echo '<li>';
echo Html::label('交易时间&nbsp&nbsp&nbsp&nbsp', '', ['style' => 'margin-left:15px;']);
//date("Y-m-d",strtotime("-1 weeks"))
echo Html::input('text', 'startdate', isset($get['startdate']) ? $get['startdate'] :'', ['class' => 'form-control', 'data-am-datepicker' => '', 'placeholder' => '开始日期', 'style' => 'width:80px;display:inline;margin-left:5px;']);
echo "-";
//date("Y-m-d")
echo Html::input('text', 'enddate', isset($get['enddate']) ? $get['enddate'] :'', ['class' => 'form-control', 'data-am-datepicker' => '', 'placeholder' => '结束日期', 'style' => 'width:80px;display:inline;margin-left:5px;']);
echo '</li>';
echo '<li>';
echo Html::label('申请状态&nbsp&nbsp&nbsp&nbsp', '', ['style' => 'margin-left:15px;']);
echo Html::dropDownList('status', isset($get['status']) ? $get['status'] : '', $status, [ 'class' => 'form-control', 'style' => 'width:85px;display:inline;margin-left:5px;']);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:22px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href='/channel/withdraw/apply-list'"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '渠道方名',
            'value' => 'name'
        ], [
            'label' => '渠道编号',
            'value' => 'cust_no'
        ], [
            'label' => '渠道电话',
            'value' => 'user_tel'
        ], [
            'label' => '申请订单号',
            'value' => 'apply_code'
        ], [
            'label' => '申请时间',
            'value' => 'create_time'
        ], [
            'label' => '提现金额',
            'value' => 'money'
        ],[
            'label' => '提现凭证',
            "format" => "raw",
            'value' => function($model){
                    $html = "";
                    if (!empty($model["voucher_pic"])) {
                        $html .= "<div style='display:inline-block' data-magnify='gallery' href={$model["voucher_pic"]} data-caption='提现凭证'>";
                        $html.= "<img style='width:40px;height:40px' src='{$model["voucher_pic"]}' />";
                        $html.= "</div>";
                    }
                    return $html;
            }
        ], [
            'label' => '收款人',
            'value' => 'user_name'
        ], [
            'label' => '银行名称',
            'value' => 'bank_open'
        ], [
            'label' => '银行卡号',
            'value' => 'card_number'
        ], [
            'label' => '备注',
            'value' => 'remark'
        ], [
            'label' => '申请状态',
            'value' => function($model) {
                $status = [
                    '' => '全部',
                    '1' => '审核中',
                    '2' => '通过',
                    '3' => '失败'
                ];
                return $status[$model["status"]];
            }
        ], [
            'label' => '审核人',
            'value' => 'nickname'
        ], [
            'label' => '审核时间',
            'value' => 'modify_time'
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function($model) {
                return '<div class="am-btn-group am-btn-group-xs"><span class="handle pointer" onclick="read(' . $model['api_user_apply_id'] . ')">查看</span>'
                             . ($model['status'] == 1 ?  ('<span class="handle pointer" onclick="withdrawReview('.$model['api_user_apply_id'].');"> | 审核</span>')  : "" ) . '
                           </div>';
            }
        ]
    ]
]);
?>

<script type="text/javascript">
    function read(applyId) {
        modDisplay({width: 450, height: 450, title: "提现信息", url: "/trading/apply-withdraw/read?applyId=" + applyId});
    }
    
    function withdrawReview(applyId) {
        modDisplay({width: 400, height: 250, title: "提现审核", url: "/trading/apply-withdraw/review?applyId=" + applyId});
    }
    
</script>