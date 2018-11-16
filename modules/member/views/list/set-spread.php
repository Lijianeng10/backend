<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>
<style>
    .add_bananer {
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
    <form class="set-spread">
        <div>
            <input type="hidden" name="userId" id="userId" value="<?php echo $user['user_id']; ?>">
            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 30px;">会员编号</span>
                <label>
                    <input name="custNo" id="custNo" type="text" class="form-control" placeholder="会员编号" style="width: 200px;" value="<?php echo $user['cust_no']; ?>" disabled="true">
                </label>
            </div>
            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 30px;">会员名称</span>
                <label>
                    <input name="userName" id="userName" type="text" class="form-control" placeholder="会员名称" style="width: 200px;" value="<?php echo $user['cust_no']; ?>" disabled="true">
                </label>
            </div>
            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 30px;">推广类型</span>
                <label>
                    <input type="radio" name="spreadType" class="type" value="1">购彩推广
                    <input type="radio" name="spreadType" class="type" value="11">专家推广
                </label>
            </div>
            <div id="setRebate" style="display:none">
                <span class="infoSpan" style="display: inline-block;padding-left: 30px;">设置返点</span>
                <label>
                    <input type="number" name="rebate" id="rebate" class="form-control" placeholder="返点" style="width: 200px;" min="0" max="100" step="0.01">
                </label>
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
        $(".type").click(function () {
            if ($(this).val() == 1) {
                $('#setRebate').show();
            } else {
                $('#setRebate').hide();
            }
        })

        $("#addSubmit").click(function () {
            document.forms[1].target = "rfFrame";
            var type = $('input:radio[name="spreadType"]:checked').val();
            if (!type) {
                msgAlert('请选择要设置的推广类型');
                return false;
            }
            var userId = $('#userId').val();
            if (type == 1) {
                var rebate = $('#rebate').val();
                if (isNaN(rebate) || rebate == "") {
                    msgAlert("请输入合法的数字");
                    return false;
                }
                if (0 > rebate || rebate > 100) {
                    msgAlert("请输入0-100之间的数");
                    return false;
                }
            }
            $.ajax({
                url: "/member/list/edit-spread-type",
                type: "POST",
                async: false,
                data: {user_id: userId, type: type, rebate: rebate},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            });
        })
    })
</script>
