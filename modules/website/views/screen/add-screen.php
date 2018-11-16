<div style="margin-top: 5px;margin-left: 10px;font-size: 14px;">
    <lable>备注<span style="color:red;">*</span>：</lable>
    <textarea style="width:80%;height: 200px" id="contents"></textarea>
</div>
<div style="margin-top: 10px">
    <button class="am-btn am-btn-primary" style="margin-left:200px;" id="addBtn">保存</button>
    <button  class="am-btn am-btn-primary" style="margin-left: 5px;" id="closeBtn">关闭</button>
</div>
<script>
    $("#addBtn").click(function () {
        var contents = $("#contents").val();
       
        if (contents == "") {
            msgAlert("备注不能为空")
        } else {
            $.ajax({
                url: "/website/screen/add-screen",
                data: {remark: contents},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.href = '/website/screen/index';
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            })
        }
    })
    $("#closeBtn").click(function () {
        closeMask();
    })
</script>