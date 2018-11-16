<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="addSfcBonus">';
echo Html::input('hidden', 'schedule_mid', $schedule_mid);
echo DetailView::widget([
    'model' => [],
    'attributes' => [
        [
            'label' => '主胜1-5',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'cha_01', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
            }
                ], [
                    'label' => '主胜6-10',
                    'format' => 'raw',
                    'value' => function() {
                        return Html::input('text', 'cha_02', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                    }
                        ], [
                            'label' => '主胜11-15',
                            'format' => 'raw',
                            'value' => function() {
                                return Html::input('text', 'cha_03', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                            }
                                ], [
                                    'label' => '主胜16-20',
                                    'format' => 'raw',
                                    'value' => function() {
                                        return Html::input('text', 'cha_04', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                    }
                                        ], [
                                            'label' => '主胜21-25',
                                            'format' => 'raw',
                                            'value' => function() {
                                                return Html::input('text', 'cha_05', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                            }
                                                ], [
                                                    'label' => '主胜26+',
                                                    'format' => 'raw',
                                                    'value' => function() {
                                                        return Html::input('text', 'cha_06', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                    }
                                                        ], [
                                                            'label' => '主负1-5',
                                                            'format' => 'raw',
                                                            'value' => function() {
                                                                return Html::input('text', 'cha_11', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                            }
                                                                ], [
                                                                    'label' => '主负6-10',
                                                                    'format' => 'raw',
                                                                    'value' => function() {
                                                                        return Html::input('text', 'cha_12', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                                    }
                                                                        ], [
                                                                            'label' => '主负11-15',
                                                                            'format' => 'raw',
                                                                            'value' => function() {
                                                                                return Html::input('text', 'cha_13', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                                            }
                                                                                ], [
                                                                                    'label' => '主负16-20',
                                                                                    'format' => 'raw',
                                                                                    'value' => function() {
                                                                                        return Html::input('text', 'cha_14', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                                                    }
                                                                                        ], [
                                                                                            'label' => '主负21-25',
                                                                                            'format' => 'raw',
                                                                                            'value' => function() {
                                                                                                return Html::input('text', 'cha_15', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                                                            }
                                                                                                ], [
                                                                                                    'label' => '主负26+',
                                                                                                    'format' => 'raw',
                                                                                                    'value' => function() {
                                                                                                        return Html::input('text', 'cha_16', '', ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
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
        $("#addSfcBonus .addSubmit").click(function () {
            $.ajax({
                url: "/lottery/lanschedule/addodds3003",
                type: "POST",
                async: false,
                data: $("#addSfcBonus").serialize(),
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
        $(".reback").click(function () {
            closeMask();
        });
    });
</script>