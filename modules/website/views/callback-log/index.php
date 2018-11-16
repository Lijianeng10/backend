<?php

use yii\grid\GridView;
use yii\bootstrap\Html;
use app\modules\common\models\CallbackDetail;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>

<div style="font-size:14px;">
    <?php
//    echo "<ul class='third_team_ul'>";
//    echo '<li >';
//    echo Html::label("执行状态", "", ["style" => "margin-left:15px;"]);
//    echo Html::dropDownList("callback_status", isset($get["callback_status"]) ? $get["callback_status"] : "", $callStatus, ["class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
//    echo '</li>';
//    echo "</ul>";
    ?>
</div>
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '基础表ID',
            'value' => 'callback_base_id'
        ],[
            'label' => '回调地址',
            'value' => 'url'
        ],[
    	'label' => '回调参数',
    	'value' => 'params'
    		],[
    	'label' => '返回结果',
    	'value' => 'return'
    		],[
    	'label' => '创建时间',
    	'value'=>'c_time'
    		],
//        [
//                'label' => '操作',
//                'format' => 'raw',
//                'value' => function ($model) {
//                return '<div class="am-btn-toolbar">
//                            <div class="am-btn-group am-btn-group-xs">
//                                <span class="handle pointer" onclick="doEdit(' . $model['id'] .  ');"> 编辑</span>
//                            </div>
//                        </div>';
//                }
//                ]
            ]
        ]);
                
        ?>
<script>
    //重置
    function goReset() {
        location.href = '/website/callback-log/index';
    }
// function addPayType() {
//     modDisplay({url: '/website/callback-third/add',title:"新增",height:480,width:540});
// };
// function doEdit(id) {
//     modDisplay({url: '/website/callback-third/edit?id=' + id,title:"编辑",height:420,width:540});
// };
</script>