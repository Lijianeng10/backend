<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="editSpffBonus">';
echo Html::input('hidden', 'schedule_id', $model["schedule_id"]);
echo Html::input('hidden', 'odds_outcome_id', $model["odds_outcome_id"]);
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '胜赔率',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'outcome_wins', $model['outcome_wins'], ['class' => 'form-control', 'placeholder' => '写法如：6.12']);
            }
        ], [
            'label' => '平赔率',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'outcome_level', $model['outcome_level'], ['class' => 'form-control', 'placeholder' => '写法如：6.12']);
            }
        ], [
            'label' => '负赔率',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'outcome_negative', $model['outcome_negative'], ['class' => 'form-control', 'placeholder' => '写法如：6.12']);
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
        var schedule_id = $("input[name='schedule_id']").val();
        $("#addSubmit").click(function () {
            var data = $("#editSpffBonus").serialize();
            $.ajax({
                url: "/lottery/schedule/editodds3010",
                type: "post",
                data: data,
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 0) {
                        msgAlert(json["msg"], function () {
                            location.href = "/lottery/schedule/readbonus?schedule_id=" + schedule_id;
                        });
                    } else {
                        console.log(json.result);
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
        $("#reback").click(function () {
            location.href = "/lottery/schedule/readbonus?schedule_id=" + schedule_id;
        });
    });
</script>