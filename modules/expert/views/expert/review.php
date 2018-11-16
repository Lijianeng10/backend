<?php

use yii\helpers\Html;
use app\modules\helpers\Constant;

$expertTypeNames = Constant::EXPERT_TYPE_NAME;
//$expertTypeSource = Constant::EXPERT_TYPE_SOURCE;
echo '<div><label for="doc-vld-name-2-1" style="margin-right:5px;">专家类型 :</label>';
echo Html::dropDownList("identity", "", Constant::EXPERT_TYPE, ["class" => "form-control", "id" => "identity", "style" => "width:120px;display:inline;"]) . "</div>";
echo '<div><label for="doc-vld-name-2-1" style="margin-right:5px;">专家身份 :</label>';
echo Html::dropDownList("expert_type", "", $expertTypeNames, ["class" => "form-control", "id" => "expert_type", "style" => "width:120px;display:inline;"]) . "</div>";
echo '<div><label for="doc-vld-name-2-1" style="margin-right:5px;margin-top:15px;">专家来源 :</label>';
echo Html::dropDownList("expert_source", isset($get["expert_source"]) ? $get["expert_source"] : "", $expertTypeSource, ["class" => "form-control", "id" => "expert_source", "style" => "width:120px;display:inline;"]) . "</div>";
echo '<div><label for="doc-vld-name-2-1">审核说明  :</label>';
echo Html::textarea("reviewContent", "", ["class" => "form-control", "id" => "reviewContent", "style" => "height:100px;"]);
echo '<span style="color:#8A8A91">审核说明不可超过100个字符</span></div>';
echo Html::button("通过", ["class" => "am-btn am-btn-primary", "id" => "pass", "style" => "margin:5px;"]);
echo Html::button("未通过", ["class" => "am-btn am-btn-primary", "id" => "noPass", "style" => "margin:5px;"]);
echo Html::button("返回", ["class" => "am-btn am-btn-primary", "style" => "margin:5px;", "onclick" => "closeMask();"]);
?>
<script>
    $(function () {
        var expertId =<?php echo $_GET["expert_id"] ?>;
        $("#pass").click(function () {
            var expertType = $("#expert_type").val();
            var expert_source = $("#expert_source").val();
            var identity = $("#identity").val();
            $.ajax({
                url: "/expert/expert/pass",
                async: false,
                dataType: "json",
                data: {expert_id: expertId, expert_type: expertType,expert_source:expert_source,identity:identity},
                type: "POST",
                success: function (json) {
                    if (json["code"] == "600") {
                        location.reload();
                        closeMask();
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
        $("#noPass").click(function () {
            var reviewContent = $("#reviewContent").val();
            if (reviewContent == "") {
                alert("审核说明不可为空！");
                return false;
            }
            if (reviewContent.length > 100) {
                alert("审核说明不可超过100个字符！");
                return false;
            }
            $.ajax({
                url: "/expert/expert/no-pass",
                async: false,
                dataType: "json",
                data: {expert_id: expertId, reviewContent: reviewContent},
                type: "POST",
                success: function (json) {
                    if (json["code"] == "600") {
                        location.reload();
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        });
    })
</script>
