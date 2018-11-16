<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\lottery\helpers\Constant;

echo '<form id="editLanResult">';
echo Html::input('hidden', 'schedule_mid', $schedule_mid, ['id'=>'schedule_mid']);
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
            [
            'label' => '胜负',
            'format' => 'raw',
            'value' => function($model) {
                $Ary=Constant::COMPETING_BET_3001;
                return Html::dropDownList("result_3001",$model["result_3001"],$Ary,["class" => "form-control", "id" => "result_3001"]);
            }
                ],
            [
                    'label' => '让分胜负',
                    'format' => 'raw',
                    'value' => function($model) {
                        $Ary=Constant::COMPETING_BET_3002;
                        return Html::dropDownList("result_3002",$model["result_3002"],$Ary,["class" => "form-control", "id" => "result_3002"]);
                    }
                        ], [
                            'label' => '胜分差',
                            'format' => 'raw',
                            'value' => function($model) {
                                $Ary=Constant::SFC_BETWEEN_ARR;
                                return Html::dropDownList("result_3003",$model["result_3003"],$Ary,["class" => "form-control", "id" => "result_3003"]);
                            }
                                ], 
                                  [
                                    'label' => '大小分',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        $Ary=Constant::COMPETING_BET_3004;
                                        return Html::dropDownList("result_3004",$model["result_3004"],$Ary,["class" => "form-control", "id" => "result_3004"]);
                                    }
                                        ], 
                                                [
                                                    'label' => '',
                                                    'format' => 'raw',
                                                    'value' => function() {
                                                        return Html::button('提交', ['class' => 'am-btn am-btn-primary', 'id' => 'editSubmit']) . '&nbsp&nbsp&nbsp' . Html::button('关闭', ['class' => 'am-btn am-btn-primary', 'id' => 'reback']);
                                                    }
                                                        ]
                                                    ]
                                                ]);
     echo '</form>';
?>

<script type="text/javascript">
    $(function () {
        var schedule_mid = $("#schedule_mid").val();
        $('#editSubmit').click(function () {
            $.ajax({
                url: '/lottery/lanschedule/editlanresult',
                async: false,
                type: 'POST',
                data: $("#editLanResult").serialize(),
                dataType: 'json',
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
        
        $("#reback").click(function () {
            closeMask();
        });
    });
    
</script>

