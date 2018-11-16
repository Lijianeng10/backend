<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>
<style>
    .add_loan {
        width: 95%;
        margin: 0 auto;
    }

    .w-e-text-container {
        height: 400px !important;
    }

    .infoSpan {
        width: 90px;
        /*text-align: right;*/
    }

    #couponsStart {
        width: 145px;
        display: inline;
    }

    #couponsEnd {
        width: 145px;
        display: inline;
        margin-left: 15px;
    }

    #addSubmit {
        margin-left: 20px;
    }
</style>
<div>
    <form class="edit_card">
        <div>
            <input type="hidden" name="cardId" id="cardId" value="<?php echo $cardData['credit_card_id'];?>">
            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">信用卡名<span style="color: red">*</span></span>
                <label>
                    <input name="cardName" id="cardName" type="text" class="form-control need" placeholder="信用卡名称" style="width: 200px;" value="<?php echo $cardData['card_name'];?>">
                </label>
            </div>
            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">取现额度</span>
                <label>
                    <input name="cashQuota" id="cashQuota" type="text" class="form-control" placeholder="取现额度 类：30%" style="width: 200px;" value="<?php echo $cardData['cash_quota'];?>">
                </label>
            </div>
            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">免息期<span style="color: red">*</span></span>
                <label>
                    <input name="freePeriods" id="freePeriods" type="text" class="form-control need" placeholder="免息期 类：30天" style="width: 200px;" value="<?php echo $cardData['free_periods'];?>">
                </label>
            </div>

            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">排序</span>
                <label>
                    <input name="sort" id="sort" type="number" class="form-control minZero" placeholder="显示优先级 数值越小 显示越靠前" style="width: 200px;" min="0"  value="<?php echo $cardData['sort'];?>">
                </label>
            </div>

            <div style="margin-top:5px;" id="jump">
                <div>
                    <span class="infoSpan" style="display: inline-block;padding-left: 10px;">跳转地址<span style="color: red">*</span></span>
                    <label><input type="text" name="jumpUrl" id="jumpUrl" class="form-control need" placeholder="跳转地址" style="width:300px" value="<?php echo $cardData['jump_url'];?>"></label>
                    <span style="color:red;font-size:14px;">跳转地址开头请加上http或者https</span>
                </div>
            </div>
            
            <div style="margin-top:5px;">
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">开卡活动&nbsp;&nbsp;</span>
                <label>
                    <textarea name="cardActivity" id="cardActivity" style="width:300px;height: 100px;solid:1px,#c2cad8; border-radius:4px;" placeholder="如果有多个活动请以’;‘ 隔开"><?php echo $cardData['card_activity']?></textarea>
                </label>
            </div>

            <div style="margin-top:5px;">
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">缩略图<span style="color: red">*</span>&nbsp;&nbsp;</span>
                <label>
                    <div style="width: 220px;height: 140px; text-align: center;border: 1px solid #BECDD5;position: relative;float: left;padding: 2px" id="pic_up">
                        <img id="img_url" style="width: 100%; height: 100%" src="<?php echo empty($cardData['pic_url']) ? '/image/u1529.png' : $cardData['pic_url'];?>">
                        <input type="file" style="width:100%;height: 100%;opacity: 0;position: absolute;top:0;left:0;" id="uploadPic" name="uploadPic">
                        <?php if(empty($cardData['pic_url'])):?>
                            <a href="#" id="delPic" style="width:20px;height:20px;position:absolute;top:-6px;right:-6px;border-radius:100%;border:1px solid #fff;background-color:#C6C6C6;text-align:center;color:#fff;display: none" class="delPic">X</a> 
                        <?php else:?>
                            <a href="#" id="delPic" style="width:20px;height:20px;position:absolute;top:-6px;right:-6px;border-radius:100%;border:1px solid #fff;background-color:#C6C6C6;text-align:center;color:#fff;" class="delPic">X</a> 
                        <?php endif;?>
                    </div>
                </label>
                <span style="color:red;font-size:14px;">图片宽高最佳为220*140;图片大小限制为200k以下</span>
            </div>
            
        </div>
        <div style="margin-top: 10px;">
            <button class="am-btn am-btn-primary" id="addSubmit">提交</button>
            <button class="am-btn am-btn-primary" id="backSubmit">返回</button>
        </div>
        <label id="error_msg"> </label>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>
<script>
    $(function () {
        $('#uploadPic').change(function () {
            var file = this.files[0];
            var scr = $('#img_url').attr("src");
            var objUrl = getObjectURL(this.files[0]);
            if (objUrl) {
                $('#img_url').attr("src", objUrl);
                $("#delPic").show();
            }
            if (!/.(jpg|jpeg|png)$/.test(file.name)) { 
                msgAlert("图片类型必须是.jpeg,jpg,png中的一种");
                $('#img_url').attr("src", scr); 
                $("#delPic").hide();    
            } else if (file.size > 200 * 1024) {
                msgAlert('图片大小不可超过200KB');
                $('#img_url').attr("src", scr); 
                $("#delPic").hide();
            }
        });
    });

    $('#pic_up').on('click', '#delPic', function () {
        $("#uploadPic").val("");
        $("#img_url").attr('src', '/image/u1529.png');
        $("#delPic").hide();
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
    

    $('#addSubmit').click(function () {
        document.forms[1].target = "rfFrame";
        var flag = 0;
        $(".need").each(function () {
            var text = $(this).val();
            if (!text) {
                flag++;
                $(this).focus();
                $("#msg").empty();
                h = '<span id="msg" style="color:red;">请填写此字段</span>';
                $(this).after(h);
                return false;
            }
        });
        if (flag != 0) {
            return false;
        }
        a = 0;
        var formData = new FormData();
        var data = $(".edit_card").serializeArray();
        formData.append("upfile", $("#uploadPic").get(0).files[0]);
        $.each(data, function (i, field) {
            formData.append(field.name, field.value);
        });
        $.ajax({
            url: '/website/credit-card/edit',
            async: false,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data['code'] == 600) {
                    msgAlert(data['msg'], function () {
                        location.href = '/website/credit-card/index';
                    });
                } else {
                    console.log(data.result);
                    msgAlert(data["msg"]);
                }
            }
        });
    })
    $("#backSubmit").click(function () {
        history.go(-1);
    })
</script>
