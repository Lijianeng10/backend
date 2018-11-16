<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\widgets\DetailView;

$this->title = '新增支付方式';
?>

<?php
echo '<form id="addPayType">';
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            'label' => '方式名称<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'pay_type_name', '', ['class' => 'form-control']);
            }
        ], [
            'label' => '方式Code<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'pay_type_code', '', ['class' => 'form-control']);
            }
        ], [
            'label' => '方式父级<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::dropDownList('parent_id', '0', $model, ['class' => 'form-control']);
            }
        ], [
            'label' => '排序<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('number', 'pay_type_sort', '1', ['class' => 'form-control']);
            }
        ], [
            'label' => '副标题',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'remark', '', ['class' => 'form-control']);
            }
        ], [
            'label' => '',
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
        $('#addSubmit').click(function () {
            console.log($('#addPayType').serialize());
            $.ajax({
                url: '/admin/paytype/add-pay-type',
                async: false,
                type: 'POST',
                data: $('#addPayType').serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        console.log(data.result)
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
//                            location.href = '/admin/role/index';
                        });
                    }
                }
            });
        });

        $("#reback").click(function () {
            closeMask();
//            location.href = '/admin/role/index';
        });
    });
</script>
