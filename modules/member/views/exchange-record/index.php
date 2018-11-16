<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\member\helpers\Constants;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/member/exchange-record/index">
        <?php
        echo "<ul class='third_team_ul'>";
        echo '<li>';
        echo Html::label("会员信息", "", ["style" => "margin-left:15px;"]);
        echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["class" => "form-control", "placeholder" => "会员信息", "style" => "width:200px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("订单号", "", ["style" => "margin-left:27px;"]);
        echo Html::input("input", "order_code", isset($get["order_code"]) ? $get["order_code"] : "", ["class" => "form-control", "placeholder" => "订单编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
        echo '</li>';
//        echo '<li>';
//        echo Html::label("所属上级代理商  ", "", ["style" => "margin-left:15px;"]);
//        echo Html::input("input", "agent", isset($get["agent"]) ? $get["agent"] : "", ["class" => "form-control", "style" => "width:200px;display:inline;margin-left:5px;"]);
//        echo '</li>';
        echo '<li>';
        echo Html::label("创建时间", "", ["style" => "margin-left:15px;"]);
        echo Html::input("text", "start_date", isset($get["start_date"]) ? $get["start_date"] : "", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始时间", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo "-";
        echo Html::input("text", "end_date", isset($get["end_date"]) ? $get["end_date"] : "", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束时间", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("支付类型  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("pay_type", isset($get["pay_type"]) ? $get["pay_type"] : "",$payType, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("兑换平台  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("ex_type", isset($get["ex_type"]) ? $get["ex_type"] : "", $exType, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("订单状态  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("order_status", isset($get["order_status"]) ? $get["order_status"] : "", $orderStatus, ["class" => "form-control", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("未支付  ", "", ["style" => "margin-left:25px;"]);
        echo Html::checkbox("choose", isset($get["choose"]) && $get["choose"] == "1" ? "true" : "", ["style" => "height:20px;width:20px;vertical-align:top;margin-left:10px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:22px;"]);
        echo Html::input("reset", '', '重置', ["class" => "am-btn am-btn-primary","onclick" => "goReset();", "style" => "margin-left:5px;"]);
        echo '</li>';
        echo "</ul>";
        ?>
    </form>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '兑换单号',
            'format' => 'raw',
            'value' => function ($model) {
                return '<a onclick="viewEx(' . $model['exchange_record_id'] . ');">' . $model['exch_code'] . '</a>';
            }
        ], [
            'label' => '第三方订单号',
            'value' => 'transaction_code'
        ],
        [
            'label' => '会员编号',
            'value' => 'cust_no'
        ], [
            'label' => '会员名称',
            'value' => 'user_name'
        ],[
            'label' => '支付类型',
            'value' => function($model){
                return $model['pay_type'] == 1 ? '咕币' : ($model['pay_type'] == 2 ? '电信积分' : '未知');
            }
        ], [
            'label' => '兑换数量',
            'value' => 'exch_nums'
        ], [
            'label' => '消费咕币/积分',
            'value' => 'exch_value'
        ], [
            'label' => '兑换平台',
            'value' => function($model) {
                return $model['exch_type'] == 1 ? '会员俱乐部兑换' : ($model['exch_type'] == 2 ? '后台管理兑换' : '未知');
            }
        ], [
            'label' => '订单状态',
            'value' => function($model) {
                $orderStatus = Constants::ORDER_STATUS;
                return $orderStatus[$model['order_status']];
            }
        ],  [
            'label' => '创建时间',
            'value' => 'create_time'
        ], [
            'label' => '审核时间',
            'value' => 'modify_time'
        ],
//        [
//            'label' => '所属上级代理商',
//            'value' => 'agent_name'
//        ],
        [
            'label' => '审核员',
            'value' => 'review_name'
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                $str = '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <span class="handle pointer" onclick="viewEx(' . $model['exchange_record_id'] . ');">查看</span> 
                            </div>
                        </div>';
                return $str;
            }
        ]
    ],
]);
?>
<script>

    function viewEx(id) {
        location.href = '/member/exchange-record/view?exchange_id=' + id;
    }
    function goReset(){
        location.href = '/member/exchange-record/index';
    }
</script>