
<div style="margin-top: 20px;margin-left: 10px;font-size: 14px;">
    <lable>推送标题 ：</lable>
    <input type="text" style="width:80%" id="title">
</div>
<div style="margin-top: 5px;margin-left: 10px;font-size: 14px;">
    <lable>推送时间 ：</lable>
    <input type="text" style="width:40%" id="pushTime">
    <label style="display: inline-block; color: #ccc;padding-left:70px">设置推送时间为定时推送，推送时间不能小于当前时间</label>
</div>
<div style="margin-top: 5px;margin-left: 10px;font-size: 14px;">
    <lable>跳转链接 ：</lable>
    <input type="text" style="width:80%" id="url">
</div>
<div style="margin-top: 5px;margin-left: 10px;font-size: 14px;">
    <lable>推送内容<span style="color:red;">*</span>：</lable>
    <textarea style="width:80%;height: 200px" id="contents"></textarea>
</div>
<div style="margin-top: 10px">
    <button class="am-btn am-btn-primary" style="margin-left:200px;" id="addBtn">保存</button>
    <button  class="am-btn am-btn-primary" style="margin-left: 5px;" id="closeBtn">关闭</button>
</div>

<script>
    //日期插件
    laydate.render({
        elem: '#pushTime',//指定元素
        type: 'datetime'
      });
    $("#addBtn").click(function(){
        var title =$("#title").val();
        var url =$("#url").val();
        var contents =$("#contents").val();
        var pushtime = $("#pushTime").val();
        if(contents==""){
            msgAlert("推送内容不能为空")
        }else{
            $.ajax({
                url: "/website/applog/add-app-log",
                data: {title:title,msg:contents,url:url,pushtime:pushtime},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/website/applog/index';
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
