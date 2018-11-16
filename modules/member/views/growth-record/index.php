<?php

use yii\helpers\Html;
use yii\grid\GridView;

?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/member/growth-record/index">
        <?php
        echo "<ul class='third_team_ul'>";
        echo '<li>';
        echo Html::label("会员信息", "", ["style" => "margin-left:15px;"]);
        echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["class" => "form-control", "placeholder" => "会员手机号、昵称、编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("获得时间", "", ["style" => "margin-left:15px;"]);
        echo Html::input("text", "start_date", isset($get["start_date"]) ? $get["start_date"] : "", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始时间", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo "-";
        echo Html::input("text", "end_date", isset($get["end_date"]) ? $get["end_date"] : "", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束时间", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("变化值  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("operator", isset($get["operator"]) ? $get["operator"] : ">=", $compar, [ "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
        echo Html::input("input", "growth_value", isset($get["growth_value"]) ? $get["growth_value"] : "", ["class" => "form-control", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("订单编号", "", ["style" => "margin-left:15px;"]);
        echo Html::input("input", "order_code", isset($get["order_code"]) ? $get["order_code"] : "", ["class" => "form-control", "placeholder" => "购彩订单编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
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
            'label' => '会员编号',
            'value' => 'cust_no'
        ], [
            'label' => '会员昵称',
            'value' => 'user_name'
        ],[
            'label' => '手机号',
            'value' => 'user_tel'
        ],[
            'label' => '变化成长值',
            'value' => function($model){
                if($model["type"]==1){
                    return "+".$model["growth_value"];
                }elseif($model["type"]==2){
                    return "-".$model["growth_value"];
                }
            }
        ],[
            'label' => '总成长值',
            'value' => 'totle_balance'
        ],[
            'label' => '用户等级',
            'value' => 'level_name'
        ],[
            'label' => '操作',
            'value' => 'growth_remark'
        ],[
            'label' => '订单编号',
            'value' => 'order_code'
        ],[
            'label' => '变化时间',
            'value' => 'create_time'
        ],
//                [
//            'label' => '操作',
//            'format' => 'raw',
//            'value' => function ($model) {
//                $str = '<div class="am-btn-toolbar">
//                            <div class="am-btn-group am-btn-group-xs">
//                                <span class="handle pointer" onclick="viewEx();">查看</span> 
//                            </div>
//                        </div>';
//                return $str;
//            }
//        ]
    ],
]);
?>
<script>

    function viewEx(id) {
        location.href = '/member/exchange-record/view?exchange_id=' + id;
    }
    function goReset(){
        location.href = '/member/growth-record/index';
    }
</script>

