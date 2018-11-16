<style>
    .txt{
        width: 50px;
        text-align: right;
    }
</style>
<div>
    <form id="doc-vld-msg">
        <div  style="margin-top: 10px;">
            <div>
                <label class="txt">标题：</label>
                <label>
                    <input type="text"   class ="form-control" style="width:300px;" id="title">
                </label>
            </div>
            <div>
                <label class="txt">内容：</label>
                <label>
                    <textarea  id="content" style="width:300px;height: 150px;"></textarea>
                </label>
            </div>
            <button type="button" class="am-btn am-btn-primary" id="addSubmit" style="margin-left: 60px;">提交</button>
            <button type="button" class="am-btn am-btn-primary" id="closeBtn" style="margin-left: 5px;">关闭</button>
        </div>
<script>
    $("#closeBtn").click(function(){
        closeMask();
    })
    $("#addSubmit").click(function(){
        var title = $("#title").val();
        var content = $("#content").val();
        if(title==""||content==""){
           msgAlert("请输入标题和内容") 
        }else{
            $.ajax({
                url: '/promote/title/add-title',
                async: false,
                type: 'POST',
                data: {title:title,content:content},
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            });
        }
    })
</script>
