<?php

use yii\grid\GridView;
use yii\bootstrap\Html;
use app\modules\common\models\CallbackBase;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>

<div style="font-size:14px;">
    <form action="/website/callback-third/index">
    <?php
    $type=isset($get["name"]) ? $get["name"] : "";
    echo "<ul class='third_team_ul'>";
    echo '<li >';
    echo Html::label("回调名称", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "name", $type, ["id" => "type", "class" => "form-control", "placeholder" => "回调名称", "style" => "width:200px;display:inline;margin-left:10px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
    echo Html::button("新增", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;","onclick" => "addPayType();"]);
    echo Html::button("重置", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
    echo '</li>';
    echo "</ul>";
    ?>
    </form>
</div>
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
//        [
//            'label' => '',
//            'format' => 'raw',
//            'value' => function($model) {
//                return Html::input('checkbox', 'delSelect', $model["admin_id"], ["class" => 'delSelect']);
//            }
//                ], 
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '回调地址',
            'value' => 'url'
        ], [
            'label' => '回调标识码',
            'value' => 'code'
        ],
    	[
    	'label' => '回调最大次数',
    	'value' => 'times'
    		],
    	[
    	'label' => '回调名称',
    	'value' => 'name'
    		],
    	[
    	'label' => '第三方类型',
    	'value'=>function($model){
                    	return  CallbackBase::THIRD_TYPE[$model['third_type']];   //主要通过此种方式实现
                	},
    		],
    	[
    	'label' => '回调第三方分销商ID',
    	'value' => 'agent_id'
    		],
    		[
    		'label' => '回调类型',
    		'value'=>function($model){
    		return  CallbackBase::TYPE[$model['type']];   //主要通过此种方式实现
    		},
    		],
    	[
    	'label' => '回调备注',
    	'value' => 'remark'
    		],
                 [
                    'label' => '创建时间',
                    'value'=>function($model){
                    	return  date('Y-m-d H:i:s',$model['c_time']);   //主要通过此种方式实现
                	},
                ],
                [
                'label' => '操作',
                'format' => 'raw',
                'value' => function ($model) {
                return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <span class="handle pointer" onclick="doEdit(' . $model['id'] .  ');"> 编辑</span>
                            </div>
                        </div>';
                }
                ]
//                        [
//                    'label' => '操作',
//                    'format' => 'raw',
//                    'value' => function($model) {
//                        return '<div class="am-btn-group am-btn-group-xs">
//                            <span class="handle pointer" onclick="readAdmin(' . $model["admin_id"] . ');">查看用户</span>
//                            <span class="handle pointer" onclick="editAdmin(' . $model["admin_id"] . ');">| 编辑</span>
//                            <span class="handle pointer" onclick="statusAdmin(' . $model["admin_id"] . ');">| '.($model["status"]?"停用":"启用").'</span>
//                            <span class="handle pointer" onclick="deleteAdmin(' . $model["admin_id"] . ');">| 删除</span>
//                        </div>';
//                    }
//                ]
            ]
        ]);
                
        ?>
<script>
function addPayType() {
    modDisplay({url: '/website/callback-third/add',title:"新增",height:480,width:540});
};
function doEdit(id) {
    modDisplay({url: '/website/callback-third/edit?id=' + id,title:"编辑",height:420,width:540});
};
//重置
function goReset() {
    location.href = '/website/callback-third/index';
}
</script>