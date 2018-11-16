<?php

use yii\helpers\Html;
use yii\grid\GridView;

echo '<h3>发放优惠券</h3>';
echo '<hr/>';
echo '<li style="margin-top:10px">';
echo Html::label("优惠券批次号  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "batck", $batch, ["class" => "form-control", "id" => "batch", "style" => "width:150px;display:inline;margin-left:5px;", "disabled" => true]);
echo Html::label("发放对象  ", "", ["style" => "margin-left:185px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("send_user", "", $levelAry, ["class" => "form-control", "id" => "send_user", "style" => "width:145px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:10px;display:none" id="txt">';
echo Html::label("输入会员编号  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("text", "user_no", "", ["class" => "form-control", "id" => "user_no", "style" => "width:150px;display:inline;margin-left:5px;"]);
echo Html::button("添加", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addBtn", "style" => "margin-left:25px;"]);
echo Html::tag("span", "请输入会员编号,一次一个，最多500个", ["style" => "color:#ccc;font-size:14px;padding-left:10px"]);
echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::label("已选用户数量  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "count", 0, ["class" => "form-control", "id" => "count", "style" => "width:150px;display:inline;margin-left:5px;", "disabled" => true]);
echo '</li>';
echo '<li style="margin-top:10px">';
echo Html::label("已选用户  ", "", ["style" => "margin-left:40px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::textarea("user", "", ["class" => "form-control", "id" => "user", "style" => "width:200px;height:200px;display:inline;margin-left:5px;", "disabled" => true]);
echo '</li>';
echo '<li style="margin-top:20px">';
echo Html::button("提交", ["class" => "am-btn am-btn-primary inputLimit", "id" => "submitBtn", "style" => "margin-left:120px;"]);
echo Html::button("重置", ["class" => "am-btn am-btn-primary inputLimit", "id" => "resetBtn"]);
echo Html::button("返回", ["class" => "am-btn am-btn-primary inputLimit", "id" => "backBtn"]);
echo '</li>';
?>


<script>
    //发放对象框变化
    $("#send_user").change(function () {
        var type = $(this).val();
        if (type == 100) {
            $("#user").html("");
            $("#count").val(0);
            $("#txt").css("display", "block")
        } else {
            $("#txt").css("display", "none")
            if (type != ""){
                $.ajax({
                    url: "/website/coupons/get-user-info",
                    data: {type: type, },
                    type: "POST",
                    dataType: "json",
                    async: false,
                    success: function (json) {
                        if (json["code"] == 600) {
                            $("#user").html("");
                            $("#count").val("");
                            $("#count").val(json["result"].length);
                            $.each(json["result"],function(k,v){
                                $("#user").append(v["cust_no"]);
                                $("#user").append("\n");
                            })
                        } else {
                            msgAlert(json["msg"]);
                        }
                    }
                });
            }

        }
    })
    //添加会员编号
    $("#addBtn").click(function () {
        var custNo = $("#user_no").val();
        var count=$("#count").val();
        if(custNo==""){
            msgAlert("会员编号不能为空")
        }else{
            $.ajax({
                url: "/website/coupons/get-custno",
                data: {custNo: custNo},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        $("#user").append(json["result"]["cust_no"]);
                        $("#user").append("\n");
                        count++;
                        $("#count").val(count);
                        $("#user_no").val("")
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        }
    })
    //重置
    $("#resetBtn").click(function(){
        $("#user_no").val("")
        $("#user").html("")
        $("#count").val("0")
    })
    //提交
    $("#submitBtn").click(function () {
        var type = $("#send_user").val();
        var batch = $("#batch").val();
        if(type==""){
           msgAlert("请选择发送对象类型")
           return false;
        }
        var userContent = $("#user").html();
        if(userContent==""){
            msgAlert("请选择需要发送的用户")
            return false;
        }
        var userAry=$.trim(userContent).split("\n");
        $.ajax({
            url: "/website/coupons/send-coupons",
            data: {batch: batch,userAry:userAry },
            type: "POST",
            dataType: "json",
            async: false,
            success: function (json) {
                if (json["code"] == 600) {
                   msgAlert(json["msg"],function(){
                       location.href = '/website/coupons/view-detail?batch='+batch;
                   });
                } else {
                    msgAlert(json["msg"]);
                }
            }
        });

    })
    //返回
    $("#backBtn").click(function(){
        location.href = '/website/coupons/index';
    })
</script>


