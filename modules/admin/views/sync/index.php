<style>
    .form-big{
        width: 80%;
        text-align: center;
        margin-top: 40px;
    }
    .txt{
        display: inline-block;
        width: 200px;
        text-align: right;
    }
</style>


<div class="form-big">
    <p>同步订单</p>
    <span class="txt">请输入订单编号oderCode:</span>
    <span>
        <input type="text" class="form-input" id="order">
        <input type="submit" value="执行" class="am-btn am-btn-primary" id="orderBtn">
    </span>
</div>

<div class="form-big">
    <p>同步更新订单赔率</p>
    <span class="txt">请输入订单编号oderCode:</span>
    <span>
        <input type="text" class="form-input" id="odd">
        <input type="submit" value="执行" class="am-btn am-btn-primary" id="oddBtn">
    </span>
</div>

<div class="form-big">
    <p>同步交易明细</p>
    <span class="txt">请输入明细ID:</span>
    <span>
        <input type="text" class="form-input" id="pay">
        <input type="submit" value="执行" class="am-btn am-btn-primary" id="payBtn">
    </span>
</div>

<script>
    //同步订单
    $("#orderBtn").click(function () {
        var order = $("#order").val();
        if(order==""){
            msgAlert("请输入订单编号")
        }else{
            msgConfirm('提醒',"确定执行同步订单?",function () {
                $.ajax({
                    url: "/admin/sync/sync-order",
                    data: {orderCode: order},
                    type: "POST",
                    dataType: "json",
                    async: false,
                    success: function (json) {
                        if (json["code"] == 600) {
                            msgAlert(json["msg"],function () {
                                location.reload();
                            })
                        }else{
                            msgAlert(json["msg"]);
                        }
                    }
                });
            })

        }
    })
    //更新赔率
    $("#oddBtn").click(function () {
        var order = $("#odd").val();
        if(order==""){
            msgAlert("请输入订单编号")
        }else{
            msgConfirm('提醒',"确定执行更新订单赔率?",function () {
                $.ajax({
                    url: "/admin/sync/sync-updateodd?",
                    data: {orderCode: order},
                    type: "POST",
                    dataType: "json",
                    async: false,
                    success: function (json) {
                        if (json["code"] == 600) {
                            msgAlert(json["msg"],function () {
                                location.reload();
                            })
                        }else{
                            msgAlert(json["msg"]);
                        }
                    }
                });
            })
        }
    })
    //同步交易明细
    $("#payBtn").click(function () {
        var id = $("#pay").val();
        if(id==""){
            msgAlert("请输入交易明细ID")
        }else{
            msgConfirm('提醒',"确定执行同步交易明细?",function () {
                $.ajax({
                    url: "/admin/sync/sync-payre?",
                    data: {id: id},
                    type: "POST",
                    dataType: "json",
                    async: false,
                    success: function (json) {
                        if (json["code"] == 600) {
                            msgAlert(json["msg"],function () {
                                location.reload();
                            })
                        }else{
                            msgAlert(json["msg"]);
                        }
                    }
                });
            })
        }
    })
</script>
