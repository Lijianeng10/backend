<?php

use yii\helpers\Html;

echo Html::label("审核说明");
echo Html::textarea("review_remark", "", ["class" => "form-control", "id" => "review_remark", "style" => "width:80%;height:60px;"]);

echo Html::tag("span", "审核说明最长不能超过250个字符", ["class" => "buttomspan", "style" => "color:#bbbbbb;"]) . "<br />";


echo Html::tag("span", "审核通过 ", ["class" => "search am-btn am-btn-primary", "onclick" => "review(2);", "style" => "margin-left:5px;"]);
echo Html::tag("span", "审核不通过 ", ["class" => "search am-btn am-btn-primary", "onclick" => "review(3);", "style" => "margin-left:5px;"]);
echo Html::tag("span", "返回 ", ["class" => "search am-btn am-btn-primary", "onclick" => "closeMask();", "style" => "margin-left:5px;"]);
?>
<script>
    function review(cert_status) {
        var applyId =<?php echo $applyId; ?>;
        var review_remark = $("#review_remark").val();
        $.ajax({
            url: "/channel/recharge/review",
            async: false,
            dataType: "json",
            type: "POST",
            data: {applyId: applyId, cert_status: cert_status, review_remark: review_remark},
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
</script>