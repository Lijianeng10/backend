<?php

use yii\helpers\Html;

echo '<div><label for="doc-vld-name-2-1" style="margin-right:5px;">中奖金额 :</label>';
echo Html::input('number', 'z_award', $data['win_amount'], ['class' => 'form-control', 'disabled' => "disabled"]);
echo '<div><label for="doc-vld-name-2-1" style="margin-right:5px;">派奖金额 :</label>';
echo Html::input('number', 'p_award', $data['win_amount'], ['class' => 'form-control', 'id' => 'p_award']);
echo Html::input('hidden', 'orderId', $data['lottery_order_id'], ['class' => 'form-control', 'id' => 'order_id']);
echo Html::button("确定", ["class" => "am-btn am-btn-primary", "id" => "pass", "style" => "margin:5px;"]);
echo Html::button("返回", ["class" => "am-btn am-btn-primary", "style" => "margin:5px;", "onclick" => "closeMask();"]);
?>
<script>
    $(function () {
        $("#pass").click(function () {
            var orderIdArr = [];
            var orderId = $('#order_id').val();
            orderIdArr.push(orderId);
            var pAward = $('#p_award').val();
            msgConfirm('提醒', '确定对选中订单进行派奖吗？', function () {
                $.ajax({
                    url: "/channel/award/do-award",
                    async: false,
                    type: 'POST',
                    data: {orderIdArr: orderIdArr, pAwardArr: pAward},
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        if (600 != data['code']) {
                            msgAlert(data['msg']);
                        } else {
                            msgAlert(data['result'], function () {
                                location.reload();
                            });
                        }
                    }
                })
            })
        });
    })
</script>
