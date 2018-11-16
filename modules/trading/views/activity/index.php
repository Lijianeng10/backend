<?php

use yii\helpers\Html;

echo Html::tag("h3", "发放活动奖金");
echo "<form id='theForm'>";
echo Html::label("手机号：");
echo Html::input("text", "userTel", "", ["placeholder" => "请填写手机号", "class" => "form-control", "style" => "display:inline-block;width:300px;"]);
echo Html::label("金额：");
echo Html::input("text", "money", "", ["placeholder" => "金额", "class" => "form-control", "style" => "display:inline-block;width:300px;"]);
echo Html::button("发放", ["id" => "activityBtn", "class" => "am-btn am-btn-primary"]);
echo "</form>";
?>
<script>
    $(function () {
        $("#activityBtn").click(function () {
            msgConfirm('提醒',"确定发放活动奖金?",function(){
                var data = $("#theForm").serializeArray();
                $.ajax({
                    url: "/trading/activity/add-money",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: data,
                    success: function (json) {
                        msgAlert(json["msg"]);
                        if (json["code"] == "600") {
                            $("#theForm input").val("");
                        }
                    }
                });
            })
           
        });
    });
</script>

