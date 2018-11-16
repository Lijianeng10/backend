<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\modules\agents\models\Agents;
use app\modules\lottery\helpers\Constant;

$dealStatus = [
    "" => "全部",
    "0" => "未处理",
    "1" => "已对奖",
    "2" => "派奖失败",
    "3" => "派奖成功",
    "4" => "退款失败",
    "5" => "退款成功"
];
$autoType = [
    "" => "全部",
    "1" => "手动",
    "2" => "自动",
];
echo '<ul class="nav nav-tabs" style="margin-bottom:10px;">
  <li role="presentation"' . ((!isset($_GET['lottery_category_id']) || $_GET['lottery_category_id'] == '4,5,6') ? ' class="active"' : '') . '><a href="/lottery/betting?lottery_category_id=4,5,6">数字彩</a></li>
  <li role="presentation"' . ((isset($_GET['lottery_category_id']) && $_GET['lottery_category_id'] == '11') ? ' class="active"' : '') . '><a href="/lottery/betting?lottery_category_id=11">足球竞彩</a></li>
  <li role="presentation"' . ((isset($_GET['lottery_category_id']) && $_GET['lottery_category_id'] == '12') ? ' class="active"' : '') . '><a href="/lottery/betting?lottery_category_id=12">篮球竞彩</a></li>
  <li role="presentation"' . ((isset($_GET['lottery_category_id']) && $_GET['lottery_category_id'] == '13') ? ' class="active"' : '') . '><a href="/lottery/betting?lottery_category_id=13">北京单场</a></li>
</ul>';
?>
<div style="font-size:14px;">
    <?php
    echo "<ul class='third_team_ul'>";
    echo '<li>';
    echo Html::label("订单信息", "order_code", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "lottery_order_code", isset($get["lottery_order_code"]) ? $get["lottery_order_code"] : "", ["id" => "lottery_order_code", "class" => "form-control", "placeholder" => "订单号,出票编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("会员信息", "user_info", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["id" => "user_info", "class" => "form-control", "placeholder" => "会员编号、会员手机号、会员名称", "style" => "width:250px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("门店信息", "store_info", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "store_info", isset($get["store_info"]) ? $get["store_info"] : "", ["id" => "store_info", "class" => "form-control", "placeholder" => "门店编号、门店注册手机号、门店名称", "style" => "width:250px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("彩种  ", "", ["style" => "margin-left:44px;"]);
    echo Html::dropDownList("lottery_code", isset($get["lottery_code"]) ? $get["lottery_code"] : "0", $lotteryNames, ["id" => "lottery_code", "class" => "form-control", "style" => "width:160px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("投注时间  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("text", "startdate", isset($get["startdate"]) ? $get["startdate"] :date("Y-m-d",strtotime("-3 day")), ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("text", "enddate", isset($get["enddate"]) ? $get["enddate"] :date("Y-m-d"), ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("出票时间  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("text", "out_start", isset($get["out_start"]) ? $get["out_start"] :"", ["id" => "out_start", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("text", "out_end", isset($get["out_end"]) ? $get["out_end"] :"", ["id" => "out_end", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("截止时间  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("text", "end_time_start", isset($get["end_time_start"]) ? $get["end_time_start"] : "", ["id" => "end_time_start", "class" => "form-control",  "placeholder" => "开始日期", "style" => "width:130px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("text", "end_time_end", isset($get["end_time_end"]) ? $get["end_time_end"] : "", ["id" => "end_time_end", "class" => "form-control", "placeholder" => "结束日期", "style" => "width:130px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo "<lable style='margin-left:25px;margin-right:6px;padding-left: 8px;font-size:15px;font-weight: normal;'>未支付</lable>";
    echo Html::checkbox("choose", isset($get["choose"]) && $get["choose"] == "1" ? "true" : "", ["style" => "height:20px;width:20px;vertical-align:top;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("订单状态  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $orderStatus, ["id" => "status", "class" => "form-control", "style" => "width:90px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("处理状态  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("deal_status", isset($get["deal_status"]) ? $get["deal_status"] : "", $dealStatus, ["id" => "deal_status", "class" => "form-control", "style" => "width:90px;display:inline;margin-left:5px;"]);
    echo '</li>';
     echo '<li>';
    echo Html::label("出票类型  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("auto_type", isset($get["auto_type"]) ? $get["auto_type"] : "", $autoType, ["id" => "auto_type", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("投注来源  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("source", isset($get["source"]) ? $get["source"] : "", $orderSource, ["id" => "source", "class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("订单来源  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("plat", isset($get["plat"]) ? $get["plat"] : "", $platform, ["id" => "plat", "class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
    echo Html::dropDownList("from", isset($get["from"]) ? $get["from"] : "", $orderFrom, ["id" => "from", "class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;", "onclick" => "search();"]);
//    echo Html::button("清空投注时间", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "clearTime();"]);
    echo Html::button("重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
    echo '</li>';
    echo "</ul>";
    ?>
</div>
<?php
if (!isset($_GET['lottery_category_id']) || $_GET['lottery_category_id'] == '4,5,6') {
    echo GridView::widget([
        "dataProvider" => $data,
        "columns" => [
            [ 'class' => 'yii\grid\SerialColumn'],
            [
                'label' => '订单号',
                'value' => 'lottery_order_code'
            ],
            [
                'label' => '投注时间',
                'value' => 'create_time'
            ], [
                'label' => '截止时间',
                'value' => 'end_time'
            ],
//            [
//                'label' => '投注内容',
//                'format' => 'raw',
//                'value' => function($model) {
//            $vals = explode("^", trim($model["bet_val"], "^"));
//            return "<span data-am-popover=\"{content: '" . implode("<br \>", $vals) . "', trigger: 'hover focus'}\" style='color:#55acee;'>查看</span>";
//        }
//            ], 
                    [
                'label' => '彩种',
                'value' => 'lottery_name'
            ], [
                'label' => '倍数',
                'value' => 'bet_double'
            ], [
                'label' => '期号',
                'value' => 'periods'
            ], [
                'label' => '投注金额',
                'value' => 'bet_money'
            ], [
                'label' => '订单状态',
                'value' => function($model) {
            $orderStatus = Constant::ORDER_STATUS;
            return isset($orderStatus[$model["status"]]) ? $orderStatus[$model["status"]] : "未知来源";
        }
            ], 
//                    [
//                'label' => '出票手续费',
//                'value' => function($model) {
//            return $model["pay_pre_money"] ? $model["pay_pre_money"] : 0;
//        }
//            ],
            [
                'label' => '中奖金额',
                'value' => function($model) {
            return $model["win_amount"] ? $model["win_amount"] : 0;
        }
            ],
//            [
//                'label' => '票机中奖金额',
//                'value' => function($model) {
//                return $model["zmf_award_money"] ? $model["zmf_award_money"] : 0;
//            }
//                ],
            [
                'label' => '实派金额',
                'value' => function($model) {
            return $model["award_amount"] ? $model["award_amount"] : "0.00";
        }
            ],[
                'label' => '处理状态',
                'value' => function($model) {
            $dealStatus = [
                "0" => "未处理",
                "1" => "已对奖",
                "2" => "派奖失败",
                "3" => "派奖成功",
                "4" => "退款失败",
                "5" => "退款成功"
            ];
            return isset($dealStatus[$model["deal_status"]]) ? $dealStatus[$model["deal_status"]] : "未知状态";
        }
            ], [
                'label' => '会员信息',
                'format' => 'raw',
                'value' => function($model){
                    return $model["cust_no"]."</br>".$model["user_tel"];
                }
            ],
            [
                'label' => '门店信息',
                'format' => 'raw',
                'value' => function($model) {
                    return $model["store_name"]."</br>".$model["consignee_name"] . "</br>" . $model["phone_num"] ;
                }
            ],
            [
                'label' => '出票编号',
                'value' => 'taking_code'
            ], [
                'label' => '投注来源',
                'value' => function($model) {
                    $source = [
                        "1" => "自购",
                        "2" => "追号",
                        "3" => "赠送",
                        "4" => "合买",
                        "5" => "分享",
                        "6" => "计划",
                        "7" => "流量单"
                    ];
            return isset($source[$model["source"]]) ? $source[$model["source"]] : "未知来源";
//                    return ($model["source"] == 1 ? "自购" : ($model["source"] == 2 ? "追号" : ($model["source"] == 3 ? "赠送" : "未知来源")));
        }
            ],[
                'label' => '出票',
                'value' => function($model){
                    $autoType = [
                        "1" => "手动",
                        "2" => "自动",
                    ];
                    return isset($autoType[$model["auto_type"]]) ? $autoType[$model["auto_type"]] : "未知";
                }
            ],[
                'label' => '下单平台',
                'value' => function ($model) {
                    $platform =  Constant::ORDER_PLAT_FORM;
                    return $platform[$model['order_platform']];
                }
            ], [
                'label' => '订单来源',
                'value' => function($model){
                    if($model['order_platform']==3){
                        //渠道
                        $bussiness = (new \yii\db\Query())->select(["b.name","u.user_id"])
                            ->from("bussiness as b")
                            ->leftJoin("user as u","u.cust_no = b.cust_no")
                            ->where(["b.status"=>1])
                            ->all();
                        foreach ($bussiness as $k=>$v){
                            $bussiness[$v['user_id']]=$v['name'];
                        }
                        return isset($bussiness[$model["user_id"]]) ? $bussiness[$model["user_id"]] : "";
                    }else{
                        $from =Agents::getAgentsArray();
                        return isset($from[$model["agent_id"]]) ? $from[$model["agent_id"]] : "";
                    }
                },
                'contentOptions' => ['style' => 'max-width:90px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']
            ],[
                'label' => '轮循',
                'value' => function ($model) {
                    $polling = ['1' => '是', '2' => '否'];
                    return $polling[$model['polling_type']];
                }
            ],  [
                'label' => '操作',
                'format' => 'raw',
                'value' => function($model) {
            return '<div class="am-btn-group am-btn-group-xs">
                            ' . (($model["suborder_status"] != 1 && $model["status"] == 2) ? ('<span class="handle pointer" onclick="subOrder(' . $model['lottery_order_id'] . ')">生成子单 |</span>') : "") . '
                            <span class="handle pointer" onclick="readDeatail(\'/lottery/betting/readdetail12?lottery_order_id=' . $model['lottery_order_id'] . '\')">查看 |</span>
                            ' . ($model['status'] == 3 ?  ('<span class="handle pointer" onclick="awardSubmit('.$model['lottery_order_id'].');">开奖</span>')  : "" ) . '
                        </div>';
        }
            ]
        ]
    ]);
} else if (isset($_GET['lottery_category_id']) && $_GET['lottery_category_id'] == '11') {

    echo GridView::widget([
        "dataProvider" => $data,
        "columns" => [
            [ 'class' => 'yii\grid\SerialColumn'],
            [
                'label' => '订单号',
                'value' => 'lottery_order_code'
            ],
//             [
//                'label' => '期数',
//                'value' => 'periods'
//            ],
            [
                'label' => '投注时间',
                'value' => 'create_time'
            ], [
                'label' => '截止时间',
                'format' => 'raw',
                'value' => function($model) {
                    return date('m-d H:i', strtotime($model['end_time']));
                }
//                'value' => 'end_time'
            ],
            [
                'label' => '最晚开赛时间',
                'format' => 'raw',
                'value' => function($model) {
                    return date('m-d H:i', $model['periods']);
                }
            ],
                    [
                'label' => '过关方式',
                'format' => 'raw',
                'value' => function($model) {
                    $html = "<span title='".$model["play_name"]."'>".$model["play_name"]."</span>";
                    return $html;
                },
                 'contentOptions' => ['style' => 'max-width:80px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']
            ], [
                'label' => '彩种玩法',
                'value' => 'lottery_name'
            ], [
                'label' => '倍数',
                'value' => 'bet_double'
            ], [
                'label' => '投注金额',
                'value' => 'bet_money'
            ], [
                'label' => '订单状态',
                'value' => function($model) {
            $orderStatus = Constant::ORDER_STATUS;
            return isset($orderStatus[$model["status"]]) ? $orderStatus[$model["status"]] : "未知来源";
        }
            ],
//                    [
//                'label' => '出票手续费',
//                'value' => function($model) {
//            return $model["pay_pre_money"] ? $model["pay_pre_money"] : 0;
//        }
//            ],
                    [
                'label' => '中奖金额',
                'value' => function($model) {
            return $model["win_amount"] ? $model["win_amount"] : 0;
        }
            ],
//            [
//                'label' => '票机中奖金额',
//                'value' => function($model) {
//                return $model["zmf_award_money"] ? $model["zmf_award_money"] : 0;
//            }
//                ],
            [
                'label' => '实派金额',
                'value' => function($model) {
            return $model["award_amount"] ? $model["award_amount"] : "0.00";
        }
            ],[
                'label' => '处理状态',
                'value' => function($model) {
            $dealStatus = [
                "0" => "未处理",
                "1" => "已对奖",
                "2" => "派奖失败",
                "3" => "派奖成功",
                "4" => "退款失败",
                "5" => "退款成功"
            ];
            return isset($dealStatus[$model["deal_status"]]) ? $dealStatus[$model["deal_status"]] : "未知状态";
        }
            ], [
                'label' => '会员信息',
                'format' => 'raw',
                'value' => function($model){
                    return $model["cust_no"]."</br>".$model["user_tel"];
                }
            ],
            [
                'label' => '门店信息',
                'format' => 'raw',
                'value' => function($model) {
                    return $model["store_name"]."</br>".$model["consignee_name"] . "</br>" . $model["phone_num"] ;
                }
            ],
            [
                'label' => '出票编号',
                'value' => 'taking_code'
            ],[
                'label' => '投注来源',
                'value' => function($model) {
                    $source = [
                        "1" => "自购",
                        "2" => "追号",
                        "3" => "赠送",
                        "4" => "合买",
                        "5" => "分享",
                        "6" => "计划",
                        "7" => "流量单"
                    ];
            return isset($source[$model["source"]]) ? $source[$model["source"]] : "未知来源";
//                    return ($model["source"] == 1 ? "自购" : ($model["source"] == 2 ? "追号" : ($model["source"] == 3 ? "赠送" : "未知来源")));
        }
            ],[
                'label' => '出票',
                'value' => function($model){
                    $autoType = [
                        "1" => "手动",
                        "2" => "自动",
                    ];
                    return isset($autoType[$model["auto_type"]]) ? $autoType[$model["auto_type"]] : "未知";
                }
            ],[
                'label' => '下单平台',
                'value' => function ($model) {
                    $platform =  Constant::ORDER_PLAT_FORM;
                    return $platform[$model['order_platform']];
                }
            ], [
                'label' => '订单来源',
                'value' => function($model){
                    if($model['order_platform']==3){
                        //渠道
                        $bussiness = (new \yii\db\Query())->select(["b.name","u.user_id"])
                            ->from("bussiness as b")
                            ->leftJoin("user as u","u.cust_no = b.cust_no")
                            ->where(["b.status"=>1])
                            ->all();
                        foreach ($bussiness as $k=>$v){
                            $bussiness[$v['user_id']]=$v['name'];
                        }
                        return isset($bussiness[$model["user_id"]]) ? $bussiness[$model["user_id"]] : "";
                    }else{
                        $from =Agents::getAgentsArray();
                        return isset($from[$model["agent_id"]]) ? $from[$model["agent_id"]] : "";
                    }
                },
                'contentOptions' => ['style' => 'max-width:90px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']
            ],[
                'label' => '轮循',
                'value' => function ($model) {
                    $polling = ['1' => '是', '2' => '否'];
                    return $polling[$model['polling_type']];
                }
            ],   [
                'label' => '操作',
                'format' => 'raw',
                'value' => function($model) {
            return '<div class="am-btn-group am-btn-group-xs">
                            ' . (($model["suborder_status"] != 1 && $model["status"] == 2) ? ('<span class="handle pointer" onclick="subOrder(' . $model['lottery_order_id'] . ')">生成子单 |</span>') : "") . '
                            <span class="handle pointer" onclick="readDeatail(\'/lottery/betting/readdetail3?lottery_order_id=' . $model['lottery_order_id'] . '\')">查看 |</span>
                                ' . ($model['status'] == 3 ?  ('<span class="handle pointer" onclick="awardSubmit('.$model['lottery_order_id'].');">开奖 |</span>')  : "" ) .
                    '<span class="handle pointer" onclick="takingDetail('.$model['lottery_order_id'].')"> 出票详情 </span>
                        </div>';
        }
            ]
        ]
    ]);
} elseif(isset($_GET['lottery_category_id']) && $_GET['lottery_category_id'] == '12'){
    echo GridView::widget([
        "dataProvider" => $data,
        "columns" => [
            [ 'class' => 'yii\grid\SerialColumn'],
            [
                'label' => '订单号',
                'value' => 'lottery_order_code'
            ],
            [
                'label' => '投注时间',
                'value' => 'create_time'
            ], [
                'label' => '截止时间',
                'value' => 'end_time'
            ],
//            [
//                'label' => '投注内容',
//                'format' => 'raw',
//                'value' => function($model) {
//            $vals = explode("^", trim($model["bet_val"], "^"));
//            return "<span data-am-popover=\"{content: '" . implode("<br \>", $vals) . "', trigger: 'hover focus'}\" style='color:#55acee;'>查看</span>";
//        }
//            ], 
                    [
                'label' => '过关方式',
                'format' => 'raw',
                'value' => function($model) {
                    $html = "<span title='".$model["play_name"]."'>".$model["play_name"]."</span>";
                    return $html;
                },
                'contentOptions' => ['style' => 'max-width:80px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']
            ], [
                'label' => '彩种玩法',
                'value' => 'lottery_name'
            ], [
                'label' => '倍数',
                'value' => 'bet_double'
            ], [
                'label' => '投注金额',
                'value' => 'bet_money'
            ], [
                'label' => '订单状态',
                'value' => function($model) {
            $orderStatus = Constant::ORDER_STATUS;
            return isset($orderStatus[$model["status"]]) ? $orderStatus[$model["status"]] : "未知来源";
        }
            ], 
//                    [
//                'label' => '出票手续费',
//                'value' => function($model) {
//            return $model["pay_pre_money"] ? $model["pay_pre_money"] : 0;
//        }
//            ],
                    [
                'label' => '中奖金额',
                'value' => function($model) {
            return $model["win_amount"] ? $model["win_amount"] : 0;
        }
            ],
//            [
//                'label' => '票机中奖金额',
//                'value' => function($model) {
//                return $model["zmf_award_money"] ? $model["zmf_award_money"] : 0;
//            }
//                ],
            [
                'label' => '实派金额',
                'value' => function($model) {
            return $model["award_amount"] ? $model["award_amount"] : "0.00";
        }
            ],[
                'label' => '处理状态',
                'value' => function($model) {
            $dealStatus = [
                "0" => "未处理",
                "1" => "已对奖",
                "2" => "派奖失败",
                "3" => "派奖成功",
                "4" => "退款失败",
                "5" => "退款成功"
            ];
            return isset($dealStatus[$model["deal_status"]]) ? $dealStatus[$model["deal_status"]] : "未知状态";
        }
            ], [
                'label' => '会员信息',
                'format' => 'raw',
                'value' => function($model){
                    return $model["cust_no"]."</br>".$model["user_tel"];
                }
            ],
            [
                'label' => '门店信息',
                'format' => 'raw',
                'value' => function($model) {
                    return $model["store_name"]."</br>".$model["consignee_name"] . "</br>" . $model["phone_num"] ;
                }
            ],
            [
                'label' => '出票编号',
                'value' => 'taking_code'
            ],[
                'label' => '投注来源',
                'value' => function($model) {
                    $source = [
                        "1" => "自购",
                        "2" => "追号",
                        "3" => "赠送",
                        "4" => "合买",
                        "5" => "分享",
                        "6" => "计划",
                        "7" => "流量单"
                    ];
            return isset($source[$model["source"]]) ? $source[$model["source"]] : "未知来源";
//                    return ($model["source"] == 1 ? "自购" : ($model["source"] == 2 ? "追号" : ($model["source"] == 3 ? "赠送" : "未知来源")));
        }
            ],[
                'label' => '出票',
                'value' => function($model){
                    $autoType = [
                        "1" => "手动",
                        "2" => "自动",
                    ];
                    return isset($autoType[$model["auto_type"]]) ? $autoType[$model["auto_type"]] : "未知";
                }
            ],[
                'label' => '下单平台',
                'value' => function ($model) {
                    $platform =  Constant::ORDER_PLAT_FORM;
                    return $platform[$model['order_platform']];
                }
            ], [
                'label' => '订单来源',
                'value' => function($model){
                    if($model['order_platform']==3){
                        //渠道
                        $bussiness = (new \yii\db\Query())->select(["b.name","u.user_id"])
                            ->from("bussiness as b")
                            ->leftJoin("user as u","u.cust_no = b.cust_no")
                            ->where(["b.status"=>1])
                            ->all();
                        foreach ($bussiness as $k=>$v){
                            $bussiness[$v['user_id']]=$v['name'];
                        }
                        return isset($bussiness[$model["user_id"]]) ? $bussiness[$model["user_id"]] : "";
                    }else{
                        $from =Agents::getAgentsArray();
                        return isset($from[$model["agent_id"]]) ? $from[$model["agent_id"]] : "";
                    }
                },
                'contentOptions' => ['style' => 'max-width:90px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']
            ],[
                'label' => '轮循',
                'value' => function ($model) {
                    $polling = ['1' => '是', '2' => '否'];
                    return $polling[$model['polling_type']];
                }
            ],  [
                'label' => '操作',
                'format' => 'raw',
                'value' => function($model) {
            return '<div class="am-btn-group am-btn-group-xs bigRead">
                            ' . (($model["suborder_status"] != 1 && $model["status"] == 2) ? ('<span class="handle pointer" onclick="subOrder(' . $model['lottery_order_id'] . ')">生成子单 |</span>') : "") . '
                            <span class="handle pointer readLan" onclick="readDeatail(\'/lottery/betting/readdetail4?lotteryOrderCode=' . $model['lottery_order_code'] . '\')">查看 |</span>
                                ' . ($model['status'] == 3 ?  ('<span class="handle pointer" onclick="awardSubmit('.$model['lottery_order_id'].');">开奖 | </span>')  : "" ) . '
                                    <span class="handle pointer" onclick="takingDetail('.$model['lottery_order_id'].')"> 出票详情 </span> </div>';
        }
            ]
        ]
    ]);
}elseif(isset($_GET['lottery_category_id']) && $_GET['lottery_category_id'] == '13'){
    echo GridView::widget([
        "dataProvider" => $data,
        "columns" => [
            [ 'class' => 'yii\grid\SerialColumn'],
            [
                'label' => '订单号',
                'value' => 'lottery_order_code'
            ],
            [
                'label' => '投注时间',
                'value' => 'create_time'
            ], [
                'label' => '截止时间',
                'value' => 'end_time'
            ],
//            [
//                'label' => '投注内容',
//                'format' => 'raw',
//                'value' => function($model) {
//            $vals = explode("^", trim($model["bet_val"], "^"));
//            return "<span data-am-popover=\"{content: '" . implode("<br \>", $vals) . "', trigger: 'hover focus'}\" style='color:#55acee;'>查看</span>";
//        }
//            ], 
                    [
                'label' => '过关方式',
                'format' => 'raw',
                'value' => function($model) {
                    $html = "<span title='".$model["play_name"]."'>".$model["play_name"]."</span>";
                    return $html;
                },
                'contentOptions' => ['style' => 'max-width:80px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']
            ], [
                'label' => '彩种玩法',
                'value' => 'lottery_name'
            ], [
                'label' => '倍数',
                'value' => 'bet_double'
            ], [
                'label' => '投注金额',
                'value' => 'bet_money'
            ], [
                'label' => '订单状态',
                'value' => function($model) {
            $orderStatus =Constant::ORDER_STATUS;
            return isset($orderStatus[$model["status"]]) ? $orderStatus[$model["status"]] : "未知状态";
        }
            ], 
//                    [
//                'label' => '出票手续费',
//                'value' => function($model) {
//            return $model["pay_pre_money"] ? $model["pay_pre_money"] : 0;
//        }
//            ],
                    [
                'label' => '中奖金额',
                'value' => function($model) {
            return $model["win_amount"] ? $model["win_amount"] : 0;
        }
            ],
//            [
//                'label' => '票机中奖金额',
//                'value' => function($model) {
//                return $model["zmf_award_money"] ? $model["zmf_award_money"] : 0;
//            }
//                ],
            [
                'label' => '实派金额',
                'value' => function($model) {
            return $model["award_amount"] ? $model["award_amount"] : "0.00";
        }
            ],[
                'label' => '处理状态',
                'value' => function($model) {
            $dealStatus = [
                "0" => "未处理",
                "1" => "已对奖",
                "2" => "派奖失败",
                "3" => "派奖成功",
                "4" => "退款失败",
                "5" => "退款成功"
            ];
            return isset($dealStatus[$model["deal_status"]]) ? $dealStatus[$model["deal_status"]] : "未知状态";
        }
            ], [
                'label' => '会员信息',
                'format' => 'raw',
                'value' => function($model){
                    return $model["cust_no"]."</br>".$model["user_tel"];
                }
            ],
            [
                'label' => '门店信息',
                'format' => 'raw',
                'value' => function($model) {
                    return $model["store_name"]."</br>".$model["consignee_name"] . "</br>" . $model["phone_num"] ;
                }
            ],
            [
                'label' => '出票编号',
                'value' => 'taking_code'
            ],[
                'label' => '投注来源',
                'value' => function($model) {
                    $source = [
                        "1" => "自购",
                        "2" => "追号",
                        "3" => "赠送",
                        "4" => "合买",
                        "5" => "分享",
                        "6" => "计划",
                        "7" => "流量单"
                    ];
            return isset($source[$model["source"]]) ? $source[$model["source"]] : "未知来源";
//                    return ($model["source"] == 1 ? "自购" : ($model["source"] == 2 ? "追号" : ($model["source"] == 3 ? "赠送" : "未知来源")));
        }
            ],[
                'label' => '出票',
                'value' => function($model){
                    $autoType = [
                        "1" => "手动",
                        "2" => "自动",
                    ];
                    return isset($autoType[$model["auto_type"]]) ? $autoType[$model["auto_type"]] : "未知";
                }
            ],[
                'label' => '下单平台',
                'value' => function ($model) {
                    $platform =  Constant::ORDER_PLAT_FORM;
                    return $platform[$model['order_platform']];
                }
            ],[
                'label' => '订单来源',
                'value' => function($model){
                    if($model['order_platform']==3){
                        //渠道
                        $bussiness = (new \yii\db\Query())->select(["b.name","u.user_id"])
                            ->from("bussiness as b")
                            ->leftJoin("user as u","u.cust_no = b.cust_no")
                            ->where(["b.status"=>1])
                            ->all();
                        foreach ($bussiness as $k=>$v){
                            $bussiness[$v['user_id']]=$v['name'];
                        }
                        return isset($bussiness[$model["user_id"]]) ? $bussiness[$model["user_id"]] : "";
                    }else{
                        $from =Agents::getAgentsArray();
                        return isset($from[$model["agent_id"]]) ? $from[$model["agent_id"]] : "";
                    }
                },
                'contentOptions' => ['style' => 'max-width:90px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']
            ], [
                'label' => '轮循',
                'value' => function ($model) {
                    $polling = ['1' => '是', '2' => '否'];
                    return $polling[$model['polling_type']];
                }
            ], [
                'label' => '操作',
                'format' => 'raw',
                'value' => function($model) {
//                 ' . ($model['status'] == 3 ?  ('<span class="handle pointer" onclick="awardSubmit('.$model['lottery_order_id'].');">开奖</span>')  : "" ) . '
            return '<div class="am-btn-group am-btn-group-xs bigRead">
                            ' . (($model["suborder_status"] != 1 && $model["status"] == 2) ? ('<span class="handle pointer" onclick="subOrder(' . $model['lottery_order_id'] . ')">生成子单 |</span>') : "") . '
                            <span class="handle pointer readLan" onclick="readDeatail(\'/lottery/betting/read-bd-detail?lotteryOrderCode=' . $model['lottery_order_code'] . '\')">查看 |</span>
                               
                    </div>';
        }
            ]
        ]
    ]);
}
?>
<div class="am-modal am-modal-no-btn" tabindex="-1" id="your-modal">
    <div class="am-modal-dialog" style="width: 700px;">
        <div class="am-modal-hd">
            <span class="modalTitle"></span>
            <a href="javascript: void(0)" class="am-close am-close-spin" data-am-modal-close>&times;</a>
        </div>
        <div class="am-modal-bd">
            <p class="modalContent">
                Modal 内容。
            </p>
        </div>
    </div>
</div>
<!--<button
  type="button"
  class="am-btn am-btn-primary"
  data-am-modal="{target: '#your-modal', closeViaDimmer: 0, width: 400, height: 225}">
</button>-->
<script type="text/javascript">
    //日期插件
    laydate.render({
        elem: '#end_time_start',//指定元素
        type: 'datetime'
    });
    laydate.render({
        elem: '#end_time_end',//指定元素
        type: 'datetime'
    });
    //重置
    function goReset() {
        location.href = '/lottery/betting/index<?php echo isset($_GET['lottery_category_id']) ? ("?lottery_category_id=" . $_GET['lottery_category_id']) : "" ?>';
    }
    function search() {
        var lottery_order_code = $("#lottery_order_code").val();
        var lottery_additional_code = $("#lottery_additional_code").val();
        var lottery_code = $("#lottery_code").val();
        var user_info = $("#user_info").val();
        var store_info = $("#store_info").val();
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var status = $("#status").val();
        var end_time_start = $("#end_time_start").val();
        var end_time_end = $("#end_time_end").val();
        var deal_status = $("#deal_status").val();
        var auto_type = $("#auto_type").val();
        var from = $("#from").val();
        var source = $("#source").val();
        var chooseSta = "1";
        var out_start = $("#out_start").val();
        var out_end = $("#out_end").val();
        var plat = $("#plat").val();
        var param = '<?php echo isset($_GET['lottery_category_id']) ? ("?lottery_category_id=" . $_GET['lottery_category_id']) : "?1=1" ?>';
        if (lottery_order_code != "") {
            param += "&lottery_order_code=" + lottery_order_code;
        }
        if (lottery_additional_code != "") {
            param += "&lottery_additional_code=" + lottery_additional_code;
        }
        if (lottery_code > 0) {
            param += "&lottery_code=" + lottery_code;
        }
        if (user_info != "") {
            param += "&user_info=" + user_info;
        }
        if (store_info != "") {
            param += "&store_info=" + store_info;
        }
        if (startdate != "") {
            param += "&startdate=" + startdate;
        }
        if (enddate != "") {
            param += "&enddate=" + enddate;
        }

        if (end_time_start != "") {
            param += "&end_time_start=" + end_time_start;
        }
        if (end_time_end != "") {
            param += "&end_time_end=" + end_time_end;
        }

        if (status != 0 && status != "") {
            param += "&status=" + status;
        }
        //判断未支付是否是选中状态
        if ($("input[type='checkbox']").is(':checked')) {
            param += "&choose=" + chooseSta;
        }
        if (deal_status != "") {
            param += "&deal_status=" + deal_status;
        }
        if (auto_type != "") {
            param += "&auto_type=" + auto_type;
        }
        if (from != "") {
            param += "&from=" + from;
        }
        if (source != "") {
            param += "&source=" + source;
        }
        if (out_start != "") {
            param += "&out_start=" + out_start;
        }
        if (out_end != "") {
            param += "&out_end=" + out_end;
        }
        if (plat != "") {
            param += "&plat=" + plat;
        }
        location.href = '/lottery/betting/index' + param;
    }
    function readDeatail(url) {
        modDisplay({title: '订单详情', url: url, height: 600, width: 800});
    }
    function subOrder(orderId) {//order_id
        if (confirm("确定生成子单?")) {
            $.ajax({
                url: "<?php echo \Yii::$app->params['userDomain']; ?>/api/cron/cron/sub-order",
                data: {order_id: orderId},
                type: "GET",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        alert(json["msg"]);
                        location.reload();
                    } else {
                        alert(json["msg"]);
                    }
                }
            });
        }
    }

    function awardSubmit(orderId) {
        msgConfirm ('提醒','确定对此单相关投注内容进行对奖？',function() {
            $.ajax({
                url:"/lottery/betting/do-award",
                async: false,
                type: 'POST',
                data: {order_id:orderId},
                dataType: 'json',
                success: function (data) {
//                    console.log(data);
                    if (600 != data['code']) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            })
        })
    }
    
    function takingDetail(orderId) {
        modDisplay({title: '出票详情', url: '/lottery/betting/get-taking-detail?orderId=' + orderId, height: 600, width: 800});
    }
    //清空投注时间默认值
    function clearTime(){
        $("#startdate").val("");
        $("#enddate").val("");
       
    }
    //订单来源
    $("#plat").change(function () {
        var plat =$(this).val();
        $("#from").empty();
        $.ajax({
            url: '/lottery/betting/get-order-from',
            async: false,
            type: 'POST',
            data: {plat: plat},
            dataType: 'json',
            success: function (json) {
                if (json['code'] != 600) {
                    msgAlert(json['msg']);
                } else {
                    var res =json["result"];
                    var html="";
                    if(res==""){
                        $("#from_id").append("<option value=''>请选择</option>");
                    }else{
                        for(var i= 0;i<res.length;i++){
                            html +="<option value='"+res[i]["id"]+"'>"+res[i]["name"]+"</option>"
                        }
                        $("#from").append(html)

                    }
                }
            }
        });
        // }else{
        //     $("#from_id").append("<option value=''>请选择</option>");
        // }

    })
</script>