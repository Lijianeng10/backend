<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="editBqcspfBonus">';
echo Html::input('hidden', 'schedule_id', $data["schedule_id"]);
echo Html::input('hidden', 'odds_3009_id', $data["odds_3009_id"]);
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            'label' => '胜胜赔率',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'bqc_33', $model["bqc_33"], ['class' => 'form-control', 'id' => 'bqc_33', 'placeholder' => '写法如：6.12']);
            }
                ], [
                    'label' => '胜平赔率',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input('text', 'bqc_31', $model["bqc_31"], ['class' => 'form-control', 'id' => 'bqc_31', 'placeholder' => '写法如：6.12']);
                    }
                        ], [
                            'label' => '胜负赔率',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input('text', 'bqc_30', $model["bqc_30"], ['class' => 'form-control', 'id' => 'bqc_30', 'placeholder' => '写法如：6.12']);
                            }
                                ], [
                                    'label' => '平胜赔率',
                                    'format' => 'raw',
                                    'value' => function($model) {

                                        return Html::input('text', 'bqc_13', $model["bqc_13"], ['class' => 'form-control', 'id' => 'bqc_13', 'placeholder' => '写法如：6.12']);
                                    }
                                        ], [
                                            'label' => '平平赔率',
                                            'format' => 'raw',
                                            'value' => function($model) {

                                                return Html::input('text', 'bqc_11', $model["bqc_11"], ['class' => 'form-control', 'id' => 'bqc_11', 'placeholder' => '写法如：6.12']);
                                            }
                                                ], [
                                                    'label' => '平负赔率',
                                                    'format' => 'raw',
                                                    'value' => function($model) {

                                                        return Html::input('text', 'bqc_10', $model["bqc_10"], ['class' => 'form-control', 'id' => 'bqc_10', 'placeholder' => '写法如：6.12']);
                                                    }
                                                        ], [
                                                            'label' => '负胜赔率',
                                                            'format' => 'raw',
                                                            'value' => function($model) {

                                                                return Html::input('text', 'bqc_03', $model["bqc_03"], ['class' => 'form-control', 'id' => 'bqc_03', 'placeholder' => '写法如：6.12']);
                                                            }
                                                                ], [
                                                                    'label' => '负平赔率',
                                                                    'format' => 'raw',
                                                                    'value' => function($model) {

                                                                        return Html::input('text', 'bqc_01', $model["bqc_01"], ['class' => 'form-control', 'id' => 'bqc_01', 'placeholder' => '写法如：6.12']);
                                                                    }
                                                                        ], [
                                                                            'label' => '负负赔率',
                                                                            'format' => 'raw',
                                                                            'value' => function($model) {

                                                                                return Html::input('text', 'bqc_00', $model["bqc_00"], ['class' => 'form-control', 'id' => 'bqc_00', 'placeholder' => '写法如：6.12']);
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
            var data = $("#editBqcspfBonus").serialize();
            $.ajax({
                url: "/lottery/schedule/savebqcspfbonus",
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