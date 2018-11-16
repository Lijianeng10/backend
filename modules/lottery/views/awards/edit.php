<?php
use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\widgets\DetailView;
$this->title = 'EditAwards';
?>
<?php
echo '<form id="editawards">';
echo Html::input('hidden', 'levels_id', $model['levels_id']);
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '所属彩种',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'lottery_name',$model['lottery_name'], ['class'=>'form-control inputLimit', 'readonly'=>'true']);
            }
        ],[
            'label' => '奖级<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::dropDownList("levels_code", $model['levels_code'], $model['levels'], ['class' => 'form-control inputLimit need']) ;
            }
        ], [
            'label' => '奖级名称<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'levels_name',$model['levels_name'], ['class'=>'form-control inputLimit need']);
            }
        ], [
            'label' => '中奖条件<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return '中红球' .  Html::dropDownList("red_nums", $model['levels_red'], $model['red_nums'], ['class' => 'form-control inputLimit']) . '个&nbsp&nbsp+&nbsp&nbsp蓝球' . Html::dropDownList("blue_nums", $model['levels_blue'], $model['blue_nums'], ['class' => 'form-control inputLimit']) . '个' ;
            }
        ],  [
            'label' => '中奖说明',
            'format' => 'raw',
            'value' => function($model) {
            return Html::input('text', 'mark', $model['levels_remark'], ['class'=>'form-control inputLimit']);
            }
        ],[
            'label' => '奖金类别<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::dropDownList("awards_code", $model['levels_bonus_category'], $model['awards'], ['class' => 'form-control inputLimit need']) ;
            }
        ],[
            'label' => '奖金',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'awards', $model['levels_bonus'], ['class'=>'form-control inputLimit'] ) ;
            }
        ],[
            'label' => '奖金说明',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'remark', $model['levels_bonus_remark'], ['class'=>'form-control inputLimit'] ) ;
            }
        ],[
            'label' => '',
            'format' => 'raw',
            'value' => function($model) {
                
                return Html::button('提交', ['class'=>'am-btn am-btn-primary', 'id'=>'editSubmit']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class'=>'am-btn am-btn-primary', 'id'=>'reback']);
            }
        ]
    ]
]);    
echo '</form>';
?>

<script type="text/javascript">
    $(function () {
            
        $('#editSubmit').click(function () {
            err = 0;
            $(".need").each(function(i){
            var text = $(this).val();
            if(text ==""){
                   err++;
                   $(this).focus();
                   $("#msg").empty();
                   h = '<span id="msg" style="color:red;">请填写此字段</span>';
                   $(this).after(h);
                   return false;
            }
            });
            if(err != 0){
                return false;
            }
            $.ajax({
                url: '/lottery/awards/editawards',
                async: false,
                type: 'POST',
                data: $('#editawards').serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
                    } else {
                        
                        msgAlert(data['msg'], function () {
                            location.href = '/lottery/awards/index';
                        });
                    }
                }
            });
        });
        
        $("#reback").click(function () {
            location.href = '/lottery/awards/index';
        });
    });
</script>