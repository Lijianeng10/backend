<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
?>
<div style="font-size:14px;">
    <form action="/promote/user/index">
        <ul class="third_team_ul">
            <?php
            echo '<li>';
            echo Html::label("用户微信ID", "", ["style" => "margin-left:15px;"]);
            echo Html::input("input", "open_id", isset($get["open_id"]) ? $get["open_id"] : "", [ "class" => "form-control", "placeholder" => "用户微信ID", "style" => "width:200px;display:inline;margin-left:10px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("门店信息", "", ["style" => "margin-left:15px;"]);
            echo Html::input("input", "storeInfo", isset($get["storeInfo"]) ? $get["storeInfo"] : "", [ "class" => "form-control", "placeholder" => "名称、编号、手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("领取状态  ", "", ["style" => "margin-left:15px;"]);
            echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $userStatus, ["class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
            echo '</li>';
             echo '<li>';
            echo Html::label("使用状态  ", "", ["style" => "margin-left:34px;"]);
            echo Html::dropDownList("codeStatus", isset($get["codeStatus"]) ? $get["codeStatus"] : "", $useStatus, ["class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
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
        [
            'label' => '门店编号',
            'value' => 'store_code'
        ],
        [
            'label' => '运营者手机号',
            'value' =>'user_tel'
        ],
        [
            'label' => '用户微信ID',
            'value' => 'open_id'
        ], [
            'label' => '兑换码编号',
            'value' => 'redeem_code'
        ],[
            'label' => '领取状态',
            'value' => function($model) {
                $status=[
                        "0"=>"未领取",
                        "1"=>"已领取",
                    ];
                return $status[$model["status"]];
            }
        ],
       [
            'label' => '使用状态',
            'value' =>function($model) {
                 $Status=[
                        ""=>"",
                        "0"=>"未领取",
                        "1"=>"未使用",
                        "2"=>"已使用",
                        "3"=>"已过期",
                        "4"=>"已废除",
                    ];
                return $Status[$model["rs"]];
            }
        ],[
            'label' => '关注时间',
            'value' => 'create_time'
        ],
//                [
//            'label' => '操作',
//            'format' => 'raw',
//            'value' => function ($model) {
//                return '<div class="am-btn-toolbar">
//                    <div class="am-btn-group am-btn-group-xs">' .
//                        ($model['status'] == 0 ? '<span class="handle pointer" onclick="editSta(' . $model['id'] .',2);"> 通过 |</span><span class="handle pointer" onclick="editSta(' . $model['id'] . ',1);"> 不通过 |</span>' : '' ) .
//                        '<span class="handle pointer" onclick="del(' . $model['id'] . ');"> 删除 |</span>
//                    </div>
//                </div>';
//            }
//            ]
        ],
    ]);
?>
<script>
    //重置
    function goReset() {
        location.href = '/promote/user/index';
    }
</script>
