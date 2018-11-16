<div style="margin-top: 20px;margin-left: 10px;font-size: 14px;">
    <lable>接口名称：</lable>
    <input type="hidden" value="<?php echo $data["api_list_id"];?>"  id="api_list_id" />
    <input type="text" value="<?php echo $data["api_name"];?>" placeholder="接口名称"  id="api_name" style="width:60%"/>
</div>
<div style="margin-top: 10px;margin-left: 41px;font-size: 14px;">
    <lable>URL：</lable>
    <input type="text" value="<?php echo $data["api_url"];?>" placeholder="URL"  id="api_url" style="width:60%"/>
</div>
<div style="margin-top: 10px;">
    <button class="am-btn am-btn-primary" style="margin-left:104px;font-size: 14px;" id="addBtn">确定</button>
    <button  class="am-btn am-btn-primary" style="margin-left: 5px;font-size: 14px;" id="closeBtn">关闭</button>
</div>

<script>
    $("#addBtn").click(function(){
        var api_list_id =$("#api_list_id").val();
        var api_name =$("#api_name").val();
        var api_url =$("#api_url").val();
        if(api_name==""){
            msgAlert("接口名称不能为空")
        }else if(api_url==""){
            msgAlert("接口URL不能为空")
        }else{
            $.ajax({
                url: "/agents/apilist/edit-api",
                data: {"api_list_id":api_list_id,"api_name":api_name,"api_url":api_url},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/agents/apilist/index';
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
