<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>
<div style="margin-top: 20px;margin-left: 10px;font-size: 14px;">
    <p>
        <lable class="form-span">活动类型名称：</lable>
        <input class="form-input" type="text" id="type_name">
    </p>
</div>
<div style="margin-top: 20px;">
    <button class="am-btn am-btn-primary" style="margin-left:104px;font-size: 14px;" id="addBtn">新增</button>
    <button  class="am-btn am-btn-primary" style="margin-left: 5px;font-size: 14px;" id="closeBtn">关闭</button>
</div>

<script>
    $("#addBtn").click(function(){
        var type_name =$("#type_name").val();
        if(type_name==""){
            msgAlert("请将活动类型名称填写完整")
            return false;
        }else{
            $.ajax({
                url: "/website/activity-type/add-type",
                data: {"type_name":type_name,},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/website/activity-type/index';
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            })
        }
    })
    $("#closeBtn").click(function(){
        closeMask();
    })
</script>


