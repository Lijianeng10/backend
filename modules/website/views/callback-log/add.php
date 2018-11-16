<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\widgets\DetailView;
use app\modules\common\models\CallbackBase;

$this->title = '新增';
?>

<?php
echo '<form id="addPayType">';
echo DetailView::widget([
    'model' => '',
    'attributes' => [
        [
            'label' => '回调地址<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'url', '', ['class' => 'form-control']);
            }
        ], [
            'label' => '回调失败尝试最大次数<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('number', 'times', '3', ['class' => 'form-control']);
            }
        ], [
            'label' => '回调名称<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'name', '', ['class' => 'form-control']);
            }
        ],
        [
        'label' => '第三方类型<span class="requiredIcon">*</span>',
        'format' => 'raw',
        'value' => function() {
        return Html::dropDownList('third_type', '1', CallbackBase::THIRD_TYPE, ["class" => "form-control", "style" => "width:100px;"]);
        }
        ],
        [
            'label' => '第三方ID<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('number', 'agent_id', '0', ['class' => 'form-control']);
            }
        ],
        [
        'label' => '回调类型<span class="requiredIcon">*</span>',
        'format' => 'raw',
        'value' => function() {
        return Html::dropDownList('type', '1', CallbackBase::TYPE, ["class" => "form-control", "style" => "width:100px;"]);
        }
        ],
        [
            'label' => '回调备注',
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
            $.ajax({
                url: '/website/callback-third/add',
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
