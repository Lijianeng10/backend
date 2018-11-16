<style>
    #userInfo p {
        margin: 0;
        padding: 0;
    }
</style>
<div style="margin-top: 20px;margin-left: 10px;font-size: 14px;">
    <lable>会员信息：</lable>
    <input type="hidden"  id="bussinessId" value="<?php echo $bussinessId; ;?>"/>
    <input type="text" placeholder="用户名，手机号，咕啦编号"  id="info" style="width:40%"/>
    <button class="am-btn am-btn-primary" style="margin-left:5px;font-size: 14px;" id="searchBtn">搜索</button>
</div>
<div id="userInfo" style="font-size: 14px;margin-left: 10px;">

</div>
<div style="margin-top: 10px;">
    <button class="am-btn am-btn-primary" style="margin-left:104px;font-size: 14px;" id="addBtn">确定</button>
    <button  class="am-btn am-btn-primary" style="margin-left: 5px;font-size: 14px;" id="closeBtn">关闭</button>
</div>

<script>
    //搜索会员信息
    $("#searchBtn").click(function () {
        var userInfo = $("#info").val();
        $("#userInfo").html("");
        if (userInfo == "") {
            msgAlert("会员信息不能为空")
        } else {
            $.ajax({
                url: "/agents/bussiness/get-user-info",
                data: {"userInfo": userInfo, },
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                      var str="";
                      str+="<p style='display:none'><span>会员ID：</span><span id='user_id'>"+json["result"]["user_id"]+"</span></p>";
                      str+="<p><span>会员编号：</span><span id='cust_no'>"+json["result"]["cust_no"]+"</span></p>";
                      str+="<p><span>会员昵称：</span><span>"+json["result"]["user_name"]+"</span></p>";
                      str+="<p><span>会员手机：</span><span>"+json["result"]["user_tel"]+"</span></p>";
                      $("#userInfo").html(str);
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            })
        }
    })
    //确定绑定
    $("#addBtn").click(function () {
        var bussinessId = $("#bussinessId").val();
        var user_id = $("#user_id").html();
        var cust_no = $("#cust_no").html();
        if (user_id == ""||user_id==undefined||cust_no==undefined) {
            msgAlert("请输入会员信息点击搜索会员")
        } else {
            $.ajax({
                url: "/agents/bussiness/bind-user",
                data: {"bussinessId": bussinessId,"user_id":user_id,"cust_no":cust_no },
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.href = '/agents/bussiness/index';
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

