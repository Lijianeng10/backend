<div style="margin-top: 20px;margin-left: 10px;font-size: 14px;">
    <p>
        <lable class="form-span">参数编号：</lable>
        <input type="text" placeholder="参数编号"  id="code" class="form-input" />
    </p>
    <p>
        <lable class="form-span">参数名称：</lable>
        <input type="text" placeholder="参数名称"  id="name" class="form-input" />
    </p>
    <p>
        <lable class="form-span">参数值：</lable>
        <input type="text" placeholder="参数值"  id="value" class="form-input" />
    </p>
    <p>
        <lable class="form-span">所属类别：</lable>
        <input type="text" placeholder="参数值"  id="type" class="form-input" />
    </p>
    <p>
        <lable class="form-span">备注：</lable>
        <input type="text" placeholder="参数说明"  id="remark" class="form-input" />
    </p>
</div>

<div style="margin-top: 20px;">
    <button class="am-btn am-btn-primary" style="margin-left:104px;font-size: 14px;" id="addBtn">新增</button>
    <button  class="am-btn am-btn-primary" style="margin-left: 5px;font-size: 14px;" id="closeBtn">关闭</button>
</div>

<script>
    $("#addBtn").click(function(){
        var code =$("#code").val();
        var name =$("#name").val();
        var value =$("#value").val();
        var type =$("#type").val();
        var remark =$("#remark").val();
        if(code==""||value==""||name==""||type==""){
            msgAlert("表单填写有误，请重新填写")
            return false;
        }else{
            $.ajax({
                url: "/admin/config/add-param",
                data: {"code":code,"name":name,"type":type,"value":value,"remark":remark},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/admin/config/index';
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

