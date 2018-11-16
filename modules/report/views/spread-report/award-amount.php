<div>
    <form id="theForm">
        <input type="hidden" name="settleid" value="<?php echo $settleid; ?>">
        <div class="form-li">
            <lable class="form-span">手机号：</lable>
            <input class="form-input" type="text" name="userTel" value="<?php echo $tel;?>"  readonly="readonly">
        </div>
        <div class="form-li">
            <lable class="form-span">金额：</lable>
            <input class="form-input" type="text" name="money" value="<?php echo $money;?>">
        </div>
    </form>
    <div class="form-li">
        <button class="am-btn am-btn-primary" style="margin-left:104px;font-size: 14px;" id="addBtn">确定</button>
        <button  class="am-btn am-btn-primary" style="margin-left: 5px;font-size: 14px;" id="closeBtn">关闭</button>
    </div>
</div>

<script>
    $("#addBtn").click(function () {
        msgConfirm('提醒',"确定发放分润?",function(){
            var data = $("#theForm").serializeArray();
            $.ajax({
                url: "/report/spread-report/award-amount",
                type: "POST",
                dataType: "json",
                async: false,
                data: data,
                success: function (json) {
                    if (json["code"] == "600") {
                        msgAlert(json["msg"],function () {
                            location.reload();
                        });
                    }else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        })
    });
    $("#closeBtn").click(function(){
        closeMask();
    })
</script>