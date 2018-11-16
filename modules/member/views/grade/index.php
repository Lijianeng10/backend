<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <?php
    echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addLevels" ]);
    ?>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '等级名称',
            'value' => 'level_name'
        ],[
            'label' => '所需成长值 ',
            'value' => 'level_growth'
        ],
        [
            'label' => '购彩赠送咕币倍数',
            'value' => 'multiple'
            
        ],
//        [
//            'label' => '充值赠送咕币',
//            'format' => 'raw',
//            'value' => function ($model) {
//                $discount = $model['glcz_discount'] * 100;
//                return $discount . '%';
//            }
//        ],
//                [
//            'label' => '充值咕啦币-积分(1-?)',
//            'value' => 'glcz_integral'
//        ], 
        [
            'label' => '升级机制',
            'format' => 'raw',
            'value' => function($model){
                return $model['up_status'] == 1 ? '正常升级' : '等级锁定';
            }
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <span class="handle pointer" onclick="editLevels(' .$model['user_level_id'] . ');">编辑</span>    
                                <span class="handle pointer" onclick="delLevels(' .$model['user_level_id'] . ');">| 删除</span>
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
        $('#addLevels').click(function() {
          modDisplay({title:'新增等级',url:'/member/grade/addlevels',height:480});
        });
        
    });
    
    function editLevels(id){
        modDisplay({title:'编辑等级',url:'/member/grade/editlevels?levels_id=' + id,height:480});
    }
    
    function delLevels(id) {
        msgConfirm ('提醒','确定要删除此会员等级吗？',function(){
            $.ajax({
                url: "/member/grade/dellevels",
                type: "POST",
                async: false,
                data: {level_id: id},
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