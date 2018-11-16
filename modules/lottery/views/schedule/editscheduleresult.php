<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

echo '<form id="editScheduleResult">';
echo Html::input('hidden', 'schedule_id', $data["schedule_id"], ['id'=>'scheduleId']);
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            'label' => '胜平负',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'schedule_result_3010', '', ['class' => 'form-control', 'id' => 'schedule_result_3010']);
            }
                ], [
                    'label' => '让球胜平负',
                    'format' => 'raw',
                    'value' => function() {
                        return Html::input('text', 'schedule_result_3006', '', ['class' => 'form-control', 'id' => 'schedule_result_3006']);
                    }
                        ], [
                            'label' => '猜比分',
                            'format' => 'raw',
                            'value' => function() {
                                return Html::input('text', 'schedule_result_3007', '', ['class' => 'form-control', 'id' => 'schedule_result_3007']);
                            }
                                ], [
                                    'label' => '总进球数',
                                    'format' => 'raw',
                                    'value' => function($model) {

                                        return Html::input('text', 'schedule_result_3008', '', ['class' => 'form-control', 'id' => 'schedule_result_3008']);
//                                        return Html::dropDownList("category_id", "0", $model, ['class' => 'form-control', 'id' => 'l_category']);
                                    }
                                        ], [
                                            'label' => '半全场胜平负',
                                            'format' => 'raw',
                                            'value' => function() {
                                                return Html::input('text', 'schedule_result_3009', '', ['class' => 'form-control', 'id' => 'schedule_result_3009']);
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
        var scheduleId = $("#scheduleId").val();
        $('#editSubmit').click(function () {
            $.ajax({
                url: '/lottery/schedule/savescheduleresult',
                async: false,
                type: 'POST',
                data: $("#editScheduleResult").serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            console.log(scheduleId)
                            location.href = "/lottery/schedule/readbonus?schedule_id=" + scheduleId;
                        });
                    }
                }
            });
        });
        
        $("#reback").click(function () {
            location.href = "/lottery/schedule/readbonus?schedule_id=" + scheduleId;
        });
    });
    
</script>