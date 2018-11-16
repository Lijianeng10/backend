<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>


<div class="am-btn-group am-btn-group-xs operat">
    <a href="javascript:addPicture();" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 新增</a>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '类型编码',
            'value' => 'picture_type_code'
        ], [
            'label' => '类型名称 ',
            'value' => 'picture_type_name'
        ], [
            'label' => '图片',
            'format' => 'raw',
            'value' => function($model) {
                return Html::img($model['picture_url'], ['width' => '40px', 'height' => '40px']);
            } 
        ], [
            'label' => '添加时间',
            'value' => 'create_time'
        ], [
            'label' => '修改时间',
            'value' => 'modify_time'
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <span class="handle pointer" onclick="doDelete(' . $model['picture_id'] .  ');">删除</span>
                                    <span class="handle pointer" onclick="doEdit(' . $model['picture_id'] .  ');">编辑</span>
                            </div>
                        </div>';
            }
        ]
    ],
]);
$this->title = '图片设置';
?>

<script>
    
    
    function doDelete(id) {
        msg = '确定要删除此图片吗？';
        msgConfirm ('提醒', msg, function(){
            $.ajax({
                url:'/admin/picture/delete',
                type:'POST',
                async:false,
                data:{
                    pictureId:id
                },
                dataType:'json',
                success:function(data) {
                    if(data['code'] != 600) {
                        msgAlert(data['msg']);
                    }else {
                        msgAlert('删除成功', function(){
                            location.reload();
                        });
                        
                    }
                }
            })
        })
    };
    
    function addPicture() {
        modDisplay({url: '/admin/picture/add-picture',title:"新增图片",height:360,width:540});
    };
    
    function doEdit(picId) {
        modDisplay({url: '/admin/picture/edit?pic_id=' + picId,title:"编辑图片",height:360,width:540});
    };
</script>