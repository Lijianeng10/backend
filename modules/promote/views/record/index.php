<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\modules\common\helpers\PublicHelpers;
?>
<div style="font-size:14px;">
    <form action="/promote/record/index">
        <ul class="third_team_ul">
            <?php
            echo '<li>';
            echo Html::label("兑换码编码 ", "", ["style" => "margin-left:32px;"]);
            echo Html::input("input", "redeem_code", isset($get["redeem_code"]) ? $get["redeem_code"] : "", [ "class" => "form-control", "placeholder" => "兑换码编码", "style" => "width:200px;display:inline;margin-left:10px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("门店信息", "", ["style" => "margin-left:32px;"]);
            echo Html::input("input", "storeInfo", isset($get["storeInfo"]) ? $get["storeInfo"] : "", [ "class" => "form-control", "placeholder" => "门店名称、门店编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("兑换码状态  ", "", ["style" => "margin-left:15px;"]);
            echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $recordStatus, ["class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
            echo Html::button("新增", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;","onclick" => "addCode();"]);
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
            'label' => '兑换码编号',
            'value' => 'redeem_code'
        ], [
            'label' => '金额',
            'value' => 'value_amount'
        ],
//        [
//            'label' => '门店ID',
//            'value' => 'store_id'
//        ],
//         [
//            'label' => '门店名称',
//            'value' => 'store_name'
//        ],
        [
            'label' => '兑换码状态',
            'value' => function($model) {
                $Status=[
                        "0"=>"未领取",
                        "1"=>"未使用",
                        "2"=>"已使用",
                        "3"=>"已过期",
                        "4"=>"已废除",
                    ];
                return $Status[$model["status"]];
            }
        ],[
            'label' => '使用时间',
            'value' => 'settle_date'
        ],[
            'label' => '所属类型',
            'value' => function($model) {
                $type=[
                    "1"=>"体彩推广",
                ];
                if(isset($type[$model["type"]])){
                    return $type[$model["type"]];
                }else{
                    return "未知类型";
                }
            }
        ],  [
            'label' => '创建时间',
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
        location.href = '/promote/record/index';
    }
    //新增兑换码
    function addCode(){
        modDisplay({width: 500, height: 250, title: "新增兑换码", url: "/promote/record/add-code"});
    }
</script>


