<?php

use yii\helpers\Html;

echo Html::textarea("review_remark", "", ["class" => "form-control", "id" => "review_remark", "style" => "width:80%;height:100px;margin-top:20px;"]);

echo Html::tag("span", "通过 ", ["class" => "search am-btn am-btn-primary", "onclick" => "reviewLog(2);", "style" => "margin-left:5px;margin-top:20px;"]);
echo Html::tag("span", "不通过 ", ["class" => "search am-btn am-btn-primary", "onclick" => "reviewLog(3);", "style" => "margin-left:5px;margin-top:20px;"]);
echo Html::tag("span", "关闭", ["class" => "search am-btn am-btn-primary", "onclick" => "closeMask();", "style" => "margin-left:5px;margin-top:20px;"]);
?>
<script>
    function reviewLog(pass_status) {
        var jpush_notice_id =<?php echo $data->jpush_notice_id; ?>;
        var review_remark = $("#review_remark").val();
        if(pass_status==3){
            if(review_remark==""){
                msgAlert("未通过审核必须填写备注信息");
                return false;
            }
        }
        $.ajax({
            url: "/website/applog/audit-app-log",
            async: false,
            dataType: "json",
            type: "POST",
            data: {jpush_notice_id: jpush_notice_id, pass_status: pass_status, review_remark: review_remark},
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

