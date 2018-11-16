<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="addSpffBonus">';
echo Html::input('hidden', 'schedule_id', $scheduleId);
echo DetailView::widget([
    'model' => [],
    'attributes' => [
        [
            'label' => '胜赔率',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'wins', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12']);
            }
                ], [
                    'label' => '平赔率',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input('text', 'level', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12']);
                    }
                        ], [
                            'label' => '负赔率',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input('text', 'negative', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12']);
                            }
                                ], [
                                    'label' => '操作',
                                    'format' => 'raw',
                                    'value' => function() {
                                        return Html::button('提交', ['class' => 'am-btn am-btn-primary addSubmit']) . '&nbsp&nbsp&nbsp' . Html::button('取消', ['class' => 'am-btn am-btn-primary reback']);
                                    }
                                        ]
                                    ]
                                ]);
                                echo '</form>';
                                ?>
<script type="text/javascript">
    $(function () {
        var schedule_id = $("input[name='schedule_id']").val();
        $("#addSpffBonus .addSubmit").click(function () {
            $.ajax({
                url: "/lottery/schedule/addodds3010",
                type: "POST",
                async: false,
                data: $("#addSpffBonus").serialize(),
                dataType: "json",
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            closeMask();
                            window._location.reload();
//                                location.href = "/lottery/schedule/readbonus?schedule_id=" + schedule_id;
                        });
                    }
                }
            });
        });
        $("#addSpffBonus .reback").click(function () {
            closeMask();
//            location.href = "/lottery/schedule/readbonus?schedule_id=" + schedule_id;
        });
    });
</script>