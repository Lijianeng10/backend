<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\member\helpers\Constants;
?>

<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
     <form action="/website/activity-type/index">
<?php
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addType"]);
echo '</li>';
?>
    </form>
</div>
<?php
    echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '类型ID',
            'value' =>'activity_type_id'
        ],
        [
            'label' => '活动类型名称',
            'value' =>'type_name'
        ],
        [
            'label' => '创建时间',
            'value' => 'create_time'
        ],
//        [
//            'label' => '操作',
//            'format' => 'raw',
//            'value' => function($model){
//                return '<div class="am-btn-toolbar"><div class="am-btn-group am-btn-group-xs">
//                         <span class="handle pointer" onclick="del('. $model['activity_type_id'].');"> 删除 </span></div></div>';
//            }
//        ],
    ]
])
?>
<script>
    //新增
    $("#addType").click(function(){
        modDisplay({title: '新增活动类型', url: '/website/activity-type/add-type', height:250, width: 500});
    })
    //查看活动详情
    function del(id) {
        msgConfirm('提醒',"您确定要删除该活动类型吗", function () {
            $.ajax({
                url: "/website/activity-type/del-type",
                type: "POST",
                async: false,
                data: {id: id},
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

