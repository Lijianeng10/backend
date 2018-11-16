<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\member\helpers\Constants;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/member/exchange-check/index">
        <ul class="third_team_ul">
    <?php
    $orderStatus = [
        "0" => "请选择",
        "1" => "未审核",
        "2" => "审核通过",
        "3" => "不通过",
    ];
    echo '<li>';
    echo Html::label("订单编号", "order_code", ["style" => "margin-left:5px;"]);
    echo Html::input("input", "ex_code", isset($get["ex_code"]) ? $get["ex_code"] : "", ["class" => "form-control", "placeholder" => "订单编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("会员信息", "", ["style" => "margin-left:5px;"]);
    echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["class" => "form-control", "placeholder" => "会员编号、昵称、", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("兑换状态", "", ["style" => "margin-left:5px;"]);
    echo Html::dropDownList("review_status", isset($get["review_status"]) ? $get["review_status"] : "", $orderStatus, [ "class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("申请时间", "", ["style" => "margin-left:5px;"]);
    echo Html::input("text", "start_date", isset($get["start_date"]) ? $get["start_date"] : "", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("text", "end_date", isset($get["end_date"]) ? $get["end_date"] : "", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "日期", "style" => "width:80px;display:inline;margin-left:5px;"]) ;
    echo '</li>';
    echo '<li>';
    echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:14px;"]);
    echo Html::input("reset", '', '重置',  ["class" => "am-btn am-btn-primary", "onclick" => "goReset();","style" => "margin-left:5px;"]);
    echo '</li>';
    ?>
        </ul>
    </form>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '订单编号',
            'value' => 'exch_code'
        ],[
            'label' => '会员编号',
            'value' => 'cust_no'
        ],[
            'label' => '会员昵称',
            'value' => 'user_name'
        ],[
            'label' => '会员手机',
            'value' => 'user_tel'
        ],[
            'label' => '礼品数量',
            'value' => 'exch_nums'
        ],[
            'label' => '支付类型',
            'value' => function($model){
                $pay = Constants::PAY_TYPE;
                return $pay[$model["pay_type"]];
            }
        ],[
            'label' => '所需咕币/积分',
            'value' => 'exch_value'
        ],[
            'label' => '兑换平台',
            'value' => function($model) {
                return $model['exch_type'] == 1 ? '会员俱乐部兑换' : ($model['exch_type'] == 2 ? '后台管理兑换' : '未知');
            }
        ],[
            'label' => '申请时间',
            'value' => 'create_time'
            
        ],[
            'label' => '兑换状态',
            'format' => 'raw',
            'value' => function($model){
                return $model['review_status'] == 1 ? '未审核' : ($model['review_status'] == 2 ? '已通过' : '未通过');
            }
        ],[
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                return  '<div class="am-btn-toolbar"><div class="am-btn-group am-btn-group-xs">'.($model['review_status'] == 1?'<span class="handle pointer" onclick="reviewEx('.$model['exchange_record_id'].');">审核</span>
                                <span class="handle pointer" onclick="viewEx('.$model['exchange_record_id'].');">| 查看</span>':'<span class="handle pointer" onclick="viewEx('.$model['exchange_record_id'].');">查看</span>'). '</div></div>';
            }
        ]
    ],
]);
$this->title = 'Lottery';
?>
<script>
    
    function reviewEx(id){
        modDisplay({title:'审核',url:'/member/exchange-check/review?exchange_id=' + id,height:280,width:450});
    }
    function viewEx(id){
        location.href = '/member/exchange-check/view?exchange_id=' + id;
    }
    function goReset(){
        location.href = '/member/exchange-check/index';
    }
</script>