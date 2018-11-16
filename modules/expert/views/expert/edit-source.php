<?php

use yii\helpers\Html;
use app\modules\helpers\Constant;

//$expertTypeNames = Constant::EXPERT_TYPE_NAME;
//$expertTypeSource = Constant::EXPERT_TYPE_SOURCE;
echo '</br>';
echo '<div><label for="doc-vld-name-2-1" style="margin-right:5px;">专家类型 :</label>';
echo Html::dropDownList("expert_source", $data['expert_source'], $expertTypeSource, ["class" => "form-control", "id" => "source", "style" => "width:120px;display:inline;"]) . "</div>";
echo '</br>';
echo Html::button("确定", ["class" => "am-btn am-btn-primary", "id" => "editSource", "style" => "margin:5px;"]);
echo Html::button("返回", ["class" => "am-btn am-btn-primary", "style" => "margin:5px;", "onclick" => "closeMask();"]);
?>
<script>
    $(function () {
        var userId =<?php echo $_GET["user_id"] ?>;
        $("#editSource").click(function () {
            var expertSource = $("#source").val();
            $.ajax({
                url: "/expert/expert/edit-source",
                async: false,
                dataType: "json",
                data: {userId: userId, expertSource: expertSource},
                type: "POST",
                success: function (data) {
                    if (data["code"] != "600") {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                            closeMask();
                        });
                    }
                }
            });
        });
    })
</script>
