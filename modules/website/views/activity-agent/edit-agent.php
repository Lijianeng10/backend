<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>
<style>
     .edit_bananer{
        width: 95%;
        margin: 0 auto;
         font-size: 14px;
    }
    .infoSpan{
        width: 100px;
        text-align: right;
    }
    #addSubmit{
        margin-left: 70px;
    }
</style>
<div>
    <form class="edit_bananer">
        <input type="hidden" value="<?php echo $data["activity_agent_id"]; ?>" id="activity_agent_id">
        <div class="am-form-group" style="margin-top: 20px;margin-bottom: 10px;">
            <span style="display: inline-block;" class="infoSpan">代理商名称</span>
            <input id="agent_name"  type="text" class="form-input" placeholder="代理商名称" style="width:34%" value="<?php echo $data["agent_name"];?>">
        </div>
        <div class="am-form-group" style="margin-top: 20px;margin-bottom: 10px;">
            <span style="display: inline-block;" class="infoSpan">代理商Code</span>
            <input id="agent_code"  type="text" class="form-input" placeholder="代理商Code" style="width:34%" value="<?php echo $data["agent_code"];?>">
        </div>
        <div style="margin-top: 20px;">
            <button class="am-btn am-btn-primary" id="addSubmit" >提交</button>
            <button class="am-btn am-btn-primary" id="backSubmit" >关闭</button>
        </div>

    </form>
</div>
<script>
    $("#addSubmit").click(function(){
        var activity_agent_id =$("#activity_agent_id").val();
        var agent_name =$("#agent_name").val();
        var agent_code =$("#agent_code").val();
        if(agent_name==""||agent_code==""){
            msgAlert("请将表单填写完整")
            return false;
        }else{
            $.ajax({
                url: "/website/activity-agent/edit-agent",
                data: {"activity_agent_id":activity_agent_id,"agent_name":agent_name,"agent_code":agent_code},
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

    $("#backSubmit").click(function () {
        closeMask()
    })
</script>
