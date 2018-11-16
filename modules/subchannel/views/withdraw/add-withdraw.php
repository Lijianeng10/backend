<style>
    .am-form-group p{
        margin-top: 8px;
    }
    .infoSpan{
        display: inline-block;
        width: 90px;
        text-align: right;
        font-size: 14px;
    }
    .infoInput{
        margin-left: 5px;
        border-radius: 4px;
    }
</style>
<div class="am-form-group">
    <?php

    use yii\helpers\Html;

if ($_SESSION["type"] == 0) {
        echo '<div style="margin-top: 8px;"><span class="infoSpan">提现申请人</span>';
        echo Html::dropDownList('bussiness_id', isset($get['bussiness_id']) ? $get['bussiness_id'] : '', $channel, [ 'class' => 'form-input', 'id' => 'bussinessId']);
        echo '</div>';
    } else {
        echo '<div style="display:none;margin-top: 8px;"><span class="infoSpan">提现申请人</span>';
        echo Html::input('hidden', 'bussiness_id', "", ['class' => 'form-input', 'id' => 'bussinessId']);
        echo '</div>';
    }
    ?>
    <div id="info"></div>
    <div style="margin-top: 8px;">
        <span class="infoSpan">提现金额</span>
        <input type="input" placeholder="请输入提现金额" class="form-input" id="money">
    </div>
    <div style="margin-top:5px">
        <span class="infoSpan">转账凭证</span>
        <label>
            <div>
                <a class="buttomspan" onclick="$('#picture').click();">上传</a>
                <a class="buttomspan" onclick="javascript:$('#picture').val('');$('#showimg').attr('src', '/image/u1529.png');$('#showimg').css({width: '140px', height: '140px', });">| 删除</a>
            </div>
        </label>
        <img src="/image/u1529.png" id="showimg" style="width: 140px;height: 140px" class="am-img-thumbnail">
        <input type="file" id="picture" class="imgupload" name="picture" required>
    </div>
    <div style="margin-top: 8px;">
        <span class="infoSpan">备注</span>
        <textarea style="width:300px;height: 100px;" id="remark"></textarea>
    </div>
    <div style="margin-top: 8px;">
        <button class="am-btn am-btn-primary" id="addBank" onclick="addBank()" style="margin-left:30px" >新增银行卡</button>
        <button class="am-btn am-btn-primary" id="addSubmit" >提交</button>
        <button class="am-btn am-btn-primary" id="backSubmit" >返回</button>
    </div>
</div>
<script>
    var bussinessId = $("#bussinessId").val();
    if (bussinessId == "") {
        $.ajax({
            url: '/subchannel/withdraw/get-bussiness-info',
            async: false,
            type: 'POST',
            data: {bussinessId: bussinessId},
            dataType: 'json',
            success: function (data) {
                if (data['code'] == 600) {
                    console.log(data)
                    var funds = data["result"]["fundInfo"];
                    var banks = data["result"]["bankLists"];
                    var bussiness = data["result"]["bussiness"];
                    var html = ""
                    if (funds != "") {
                        html += "<div><span class='infoSpan'>可用余额:</span><span>" + funds["able_funds"] + "</span></div>"
                        html += "<div><span class='infoSpan'>不可提现余额: </span><span>" + funds["no_withdraw"] + "</span></div>"
                        html += "<div><span class='infoSpan' style='color:red'>可提现余额: </span><span id='drawMoney'>" + (funds["able_funds"] - funds["no_withdraw"]).toFixed(2) + "</span></div>"
                    }
                    if (banks != "") {
                        html += "<div><span class='infoSpan'>银行卡: </span><select id='banksId' class='form-input'>"
                        for (var i = 0; i < banks.length; i++) {
                            html += "<option value=" + banks[i]["api_user_bank_id"] + ">****" + banks[i]["card_number"].substr(banks[i]["card_number"].length - 4, 4) + "(" + banks[i]["bank_open"] + ")</option>"
                        }
                        html += "</select></div>"
                    } else {
                        msgAlert("尚无绑定银行卡，请先新增绑定银行卡")
                    }
                    if (bussiness != "") {
                        html += "<input type='hidden' value=" + bussiness["bussiness_id"] + " id='bussiness_id'>"
                        html += "<input type='hidden' value=" + bussiness["user_id"] + " id='user_id'>"
                        html += "<input type='hidden' value=" + bussiness["cust_no"] + " id='cust_no'>"
                        html += "<input type='hidden' value=" + bussiness["bussiness_appid"] + " id='bussiness_appid'>"
                    }
                    $("#info").html(html);
                } else {
                    msgAlert(data["msg"]);
                }
            }
        });
    }
    //渠道商银行卡、账户信息
    $("#bussinessId").change(function () {
        var bussinessId = $("#bussinessId").val()
        if (bussinessId != "0") {
            $.ajax({
                url: '/subchannel/withdraw/get-bussiness-info',
                async: false,
                type: 'POST',
                data: {bussinessId: bussinessId},
                dataType: 'json',
                success: function (data) {
                    if (data['code'] == 600) {
                        var funds = data["result"]["fundInfo"];
                        var banks = data["result"]["bankLists"];
                        var bussiness = data["result"]["bussiness"];
                        var html = ""
                        if (funds != "") {
                            var funds = data["result"]["fundInfo"];
                            html += "<div><span class='infoSpan'>可用余额:</span><span>" + funds["able_funds"] + "</span></div>"
                            html += "<div><span class='infoSpan'>不可提现余额: </span><span>" + funds["no_withdraw"] + "</span></div>"
                            html += "<div><span class='infoSpan' style='color:red'>可提现余额: </span><span id='drawMoney'>" + (funds["able_funds"] - funds["no_withdraw"]).toFixed(2) + "</span></div>"
                        }
                        if (banks != "") {
                            var banks = data["result"]["bankLists"];
                            html += "<div><span class='infoSpan'>银行卡: </span><select id='banksId' class='form-input'>"
                            for (var i = 0; i < banks.length; i++) {
                                html += "<option value=" + banks[i]["api_user_bank_id"] + ">****" + banks[i]["card_number"].substr(banks[i]["card_number"].length - 4, 4) + "(" + banks[i]["bank_open"] + ")</option>"
                            }
                            html += "</select></div>"
                        } else {
                            msgAlert("尚无绑定银行卡，请先新增绑定银行卡")
                        }
                        if (bussiness != "") {
                            html += "<input type='hidden' value=" + bussiness["bussiness_id"] + " id='bussiness_id'>"
                            html += "<input type='hidden' value=" + bussiness["user_id"] + " id='user_id'>"
                            html += "<input type='hidden' value=" + bussiness["cust_no"] + " id='cust_no'>"
                            html += "<input type='hidden' value=" + bussiness["bussiness_appid"] + " id='bussiness_appid'>"
                        }
                        $("#info").html(html);
                    } else {
                        msgAlert(data["msg"]);
                    }
                }
            });
        } else {
            $("#info").html("");
        }
    })
    //添加提现申请
    $('#addSubmit').click(function () {
        var bussinessId = $('#bussiness_id').val();
        var userId = $('#user_id').val();
        var custNo = $('#cust_no').val();
        var bussinessAppid = $('#bussiness_appid').val();
        var banksId = $('#banksId').val();
        var money = $("#money").val();
        var remark = $("#remark").val();
        var drawMoney = $("#drawMoney").html();
        if (!bussinessId || !custNo || !bussinessAppid) {
            msgAlert("请选择提现商户")
            return false;
        }
        if (!banksId) {
            msgAlert("请选择提现银行卡")
            return false;
        }
        if (isNaN(parseInt(money)) || money <= 0) {
            msgAlert("请输入合法的数字");
            return false;
        } else if (parseFloat(money) > parseFloat(drawMoney)) {
            msgAlert("提现金额不能大于可提现金额");
            return false;
        }
        if ($('#showimg').attr("src") == '/image/u1529.png') {
            msgAlert("请上传转账凭证")
            return false;
        }
        var formData = new FormData();
        formData.append("bussinessId", bussinessId);
        formData.append("upfile", $("#picture").get(0).files[0]);
        formData.append("userId", userId);
        formData.append("custNo", custNo);
        formData.append("bussinessAppid", bussinessAppid);
        formData.append("banksId", banksId);
        formData.append("money", money);
        formData.append("remark", remark);
        $.ajax({
            url: '/subchannel/withdraw/add-withdraw',
            async: false,
            processData: false,
            contentType: false,
            type: 'POST',
            data: formData,
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
    //新增银行卡
    function addBank() {
        var bussinessId = $('#bussinessId').val();
        if (bussinessId == "0") {
            msgAlert("请选择渠道商户");
            return false;
        }
        closeMask();
        modDisplay({width: 500, height: 500, title: "新增银行卡", url: "/subchannel/withdraw/add-banks?bussiness_id=" + bussinessId});
    }


    $("#backSubmit").click(function () {
        closeMask();
    })
    //提现凭证
    $('#picture').change(function () {
        var file = this.files[0];
        var scr = $('#showimg').attr("src");
        var objUrl = getObjectURL(this.files[0]);
        if (objUrl) {
            $('#showimg').attr("src", objUrl);
        }
        var imgType=["image/png","image/jpg","image/jpeg","image/gif"];
        if ($.inArray(file.type,imgType)=="-1") { 
            msgAlert("图片类型必须是.jpeg,jpg,png,gif中的一种");
            $('#showimg').attr("src", "/image/u1529.png")
            return false;     
        }
    });
    function getObjectURL(file) {
         var url = null;
         if (window.createObjectURL != undefined) { // basic
              url = window.createObjectURL(file);
         } else if (window.URL != undefined) { // mozilla(firefox)
              url = window.URL.createObjectURL(file);
         } else if (window.webkitURL != undefined) { // webkit or chrome
              url = window.webkitURL.createObjectURL(file);
         }
         return url;
    }   
</script>

