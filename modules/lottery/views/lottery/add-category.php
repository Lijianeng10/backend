<?php
use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\widgets\DetailView;
?>
<?php
echo '<form id="addCategory">';
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
       [
            'label' => '类别名称<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'cp_category_name','',['class'=>'form-control need', 'id'=>'cp_category_name']);
            }
        ],  [
            'label' => '所属分类<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::dropDownList("parent_id", "0", $model, ['class' => 'form-control', 'id'=>'parent_id']) ;
            }
        ],[
            'label' => '',
            'format' => 'raw',
            'value' => function() {
                $html = "<div class = 'error_msg'></div>";
                return $html . Html::button('提交', ['class'=>'am-btn am-btn-primary', 'id'=>'addSubmit']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class'=>'am-btn am-btn-primary', 'id'=>'reback']);
            }
        ]
    ]
]);    
echo '</form>';
?>

<script>
    $("#addSubmit").click(function(){
        var data =$("#addCategory").serializeArray();
        if(data[0].value==""){
            msgAlert("请填写类别名")
        }else{
            $.ajax({
                url: '/lottery/lottery/add-category',
                async: false,
                type: 'POST',
                data: data,
                dataType: 'json',
                success:function(json){
                    if(json["code"]!=600){
                        msgAlert(json["msg"]);
                    }else{
                        msgAlert(json["msg"],function(){
                            location.reload();
                        });
                    }
                }
            })
        }
    })
    $("#reback").click(function(){
        closeMask();
    })
</script>
