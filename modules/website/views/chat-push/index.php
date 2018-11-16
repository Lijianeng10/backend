<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\modules\common\helpers\PublicHelpers;
?>
<div style="font-size:14px;">
    <form  action="/website/chat-push/index" method="get">
    <?php
    echo "<ul class='third_team_ul'>";
    echo '<li >';
    echo Html::label("推送信息", "", ["style" => "margin-left:5px;"]);
    echo Html::input("input", "push_info", isset($get["push_info"]) ? $get["push_info"] : "", ["class" => "form-control", "placeholder" => "推送标题、推送内容", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("创建时间  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "startdate", isset($get["startdate"]) ? $get["startdate"] : "", ["class" => "form-control", "data-am-datepicker"=>"","id"=>"test","placeholder" => "开始日期", "style" => "width:120px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("input", "enddate", isset($get["enddate"]) ? $get["enddate"] : "", ["id" => "enddate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:120px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("推送结果  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $status, ["class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("推送类型  ", "", ["style" => "margin-left:5px;"]);
    echo Html::dropDownList("type", isset($get["type"]) ? $get["type"] : "", $type, ["class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
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
            'value' => 'title'
        ],
        [
            'label' => '推送内容',
            'value' => 'content',
            'contentOptions' => ['style' => 'max-width:150px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']
        ],[
            'label' => '缩略图',
            'format'=> 'raw',
            'value' => function($model){
                $html = '';
                if(!empty($model['img_url'])){
                    $html = '<img data-magnify="gallery" data-src="'.$model["img_url"].'" src="'.$model["img_url"].'" style="width:40px;height:40px">';
                }
                return $html;

            }
        ], [
            'label' => '跳转URL',
            'value' => 'jump_url'
        ], [
            'label' => '推送类型',
            'value' => function($model){
                $type =PublicHelpers::PUSH_TYPE;
                return $type[$model["type"]];
            }
        ],[
            'label' => '推送用户类型',
            'value' => function($model){
                $userType =PublicHelpers::PUSH_USER_TYPE;
                return $userType[$model["send_type"]];
            }
        ],
//        [
//            'label' => '推送用户',
//            'value' => 'send_user'
//        ],
        [
            'label' => '推送状态',
            'value' => function($model){
                $status =PublicHelpers::PUSH_STATUS;
                return $status[$model["status"]];
            }
        ],[
            'label' => '推送时间',
            'value' => 'push_time'
        ],[
            'label' => '创建时间',
            'value' => 'create_time'
        ],[
            'label' => '操作人',
            'value' =>"opt_name"
        ],[
            'label' => '操作',
            'format' => 'raw',
//            <span class="handle pointer" onclick="editDispenser(' . $model["jpush_notice_id"] . ')"> 编辑 |</span>
            'value' => function( $model) {
                        return '<div class="am-btn-group am-btn-group-xs">'.
                            '<span class="handle pointer" onclick="readPush(' . $model["chat_push_id"] . ')"> 查看 |</span>'.
                 ($model["status"] == 0 || $model["status"] == 2? '<span class="handle pointer" onclick="sendPush(' . $model["chat_push_id"] . ')"> 发送 |</span>' : '').
                            ($model["status"] == 0 ? '<span class="handle pointer" onclick="editPush(' . $model["chat_push_id"] . ')"> 编辑 |</span>' : '').
                            ($model["status"]==0 || $model["status"] == 2? '<span class="handle pointer" onclick="deletePush(' . $model["chat_push_id"] .','.$model["status"].')"> 删除</span>':"").'</div>';
            }
        ],
        ]
    ]);
?>
<script>
    //新增
    function addPushLog(){
        location.href ='/website/chat-push/add-chat-push'
        //  modDisplay({title: '新增推送消息', url: '/website/chat-push/add-chat-push', height: 600, width: 700});
    }
    //编辑
    function editPush(id) {
        // modDisplay({title: '编辑推送消息', url: '/website/chat-push/edit-chat-push?chat_push_id='+id, height: 600, width: 700});
        location.href ='/website/chat-push/edit-chat-push?chat_push_id='+id
    }
    //编辑
    function readPush(id) {
        // modDisplay({title: '编辑推送消息', url: '/website/chat-push/edit-chat-push?chat_push_id='+id, height: 600, width: 700});
        location.href ='/website/chat-push/read-chat-push?chat_push_id='+id
    }

    //发送
    function sendPush(id){
        msgConfirm("提示","确定发送吗?",function(){
            $.ajax({
                url: "/website/chat-push/send-push",
                data: {chat_push_id:id},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/website/chat-push/index';
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
        msgConfirm("提示","确定删除该条记录吗?",function(){
            $.ajax({
                url: "/website/chat-push/delete-push",
                data: {id:id},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/website/chat-push/index';
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
        location.href = '/website/chat-push/index';
    }   
</script>
