<style>
    .am-form-group p{
        margin-top: 8px;
    }
    .infoSpan{
        display: inline-block;
        width: 100px;
        text-align: right;
        font-size: 14px;
    }
    .infoInput{
        margin-left: 5px;
        border-radius: 4px;
    }
</style>
<div class="am-form-group">
    <input type="hidden" value="<?php echo $bussiness['user_id']; ?>" id="user_id">
    <input type="hidden" value="<?php echo $bussiness['bussiness_id']; ?>" id="bussiness_id">
    <div style="margin-top: 8px;">
        <span class="infoSpan">持卡人</span>
        <input type="input" placeholder="请输入持卡人姓名" class="form-input" id="name">
    </div>
    <div style="margin-top: 8px;">
        <span class="infoSpan">开户行</span>
        <select id="banks" class="form-input">
            <option value="0">请选择</option>
            <option value="中国工商银行">中国工商银行</option>
            <option value="中国建设银行">中国建设银行</option>
            <option value="中国银行">中国银行</option>
            <option value="交通银行">交通银行</option>
            <option value="中国农业银行">中国农业银行</option>
            <option value="招商银行">招商银行</option>
            <option value="中国邮政储蓄银行">中国邮政储蓄银行</option>
            <option value="中国光大银行">中国光大银行</option>
            <option value="中国民生银行">中国民生银行</option>
            <option value="中国平安银行">中国平安银行</option>
            <option value="浦发银行">浦发银行</option>
            <option value="中信银行">中信银行</option>
            <option value="兴业银行">兴业银行</option>
            <option value="华夏银行">华夏银行</option>
            <option value="广发银行">广发银行</option>
        </select>
    </div>
    <div style="margin-top: 8px;">
        <span class="infoSpan">开户省份</span>
        <input type="input" placeholder="请输入开户省份" class="form-input" id="province">
    </div>
    <div style="margin-top: 8px;">
        <span class="infoSpan">开户城市（市）</span>
        <input type="input" placeholder="请输入开户城市" class="form-input" id="city">
    </div>
    <div style="margin-top: 8px;">
        <span class="infoSpan">开户支行</span>
        <input type="input" placeholder="请输入开户支行" class="form-input" id="branch">
    </div>
    <div style="margin-top: 8px;">
        <span class="infoSpan">卡号</span>
        <input type="input" placeholder="请输入卡号" class="form-input" id="card">
    </div>
    <div style="margin-top: 8px;">
        <button class="am-btn am-btn-primary" id="addSubmit" style="margin-left:107px">新增</button>
        <button class="am-btn am-btn-primary" id="backSubmit" >关闭</button>
    </div>
</div>
<script>
    //新增银行卡信息
    $("#addSubmit").click(function () {
        var userId = $("#user_id").val();
        var bussinessId = $("#bussiness_id").val();
        var name = $("#name").val();
        var banks = $("#banks").val();
        var province = $("#province").val();
        var city = $("#city").val();
        var branch = $("#branch").val();
        var card = $("#card").val();
        if (name == "") {
            msgAlert("请填写持卡人");
            return false;
        } else if (banks == "0") {
            msgAlert("请选择开户行");
            return false;
        } else if (province == "") {
            msgAlert("请填写开户省份信息");
            return false;
        } else if (city == "") {
            msgAlert("请填写开户城市信息");
            return false;
        } else if (branch == "") {
            msgAlert("请填写开户支行信息");
            return false;
        } else if (card == "") {
            msgAlert("请填写卡号");
            return false;
        }
        $.ajax({
            url: '/subchannel/withdraw/add-banks',
            async: false,
            type: 'POST',
            data: {bussinessId:bussinessId,userId:userId,name:name,banks:banks,province:province,city:city,branch:branch,card:card},
            dataType: 'json',
            success: function (data) {
                if (data['code'] == 600) {
                    msgAlert(data['msg'], function () {
                        location.href = '/subchannel/withdraw/index';
                    });
                } else {
                    msgAlert(data["msg"]);
                }
            }
        });
    })
    //关闭
    $("#backSubmit").click(function () {
        closeMask();
    })
</script>
