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
    <form class="edit-base-source">
        <input type="hidden" name="sourceId" id="sourceId" value="<?php echo $sourceData['source_id'];?>">
        <div>
            <div>
                <span class="infoSpan" style="display: inline-block;padding-left: 30px;">来源名称</span>
                <label>
                    <input name="sourceName" id="sourceName" type="text" class="form-control" placeholder="来源名称" style="width: 200px;" value="<?php echo $sourceData['source_name'];?>">
                </label>
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
        $("#addSubmit").click(function () {
            document.forms[1].target = "rfFrame";
            var sourceName = $('#sourceName').val();
            var sourceId = $('#sourceId').val();
            if (!sourceName) {
                msgAlert("请输入来源名称！！");
                return false;
            }
            $.ajax({
                url: "/expert/source/edit-base-source",
                type: "POST",
                async: false,
                data: {sourceId: sourceId, sourceName: sourceName},
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
