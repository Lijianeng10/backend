<?php

use yii\helpers\Html;

echo '<form>';
echo "<ul>";
echo '<li class="form-li">';
echo Html::input("hidden", "id", $data["id"],["id"=>"id"]);
echo Html::label("用户名称  ", "", ["style" => "margin-left:45px;","class"=>"form-span"]);
echo Html::input("input", "user_name", $data['user_name'], ["class" => "form-input","id"=>"user_name", "placeholder" => "用户名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="form-li">';
echo Html::label("手机号  ", "", ["style" => "margin-left:45px;","class"=>"form-span"]);
echo Html::input("input", "user_tel", $data['user_tel'], ["class" => "form-input","id"=>"user_tel", "placeholder" => "手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="form-li">';
echo Html::label("场次信息  ", "", ["style" => "margin-left:45px;","class"=>"form-span"]);
echo Html::input("input", "field", $data['field'], ["class" => "form-input","id"=>"field", "placeholder" => "场次信息", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="form-li">';
echo Html::label("场次名称  ", "", ["style" => "margin-left:45px;","class"=>"form-span"]);
echo Html::input("input", "field_name", $data['field_name'], ["class" => "form-input","id"=>"field_name", "placeholder" => "场次名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="form-li">';
echo Html::label("座位等级  ", "", ["style" => "margin-left:45px;","class"=>"form-span"]);
echo Html::input("input", "level", $data['level'], ["class" => "form-input","id"=>"level", "placeholder" => "座位等级", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="form-li">';
echo Html::label("价格  ", "", ["style" => "margin-left:45px;","class"=>"form-span"]);
echo Html::input("input", "money", $data['money'], ["class" => "form-input","id"=>"money", "placeholder" => "价格", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="form-li">';
echo Html::label("处理状态", "", ["style" => "margin-left:43px;","class"=>"form-span"]);
echo Html::dropDownList("status", $data['status'], $applyStatus, ["class" => "form-input","id"=>"status", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="form-li">';
echo Html::label("备注信息", "", ["style" => "margin-left:43px;","class"=>"form-span"]);
echo Html::textarea("remark", $data['remark'],["class" => "form-input","id"=>"remark", "style" => "width:200px;height:80px;display:inline;margin-left:5px;"]);
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
        var id=$("#id").val();
        var user_name=$("#user_name").val();
        var user_tel=$("#user_tel").val();
        var field=$("#field").val();
        var field_name=$("#field_name").val();
        var remark=$("#remark").val();
        var status=$("#status").val();
        var level=$("#level").val();
        var money=$("#money").val();
        if (user_name==""||user_tel==""||field==""||field_name==""||status==""||level==""||money=="") {
            msgAlert("请将表单信息填写完整");
            return fasle;
        }
        $.ajax({
            url: "/website/worldcup/edit-world",
            async: false,
            dataType: "json",
            type: "POST",
            data: {id: id, user_name: user_name,user_tel:user_tel,field:field,field_name:field_name,remark:remark,status:status,level:level,money:money},
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