<?php

use yii\helpers\Html;
use yii\grid\GridView;
//use app\modules\common\helpers\Constants;
use app\modules\member\helpers\Constants;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/member/change-records/index">
        <?php
        echo "<ul class='third_team_ul'>";
        echo '<li>';
        echo Html::label("会员信息", "", ["style" => "margin-left:15px;"]);
        echo Html::input("input", "user_infor", isset($get["user_infor"]) ? $get["user_infor"] : "", ["class" => "form-control", "placeholder" => "手机号、编号、昵称", "style" => "width:200px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("订单号", "", ["style" => "margin-left:27px;"]);
        echo Html::input("input", "order_code", isset($get["order_code"]) ? $get["order_code"] : "", ["class" => "form-control", "placeholder" => "订单编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
        echo '</li>';
//        echo '<li>';
//        echo Html::label("所属上级代理商  ", "", ["style" => "margin-left:15px;"]);
//        echo Html::input("input", "agent", isset($get["agent"]) ? $get["agent"] : "", ["class" => "form-control", "placeholder" => "上级代理商编码、昵称", "style" => "width:200px;display:inline;margin-left:5px;"]);
//        echo '</li>';
        echo '<li>';
        echo Html::label("交易分类","",["style"=>"margin-left:15px"]);
        echo Html::dropDownList("transaction_type",isset($get["transaction_type"])?$get["transaction_type"]:"",$transactionType,["class"=>"form-control","style"=>"width:100px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("支付类型  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("exchange_type", isset($get["exchange_type"]) ? $get["exchange_type"] : "",$payType, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("交易时间", "", ["style" => "margin-left:15px;"]);
        echo Html::input("text", "start_date", isset($get["start_date"]) ? $get["start_date"] : "", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始时间", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo "-";
        echo Html::input("text", "end_date", isset($get["end_date"]) ? $get["end_date"] : "", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束时间", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("交易咕币  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("integral", isset($get["integral"]) ? $get["integral"] : "", $compar, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
        echo Html::input("input", "integral_val", isset($get["integral_val"]) ? $get["integral_val"] : "", ["class" => "form-control", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:22px;"]);
        echo Html::input("reset", '', '重置', ["class" => "am-btn am-btn-primary","onclick" => "goReset();", "style" => "margin-left:5px;"]);
        echo '</li>';
        echo "</ul>";
        echo '</form>';
        ?>
    </form>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '交易时间',
            'value' => 'itime'
        ],[
            'label' => '交易说明',
            'value' => 'remark'
        ],[
            'label' => '订单号',
            'value' => 'order_code'
        ],[
            'label' => '交易分类',
            'value' => function($model){
                $source=Constants::TRANSACTION_TYPE;
                return isset($source[$model["transaction_type"]])?$source[$model["transaction_type"]]:"";
            }
        ],[
            'label' => '支付类型',
            'value' => function($model){
                return $model['exchange_type'] == 1 ? '咕币' : ($model['exchange_type'] == 2 ? '电信积分' : '未知');
            }
        ],[
            'label' => '需付咕币',
            'value' => function($model){
                if($model["type"]==1){
                    return "+".intval($model['coin_value']);
                }elseif($model["type"]==2){
                    return "-".intval($model['coin_value']);
                }else{
                    return "";
                }
            }
        ],[
            'label' => '需付积分',
            'value' => function($model){
                if($model["type"]==1){
                    return "+".intval($model['integral_value']);
                }elseif($model["type"]==2){
                    return "-".intval($model['integral_value']);
                }else{
                    return "";
                }
            }
        ],  [
            'label' => '当前咕币',
            'value' => 'totle_balance'
        ],
        [
            'label' => '会员编号',
            'value' => 'cust_no'
        ], [
            'label' => '会员名称',
            'value' => 'user_name'
        ],
//        [
//            'label' => '所属上级代理商',
//            'value' => 'agent_name'
//        ],

//        [
//            'label' => '操作员',
//            'value' => 'opt_name'
//        ]
    ],
]);
?>
<script>
     function goReset(){
        location.href = '/member/change-records/index';
    }
</script>