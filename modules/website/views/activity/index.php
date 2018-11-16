<?php
use yii\db\Query;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\member\helpers\Constants;
?>

<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
     <form action="/website/activity/index">
<?php
echo "<ul class='third_team_ul'>";
//echo '<li>';
//echo Html::label("批次号", "", ["style" => "margin-left:32px;"]);
//echo Html::input("input", "use_agents", isset($get["batch"]) ? $get["batch"] : "", ["class" => "form-control", "placeholder" => "批次号", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
echo '<li>';
echo Html::label("使用代理", "", ["style" => "margin-left:5px;"]);
echo Html::dropDownList("use_agents", isset($get["use_agents"]) ? $get["use_agents"] : "",$proplayform, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("活动类型", "", ["style" => "margin-left:5px;"]);
echo Html::dropDownList("type_id", isset($get["type_id"]) ? $get["type_id"] : "",$ac_type, ["class" => "form-control",  "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("活动状态", "", ["style" => "margin-left:5px;"]);
echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $acStatus, [ "class" => "form-control", "style" => "width:140px;display:inline;margin-left:5px;"]);
echo '</li>';
//echo '<li>';
//echo Html::label("优惠券名称", "", ["style" => "margin-left:5px;"]);
//echo Html::input("input", "coupons_name", isset($get["coupons_name"]) ? $get["coupons_name"] : "", ["class" => "form-control", "placeholder" => "优惠券名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
//echo Html::label("申请时间", "", ["style" => "margin-left:15px;"]);
//echo Html::input("text", "start_date", isset($get["start_date"]) ? $get["start_date"] : "", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
//echo "-";
//echo Html::input("text", "end_date", isset($get["end_date"]) ? $get["end_date"] : "", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "日期", "style" => "width:80px;display:inline;margin-left:5px;"]) ;
echo '<li>';
echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:15px;"]);
echo Html::input("reset", '', '重置',  ["class" => "am-btn am-btn-primary", "onclick" => "goReset();","style" => "margin-left:5px;"]);
echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addActivity"]);

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
            'label' => '活动名称',
            'value' => 'activity_name'
        ],
        [
            'label' => '使用代理',
            'value' => function($model){
                $use_agents= (new Query())->select("agent_name")->from("activity_agent")->where(["agent_code"=>$model["use_agents"]])->one();
                return $use_agents["agent_name"]??"";
            }
        ],
        [
            'label' => '活动类型',
            'value' => function($model){
                $type= (new Query())->select("type_name")->from("activity_type")->where(["activity_type_id"=>$model["type_id"]])->one();
                return $type["type_name"]??"";
            }
        ],
        [
            'label' => '有效期',
            'value' => function($model){
                return $model["start_date"]." - ".$model["end_date"];
            }
        ],
//        [
//            'label' => '有效状态',
//            'value' => function($model){
//               $timer = date("Y-m-d H:i:s");
//               if(strtotime($timer)>strtotime($model["end_date"])){
//                   return "已过期";
//               }else{
//                   return "生效中";
//               }
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
                return '<div class="am-btn-toolbar"><div class="am-btn-group am-btn-group-xs">
                         <span class="handle pointer" onclick="viewDetail('. $model['activity_id'] .');"> 查看详情 |</span>'.($model['status'] == 1 ? '<span class="handle pointer" onclick="editSta('.$model['activity_id'].',2);"> 禁用 |</span>' :'<span class="handle pointer" onclick="editSta('.$model['activity_id'].',1);"> 启用 |</span>' ).'<span class="handle pointer" onclick="del('. $model['activity_id'] .');"> 删除 </span></div></div>';
            }
        ],
    ]
])
?>
<script>
    //新增
    $("#addActivity").click(function(){
        modDisplay({title: '新增优惠活动', url: '/website/activity/add-activity', height:500, width: 800});
    })
    //查看活动详情
    function viewDetail(id){
        location.href = '/website/activity/view-coupons?id='+id;
    }
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
                url: "/website/activity/edit-activity-status",
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
    //重置
    function goReset() {
        location.href="/website/activity/index"
    }
    
    //修改活动状态
    function del(id) {
        msgConfirm('提醒', '你确定要删除此活动吗', function () {
            $.ajax({
                url: "/website/activity/delete",
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

