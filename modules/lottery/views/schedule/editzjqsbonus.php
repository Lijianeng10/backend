<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="editZjqsBonus">';
echo Html::input('hidden', 'schedule_id', $data["schedule_id"]);
echo Html::input('hidden', 'odds_3008_id', $data["odds_3008_id"]);
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            'label' => '总共进0球赔率',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'total_gold_0', $model['total_gold_0'], ['class' => 'form-control', 'id' => 'total_gold_0', 'placeholder' => '写法如：6.12']);
            }
                ], [
                    'label' => '总共进1球赔率',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input('text', 'total_gold_1', $model['total_gold_1'], ['class' => 'form-control', 'id' => 'total_gold_1', 'placeholder' => '写法如：6.12']);
                    }
                        ], [
                            'label' => '总共进2球赔率',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input('text', 'total_gold_2',$model['total_gold_2'], ['class' => 'form-control', 'id' => 'total_gold_2', 'placeholder' => '写法如：6.12']);
                            }
                                ], [
                                    'label' => '总共进3球赔率',
                                    'format' => 'raw',
                                    'value' => function($model) {

                                        return Html::input('text', 'total_gold_3', $model['total_gold_3'], ['class' => 'form-control', 'id' => 'total_gold_3', 'placeholder' => '写法如：6.12']);
                                    }
                                        ], [
                                            'label' => '总共进4球赔率',
                                            'format' => 'raw',
                                            'value' => function($model) {

                                                return Html::input('text', 'total_gold_4', $model['total_gold_4'], ['class' => 'form-control', 'id' => 'total_gold_4', 'placeholder' => '写法如：6.12']);
                                            }
                                                ], [
                                                    'label' => '总共进5球赔率',
                                                    'format' => 'raw',
                                                    'value' => function($model) {

                                                        return Html::input('text', 'total_gold_5', $model['total_gold_5'], ['class' => 'form-control', 'id' => 'total_gold_5', 'placeholder' => '写法如：6.12']);
                                                    }
                                                        ], [
                                                            'label' => '总共进6球赔率',
                                                            'format' => 'raw',
                                                            'value' => function($model) {

                                                                return Html::input('text', 'total_gold_6', $model['total_gold_6'], ['class' => 'form-control', 'id' => 'total_gold_6', 'placeholder' => '写法如：6.12']);
                                                            }
                                                                ], [
                                                                    'label' => '总共进7球及以上赔率',
                                                                    'format' => 'raw',
                                                                    'value' => function($model) {

                                                                        return Html::input('text', 'total_gold_7', $model['total_gold_7'], ['class' => 'form-control', 'id' => 'total_gold_7', 'placeholder' => '写法如：6.12']);
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
            var data = $("#editZjqsBonus").serialize();
            $.ajax({
                url: "/lottery/schedule/savezjqsbonus",
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