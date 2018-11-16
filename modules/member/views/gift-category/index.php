<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/member/gift-category/index">
    <?php
    echo Html::label("类别名称", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "category_name", isset($get["category_name"]) ? $get["category_name"] : "", ["class" => "form-control", "placeholder" => "类别名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
    
    echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;"]);
    echo Html::button("重置", ["class" => " am-btn am-btn-primary","id" => "ressetGift", "style" => "margin-left:5px;"]);
    echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addCate" ]);
    ?>
    </form>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '类别名称',
            'value' => function ($model) {
                if($model['parent_id'] != 0){
                    $str = '---' . $model['category_name'];
                }  else {
                    $str = $model['category_name'];
                }
                return $str;
            },
//            'contentOptions' => ['style' => 'text-align:center'],
//            'headerOptions' => ['style' => 'text-align:center'] 
        ], [
            'label' => '类别备注',
            'value' => 'category_remark',
//            'contentOptions' => ['style' => 'text-align:center'],
//            'headerOptions' => ['style' => 'text-align:center'] 
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                return  '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <span class="handle pointer" onclick="editCate('.$model['gift_category_id'].');">编辑</span>    
                                <span class="handle pointer" onclick="delCate('.$model['gift_category_id'].');">| 删除</span>
                            </div>
                        </div>';
            }
        ]
    ],
]);
$this->title = 'Lottery';
?>
<script>
    $(function() {
        $('#addCate').click(function() {
            modDisplay({title:'新增类别',url:'/member/gift-category/addcate',height:350,width:450});
        });
        
    });
    function editCate(id){
        modDisplay({title:'编辑类别',url:'/member/gift-category/editcate?cate_id=' + id,height:350,width:450});
    }
    
    function delCate(id) {
        msgConfirm ('提醒','确定要删除此礼品类别吗？',function(){
            $.ajax({
                url: "/member/gift-category/delcate",
                type: "POST",
                async: false,
                data: {cate_id: id},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            });
        })
    }
     //礼品重置
        $("#ressetGift").click(function(){
           location.href="/member/gift-category/index"
        })
    
</script>