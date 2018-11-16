<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\helpers\Constants;
?>
<div style="font-size:14px;">
    <?php
     echo "<ul class='third_team_ul'>";
     echo "<form action='/lottery/taking/index'>";
    echo '<li>';
    echo Html::label("订单号", "order_code", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "order_code", (isset($_GET["order_code"]) ? $_GET["order_code"] : ""), [ "class" => "form-control", "placeholder" => "订单号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
     echo '<li>';
    echo Html::label("门店信息", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "store_info", (isset($_GET["store_info"]) ? $_GET["store_info"] : ""), ["class" => "form-control", "placeholder" => "门店编号、运营者手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("接单状态  ", "", ["style" => "margin-left:42px;"]);
    echo Html::dropDownList("status", (isset($_GET["status"]) ? $_GET["status"] : ""), $orderStatus, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:25px;"]);
    echo Html::button("重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
    echo '</li>';
    echo "</form>";
echo "</ul>";
    ?>
</div>
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '订单号',
            'value' => 'order_code'
        ], [
            'label' => '门店编码',
            'value' => 'store_code'
        ],[
            'label' => '门店名称',
            'value' => 'store_name'
        ],[
            'label' => '运营者手机号',
            'value' => 'phone_num'
        ], [
            'label' => '状态',
            'value' => function($model) {
                $orderStatus = Constants::ORDER_TAKING_STATUS;
                return $orderStatus[$model["status"]];
            }
        ],[
            'label' => '创建时间',
            'value' => 'create_time'
        ],[
            'label' => '更新时间',
            'value' => 'modify_time'
        ],
    ]
]);
?>

<script type="text/javascript">
    function goReset() {
        location.href = '/lottery/taking/index';
    }
</script>