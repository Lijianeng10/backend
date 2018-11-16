<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="addBqcspfBonus">';
echo Html::input('hidden', 'schedule_id', $data["schedule_id"]);
echo DetailView::widget([
    'model' => [],
    'attributes' => [
        [
            'label' => '胜胜赔率',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'bqc_33', '', ['class' => 'form-control', 'id' => 'bqc_33', 'placeholder' => '写法如：6.12']);
            }
                ], [
                    'label' => '胜平赔率',
                    'format' => 'raw',
                    'value' => function() {
                        return Html::input('text', 'bqc_31', '', ['class' => 'form-control', 'id' => 'bqc_31', 'placeholder' => '写法如：6.12']);
                    }
                        ], [
                            'label' => '胜负赔率',
                            'format' => 'raw',
                            'value' => function() {
                                return Html::input('text', 'bqc_30', '', ['class' => 'form-control', 'id' => 'bqc_30', 'placeholder' => '写法如：6.12']);
                            }
                                ], [
                                    'label' => '平胜赔率',
                                    'format' => 'raw',
                                    'value' => function() {

                                        return Html::input('text', 'bqc_13', '', ['class' => 'form-control', 'id' => 'bqc_13', 'placeholder' => '写法如：6.12']);
                                    }
                                        ], [
                                            'label' => '平平赔率',
                                            'format' => 'raw',
                                            'value' => function() {

                                                return Html::input('text', 'bqc_11', '', ['class' => 'form-control', 'id' => 'bqc_11', 'placeholder' => '写法如：6.12']);
                                            }
                                                ], [
                                                    'label' => '平负赔率',
                                                    'format' => 'raw',
                                                    'value' => function() {

                                                        return Html::input('text', 'bqc_10', '', ['class' => 'form-control', 'id' => 'bqc_10', 'placeholder' => '写法如：6.12']);
                                                    }
                                                        ], [
                                                            'label' => '负胜赔率',
                                                            'format' => 'raw',
                                                            'value' => function() {

                                                                return Html::input('text', 'bqc_03', '', ['class' => 'form-control', 'id' => 'bqc_03', 'placeholder' => '写法如：6.12']);
                                                            }
                                                                ], [
                                                                    'label' => '负平赔率',
                                                                    'format' => 'raw',
                                                                    'value' => function() {

                                                                        return Html::input('text', 'bqc_01', '', ['class' => 'form-control', 'id' => 'bqc_01', 'placeholder' => '写法如：6.12']);
                                                                    }
                                                                        ], [
                                                                            'label' => '负负赔率',
                                                                            'format' => 'raw',
                                                                            'value' => function() {

                                                                                return Html::input('text', 'bqc_00', '', ['class' => 'form-control', 'id' => 'bqc_00', 'placeholder' => '写法如：6.12']);
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
        $("#addBqcspfBonus .addSubmit").click(function () {
            var data = $("#addBqcspfBonus").serialize();
            $.ajax({
                url: "/lottery/schedule/savebqcspfbonus",
                type: "post",
                data: data,
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 0) {
                        msgAlert(json["msg"], function () {
                            closeMask();
                            window._location.reload();
//                            location.href = "/lottery/schedule/readbonus?schedule_id=" + schedule_id;
                        });
                    } else {
                        console.log(json);
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
        $("#addBqcspfBonus .reback").click(function () {
            closeMask();
//            location.href = "/lottery/schedule/readbonus?schedule_id=" + schedule_id;
        });
    });
</script>