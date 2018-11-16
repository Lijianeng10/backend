<?php
use yii\db\Query;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\member\helpers\Constants;
echo Html::input("reset", '', '返回',  ["class" => "am-btn am-btn-primary", "onclick" => "goBack();","style" => "margin-left:5px;"]);
    echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '优惠券名称',
            'value' => 'coupons_name'
        ],
        [
            'label' => '优惠券批次',
            'value' => 'batch'
        ], [
            'label' => '赠送数量',
            'value' => 'send_num'
        ],[
            'label' => '最低消费',
            'value' => 'less_consumption'
        ],[
            'label' => '优惠金额',
            'value' => 'reduce_money'
        ], [
            'label' => '单日限用（张）',
            'value' => 'days_num'
        ],  [
            'label' => '是否可叠加',
            'value' => function($model){
                $is_gift=Constants::IS_GIFT;
                return $is_gift[$model["stack_use"]];
            }
        ],
//        [
//            'label' => '活动类型',
//            'value' => function($model){
//                $type= (new Query())->select("type_name")->from("activity_type")->where(["activity_type_id"=>$model["type_id"]])->one();
//                return $type["type_name"]??"";
//            }
//        ],
        [
            'label' => '活动状态',
            'value' => function($model){
                $status=Constants::ACTIVITY_STATUS;
                return $status[$model["status"]]??"";
            }
        ],[
            'label' => '创建时间',
            'value' => 'create_time'
        ],
        [
            'label' => '操作',
            'format' => 'raw',
            'value' => function($model){
                return '<div class="am-btn-toolbar"><div class="am-btn-group am-btn-group-xs">'.
 ($model['status'] == 1 ? '<span class="handle pointer" onclick="editSta('.$model['coupons_activity_id'].',2);"> 禁用 |</span>' :'<span class="handle pointer" onclick="editSta('.$model['coupons_activity_id'].',1);"> 启用 |</span>' ).'<span class="handle pointer" onclick="delete('.$model['activity_id'] .');"> 删除 </span></div></div>';
            }
        ],
    ]
])
?>
<script>
    //修改活动状态
    function editSta(id, status) {
        var str = "";
        if (status == 1) {
            str = "您确定要启用该活动吗?";
        } else {
            str = "您确定要禁用该活动吗?";
        }
        msgConfirm('提醒', str, function () {
            $.ajax({
                url: "/website/activity/edit-status",
                type: "POST",
                async: false,
                data: {id: id, status: status},
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
    //返回
    function goBack(){
        location.href = '/website/activity/index';
    }
</script>

