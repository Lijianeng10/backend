<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\modules\common\helpers\PublicHelpers;
?>
<div style="font-size:14px;">
    <form action="/website/found/index">
        <ul class="third_team_ul">
            <?php
            echo '<li>';
            echo Html::label("标题名称", "", ["style" => "margin-left:32px;"]);
            echo Html::input("input", "picName", isset($get["picName"]) ? $get["picName"] : "", [ "class" => "form-control", "placeholder" => "标题名称", "style" => "width:200px;display:inline;margin-left:10px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("使用状态  ", "", ["style" => "margin-left:15px;"]);
            echo Html::dropDownList("picStatus", isset($get["picStatus"]) ? $get["picStatus"] : "", $status, ["class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("使用范围  ", "", ["style" => "margin-left:15px;"]);
            echo Html::dropDownList("picUse", isset($get["picUse"]) ? $get["picUse"] : "", $picUse, ["class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
            echo Html::button("新增", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addBananer();"]);
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
            'label' => '标题名称',
            'value' => 'pic_name'
        ], [
            'label' => 'APP图片',
            'format' => 'raw',
            'value' => function($model) {
                if (!empty($model['pic_url'])) {
                    return Html::img($model['pic_url'], ['width' => '40px', 'height' => '40px', "class" => "bananerImg"]);
                } else {
                    return "";
                }
            }
         ], [
            'label' => 'PC图片',
            'format' => 'raw',
            'value' => function($model) {
                if (!empty($model['pc_pic_url'])) {
                    return Html::img($model['pc_pic_url'], ['width' => '40px', 'height' => '40px', "class" => "bananerImg"]);
                } else {
                    return "";
                }
            }
        ], [
            'label' => '使用范围',
            'value' => function($model){
                $picUse = PublicHelpers::BANANER_USE;
                return $picUse[$model["use_type"]];
            }
         ],[
            'label' => '跳转类型',
            'value' => function($model){
                $jumpType = PublicHelpers::JUMP_TYPE;
                return $jumpType[$model["jump_type"]];
            }
        ],[
            'label' => '链接标题',
            'value' => 'jump_title'
        ],[
            'label' => '链接地址',
            'value' => 'jump_url'
        ],  [
            'label' => '使用状态',
            'value' => function($model) {
                $picStatus = PublicHelpers::BANANER_STATUS;
                return $picStatus[$model["status"]];
            }
        ], [
            'label' => '创建时间',
            'value' => 'create_time'
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
//                    </span><span class="handle pointer" onclick="readBananer(' . $model['bananer_id'] . ');"> 查看详情 |</span>'
                return '<div class="am-btn-toolbar">
                    <div class="am-btn-group am-btn-group-xs"><span class="handle pointer" onclick="editBananer(' . $model['bananer_id'] . ');">编辑 |</span>'
                        . '<span class="handle pointer" onclick="delBananer(' . $model['bananer_id'] . ');"> 删除 |</span>' .
                        ($model['status'] == 0||$model['status'] == 2 ? '<span class="handle pointer" onclick="editSta(' . $model['bananer_id'] . ',1);"> 发布</span>' :"" ) .
                        ($model['status'] == 1 ? '<span class="handle pointer" onclick="editSta(' . $model['bananer_id'] . ',2);"> 下线</span>' :"" ) .
                        '
                    </div>
                </div>';
            }
        ]
        ],
    ]);
        $this->title = 'Lottery';
        ?>
<script>
    $(function () {
        $(".bananerImg").bigShow();
    });
    //新增
    function addBananer() {
        location.href ='/website/found/add-bananer'
//        modDisplay({title: '新增广告', url: '/website/bananer/add-bananer', height: 780, width: 800});
    }
    //重置
    function goReset() {
        location.href = '/website/found/index';
    }
    //删除广告
    function delBananer(id) {
        msgConfirm('提醒',"您确定要删除该广告吗", function () {
            $.ajax({
                url: "/website/found/del-bananer",
                type: "POST",
                async: false,
                data: {bananer_id: id},
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
    //修改广告状态
    function editSta(id, status) {
        var str = "";
        if (status == 1) {
            str = "您确定要上线该广告吗?";
        } else {
            str = "您确定要下线该广告吗?";
        }
        msgConfirm('提醒', str, function () {
            $.ajax({
                url: "/website/found/edit-status",
                type: "POST",
                async: false,
                data: {bananer_id: id, status: status},
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
    function editBananer(id){
        location.href ='/website/found/edit-bananer?bananer_id='+id
//        modDisplay({title: '编辑广告', url: '/website/bananer/edit-bananer?bananer_id='+id, height: 780, width: 800});
    }
</script>

