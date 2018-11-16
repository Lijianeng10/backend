<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="editSfBonus">';
echo Html::input('hidden', 'odds_3001_id', $data["odds_3001_id"]);
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            'label' => '胜赔率',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'wins_3001', $model['wins_3001'], ['class' => 'form-control', 'placeholder' => '写法如：6.12']);
            }
        ], [
            'label' => '负赔率',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'lose_3001', $model['lose_3001'], ['class' => 'form-control', 'placeholder' => '写法如：6.12']);
            }
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function($model) {
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
            var data = $("#editSfBonus").serialize();
            $.ajax({
                url: "/lottery/lanschedule/editodds3001",
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

