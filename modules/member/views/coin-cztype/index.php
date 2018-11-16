<?php

use yii\helpers\Html;
use yii\grid\GridView;

?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/member/coin-cztype/index">
    <?php
    echo Html::label("充值类型", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "ccz_type", empty($cczType)? '' : $cczType, ["class" => "form-control", "placeholder" => "充值类型", "style" => "width:200px;display:inline;margin-left:5px;"]);
    
    echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;"]);
    echo Html::button("重置", ["class" => " am-btn am-btn-primary","id" => "ressetGift", "style" => "margin-left:5px;"]);
    echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addType" ]);
    echo '</br>';
    echo '</br>';
    echo Html::label('福利类型：1：无福利 2：充值立得对应咕币的百分比 3：首充立得对应咕币的百分比 4：充值后得赠送咕币', '', ["style" => "margin-left:15px;color:red"])
    
    ?>
    </form>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '充值类型',
            'value' => 'cz_type',
        ], [
            'label' => '类型名称',
            'value' => 'cz_type_name',
        ], [
            'label' => '充值价格(元)',
            'value' => 'cz_money',
        ], [
            'label' => '对应咕币(个)',
            'value' => 'cz_coin',
        ], [
            'label' => '福利类型',
            'value' => 'weal_type',
        ], [
            'label' => '福利值(%|个)',
            'value' => function ($model) {
                return $model['weal_type'] == 1 ? '无福利' : ($model['weal_type'] == 2 ? $model['weal_value']. '%' : ($model['weal_type'] == 3 ? $model['weal_value']. '%' : ($model['weal_type'] == 4 ? (int)$model['weal_value'] . '个' : '无福利')));
            },
        ], [
            'label' => '福利有效期',
            'value' => function($model) {
                return $model['weal_time'] . '天';
            },
        ], [
            'label' => '状态',
            'value' => function($model) {
                return $model['status'] == 1 ? '启用' : '禁用';
            },
        ], [
            'label' => '操作人',
            'value' => 'opt_name'
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                $status = $model['status'] == 1 ? '禁用' : '启用';
                return  '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <span class="handle pointer" onclick="editType('.$model['coin_cz_type_id'].');">编辑</span>    
                                <span class="handle pointer" onclick="delType('.$model['coin_cz_type_id'].');">| 删除</span>
                                <span class="handle pointer" onclick="editStatus('.$model['coin_cz_type_id'].');">| '.$status .'</span>
                            </div>
                        </div>';
            }
        ]
    ],
]);
$this->title = 'Member';
?>
<script>
    $(function() {
        $('#addType').click(function() {
            modDisplay({title:'新增类型',url:'/member/coin-cztype/add-cztype',height:400,width:450});
        });
        
    });
    function editType(id){
        modDisplay({title:'编辑类型',url:'/member/coin-cztype/edit-cztype?cz_type_id=' + id,height:400,width:450});
    }
    
    function delType(id) {
        msgConfirm ('提醒','确定要删除此充值类型吗？',function(){
            $.ajax({
                url: "/member/coin-cztype/deltype",
                type: "POST",
                async: false,
                data: {type_id: id},
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
    
    function editStatus(id) {
        msgConfirm ('提醒','确定要删除此充值类型吗？',function(){
            $.ajax({
                url: "/member/coin-cztype/edit-status",
                type: "POST",
                async: false,
                data: {type_id: id},
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