<?php

use yii\helpers\Html;

echo Html::label("合作商名称  ", "", ["style" => "margin-left:12px;margin-top:20px"]) . ":  " . Html::tag("span", $data['name'], ["class" => "requiredIcon"]) . "<br/>";
echo Html::label("合作商Ip  ", "", ["style" => "margin-left:28px;margin-top:20px"]) . ":  " . Html::input("input", "", "", ["class" => "form-control", "id" => "ipAddress", "placeholder" => "合作商Ip地址", "style" => "width:200px;display:inline;margin-left:5px;"]) . "<br/>";
echo Html::tag("span", "增加 ", ["class" => "search am-btn am-btn-primary", "onclick" => "add();", "style" => "margin-top:20px;margin-left:100px;"]);
echo Html::tag("span", "返回 ", ["class" => "search am-btn am-btn-primary", "onclick" => "closeMask();", "style" => "margin-top:20px;margin-left:5px;"]);
?>
<script>
    function add() {
        var bussiness_id =<?php echo $data->bussiness_id; ?>;
        var ipAddress = $("#ipAddress").val();
        if (ipAddress == "") {
             msgAlert("请输入Ip地址！");
        } else {
            $.ajax({
                url: "/agents/bussiness/add-bussiness-ip",
                async: false,
                dataType: "json",
                type: "POST",
                data: {bussiness_id: bussiness_id, ipAddress: ipAddress},
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"], function () {
                            closeMask();
                        });
                    }
                }
            })
        }

    }
</script>
