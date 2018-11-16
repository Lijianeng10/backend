<style>
    #addFrom p{
        margin: 10px 0px;
        padding: 0;
    }
</style>
<div style="margin-top: 10px;">
    <p>代理商基本信息</p>
</div>
<form style="margin-top: 10px;font-size: 14px;" id="addFrom">
    <p>
        <span  style="width:125px;display: inline-block;text-align: right;"><label style="color:red;font-size:24px;vertical-align: middle">* </label><span> 代理商名称:</span></span>
        <input type="text" id="agentsName" size="25">
    </p>
    <p>
        <span  style="width:125px;display: inline-block;text-align: right;"><label style="color:red;font-size:24px;vertical-align: middle">* </label><span> 代理商类型:</span></span>
        <select id="agentsType">
            <option value="">请选择</option>
            <option value="1">总部</option>
            <option value="2">地推</option>
            <option value="3">体彩店</option>
            <option value="4">福彩店</option>
            <option value="5">便利店</option>
            <option value="6">个人</option>
        </select>
    </p>
     <p>
        <span  style="width:125px;display: inline-block;text-align: right;">代理商简码:</span>
        <input type="text" id="agentsCode" size="25" placeholder="例如:gula">
    </p>
    <p>
        <span  style="width:125px;display: inline-block;text-align: right;">跳转URL:</span>
        <input type="text" id="toUrl" size="32" placeholder="例如:http://211.149.205.201:8070">
    </p>
    <p>
        <span  style="width:125px;display: inline-block;text-align: right;">备注:</span>
        <textarea style="width:30%;height: 200px;" id="remark"></textarea>
    </p>
    <p >
        <input type="button"  class="search am-btn am-btn-primary" value="保存" style="margin-left:125px;" id="add">
        <input type="reset" class="search am-btn am-btn-primary" value="返回" id="comeback">
    </p>
</form>
<script>
    $("#add").click(function () {
        var agentsName = $("#agentsName").val();
        var agentsType = $("#agentsType").val();
        var agentsCode = $("#agentsCode").val();
        var toUrl = $("#toUrl").val();
        var remark = $("#remark").val();
        if (agentsName == "") {
            msgAlert("请填写代理商名称")
        } else if (agentsType == "") {
            msgAlert("请选择代理商类型")
        } else {
            $.ajax({
                url: '/agents/subagents/addagents',
                async: false,
                type: 'POST',
                data: {agentsName: agentsName, agentsType: agentsType, remark: remark,agentsCode:agentsCode,toUrl:toUrl},
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data['code'] != 600) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.href = '/agents/subagents/index';
                        });
                    }
                }
            })
        }
    })
    $("#comeback").click(function(){
        window.history.go(-1);
    })
</script>

