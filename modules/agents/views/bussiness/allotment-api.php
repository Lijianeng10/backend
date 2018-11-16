<style>
    p{
        margin: 0;
        padding: 0;
    }
    #ipList{
        width: 30%;
        height: 400px;
        margin: 10px;
        border: 1px solid #ccc;
        float:left;
        overflow-y: auto;
    }
    #apiList{
        width: 30%;
        height: 400px;
        margin: 10px;
        border: 1px solid #ccc;
        float:left;
        overflow-y: auto;
    }
    #ipContent,#apiContent{
        padding: 10px;
    }
    .ipChild,.apiChild{
        width: 18px;
        height: 18px;
        vertical-align: middle;
    }
    #btn{
        margin-top: 10px;
        clear: both;
    }
</style>
<input type="hidden" value="<?php echo $bussinessId; ?>" id="bussiness_id">
<div id="ipList">
    <div id="ipContent">
        <h3>IP列表</h3>
        <div>
            <p>
                <button id="clearIp" type="button" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-star"></span> 清空</button>
                <button id="ipSelected" type="button" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-star"></span> 全选/反选</button>
            </p>
            <?php
            foreach ($ipList as $key => $val) {
                if(in_array($val["bussiness_ip_white_id"], $ip)){
                     echo '<p><input type="checkbox"  flag=' . $val["bussiness_ip_white_id"] . ' class="ipChild" checked="checked">  ' . $val["ip"] . '</p>';
                }  else {
                    echo '<p><input type="checkbox"  flag=' . $val["bussiness_ip_white_id"] . ' class="ipChild">  ' . $val["ip"] . '</p>';
                }
            }
            ?>
        </div>
    </div>
</div>
<div id="apiList">
    <div id="apiContent">
        <h3>接口列表</h3>
        <div>
            <p>
                <button id="clearApi" type="button" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-star"></span> 清空</button>
                <button id="apiSelected" type="button" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-star"></span> 全选/反选</button>
            </p>
            <?php
            foreach ($apiList as $key => $val) {
                 if(in_array($val["api_list_id"], $api)){
                     echo '<p><input type="checkbox" flag=' . $val["api_list_id"] . ' class="apiChild" checked="checked">  ' . $val["api_name"] . '</p>';
                }  else {
                     echo '<p><input type="checkbox" flag=' . $val["api_list_id"] . ' class="apiChild">  ' . $val["api_name"] . '</p>';
                }
               
            }
            ?>
        </div>
    </div>
</div>
<div id="btn">
    <button class="am-btn am-btn-primary" style="margin-left:20px;" id="addBtn">新增</button>
    <button class="am-btn am-btn-primary" style="margin-left:5px;" id="backBtn">返回</button>
</div>
<script>
    //清空
    $("#clearIp").click(function () {
        $.each($(".ipChild"), function () {
            if ($(this).is(':checked')) {
                $(this).prop("checked", false);
            }
        });
    });
    $("#clearApi").click(function () {
        $.each($(".apiChild"), function () {
            if ($(this).is(':checked')) {
                $(this).prop("checked", false);
            }
        });
    });
    //权限反选
    $("#ipSelected").click(function () {
        $.each($(".ipChild"), function () {
            if ($(this).is(':checked')) {
                $(this).prop("checked", false);
            } else {
                $(this).prop("checked", true);
            }
        });
    });
    $("#apiSelected").click(function () {
        $.each($(".apiChild"), function () {
            if ($(this).is(':checked')) {
                $(this).prop("checked", false);
            } else {
                $(this).prop("checked", true);
            }
        });
    });
    //新增
    $("#addBtn").click(function () {
        var ipInput = $("#ipContent input[type=checkbox]");
        var apiInput = $("#apiContent input[type=checkbox]");
        var bussinessId = $("#bussiness_id").val();
        //获取选中的ip id
        var chooseIp = [];
        for (var i = 0; i < ipInput.length; i++) {
            if ($(ipInput[i]).prop("checked") == true) {
                chooseIp.push($(ipInput[i]).attr("flag"));
            }
        }
        //获取选中的api id
        var chooseApi = [];
        for (var i = 0; i < apiInput.length; i++) {
            if ($(apiInput[i]).prop("checked") == true) {
                chooseApi.push($(apiInput[i]).attr("flag"));
            }
        }
        if(chooseApi.length == 0){
            chooseApi=0;
        }
        if (chooseIp.length == 0) {
            msgAlert("请选择IP，若没有IP列表请先分配IP")
        } else {
            $.ajax({
                type: "POST",
                url: "/agents/bussiness/allotment-api",
                data: {ipAry: chooseIp, apiAry: chooseApi,"bussinessId":bussinessId},
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600){
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    }else{
                        msgAlert(json["msg"]);
                    }
                },
            })
        }

    })
    //返回
    $("#backBtn").click(function () {
        location.href = "/agents/bussiness/index"
    })
</script>

