<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>
<?php
echo '<h3>新增优惠券</h3>';
echo '<hr/>';
echo '<li style="margin-top:10px">';
echo Html::label("优惠类型  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("yh_type", "", $yh_type, ["class" => "form-control", "id" => "yh_type", "style" => "width:120px;display:inline;margin-left:5px;"]);
//echo Html::label("使用范围  ", "",["style"=>"margin-left:169px"]).Html::tag("span","*",["class"=>"requiredIcon"]);
//echo Html::dropDownList("use_range","", $use_range, ["class" => "form-control","id"=>"use_range" , "style" => "width:120px;display:inline;margin-left:5px;"]);
echo Html::label("使用范围  ", "", ["style" => "margin-left:185px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("use_range", "", $use_range, ["class" => "form-control", "id" => "use_range", "style" => "width:145px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:10px">';
//echo Html::label("批次ID  ", "",["style"=>"margin-left:15px"]).Html::tag("span","*",["class"=>"requiredIcon"]);
//echo Html::input("input", "",  "", ["class" => "form-control","id"=>"batch" ,"placeholder" => "优惠券批次:例如 GL000X", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo Html::label("优惠券名称  ", "", ["style" => "margin-left:0px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "", "", ["class" => "form-control", "id" => "coupons_name", "placeholder" => "优惠券名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo Html::label("优惠金额  ", "", ["style" => "margin-left:105px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("number", "reduce_money", "", ["class" => "form-control", "id" => "reduce_money", "placeholder" => "优惠金额", "style" => "width:200px;display:inline;margin-left:5px;", "min" => "0"]);

echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::label("适用类型  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("application_type", "1", $application_type, ["class" => "form-control", "id" => "application_type", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo Html::label("是否为礼品  ", "", ["style" => "margin-left:170px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("is_gift", "2", $is_gift, ["class" => "form-control", "id" => "is_gift", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::label("使用代理  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::dropDownList("use_agents", "", $proplayform, ["class" => "form-control", "id" => "use_agents", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::label("预发数量  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("number", "numbers", "0", ["class" => "form-control", "id" => "numbers", "placeholder" => "预发数量", "style" => "width:200px;display:inline;margin-left:5px;", "min" => "0"]);
echo Html::tag("span", "用户兑换类填写具体预发数量,系统发放类填写0", ["style" => "color:#ccc;font-size:14px;padding-left:10px"]);
echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::label("单日限用  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("number", "days_num", "0", ["class" => "form-control", "id" => "days_num", "placeholder" => "单日限用张数", "style" => "width:200px;display:inline;margin-left:5px;", "min" => "0"]);
echo Html::tag("span", "单个会员每日限用几张优惠券,填写0则不受限制", ["style" => "color:#ccc;font-size:14px;padding-left:10px"]);
echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::label("是否可叠加  ", "", ["style" => "margin-left:0px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::radioList('stack_use', '2', ['1' => '是', '2' => '否'], ["style" => "display:inline;margin-left:7px;"]);
echo Html::tag("span", "指在同优惠类型和同批次优惠券上是否可以叠加使用", ["style" => "color:#ccc;font-size:14px;padding-left:10px"]);
echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::label("最低消费是否限定  ", "", ["style" => "margin-left:0px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::radioList('is_less_money', '', ['0' => '否', '1' => '是'], ["style" => "display:inline;margin-left:7px;"]);
echo Html::tag("span", "指使用优惠券是否有最低消费限定 是：必须有最低消费 否： 默认消费大于0就可用", ["style" => "color:#ccc;font-size:14px;padding-left:10px"]);
echo '</li>';
echo '<li style="margin-top:10px;display:none" id="less_user">';
echo Html::label("最低消费  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("number", "less_money", "0", ["class" => "form-control", "id" => "less_money", "placeholder" => "最低消费", "style" => "width:200px;display:inline;margin-left:5px;", "min" => "0"]);
echo Html::tag("span", "单笔消费满多少元允许使用该优惠券", ["style" => "color:#ccc;font-size:14px;padding-left:10px"]);
echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::label("有效期区间是否限定  ", "", ["style" => "margin-left:0px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::radioList('is_sure_date', '', ['0' => '否', '1' => '是'], ["style" => "display:inline;margin-left:7px;"]);
echo Html::tag("span", "指是否指明有效使用日期时间；是：明确的使用日期区间 否：明确的使用天数，领取时即日生效", ["style" => "color:#ccc;font-size:14px;padding-left:10px"]);
echo '</li>';
echo '<li style="margin-top:10px;display:none" id="sure_periods">';
echo Html::label("有效期区间  ", "", ["style" => "margin-left:1px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("text", "startdate", "", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "enddate", "", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:10px;display:none" id="sure_date">';
echo Html::label("有效天数  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("number", "sure_time", "", ["class" => "form-control", "id" => "sure_time", "placeholder" => "有效天数", "style" => "width:200px;display:inline;margin-left:5px;", "min" => "1"]);
echo Html::tag("span", "使用有效期未定,使用有效天数确定", ["style" => "color:#ccc;font-size:14px;padding-left:10px"]);
echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::label("发送内容  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::tag("textarea", "", ["class" => "form-control", "id" => "content", "placeholder" => "尊敬的会员,您获得优惠券{优惠券},券号{券号}，有效期至{有效期}，使用条件:需最低消费{最低消费}元。详情咨询:{电话}", "style" => "width:570px;display:inline;margin-left:5px;height:110px"]);
echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::button("提交", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addBtn", "style" => "margin-left:250px;"]);
echo Html::button("返回", ["class" => "am-btn am-btn-primary inputLimit", "id" => "backBtn"]);
echo '</li>';
?>
<script>
    //返回
    $("#backBtn").click(function () {
        location.href = '/website/coupons/index';
    })
    //监测优惠券适用类型
    $("#application_type").change(function () {
        var type = $(this).val();
        if (type == 1) {
            $("#is_gift").val(2);
        }
    })
    
    //根据选择，是否限制最低消费
    $("input[name=is_less_money]").change(function () {
        var lessUser = $(this).val();
        if (lessUser == 0) {
            $("#less_user").hide();
        } else if (lessUser == 1) {
            $("#less_user").show();
        } 
    })

    //根据选择，显示有效期还是有效天数
    $("input[name=is_sure_date]").change(function () {
        var sureType = $(this).val();
        if (sureType == "") {
            $("#sure_periods").hide();
            $("#sure_date").hide();
        } else if (sureType == 1) {
            $("#sure_date").hide();
            $("#sure_periods").show();
        } else {
            $("#sure_periods").hide();
            $("#sure_date").show();
        }
    })

    //提交生成优惠券
    $("#addBtn").click(function () {
        var type = $("#yh_type").val();
        var use_range = $("#use_range").val();
        var application_type = $("#application_type").val();
        var is_gift = $("#is_gift").val();
        var coupons_name = $("#coupons_name").val();
        var numbers = $("#numbers").val();
        var days_num = $("#days_num").val();
        var less_money = parseInt($("#less_money").val());
        var start_date = $("#startdate").val();
        var end_date = $("#enddate").val();
        var reduce_money = parseInt($("#reduce_money").val());
        var stack_use = $("input[name=stack_use]:checked").val();
        var send_content = $("#content").val();
        var use_agents = $("#use_agents").val();
        var is_sure_date = $("input[name=is_sure_date]:checked").val();
        var sure_time = $("#sure_time").val();
        var is_limit_less = $("input[name=is_less_money]:checked").val();
//      var reduce_money = "";
//      var discount="";
//      if(type==2){
//            discount=$("#discount").val();
//            if(discount==""){
//                msgAlert("请填写折扣比例")
//            }
//        }else if(type==1){
//            reduce_money=$("#reduce_money").val();
//            if(reduce_money==""){
//                 msgAlert("请填写优惠金额")
//            }
//        }
        if(is_limit_less == undefined) {
            msgAlert("请选择是否限定最低消费");
            return false;
        }
        if (is_limit_less == 1 && less_money == "") {
            msgAlert("请填写最低消费金额");
            return false;
        }
        if(is_limit_less == 1 && (less_money < reduce_money)){
            msgAlert("最低消费不可低于优惠金额");
            return false;
        }
        if (application_type == 0) {
            msgAlert("请选择优惠券适用类型")
            return false;
        } else if (application_type == 1) {
            if (numbers != 0 || numbers == "") {
                msgAlert("系统发放类优惠券，预发数量请填写0")
                return false;
            }
            if (use_agents == "") {
                msgAlert("系统发放类优惠券，请选择使用代理商")
                return false;
            }
        } else {
            if (parseInt(numbers) <= 0) {
                msgAlert("请填写具体预发数量")
                return false;
            }
        }
        if(is_sure_date == undefined) {
            msgAlert("有效期限定必须选择");
            return false;
        }
        if (type == 0 || use_range == "") {
            msgAlert("请选择优惠类型或使用彩种")
        } else if (coupons_name == "" || days_num == "" || is_gift == "" || reduce_money == "" ) {
            msgAlert("请将带*的参数填写完整")
        } else if(is_sure_date == 1 && (start_date == "" || end_date == "")){
            msgAlert("请填写有效期区间")
        } else if(is_sure_date == 0 && sure_time == ""){
            msgAlert("请填写有效天数")
        }else {
            $.ajax({
                url: "addview",
                data: {type: type, use_range: use_range, coupons_name: coupons_name, numbers: numbers, reduce_money: reduce_money, days_num: days_num,
                    less_money: less_money, send_content: send_content, start_date: start_date, end_date: end_date, is_gift: is_gift, stack_use: stack_use,
                    application_type: application_type, use_agents: use_agents,is_sure_date:is_sure_date,sure_time:sure_time,is_limit_less:is_limit_less
                },
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.href = '/website/coupons/index';
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        }






    })
</script>


