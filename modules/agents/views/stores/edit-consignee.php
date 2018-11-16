<style>
    p{
        margin: 3px;
        padding: 0;
    }
</style>
<div>
    <input type="hidden" value="<?php echo isset($data["store_id"]) ? $data["store_id"] : ""; ?>" id="store_id" >
    <h6 style="margin-top: 10px;padding: 0;">现运营者</h6>
    <p>编号：<?php echo "<span id='oldCustNo'>".$data["cust_no"]."</span>"; ?></p>
    <p>姓名：<?php echo $data["consignee_name"]; ?></p>
    <p>身份证：<?php echo $data["consignee_card"]; ?></p>
    <p>手机号：<?php echo $data["phone_num"]; ?></p>
</div>
<div>
    <h6 style="margin-top: 10px;padding: 0;">新运营者</h6>
    <p>
        <input type="text" placeholder="请输入新运营者手机号、编号" id="newConsignee" style="width:50%;">
        <input class="search am-btn am-btn-primary" type="button" id="searchBtn" value="查询" style="margin-left: 10px">
    </p>
    <p>编号：<span id="cust_no"></span></p>
    <p>姓名：<span id="consignee_name"></span></p>
    <p>身份证：<span id="consignee_card"></span></p>
    <p>手机号：<span id="consignee_tel"></span></p>
    <p>
        <input class="search am-btn am-btn-primary" type="button" id="editBtn" value="确定" style="margin-top: 10px">
        <input class="search am-btn am-btn-primary" type="button" id="closeBtn" value="取消" style="margin-top: 10px;margin-left: 10px">
    </p>
</div>
<script>
    $("#searchBtn").click(function () {
        var userInfo = $("#newConsignee").val();
        if (userInfo == "") {
            msgAlert("请输入新运营者信息")
        } else {
            $.ajax({
                url: "/agents/stores/get-user-info",
                async: false,
                dataType: "json",
                type: "POST",
                data: {userInfo: userInfo},
                success: function (data) {
                    if (data["code"] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        $("#cust_no").html(data["result"]["cust_no"]);
                        $("#consignee_name").html(data["result"]["realName"]);
                        $("#consignee_card").html(data["result"]["card"]);
                        $("#consignee_tel").html(data["result"]["user_tel"]);
                    }
                }
            })
        }
    })
    $("#editBtn").click(function () {
        var store_id = $("#store_id").val();
        var oldCustNo = $("#oldCustNo").html();
        var cust_no = $("#cust_no").html();
        var consignee_name = $("#consignee_name").html();
        var consignee_card = $("#consignee_card").html();
        var phone_num = $("#consignee_tel").html();
        if (cust_no == "") {
            msgAlert("新运营者信息不全,请先查询相关信息")
        }else if(oldCustNo==cust_no){
             msgAlert("运营者相同，无需更改")
        } else {
            $.ajax({
                url: "/agents/stores/edit-consignee",
                async: false,
                dataType: "json",
                type: "POST",
                data: {store_id: store_id, cust_no: cust_no, consignee_name: consignee_name, consignee_card: consignee_card, phone_num: phone_num},
                success: function (data) {
                    if (data["code"] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert(data["msg"], function () {
                            location.reload();
                        });
                    }
                }
            })
        }
    })
    $("#closeBtn").click(function(){
        closeMask();
    })
</script>
