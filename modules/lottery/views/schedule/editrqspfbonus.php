<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="editRqspfBonus">';
echo Html::input('hidden', 'schedule_id', $data["schedule_id"]);
echo Html::input('hidden', 'odds_let_id', $data["odds_let_id"]);
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            'label' => '让球',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'let_ball_nums', $model['let_ball_nums'], ['class' => 'form-control', 'placeholder' => '让几球写法如：-1 、 +1']);
            }
                ], [
                    'label' => '胜赔率',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input('text', 'let_wins', $model['let_wins'], ['class' => 'form-control', 'placeholder' => '写法如：6.12']);
                    }
                        ], [
                            'label' => '平赔率',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input('text', 'let_level', $model['let_level'], ['class' => 'form-control', 'id' => 'let_level', 'placeholder' => '写法如：6.12']);
                            }
                                ], [
                                    'label' => '负赔率',
                                    'format' => 'raw',
                                    'value' => function($model) {

                                        return Html::input('text', 'let_negative', $model['let_negative'], ['class' => 'form-control', 'id' => 'let_negative', 'placeholder' => '写法如：6.12']);
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
            var data = $("#editRqspfBonus").serialize();
            $.ajax({
                url: "/lottery/schedule/saverqspfbonus",
                type: "post",
                data: data,
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 0) {
                        msgAlert(json["msg"], function () {
                            location.href = "/lottery/schedule/readbonus?schedule_id=" + schedule_id;
                        });
                    } else {
                        console.log(json);
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