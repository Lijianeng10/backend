<?php

use yii\helpers\Html;

$pactStatus = [
    "1" => "未签",
    "2" => "已签",
    "3" => "失效"
];

echo '<div style="margin:10px 0 0 0;"><label for="doc-vld-name-2-1" style="margin-right:5px;">协议状态 :</label>';
echo Html::dropDownList("pact_status", $data["pact_status"], $pactStatus, ["class" => "form-control", "id" => "pact_status", "style" => "width:120px;display:inline;"]);

echo Html::button("确定", ["class" => "am-btn am-btn-primary", "id" => "onOk", "style" => "margin:5px;display:inline-block;"]);
echo "</div>";
?>
<script>
    $(function () {
        var expertId =<?php echo $_GET["expert_id"] ?>;
        $("#onOk").click(function () {
            if (!confirm("确定修改协议状态？")) {
                return false;
            }
            var pactStatus = $("#pact_status").val();
            $.ajax({
                url: "/expert/expert/update-pact-status",
                async: false,
                dataType: "json",
                data: {expert_id: expertId, pactStatus: pactStatus},
                type: "POST",
                success: function (json) {
                    if (json["code"] == "600") {
                        location.reload();
                        closeMask();
                    } else {
                        alert(json["msg"]);
                    }
                }
            });
        });
    })
</script>
