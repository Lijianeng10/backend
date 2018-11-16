<?php

use yii\helpers\Html;
use yii\grid\GridView;

echo '<ul>';
echo '<li style="margin-top:10px" id="txt">';
echo Html::input("hidden", "",$cust_no, ["class" => "form-control","id" => "cust","style" => "width:120px;display:inline;margin-left:5px;"]);
echo Html::label("输入会员信息  ", "", ["style" => "margin-left:15px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("text", "user_info", "", ["class" => "form-control", "id" => "user_info", "style" => "width:150px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin-top:20px">';
echo Html::button("提交", ["class" => "am-btn am-btn-primary inputLimit", "id" => "submitBtn", "style" => "margin-left:125px;"]);
echo Html::button("返回", ["class" => "am-btn am-btn-primary inputLimit", "id" => "backBtn"]);
echo '</li>';
echo '</ul>';
?>


<script>
    $("#submitBtn").click(function () {
        var cust_no = $("#cust").val();
        var user_info = $("#user_info").val();
        if(user_info==""){
            msgAlert("请输入会员信息");
        }else{
            $.ajax({
                url: "/member/list/add-spread",
                async: false,
                dataType: "json",
                type: "POST",
                data: {cust_no: cust_no, user_info: user_info},
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.href = '/member/list/read-user';
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            })
        }
    })
    //返回
    $("#backBtn").click(function(){
        closeMask()
    })
</script>


