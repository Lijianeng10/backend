<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="addDxfBonus">';
echo Html::input('hidden', 'schedule_mid', $schedule_mid);
echo DetailView::widget([
    'model' => [],
    'attributes' => [
        [
            'label' => '大小分切割点',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'fen_cutoff',"", ['class' => 'form-control', 'placeholder' => '写法：保留小数点后两位']);
            }
        ],
        [
            'label' => '大分赔率',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'da_3004',"", ['class' => 'form-control', 'placeholder' => '写法：保留小数点后两位']);
            }
        ], [
            'label' => '小分赔率',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'xiao_3004',"", ['class' => 'form-control', 'placeholder' => '写法：保留小数点后两位']);
            }
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function() {
                return Html::button('提交', ['class' => 'am-btn am-btn-primary', 'id' => 'addSubmit']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class' => 'am-btn am-btn-primary', 'id' => 'reback']);
            }
        ]
    ]
]);
 echo '</form>';
 ?>
<script type="text/javascript">
    $(function () {
        var schedule_mid = $("input[name='schedule_mid']").val();
        $("#addSubmit").click(function () {
            $.ajax({
                url: "/lottery/lanschedule/addodds3004",
                type: "POST",
                async: false,
                data: $("#addDxfBonus").serialize(),
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
        $("#reback").click(function (){
            closeMask();
        });
    });
</script>

