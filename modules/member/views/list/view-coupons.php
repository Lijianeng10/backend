<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\member\helpers\Constants;
?>
<button class="am-btn am-btn-secondary" id="backSubmit" >返回</button>
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '优惠券号',
            'value' => 'coupons_no'
        ],
        [
            'label' => '优惠券兑换码',
            'value' => 'conversion_code'
        ],
        [
            'label' => '所属批次',
            'value' => 'coupons_batch'
        ], [
            'label' => '发送状态',
            'value' => function($model){
                return $model["send_status"]==1?"未发送":"已发送";
            }
        ], [
            'label' => '使用状态',
            'value' => function($model){
                return $model["use_status"]==0?"未领取":($model["use_status"]==1?"未使用":"已使用");
            }
        ],[
            'label' => '优惠券状态',
            'value' => function($model){
                return $model["status"]==1?"激活":"锁定";
            }
        ],[
            'label' => '发送时间',
            'value' => 'send_time',
        ],[
            'label' => '使用时间',
            'value' => 'use_time',
        ],[
            'label' => '使用订单号',
            'value' => 'use_order_code',
        ],
    ]
])
?>
<script>
    $("#backSubmit").click(function () {
        history.go(-1);
    })
</script>
