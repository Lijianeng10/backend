<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
?>
<div style="font-size:14px;">
    <form  action="/website/screen/index" method="get">
    <?php
    echo "<ul class='third_team_ul'>";
//    echo '<li >';
//    echo Html::label("操作人", "", ["style" => "margin-left:32px;"]);
//    echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["id" => "user_info", "class" => "form-control", "placeholder" => "操作人", "style" => "width:200px;display:inline;margin-left:10px;"]);
//    echo '</li>';
//    echo '<li>';
//    echo Html::label("创建时间  ", "", ["style" => "margin-left:15px;"]);
//    echo Html::input("input", "startdate", isset($get["startdate"]) ? $get["startdate"] : "", ["id" => "startdate","class" => "form-control", "data-am-datepicker"=>"","id"=>"test","placeholder" => "开始日期", "style" => "width:120px;display:inline;margin-left:5px;"]);
//    echo "-";
//    echo Html::input("input", "enddate", isset($get["enddate"]) ? $get["enddate"] : "", ["id" => "enddate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:120px;display:inline;margin-left:5px;"]);
//    echo '</li>';
//    echo '<li>';
//    echo Html::label("推送结果  ", "", ["style" => "margin-left:15px;"]);
//    echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $status, ["id" => "status", "class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
//    echo '</li>';
    echo '<li>';
//    echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
    echo Html::button("新增", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addPushLog();"]);
//    echo Html::button("重置", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
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
            'label' => '授权码',
            'value' => 'screen_key'
        ],[
            'label' => '备注',
            'value' =>"store_code"
        ],[
            'label' => '创建时间',
            'value' => 'create_time'
        ],[
            'label' => '登录状态',
            'value' => 'is_login'
        ],
//        [
//            'label' => '操作',
//            'format' => 'raw',
//            'value' => function( $model) {
//                        return '<div class="am-btn-group am-btn-group-xs">'.
//                 ($model["status"] == "1" ? '<span class="handle pointer" onclick="auditPush(' . $model["jpush_notice_id"] . ')"> 审核 |</span>' : '') .
//                 ($model["status"] == "2" ? '<span class="handle pointer" onclick="sendPush(' . $model["jpush_notice_id"] . ')"> 发送 |</span>' : '').
//                  '<span class="handle pointer" onclick="deletePush(' . $model["jpush_notice_id"] .','.$model["status"].')"> 删除</span>
//            </div>';
//            }
//        ],
        ]
    ]);
?>
<script>
    //新增
    function addPushLog(){
         modDisplay({title: '新增TV授权码', url: '/website/screen/add-screen', height: 350, width: 550});
    }
</script>
