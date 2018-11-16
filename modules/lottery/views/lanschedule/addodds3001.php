<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="addSfBonus">';
echo Html::input('hidden', 'schedule_mid', $schedule_mid);
echo DetailView::widget([
    'model' => [],
    'attributes' => [
        [
            'label' => '胜赔率',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'wins_3001', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
            }
                ],[
                    'label' => '负赔率',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input('text', 'lose_3001', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
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
        var schedule_mid = $("input[name='schedule_mid']").val();
        $("#addSfBonus .addSubmit").click(function () {
            $.ajax({
                url: "/lottery/lanschedule/addodds3001",
                type: "POST",
                async: false,
                data: $("#addSfBonus").serialize(),
                dataType: "json",
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            closeMask();
                            window._location.reload();
                        });
                    }
                }
            });
        });
        //关闭页面
        $(".reback").click(function (){
            closeMask();
        });
    });
</script>

