<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="editDxfBonus">';
echo Html::input('hidden', 'odds_3004_id', $data["odds_3004_id"]);
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            'label' => '大小分切割点',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'fen_cutoff',$model["fen_cutoff"], ['class' => 'form-control', 'placeholder' => '写法：保留小数点后两位']);
            }
        ],
        [
            'label' => '大分赔率',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'da_3004',$model["da_3004"], ['class' => 'form-control', 'placeholder' => '写法：保留小数点后两位']);
            }
        ], [
            'label' => '小分赔率',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'xiao_3004',$model["xiao_3004"], ['class' => 'form-control', 'placeholder' => '写法：保留小数点后两位']);
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
            var data = $("#editDxfBonus").serialize();
            $.ajax({
                url: "/lottery/lanschedule/editodds3004",
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

