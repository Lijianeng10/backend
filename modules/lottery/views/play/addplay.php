<?php
use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\widgets\DetailView;
$this->title = 'AddPlay';
?>
<?php
echo '<form id="addplay">';
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '所属彩种<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::dropDownList("lottery_code", '', $model, ['class' => 'form-control need']) ;
            }
        ],[
            'label' => '玩法编码<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'play_code','', ['class'=>'form-control need']);
            }
        ], [
            'label' => '玩法名称<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'play_name','', ['class'=>'form-control need']);
            }
        ], [
            'label' => '号码格式示例',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'example','', ['class'=>'form-control']);
            }
        ],  [
            'label' => '号码个数<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'nums_count', '', ['class'=>'form-control need']);
            }
        ],[
            'label' => '格式说明',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'remark','', ['class'=>'form-control'] ) ;
            }
        ],[
            'label' => '',
            'format' => 'raw',
            'value' => function() {
                
                return Html::button('提交', ['class'=>'am-btn am-btn-primary', 'id'=>'addSubmit']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class'=>'am-btn am-btn-primary', 'id'=>'reback']);
            }
        ]
    ]
]);    
echo '</form>';
?>

<script type="text/javascript">
    $(function () {
        $('#addSubmit').click(function () {
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
                url: '/lottery/play/addplay',
                async: false,
                type: 'POST',
                data: $('#addplay').serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.href = '/lottery/play/index';
                        });
                    }
                }
            });
        });
        
        $("#reback").click(function () {
            location.href = '/lottery/play/index';
        });
    });
</script>