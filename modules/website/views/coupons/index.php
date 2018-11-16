<?php

use yii\db\Query;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\member\helpers\Constants;
?>

<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/website/coupons/index">
        <?php
        echo "<ul class='third_team_ul'>";
        echo '<li>';
        echo Html::label("批次号", "", ["style" => "margin-left:32px;"]);
        echo Html::input("input", "batch", isset($get["batch"]) ? $get["batch"] : "", ["class" => "form-control", "placeholder" => "批次号", "style" => "width:200px;display:inline;margin-left:5px;"]);
        echo '</li>';
//echo '<li>';
//echo Html::label("优惠类型", "", ["style" => "margin-left:5px;"]);
//echo Html::dropDownList("yh_type", isset($get["yh_type"]) ? $get["yh_type"] : "",$yh_type, ["class" => "form-control", "placeholder" => "优惠类型", "style" => "width:120px;display:inline;margin-left:5px;"]);
//echo '</li>';
        echo '<li>';
        echo Html::label("适用类型", "", ["style" => "margin-left:5px;"]);
        echo Html::dropDownList("application_type", isset($get["application_type"]) ? $get["application_type"] : "", $application_type, ["class" => "form-control", "placeholder" => "适用类型", "style" => "width:120px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("使用彩种", "", ["style" => "margin-left:5px;"]);
        echo Html::dropDownList("use_range", isset($get["use_range"]) ? $get["use_range"] : "", $use_range, ["class" => "form-control", "style" => "width:140px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("优惠券名称", "", ["style" => "margin-left:5px;"]);
        echo Html::input("input", "coupons_name", isset($get["coupons_name"]) ? $get["coupons_name"] : "", ["class" => "form-control", "placeholder" => "优惠券名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("是否为礼品", "", ["style" => "margin-left:-10px;"]);
        echo Html::dropDownList("is_gift", isset($get["is_gift"]) ? $get["is_gift"] : "", $is_gift, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("使用代理", "", ["style" => "margin-left:5px;"]);
        echo Html::dropDownList("use_agents", isset($get["use_agents"]) ? $get["use_agents"] : "", $proplayform, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
        echo '</li>';
//echo Html::label("申请时间", "", ["style" => "margin-left:15px;"]);
//echo Html::input("text", "start_date", isset($get["start_date"]) ? $get["start_date"] : "", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
//echo "-";
//echo Html::input("text", "end_date", isset($get["end_date"]) ? $get["end_date"] : "", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "日期", "style" => "width:80px;display:inline;margin-left:5px;"]) ;
        echo '<li>';
        echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:15px;"]);
        echo Html::input("reset", '', '重置', ["class" => "am-btn am-btn-primary", "onclick" => "goReset();", "style" => "margin-left:5px;"]);
        echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addCoupons"]);
        echo '</li>';
        ?>
    </form>
</div>
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '批次ID',
            'value' => 'batch'
        ], [
            'label' => '优惠券名称',
            'value' => 'coupons_name'
        ], [
            'label' => '优惠券类型',
            'value' => function($model) {
                $yh_type = Constants::YH_TYPE;
                return $yh_type[$model["type"]];
            }
        ], [
            'label' => '适用类型',
            'value' => function($model) {
                $application_type = [
                    "1" => "系统发放类",
                    "2" => "用户兑换类",
                ];
                return $application_type[$model["application_type"]];
            }
        ], [
            'label' => '使用代理',
            'value' => function($model) {
                $use_agents = (new Query())->select("agent_name")->from("activity_agent")->where(["agent_code" => $model["use_agents"]])->one();
                return $use_agents["agent_name"] ?? "";
            }
        ], [
            'label' => '使用彩种',
            'value' => function($model) {
                $use_range = Constants::LOTTERY_TYPE;
                return isset($model["use_range"]) ? $use_range[$model["use_range"]] : "";
            }
        ],
//                [
//            'label' => '是否为礼品',
//            'value' => function($model){
//                $is_gift=Constants::IS_GIFT;
//                return $is_gift[$model["is_gift"]];
//            }
//        ],
        [
            'label' => '是否可叠加',
            'value' => function($model) {
                $is_gift = Constants::IS_GIFT;
                return $is_gift[$model["stack_use"]];
            }
        ], [
            'label' => '最低消费',
            'value' => 'less_consumption'
        ], [
            'label' => '优惠金额',
            'value' => 'reduce_money'
        ], [
            'label' => '单日限用（张）',
            'value' => 'days_num'
        ], [
            'label' => '预发数量',
            'format' => 'raw',
            'value' => 'numbers'
        ], [
            'label' => '已发数量',
            'value' => 'send_num'
        ], [
            'label' => '已用数量',
            'value' => 'use_num'
        ], [
            'label' => '有效期',
            'value' => function($model) {
                if ($model['is_sure_date'] == 1) {
                    return $model["start_date"] . " - " . $model["end_date"];
                } else {
                    return $model['sure_time'] . '天';
                }
            }
        ], [
            'label' => '有效状态',
            'value' => function($model) {
                return $model['status'] == 1 ? '有效' : '失效';
            }
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function($model) {
                return '<div class="am-btn-toolbar"><div class="am-btn-group am-btn-group-xs">' .
                        ($model['status'] == 1 ? '<span class="handle pointer" onclick="sendCoupons(' . "'" . $model['end_date'] . "'," . "'" . $model['batch'] . "'," . $model['application_type'] . ');"> 发放优惠券 |</span>' : "") .
                        '<span class="handle pointer" onclick="viewDetail(' . "'" . $model['batch'] . "'" . ');"> 查看详情 |</span>' .
                        ($model['status'] == 1 ? '<span class="handle pointer" onclick="editCoupons(' . "'" . $model['batch'] . "'" . ');"> 编辑 |</span>' : "") .
                        ($model['status'] == 1 ? '<span class="handle pointer" onclick="editSta(' . "'" . $model['batch'] . "'" . ')"> 失效 |</span>' : "") .
                        '<span class="handle pointer" onclick="delete(' . "'" . $model['batch'] . "'" . ');"> 删除 </span></div></div>';
            }
        ],
    ]
])
?>
<script>
    //新增
    $("#addCoupons").click(function () {
        location.href = '/website/coupons/addview';
    })
    //查看优惠券详情
    function viewDetail(batch) {
        location.href = '/website/coupons/view-detail?batch=' + batch;
    }
    //重置
    function goReset() {
        location.href = '/website/coupons/index';
    }
    //发放优惠券
    function sendCoupons(endtime, batch, type) {
        var nowDate = new Date().getTime();
        var endDate = new Date(endtime).getTime();
        if (type == 2) {
            msgAlert("用户兑换类优惠券不可用于发放给用户")
        } else if (nowDate >= endDate) {
            msgAlert("该优惠券已过期，不允许发放")
        } else {
            location.href = '/website/coupons/send-coupons?batch=' + batch;
        }

    }
    //编辑
    function editCoupons(batch) {
        $.ajax({
            url: '/website/coupons/edit-coupons',
            async: false,
            type: 'POST',
            data: {batch: batch},
            dataType: 'json',
            success: function (data) {
                if (data['code'] != 600) {
                    msgAlert(data["msg"]);
                } else {
                    window.parent._location = location;
                    location.href = '/website/coupons/edit-coupons-detail?batch=' + batch;
//                    window.parent.modDisplay({title: '编辑优惠券', url: , height: 600, width: 800});
                }
            }
        });
    }

    //优惠券批次失效
    function editSta(batch) {
        msgConfirm('提醒', '确定要让该批次电子优惠券失效？失效后无法重新生效', function () {
            $.ajax({
                url: '/website/coupons/edit-sta',
                async: false,
                type: 'POST',
                data: {batch: batch},
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            })
        })
    }
</script>

