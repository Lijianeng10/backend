<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="editSfcBonus">';
echo Html::input('hidden', 'odds_3003_id', $data["odds_3003_id"]);
echo DetailView::widget([
    'model' => $data,
   'attributes' => [
        [
            'label' => '主胜1-5',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'cha_01', $model['cha_01'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
            }
                ], [
                    'label' => '主胜6-10',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input('text', 'cha_02',$model['cha_02'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                    }
                        ], [
                            'label' => '主胜11-15',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input('text', 'cha_03',$model['cha_03'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                            }
                                ], [
                                    'label' => '主胜16-20',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::input('text', 'cha_04',$model['cha_04'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                    }
                                        ], [
                                            'label' => '主胜21-25',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                return Html::input('text', 'cha_05', $model['cha_05'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                            }
                                                ], [
                                                    'label' => '主胜26+',
                                                    'format' => 'raw',
                                                    'value' => function($model) {
                                                        return Html::input('text', 'cha_06', $model['cha_06'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                    }
                                                        ], [
                                                            'label' => '主负1-5',
                                                            'format' => 'raw',
                                                            'value' => function($model) {
                                                                return Html::input('text', 'cha_11',$model['cha_11'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                            }
                                                                ], [
                                                                    'label' => '主负6-10',
                                                                    'format' => 'raw',
                                                                    'value' => function($model) {
                                                                        return Html::input('text', 'cha_12',$model['cha_12'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                                    }
                                                                        ], [
                                                                            'label' => '主负11-15',
                                                                            'format' => 'raw',
                                                                            'value' => function($model) {
                                                                                return Html::input('text', 'cha_13', $model['cha_13'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                                            }
                                                                                ], [
                                                                                    'label' => '主负16-20',
                                                                                    'format' => 'raw',
                                                                                    'value' => function($model) {
                                                                                        return Html::input('text', 'cha_14',$model['cha_14'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                                                    }
                                                                                        ], [
                                                                                            'label' => '主负21-25',
                                                                                            'format' => 'raw',
                                                                                            'value' => function($model) {
                                                                                                return Html::input('text', 'cha_15',$model['cha_15'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
                                                                                            }
                                                                                                ], [
                                                                                                    'label' => '主负26+',
                                                                                                    'format' => 'raw',
                                                                                                    'value' => function($model) {
                                                                                                        return Html::input('text', 'cha_16', $model['cha_16'], ['class' => 'form-control', 'placeholder' => '写法如：6.12，保留两位小数']);
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
        $("#addSubmit").click(function (){
            var data = $("#editSfcBonus").serialize();
            $.ajax({
                url: "/lottery/lanschedule/editodds3003",
                type: "post",
                data: data,
                dataType: "json",
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            closeMask();
                            window._location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
        $("#reback").click(function () {
            closeMask();
        });
    });
</script>

