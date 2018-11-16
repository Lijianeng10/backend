<?php

use yii\helpers\Html;

$type = [
    "0"=>"否",
    "1"=>"是",
];

echo '<form>';
echo "<ul>";
echo '<li class="form-li">';
echo Html::input("hidden", "user_id", $data["user_id"],["id"=>"user_id"]);
echo Html::label("设置返点  ", "", ["style" => "margin-left:45px;","class"=>"form-span"]);
echo Html::input("input", "rebate", $data['rebate'], ["class" => "form-input","id"=>"rebate", "placeholder" => "推广员返点", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="form-li">';
echo Html::label("是否参与分润", "", ["style" => "margin-left:43px;","class"=>"form-span"]);
echo Html::dropDownList("is_profit", $data['is_profit'], $type, ["class" => "form-input","id"=>"is_profit", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="width:90%;padding-left:100px;" class="form-li">';
echo Html::tag("span", "确定 ", ["class" => "search am-btn am-btn-primary", "id" => "sureBtn", "style" => "margin-left:45px;margin-top:20px;"]);
echo Html::tag("span", "关闭", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:30px;margin-top:20px;","id" => "closeBtn"]);
echo '</li>';
echo '</ul>';
echo '</form>';
?>
<script>

$("#sureBtn").click(function(){
    var user_id=$("#user_id").val();
   var rebate=$("#rebate").val();
   var is_profit=$("#is_profit").val();
    if (isNaN(rebate) || rebate == "") {
        msgAlert("请输入合法的数字");
        return fasle;
    }
    if (0 > rebate || rebate > 100) {
        msgAlert("请输入0-100之间的数");
        return fasle;
    }
    $.ajax({
        url: "/member/list/edit-spread",
        async: false,
        dataType: "json",
        type: "POST",
        data: {user_id: user_id, rebate: rebate,is_profit:is_profit},
        success: function (json) {
            if (json["code"] == 600) {
                msgAlert(json["msg"], function () {
                    location.reload();
                });
            } else {
                msgAlert(json["msg"]);
            }
        }
    })
})

    //关闭
    $("#closeBtn").click(function () {
        closeMask();
    })
</script>

