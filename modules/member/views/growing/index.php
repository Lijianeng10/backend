<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <?php
    echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addGrowth" ]);
    ?>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '会员成长ID',
            'value' => 'user_growth_id'
            
        ],[
            'label' => '成长值来源',
            'value' => 'growth_source'
        ],[
            'label' => '成长类型',
            'value' => 'growth_type'
        ],[
            'label' => '成长值',
            'value' => 'growth_value'
            
        ],[
            'label' => '成长机制',
            'value' => 'growth_remark'
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <span class="handle pointer" onclick="editGrowing(' .$model['user_growth_id'] . ');">编辑</span>    
                                <span class="handle pointer" onclick="delGrowing(' .$model['user_growth_id'] . ');">| 删除</span>
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
        $('#addGrowth').click(function() {
          modDisplay({title:'新增等级',url:'/member/growing/addgrowing',height:350,width:550});
        });
        
    });
    
    function editGrowing(id){
        modDisplay({title:'编辑等级',url:'/member/growing/editgrowing?growth_id=' + id,height:350,width:550});
    }
    
    function delGrowing(id) {
        msgConfirm ('提醒','确定要删除此成长机制吗？',function(){
            $.ajax({
                url: "/member/growing/delgrowing",
                type: "POST",
                async: false,
                data: {growth_id: id},
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
    
</script>