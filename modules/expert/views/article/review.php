<?php

use yii\helpers\Html;

echo '<div><label for="doc-vld-name-2-1">审核说明</label>';
echo Html::textarea("reviewContent", $data["remark"], ["class" => "form-control", "id" => "reviewContent", "style" => "height:100px;"]);
echo '<span style="color:#8A8A91">审核说明不可超过100个字符</span></div>';
echo Html::button("通过", ["class" => "am-btn am-btn-primary", "id" => "pass", "style" => "margin:5px;"]);
echo Html::button("未通过", ["class" => "am-btn am-btn-primary", "id" => "noPass", "style" => "margin:5px;"]);
echo Html::button("返回", ["class" => "am-btn am-btn-primary", "style" => "margin:5px;", "onclick" => "closeMask();"]);
?>
<script>
    $(function () {
        var expertArticlesId =<?php echo $_GET["expert_articles_id"] ?>;
        $("#pass").click(function () {
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
                url: "/expert/article/pass",
                async: false,
                dataType: "json",
                data: {expert_articles_id: expertArticlesId, reviewContent: reviewContent},
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
                url: "/expert/article/no-pass",
                async: false,
                dataType: "json",
                data: {expert_articles_id: expertArticlesId, reviewContent: reviewContent},
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
