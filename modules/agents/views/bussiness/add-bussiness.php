<div style="margin-top: 20px;margin-left: 10px;font-size: 14px;">
    <lable>合作商名称：</lable>
    <input type="text" placeholder="合作商名称"  id="contents" style="width:40%"/>
</div>
<div style="margin-top: 10px;">
    <button class="am-btn am-btn-primary" style="margin-left:104px;font-size: 14px;" id="addBtn">新增</button>
    <button  class="am-btn am-btn-primary" style="margin-left: 5px;font-size: 14px;" id="closeBtn">关闭</button>
</div>

<script>
    $("#addBtn").click(function(){
        var contents =$("#contents").val();
        if(contents==""){
            msgAlert("合作商名称不能为空")
        }else{
            $.ajax({
                url: "/agents/bussiness/add-bussiness",
                data: {"contents":contents,},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/agents/bussiness/index';
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

