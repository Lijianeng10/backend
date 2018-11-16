<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\modules\common\helpers\PublicHelpers;
?>
<div style="font-size:14px;">
    <form action="/website/worldcup/index">
        <ul class="third_team_ul">
            <?php
            echo '<li>';
            echo Html::label("用户信息", "", ["style" => "margin-left:32px;"]);
            echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", [ "class" => "form-control", "placeholder" => "用户名称、手机号", "style" => "width:200px;display:inline;margin-left:10px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("处理状态  ", "", ["style" => "margin-left:15px;"]);
            echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $applyStatus, ["class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
            echo Html::button("重置", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
            echo '</li>';
            ?>
        </ul>
    </form>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $dataList,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '用户名称',
            'value' => 'user_name'
        ],[
                    'label' => '手机号',
                    'value' => 'user_tel'
                ], [
                    'label' => '场次',
                    'value' =>'field',
                ],[
                    'label' => '场次名称',
                    'value' =>'field_name',
                ],[
                    'label' => '座位等级',
                    'value' =>'level',
                ],[
                    'label' => '价格',
                    'value' =>'money',
                ],[
                    'label' => '备注',
                    'value' => 'remark'
                ],  [
                    'label' => '处理状态',
                    'value' => function($model) {
                        $applyStatus = PublicHelpers::APPLY_STATUS;
                        return $applyStatus[$model["status"]];
                    }
                ], [
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="editInfo(' . $model['id'] . ');">编辑 |</span>
                            <span class="handle pointer" onclick="delInfo(' . $model['id'] . ');"> 删除 |</span></div></div>';
                    }
                ]
            ],
        ]);
        ?>
<script>
    //重置
    function goReset() {
        location.href = '/website/worldcup/index';
    }
    //删除广告
    function delInfo(id) {
        msgConfirm('提醒',"您确定要删除该订单记录吗", function () {
            $.ajax({
                url: "/website/worldcup/del-world",
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
    // //修改广告状态
    // function editSta(id, status) {
    //     var str = "";
    //     if (status == 1) {
    //         str = "您确定要上线该广告吗?";
    //     } else {
    //         str = "您确定要下线该广告吗?";
    //     }
    //     msgConfirm('提醒', str, function () {
    //         $.ajax({
    //             url: "/website/bananer/edit-status",
    //             type: "POST",
    //             async: false,
    //             data: {bananer_id: id, status: status},
    //             dataType: "json",
    //             success: function (data) {
    //                 if (data["code"] != 600) {
    //                     msgAlert(data["msg"]);
    //                 } else {
    //                     msgAlert(data['msg'], function () {
    //                         location.reload();
    //                     });
    //                 }
    //             }
    //         });
    //     })
    // }
    //编辑
    function editInfo(id){
       modDisplay({title: '编辑信息', url: '/website/worldcup/edit-world?id='+id, height: 500, width: 600});
    }
</script>

