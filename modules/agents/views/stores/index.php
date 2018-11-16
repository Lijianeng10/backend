<style>
.opts{margin:0px auto;width:150px;height:30px;}    
.opts ul{margin:0px;padding:0px;}
.ul1 li{
     width:33px;
}
.ul2{
    z-index: 99;
}
.ul2 li{
    width: 80px;
    text-align: center;
}
.opts ul li{
    position:relative;
    float:left;  
    line-height:30px;
} 
.opts ul li ul{
    display:none;
} 
/*.menu ul li ul li{
    margin-top:1px
}*/
/*.menu ul li:hover{background:red;}*/
.opts ul li span:hover{
    color:#fff;
}
.opts ul li:hover ul{
    background:#ccc;
    display:block;
    position: absolute; 
    left: -45px; 
    top: 30px;
}
</style>
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
    "1" => "个人自营店",
    "2" => "个体转让店",
    "3" => "咕啦自营店",
    "4" => "贵人鸟加盟店"
];
$company_type = [
    "" => "请选择",
    "1" => "是",
    "2" => "否",
];
$store_sta = [
    "" => "请选择",
    "1" => "正常",
    "2" => "禁用",
];
$business_status = [
    "" => "请选择",
    "0" => "未开业",
    "1" => "营业中",
    "2" => "暂停营业",
];
$consignmentType = [
    "" => "请选择",
    "1" => "体彩店",
    "2" => "福彩店",
    "3" => "双彩店",
];
echo '<form action="/agents/stores/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("门店信息", "", ["style" => "margin-left:30px;"]);
echo Html::input("input", "store_info", isset($_GET["store_info"]) ? $_GET["store_info"] : "", ["class" => "form-control", "placeholder" => "门店名称、门店编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("经营类型  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("store_type", isset($_GET["store_type"]) ? $_GET["store_type"] : "", $store_type, ["class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("运营者信息  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "info", isset($_GET["info"]) ? $_GET["info"] : "", ["class" => "form-control", "placeholder" => "用户昵称、用户咕啦编号、运营者手机号", "style" => "width:250px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("开户日期  ", "", ["style" => "margin-left:30px;"]);
echo Html::input("text", "startdate", isset($_GET["startdate"]) ? $_GET["startdate"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "enddate", isset($_GET["enddate"]) ? $_GET["enddate"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("认证状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("cert_status", isset($_GET["cert_status"]) ? $_GET["cert_status"] : "", $cert_status, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("所在地区  ", "", ["style" => "margin-left:28px;"]);
echo '<div id="city_china" class="am-form-group" style="display:inline-block;margin-left:6px;">
            <select class="form-control province cxselect" disabled="disabled" name="province" ' . (isset($_GET["province"]) ? ('data-value="' . $_GET["province"] . '"') : "") . ' style="width:135px;display:inline;"></select>
    </div>';
echo '</li>';
echo '<li>';
echo Html::label("咕啦旗舰店  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("company_id", isset($_GET["company_id"]) ? $_GET["company_id"] : "", $company_type, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("门店状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("store_sta", isset($_GET["store_sta"]) ? $_GET["store_sta"] : "", $store_sta, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("营业状态  ", "", ["style" => "margin-left:30px;"]);
echo Html::dropDownList("business_status", isset($_GET["business_status"]) ? $_GET["business_status"] : "", $business_status, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("对外联系号  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "telephone", isset($_GET["telephone"]) ? $_GET["telephone"] : "", ["class" => "form-control", "placeholder" => "对外联系手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("门店类型  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("consignment_type", isset($_GET["consignment_type"]) ? $_GET["consignment_type"] : "", $consignmentType, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("收取服务费  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("is_service_fee", isset($_GET["is_service_fee"]) ? $_GET["is_service_fee"] : "", $company_type, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:21px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/agents/stores/index'"]);
echo Html::tag("span", "新增", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/agents/stores/addstore'"]);
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
            'label' => '门店名称',
            'headerOptions' => ['style' => 'width:8%'],
            'value' => 'store_name'
        ], [
            'label' => '对外联系号',
            'value' => 'telephone'
        ], [
            'label' => '省份',
            'value' => 'province'
        ], [
            'label' => '城市',
            'value' => 'city'
        ], [
            'label' => '地区',
            'value' => 'area'
        ], [
            'label' => '运营者',
            'format' => 'raw',
            'value' => function($model) {
                return $model["cust_no"] . "</br>" . $model["consignee_name"] . "</br>" . $model["phone_num"];
            }
        ],  [
            'label' => '总金额',
            'headerOptions' => ['style' => 'background:#FF9999'],
            'value' => 'all_funds'
        ], [
            'label' => '可用金额',
            'headerOptions' => ['style' => 'background:#FF9999'],
            'value' => 'able_funds'
        ], [
            'label' => '冻结金额',
            'headerOptions' => ['style' => 'background:#FF9999'],
            'value' => 'ice_funds'
        ], [
            'label' => '不可提现金额',
            'headerOptions' => ['style' => 'background:#FF9999'],
            'value' => 'no_withdraw'
        ],[
            'label' => '可提现金额',
            'headerOptions' => ['style' => 'background:#FF9999'],
            'value' => function($model) {
                return sprintf("%.2f", $model["able_funds"] - $model["no_withdraw"]);
            }
        ], [
            'label' => '经营类型',
            'value' => function($model) {
                if ($model["store_type"] == null) {
                    return "";
                }
                $store_type = [
                    "0" => "未知",
                    "1" => "个人自营店",
                    "2" => "个体转让店",
                    "3" => "咕啦自营店",
                    "4" => "贵人鸟加盟店"
                ];
                return $store_type[$model["store_type"]];
            }
                ], [
                    'label' => '门店类型',
                    'value' => function($model) {
                        if ($model["consignment_type"] == null) {
                            return "";
                        }
                        $consignmentType = [
                            "1" => "体彩店",
                            "2" => "福彩店",
                            "3" => "双彩店",
                        ];
                        return $consignmentType[$model["consignment_type"]];
                    }
                ], [
                    
                    'label' => '咕啦旗舰店',
                    'value' => function ($model) {
                        return $model['company_id'] == 1 ? '是' : '否';
                    }
                ],[
                    'label' => '收取服务费',
                    'value' => function ($model) {
                        return $model['is_service_fee'] == 1 ? '是' : '否';
                    }
                ], [
                    'label' => '开户时间',
                    'value' => 'create_time'
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
                            'label' => '门店状态',
                            'value' => function($model) {
                                if ($model["status"] == null) {
                                    return "";
                                }
                                $status = [
                                    "1" => "正常",
                                    "2" => "禁用"
                                ];
                                return $status[$model["status"]];
                            }
                                ], [
                                    'label' => '营业状态',
                                    'value' => function($model) {
                                        if ($model["business_status"] != "") {
                                            $status = [
                                                "0" => "未开业",
                                                "1" => "营业中",
                                                "2" => "暂停营业",
                                            ];
                                            return $status[$model["business_status"]];
                                        } else {
                                            return "";
                                        }
                                    }
                                        ], [
                                            'label' => '操作',
                                            'format' => 'raw',
                                            'value' => function( $model) {
                                                return '<div class="am-btn-group am-btn-group-xs opts">
                            <ul class="ul1"><li><span class="handle pointer" onclick="review(' . $model["store_id"] . ')">审核</span></li>
                            <li><span class="handle pointer" onclick="location.href = \'/agents/stores/readstore?store_id=' . $model["store_id"] . '\'">| 查看</span></li>
                            <li><span class="handle pointer" onclick="location.href = \'/agents/stores/editstore?store_id=' . $model["store_id"] . '\'">| 编辑 </span></li>
                            <li><span class="handle pointer">| 更多</span><ul class="ul2">'
                                                        . ($model["status"] == 1 ? '<li><span class="handle pointer" onclick="statusChange(' . $model["store_id"] . ',2)"> 禁用 </span></li>' : '<li><span class="handle pointer" onclick="statusChange(' . $model["store_id"] . ',1)"> 启用 </span></li>'). ($model["invite_status"] == 1 ? '<li><span class="handle pointer" onclick="inviteChange(' . $model["store_id"] . ',2)"> 邀请禁用 </span></li>' : '<li><span class="handle pointer" onclick="inviteChange(' . $model["store_id"] . ',1)"> 邀请启用 </span></li>') .
                                                        ($model["business_status"] == 1 ? '<li><span class="handle pointer " onclick="businessChange(' . $model["store_id"] . ','.$model["cert_status"].',2)"> 暂停接单 </span></li>' : '<li><span class="handle pointer " onclick="businessChange(' . $model["store_id"] . ','.$model["cert_status"]. ',1)"> 开始接单 </span></li>') .($model["is_service_fee"] == 1 ? '<li><span class="handle pointer " onclick="serviceChange(' . $model["store_id"] .',2)"> 不收取服务费 </span></li>' : '<li><span class="handle pointer " onclick="serviceChange(' . $model["store_id"] .',1)"> 收取服务费 </span></li>').'
                            <li><span class="handle pointer " onclick="deleteStore(' . $model["store_id"] . ',' . $model["cert_status"] . ')"> 删除 </span></li>
                            <li><span class="handle pointer " onclick="flagship(' . $model["store_id"] . ',' . $model['company_id'] . ')"> 旗舰店认证 </span></li>
                            <li><span class="handle pointer " onclick="editConsignee(' . $model["store_id"] . ',' . $model["cert_status"] . ')"> 更换运营者 </span></li><li><span class="handle pointer" onclick="changeMoney(\'' . $model["cust_no"] . '\',' . $model['no_withdraw'] . ')"> 资金转变 </span></li> '
                                                        . ($model['company_id'] == 1 && $model['status'] == 1 ? '<li><span class="handle pointer " onclick="setWeight(' . $model["store_code"] . ')"> 权重设置 </span></li>' : '') .
                                                        ($model["cert_status"]==3&&$model['status'] == 1 ? '<li><span class="handle pointer " onclick="addDispenser(' . $model["store_code"] . ')"> 新增出票机器 </span></li>' : '').
                                                        ' </ul></li></ul></div>';
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
    function review(store_id) {
        modDisplay({width: 500, height: 240, title: "审核", url: "/agents/stores/review-store?store_id=" + store_id});
    }
    function editConsignee(store_id, sta) {
        if (sta != 3) {
            msgAlert("该店铺尚未通过审核，无法更换运营者");
        } else {
            modDisplay({width: 500, height: 500, title: "更换运营者", url: "/agents/stores/edit-consignee?store_id=" + store_id});
        }
    }
    //更改运营状态
    function statusChange(id,sta) {
        msgConfirm("提示", "确定更改门店状态？", function () {
            $.ajax({
                url: "/agents/stores/status-change",
                async: false,
                type: "POST",
                dataType: "json",
                data: {store_id: id, status: sta},
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
        })
    }
    //更改接单状态
    function businessChange(id,passSta,sta) {
        msgConfirm("提示", "确定更改门店营业状态？", function () {
            if(passSta!=3){
                msgAlert("审核未通过门店不可更改营业状态");
                return false;
            }
            $.ajax({
                url: "/agents/stores/business-change",
                async: false,
                type: "POST",
                dataType: "json",
                data: {store_id: id, business_status: sta},
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
        })
    }
    //删除店铺
    function deleteStore(store_id, sta) {
        if (sta == 3 || sta == 4) {
            msgAlert("该门店不可删除")
        } else {
            msgConfirm("提示", "确定删除？", function () {
                $.ajax({
                    url: "/agents/stores/delete-store",
                    async: false,
                    type: "POST",
                    dataType: "json",
                    data: {store_id: store_id},
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
            })
        }
    }

    function flagship(store_id, companyId) {
        str = companyId == 1 ? '确定此店不再是公司自营门店吗？' : '确定此店为公司自营门店吗？';
        msgConfirm("提示", str, function () {
            $.ajax({
                url: "/agents/stores/flagship-store",
                async: false,
                type: "POST",
                dataType: "json",
                data: {store_id: store_id},
                success: function (json) {
                    if (json["code"] == 600) {
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
    function setWeight(storeCode) {
        modDisplay({width: 350, height: 200, title: "权重设置", url: "/agents/stores/set-weight?store_code=" + storeCode});
    }
    //新增门店出票机器
    function addDispenser(storeCode){
        modDisplay({width: 500, height: 350, title: "新增出票机器", url: "/agents/stores/add-dispenser?store_code=" + storeCode});
    }
    //不可提现金额转可提现金额
    function changeMoney(cust_no,value){
         msgPrompt("提示","确定不可提现金额转可提现金额?",value,function(){
            var stick = $("#num").val();
            if(isNaN(parseInt(stick))||stick<0){
                msgAlert("请输入合法的数字");
                return false;
            }
            if(stick>value){
               msgAlert("转移金额不可大于原不可提现金额");
               return false; 
            }
            $.ajax({
                url: "/agents/stores/money-change",
                type: "POST",
                dataType: "json",
                aysnc: false,
                data: {cust_no: cust_no,stick:stick},
                success: function (json) {
                    if (json["code"] == 600) {
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
    //门店邀请状态
    function inviteChange(id,sta) {
        msgConfirm("提示", "确定更改门店邀请状态？", function () {
            $.ajax({
                url: "/agents/stores/invite-change",
                async: false,
                type: "POST",
                dataType: "json",
                data: {store_id: id, status: sta},
                success: function (json) {
                    if (json["code"] == 600) {
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
    //是否收取服务费
    function serviceChange(id,sta) {
        msgConfirm("提示", "确定更改门店手续费状态？", function () {
            $.ajax({
                url: "/agents/stores/service-change",
                async: false,
                type: "POST",
                dataType: "json",
                data: {store_id: id, status: sta},
                success: function (json) {
                    if (json["code"] == 600) {
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

