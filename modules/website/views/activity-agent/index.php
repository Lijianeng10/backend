<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\member\helpers\Constants;
?>

<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
     <form action="/website/activity/index">
<?php
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addAgent"]);
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
            'label' => '代理商名称',
            'value' =>'agent_name'
        ],
        [
            'label' => '代理商Code',
            'value' => 'agent_code'
        ],[
            'label' => '创建时间',
            'value' => 'create_time'
        ],
        [
            'label' => '操作',
            'format' => 'raw',
            'value' => function($model){
                return '<div class="am-btn-toolbar"><div class="am-btn-group am-btn-group-xs">
                        <span class="handle pointer" onclick="edit('. $model['activity_agent_id'].');"> 编辑 |</span>
                        <span class="handle pointer" onclick="del('. $model['activity_agent_id'].');"> 删除 </span></div></div>';
            }
        ],
    ]
])
?>
<script>
    //新增
    $("#addAgent").click(function(){
        modDisplay({title: '新增代理', url: '/website/activity-agent/add-agent', height:250, width: 500});
    })
    //查看活动详情
    function del(id) {
        msgConfirm('提醒',"您确定要删除该代理信息吗", function () {
            $.ajax({
                url: "/website/activity-agent/del-agent",
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
    //编辑
    function edit(id) {
        modDisplay({title: '新增代理', url: '/website/activity-agent/edit-agent?agent_id='+id, height:250, width: 500});
    }

</script>

