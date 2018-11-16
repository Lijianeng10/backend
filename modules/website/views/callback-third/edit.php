<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\widgets\DetailView;
use app\modules\common\models\CallbackBase;

$this->title = '编辑支付方式';
?>

<?php
echo '<form id="editPayType">';
echo '<input type="hidden" name="id" value="' .$data['id'] . '">';
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            'label' => '回调地址<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'url', $model['url'], ['class' => 'form-control']);
            }
        ], [
            'label' => '回调最大尝试次数<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('number', 'times', $model['times'], ['class' => 'form-control']);
            }
        ], [
            'label' => '回调名称<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'name', $model['name'], ['class' => 'form-control']);
            }
        ],
        [
        'label' => '第三方类型<span class="requiredIcon">*</span>',
        'format' => 'raw',
        'value' => function($model) {
        return Html::dropDownList('third_type', $model['third_type'], CallbackBase::THIRD_TYPE, ["class" => "form-control", "style" => "width:100px;"]);
        }
        ],
        [
        'label' => '第三方ID<span class="requiredIcon">*</span>',
        'format' => 'raw',
        'value' => function($model) {
        return Html::input('number', 'agent_id', $model['type'], ['class' => 'form-control']);
        }
        ],
        [
        'label' => '回调类型<span class="requiredIcon">*</span>',
        'format' => 'raw',
        'value' => function($model) {
        return Html::dropDownList('type', $model['type'], CallbackBase::TYPE, ["class" => "form-control", "style" => "width:100px;"]);
        }
        ],
        [
            'label' => '回调备注',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'remark', $model['remark'], ['class' => 'form-control']);
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
                url: '/website/callback-third/edit',
                async: false,
                type: 'POST',
                data: $('#editPayType').serialize(),
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
