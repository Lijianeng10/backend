<?php

use yii\helpers\Html;
use yii\grid\GridView;

$cert_status = [
    "" => "请选择",
    "1" => "未认证",
    "2" => "审核中",
    "3" => "已通过",
    "4" => "未通过"
];
$store_type = [
    "" => "请选择",
    "1" => "个体自营店",
    "2" => "个体转让店",
    "3" => "企业自营店",
];

echo '<form action="/agents/amap/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("门店信息", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "store_info", isset($_GET["store_info"]) ? $_GET["store_info"] : "", ["class" => "form-control", "placeholder" => "门店名称、编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("手机号  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "phone_num", isset($_GET["phone_num"]) ? $_GET["phone_num"] : "", ["class" => "form-control", "placeholder" => "手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("认证状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("cert_status", isset($_GET["cert_status"]) ? $_GET["cert_status"] : "", $cert_status, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("所在地区  ", "", ["style" => "margin-left:15px;"]);
echo '<div id="city_china" class="am-form-group" style="display:inline-block;">
            <label><select class="form-control province cxselect" disabled="disabled" name="province" ' . (isset($_GET["province"]) ? ('data-value="' . $_GET["province"] . '"') : "") . ' style="width:135px;display:inline;"></select></label>
    </div>';
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/agents/amap/index'"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '门店编号',
            'value' => 'store_code'
        ], [
            'label' => '门店No',
            'value' => 'cust_no'
        ], [
            'label' => '门店名称',
            'value' => 'store_name'
        ], [
            'label' => '手机号',
            'value' => 'phone_num'
        ], [
            'label' => '所在省份',
            'value' => 'province'
        ], [
            'label' => '所在城市',
            'value' => 'city'
        ], [
            'label' => '所在地区',
            'value' => 'address'
        ], [
            'label' => '坐标点',
            'value' => 'coordinate'
        ], [
            'label' => '认证状态',
            'value' => function($model) {
                if ($model["cert_status"] == null) {
                    return "";
                }
                $cert_status = [
                    "1" => "未认证",
                    "2" => "审核中",
                    "3" => "已通过",
                    "4" => "未通过"
                ];
                return $cert_status[$model["cert_status"]];
            }
        ], [
            'label' => '使用状态',
            'value' => function($model) {
                if ($model["status"] == null) {
                    return "";
                }
                $status = [
                    "1" => "启用",
                    "2" => "禁用"
                ];
                return $status[$model["status"]];
            }
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function( $model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="doDelete(' . $model["store_code"] . ')">删除</span>
                            <span class="handle pointer" onclick="createAmap(' . $model["store_code"] . ')">| 上传</span>
                        </div>';
            }
        ]
    ]
]);
?>
<script src="/js/jquery.cxselect.min.js"></script>
<script>
    $('#city_china').cxSelect({
        url: '/js/cityData.min.json',
        selects: ['province'],
        emptyStyle: 'none'
    });
    function createAmap(store_no) {
        $.ajax({
            url: "/agents/amap/create-amap",
            async: false,
            type: "POST",
            dataType: "json",
            data: {store_no: store_no},
            success: function (json) {
                if (json["code"] == 0) {
                    msgAlert(json["msg"], function () {
                        location.reload();
                    });
                } else {
                    msgAlert(json["msg"]);
                }
            }
        });
    }
    function doDelete(store_no) {
        msgConfirm("提示", "确定删除？", function () {
            $.ajax({
                url: "/agents/amap/delete-amap",
                async: false,
                type: "POST",
                dataType: "json",
                data: {store_no: store_no},
                success: function (json) {
                    console.log(json);
                    if (json["code"] == 0) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        })
    }
</script>

