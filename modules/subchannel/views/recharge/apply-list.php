<?php

use yii\helpers\Html;
use yii\grid\GridView;

$status = [
    '' => '全部',
    '1' => '审核中',
    '2' => '通过',
    '3' => '失败'
];

echo '<form action="/subchannel/recharge/index" method="get">';
echo "<ul class='third_team_ul'>";
//echo '<li>';
//echo Html::label('渠道商', '', ['style' => 'margin-left:15px;']);
//echo Html::dropDownList('bussiness_id', isset($get['bussiness_id']) ? $get['bussiness_id'] : '', $channel, [ 'class' => 'form-control', 'style' => 'width:85px;display:inline;margin-left:5px;']);
//echo '</li>';
echo '<li>';
echo Html::label('交易时间&nbsp&nbsp&nbsp&nbsp', '', ['style' => 'margin-left:15px;']);
echo Html::input('text', 'startdate', isset($get['startdate']) ? $get['startdate'] :"", ['class' => 'form-control', 'data-am-datepicker' => '', 'placeholder' => '开始日期', 'style' => 'width:80px;display:inline;margin-left:5px;']);
echo "-";
echo Html::input('text', 'enddate', isset($get['enddate']) ? $get['enddate'] :"", ['class' => 'form-control', 'data-am-datepicker' => '', 'placeholder' => '结束日期', 'style' => 'width:80px;display:inline;margin-left:5px;']);
echo '</li>';
echo '<li>';
echo Html::label('申请状态&nbsp&nbsp&nbsp&nbsp', '', ['style' => 'margin-left:15px;']);
echo Html::dropDownList('status', isset($get['status']) ? $get['status'] : '', $status, [ 'class' => 'form-control', 'style' => 'width:85px;display:inline;margin-left:5px;']);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:22px;"]);
echo Html::tag("span", "充值申请", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "add()"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href='/subchannel/recharge/index'"]);
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
            'label' => '充值金额',
            'value' => 'money'
        ], [
            'label' => '转账凭证',
            'format' => 'raw',
            'value' => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="read(' . $model['api_user_apply_id'] . ')">查看</span>
                           </div>';
            }
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
//            . ($model['status'] == 1 ?  ('<span class="handle pointer" onclick="rechargeReview('.$model['api_user_apply_id'].');">| 审核</span>')  : "" )
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="read(' . $model['api_user_apply_id'] . ')">查看</span>'. '</div>';
            }
        ]
    ]
]);
?>

<script type="text/javascript">
    function read(applyId) {
        modDisplay({width: 450, height: 300, title: "转账凭证", url: "/subchannel/recharge/read?applyId=" + applyId});
    }
    //充值申请
    function add(){
        modDisplay({width: 500, height: 500, title: "充值申请", url: "/subchannel/recharge/add-recharge"});
    }
</script>
        

        