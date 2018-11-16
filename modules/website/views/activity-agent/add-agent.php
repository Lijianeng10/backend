<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>
<div style="margin-top: 20px;margin-left: 10px;font-size: 14px;">
    <p>
        <lable class="form-span">代理商名称：</lable>
        <input class="form-input" type="text" id="agent_name">
    </p>
    <p>
        <lable class="form-span">代理商Code：</lable>
        <input class="form-input" type="text" id="agent_code">
    </p>
</div>
<div style="margin-top: 20px;">
    <button class="am-btn am-btn-primary" style="margin-left:104px;font-size: 14px;" id="addBtn">新增</button>
    <button  class="am-btn am-btn-primary" style="margin-left: 5px;font-size: 14px;" id="closeBtn">关闭</button>
</div>

<script>
    $("#addBtn").click(function(){
        var agent_name =$("#agent_name").val();
        var agent_code =$("#agent_code").val();
        if(agent_name==""||agent_code==""){
            msgAlert("请将表单填写完整")
            return false;
        }else{
            $.ajax({
                url: "/website/activity-agent/add-agent",
                data: {"agent_name":agent_name,"agent_code":agent_code},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/website/activity-agent/index';
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


