<?php

use yii\grid\GridView;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>

<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/admin/paytype/index">
    <?php
    echo Html::label('支付方式 ', '', ['style' => 'margin-left:15px;']);
    echo Html::dropDownList('pay_type_id', (isset($payTypeId) ? $payTypeId : '0'), $typeList, ['class' => 'form-control', 'style' => 'width:200px;display:inline;margin-right:5px;']);
    echo Html::submitButton('搜索', ["class" => 'search am-btn am-btn-primary', 'style' => 'margin-left:5px;']);
    echo Html::input('reset', '', '重置', ['class' => 'am-btn am-btn-primary', 'style' => 'margin-left:5px;']);
    ?>
    </form>
</div>
<div class="am-btn-group am-btn-group-xs operat">
    <a href="javascript:addPayType();" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 新增</a>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '支付方式ID',
            'value' => 'pay_type_id'
        ], [
            'label' => '方式名称 ',
            'value' => 'pay_type_name'
        ], [
            'label' => '方式Code',
            'value' => 'pay_type_code'
        ], [
            'label' => '所属父方式',
            'value' => 'parent_name'
        ], [
            'label' => '副标题',
            'value' => 'remark'
        ], [
            'label' => '排序',
            'value' => 'pay_type_sort'
        ], [
            'label' => '是否默认',
            'value' => function ($model) {
                return $model['default'] == 1 ? '默认' : '常态';
            }
        ], [
            'label' => '是否开放',
            'value' => function ($model) {
                return $model['status'] == 1 ? '停用' : '开放';
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
                $sta = $model['status'] == 1 ? '开放' : '停用'; 
                $default = $model['default'] == 1 ? '常态' : '默认';
                return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <span class="handle pointer" onclick="doSta(' . $model['pay_type_id'] . ',' . $model['status'] .');">'. $sta .'</span>
                                <span class="handle pointer" onclick="doDefault(' . $model['pay_type_id'] . ',' . $model['default'] .');">| '. $default .'</span>
                                <span class="handle pointer" onclick="doEdit(' . $model['pay_type_id'] .  ');">| 编辑</span>
                                <span class="handle pointer" onclick="doDelete(' . $model['pay_type_id'] .  ');">| 删除</span>
                            </div>
                        </div>';
            }
        ]
    ],
]);
$this->title = '支付方式设置';
?>

<script>
    
    function doSta(id,sta) {
        strSta = sta == 1? '开放' : '停用';
        msg = '确定要'+ strSta +'此支付方式吗？';
        msgConfirm ('提醒',msg,function(){
            if(sta == 1){
                sta = 2;
            }else{
                sta = 1;
            }
            console.log(sta);
            $.ajax({
                url: "/admin/paytype/set-pay-type",
                type: "POST",
                async: false,
                data: {id: id, status: sta},
                dataType: "json",
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert('设置成功',function(){
                            location.reload();
                        });
                        
                    }
                }
            });
        })
    };
    
    function doDefault(id,typeDetault) {
        strData = typeDetault == 1? '常态' : '默认';
        msg = '确定要将此支付方式设置为' + strData + '吗？';
        msgConfirm ('提醒',msg,function(){
            if(typeDetault == 1){
                typeDetault = 0;
            }else{
                typeDetault = 1;
            }
            $.ajax({
                url: "/admin/paytype/set-default",
                type: "POST",
                async: false,
                data: {id: id, type_detault: typeDetault},
                dataType: "json",
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert('设置成功',function(){
                            location.reload();
                        });
                        
                    }
                }
            });
        })
    };
    
    function doDelete(id) {
        msg = '确定要删除此支付方式吗？';
        msgConfirm ('提醒', msg, function(){
            $.ajax({
                url:'/admin/paytype/delete-type',
                type:'POST',
                async:false,
                data:{
                    typeId:id
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
    
    function addPayType() {
        modDisplay({url: '/admin/paytype/add-pay-type',title:"新增支付方式",height:420,width:540});
    };
    
    function doEdit(id) {
        modDisplay({url: '/admin/paytype/edit-pay-type?pay_type_id=' + id,title:"编辑支付方式",height:420,width:540});
    };
    
</script>