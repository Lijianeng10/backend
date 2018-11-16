<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
?>
<div style="font-size:14px;">
    <form  action="/website/applog/index" method="get">
    <?php
    echo "<ul class='third_team_ul'>";
    echo '<li >';
    echo Html::label("操作人", "", ["style" => "margin-left:32px;"]);
    echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["id" => "user_info", "class" => "form-control", "placeholder" => "操作人", "style" => "width:200px;display:inline;margin-left:10px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("创建时间  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "startdate", isset($get["startdate"]) ? $get["startdate"] : "", ["id" => "startdate","class" => "form-control", "data-am-datepicker"=>"","id"=>"test","placeholder" => "开始日期", "style" => "width:120px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("input", "enddate", isset($get["enddate"]) ? $get["enddate"] : "", ["id" => "enddate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:120px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("推送结果  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $status, ["id" => "status", "class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
    echo Html::button("新增", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addPushLog();"]);
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
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '推送标题',
            'value' => 'titile'
        ],
        [
            'label' => '推送内容',
            'value' => 'msg'
        ], [
            'label' => '跳转URL',
            'value' => 'jump_url'
        ],[
            'label' => '推送时间',
            'value' => 'push_time'
        ],[
            'label' => '状态',
            'value' => function($model){
                $status =[
                    "1"=>"审核中",
                    "2"=>"已通过",
                    "3"=>"未通过",
                    "4"=>"已发送",
                ];
                return $status[$model["status"]];
            }
        ],[
            'label' => '创建人',
            'value' =>"admin_name"
        ],[
            'label' => '创建时间',
            'value' => 'create_time'
        ],[
            'label' => '审核人',
            'value' =>"remark_name"
        ],[
            'label' => '审核备注',
            'value' =>"remark"
        ],[
            'label' => '操作',
            'format' => 'raw',
//            <span class="handle pointer" onclick="editDispenser(' . $model["jpush_notice_id"] . ')"> 编辑 |</span>
            'value' => function( $model) {
                        return '<div class="am-btn-group am-btn-group-xs">'.
                 ($model["status"] == "1" ? '<span class="handle pointer" onclick="auditPush(' . $model["jpush_notice_id"] . ')"> 审核 |</span>' : '') .
                 ($model["status"] == "2" ? '<span class="handle pointer" onclick="sendPush(' . $model["jpush_notice_id"] . ')"> 发送 |</span>' : '').
                  '<span class="handle pointer" onclick="deletePush(' . $model["jpush_notice_id"] .','.$model["status"].')"> 删除</span>
            </div>';
            }
        ],
        ]
    ]);
?>
<script>
    //新增
    function addPushLog(){
         modDisplay({title: '新增推送消息', url: '/website/applog/add-app-log', height: 450, width: 550});
    }
    //推送审核
    function auditPush(id){
       modDisplay({title: '审核推送消息', url: '/website/applog/audit-app-log?jpush_notice_id='+id, height: 280, width: 450}); 
    }
    //发送
    function sendPush(id){
        msgConfirm("提示","确定发送吗?",function(){
            $.ajax({
                url: "/website/applog/send-push",
                data: {jpush_notice_id:id},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/website/applog/index';
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            })
        })
    }
    //删除
    function deletePush(id,sta){
        if(sta==4){
            msgAlert("已推送消息不可删除")
            return false;
        }
        msgConfirm("提示","确定删除该条记录吗?",function(){
            $.ajax({
                url: "/website/applog/delete-push",
                data: {jpush_notice_id:id},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/website/applog/index';
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            })
        })
    }
    //重置
    function goReset(){
        location.href = '/website/applog/index';
    }   
</script>
