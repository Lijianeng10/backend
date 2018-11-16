<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Edit';
?>
<?php
echo '<form id="editAutoOrder">';
echo Html::input('hidden', 'out_order_id', $data['out_order_id'], ['id' => 'out_order_id']);
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            'label' => '串关方式',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'free_type', $model['free_type'], ['class' => 'form-control need', 'id' => 'free_type']);
            }
        ], [
            'label' => '彩种编号',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'lottery_code', $model['lottery_code'], ['class' => 'form-control need', 'id' => 'lottery_code']);
            }
        ], [
            'label' => '玩法编号',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'play_code', $model['play_code'], ['class' => 'form-control', 'id' => 'play_code']);
            }
       ], [
            'label' => '期数',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'periods', $model['periods'], ['class' => 'form-control', 'id' => 'periods']);
            }
       ], [
            'label' => '投注内容',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'bet_val', $model['bet_val'], ['class' => 'form-control', 'id' => 'bet_val']);
            }
       ], [
            'label' => '投注金额',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'amount', $model['amount'], ['class' => 'form-control', 'id' => 'amount']);
            }
       ],[
            'label' => '',
            'format' => 'raw',
            'value' => function() {
                $html = "<div class = 'error_msg'></div>";
                    return $html . Html::button('提交', ['class' => 'am-btn am-btn-primary', 'id' => 'editSubmit']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class' => 'am-btn am-btn-primary', 'id' => 'reback']);
            }
        ]
    ]
]);
echo '</form>';
?>

<script type="text/javascript">
    $(function () {
        $('#editSubmit').click(function () {
            var fromData = $("#editAutoOrder").serializeArray();
           
            $.ajax({
                url: '/lottery/autoticket/auto-order-edit',
                async: false,
                type: 'POST',
                processData: false,
                contentType: false,
                data: fromData,
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(json["msg"], function () {
                            closeMask();
                        });
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            });
        });

        $("#reback").click(function () {
            closeMask();
        });
    });

</script>