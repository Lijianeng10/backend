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
    <form class="add_loan">
        <div>
            <input type="hidden" name="loanId" id="loanId" value="<?php echo $loanData['loan_id'];?>">
            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">贷款标题<span style="color: red">*</span></span>
                <label>
                    <input name="title" id="title" type="text" class="form-control need" placeholder="贷款标题" style="width: 200px;" value="<?php echo $loanData['title'];?>">
                </label>
            </div>
            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">副标题</span>
                <label>
                    <input name="subTitle" id="subTitle" type="text" class="form-control" placeholder="副标题" style="width: 200px;" value="<?php echo $loanData['sub_title'];?>">
                </label>
            </div>
            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">额度范围<span style="color: red">*</span></span>
                <label>
                    <input name="quota" id="quota" type="text" class="form-control need" placeholder="额度范围 类：0元~20元" style="width: 200px;" value="<?php echo $loanData['quota'];?>">
                </label>
            </div>

            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">利率</span>
                <label>
                    <input name="profit" id="profit" type="number" class="form-control need" placeholder="贷款利率 保留两位小数" style="width: 200px;" min="0" max="100" step="0.01" value="<?php echo $loanData['profit'];?>">
                </label>
            </div>

            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">利率说明<span style="color: red">*</span></span>
                <label>
                    <input name="profitRemarkTop" id="profitRemarkTop" type="text" class="form-control nend minZero" placeholder="利率说明 类：日利息 等" style="width: 150px;border-bottom: 1px solid #dbdbdb;border-top:0px;border-left:0px;border-right:0px;display:inline-block"  value="<?php echo $loanData['profit_remark_top'];?>">
                    <span id="p-profit"> <?php echo $loanData['profit'] . '%';?></span>
                    <input name="profitRemarkTail" id="profitRemarkTail" type="text" class="form-control" placeholder="类：起 等" style="width: 100px;border-bottom: 1px solid #dbdbdb;border-top:0px;border-left:0px;border-right:0px;display:inline-block;"  value="<?php echo $loanData['profit_remark_tail'];?>">
                </label>
            </div>

            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">贷款期限<span style="color: red">*</span></span>
                <label>
                    <input name="loanPeriods" id="loan_periods" type="text" class="form-control nend" placeholder="贷款期限  类：6~12月" style="width: 200px;" value="<?php echo $loanData['loan_periods'];?>">
                </label>
            </div>

            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">排序</span>
                <label>
                    <input name="sort" id="sort" type="number" class="form-control minZero" placeholder="显示优先级 数值越小 显示越靠前" style="width: 200px;" min="0"  value="<?php echo $loanData['sort'];?>">
                </label>
            </div>

            <div style="margin-top:5px;" id="jump">
                <div>
                    <span class="infoSpan" style="display: inline-block;padding-left: 10px;">跳转地址<span style="color: red">*</span></span>
                    <label><input type="text" name="jumpUrl" id="jumpUrl" class="form-control need" placeholder="跳转地址" style="width:300px" value="<?php echo $loanData['jump_url'];?>"></label>
                    <span style="color:red;font-size:14px;">跳转地址开头请加上http或者https</span>
                </div>
            </div>

            <div style="margin-top:2px;">
                <span class="infoSpan" style="display: inline-block;padding-left: 10px;">缩略图<span style="color: red">*</span>&nbsp;&nbsp;</span>
                <label>
                    <div style="width: 140px;height: 140px; text-align: center;border: 1px solid #BECDD5;position: relative;float: left;padding: 2px" id="pic_up">
                        <img id="img_url" style="width: 100%; height: 100%" src="<?php echo empty($loanData['pic_url']) ? '/image/u1529.png' : $loanData['pic_url'];?>">
                        <input type="file" style="width:100%;height: 100%;opacity: 0;position: absolute;top:0;left:0;" id="uploadPic" name="uploadPic">
                        <?php if(empty($loanData['pic_url'])):?>
                            <a href="#" id="delPic" style="width:20px;height:20px;position:absolute;top:-6px;right:-6px;border-radius:100%;border:1px solid #fff;background-color:#C6C6C6;text-align:center;color:#fff;display: none" class="delPic">X</a> 
                        <?php else:?>
                            <a href="#" id="delPic" style="width:20px;height:20px;position:absolute;top:-6px;right:-6px;border-radius:100%;border:1px solid #fff;background-color:#C6C6C6;text-align:center;color:#fff;" class="delPic">X</a> 
                        <?php endif;?>
                    </div>
                </label>
                <span style="color:red;font-size:14px;">图片大小限制为200k以下</span>
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

    $('#profit').change(function(){ 
        $('#p-profit').html($('#profit').val() + '%');
    })
    
    //添加幻灯片
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
        var data = $(".add_loan").serializeArray();
        formData.append("upfile", $("#uploadPic").get(0).files[0]);
        $.each(data, function (i, field) {
            formData.append(field.name, field.value);
        });
        $.ajax({
            url: '/website/loan/edit-loan',
            async: false,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data['code'] == 600) {
                    msgAlert(data['msg'], function () {
                        location.href = '/website/loan/index';
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