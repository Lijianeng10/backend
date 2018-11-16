<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\modules\common\helpers\PublicHelpers;
?>
<div style="font-size:14px;">
    <form action="/promote/store/index">
        <ul class="third_team_ul">
            <?php
//            echo '<li>';
//            echo Html::label("手机号", "", ["style" => "margin-left:32px;"]);
//            echo Html::input("input", "user_tel", isset($get["user_tel"]) ? $get["user_tel"] : "", [ "class" => "form-control", "placeholder" => "手机号", "style" => "width:200px;display:inline;margin-left:10px;"]);
//            echo '</li>';
            echo '<li>';
            echo Html::label("门店信息", "", ["style" => "margin-left:32px;"]);
            echo Html::input("input", "storeInfo", isset($get["storeInfo"]) ? $get["storeInfo"] : "", [ "class" => "form-control", "placeholder" => "门店编号、手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("用户状态  ", "", ["style" => "margin-left:15px;"]);
            echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $userStatus, ["class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("创建时间", "", ["style" => "margin-left:21px;"]);
            echo Html::input("input", "startdate", isset($get["startdate"]) ? $get["startdate"] : "", ["id" => "startdate","class" => "form-control", "data-am-datepicker"=>"","id"=>"test","placeholder" => "开始日期", "style" => "width:120px;display:inline;margin-left:5px;"]);
            echo "-";
            echo Html::input("input", "enddate", isset($get["enddate"]) ? $get["enddate"] : "", ["id" => "enddate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:120px;display:inline;margin-left:5px;"]);
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
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
//       [
//            'label' => '门店名称',
//            'value' => 'store_name'
//        ],
        [
            'label' => '门店编号',
            'value' => 'store_code'
        ], 
//        [
//            'label' => '用户ID',
//            'value' => 'user_id'
//        ],
        [
            'label' => '用户手机号',
            'value' => 'user_tel'
        ],
//        [
//            'label' => '二维码地址',
//            'value' => 'qr_url'
//        ], 
        [
            'label' => '用户状态',
            'value' => function($model) {
                $userStatus=[
                        "0"=>"待审核",
                        "1"=>"未通过",
                        "2"=>"已通过",

                    ];
                return $userStatus[$model["status"]];
            }
        ], [
            'label' => '创建时间',
            'value' => 'create_time'
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                return '<div class="am-btn-toolbar">
                    <div class="am-btn-group am-btn-group-xs">' .
                         '<span class="handle pointer" onclick="editSta(' . $model['id'] .',2);"> 通过 | </span><span class="handle pointer" onclick="editSta(' . $model['id'] . ',1);"> 不通过 | </span>' .
                       ($model['status'] == 1||$model['status'] == 0 ? '<span class="handle pointer" onclick="del(' . $model['id'] . ');"> 删除 </span>': '' ).'</div>
                </div>';
            }
            ]
        ],
    ]);
    ?>
<script>
    $(function () {
        $(".bananerImg").bigShow();
    });
    //重置
    function goReset() {
        location.href = '/promote/store/index';
    }
    //删除
    function del(id) {
        msgConfirm('提醒',"您确定要删除该门店吗", function () {
            $.ajax({
                url: "/promote/store/del-user",
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
    //修改状态
    function editSta(id,sta) {
        var str = "";
        if (sta == 2) {
            str = "您确定通过该门店吗?";
        } else {
            str = "您确定不通过该门店吗?";
        }
        msgConfirm('提醒', str, function () {
            $.ajax({
                url: "/promote/store/edit-status",
                type: "POST",
                async: false,
                data: {id: id, status: sta},
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


