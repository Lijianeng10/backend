<style>
    .txt{
        width: 121px;
        text-align: right;
    }
    .action{
        width: 18%;
        text-align: left;
        padding-left: 40px;
        display: inline-block;
    }
</style>
<div>
    <form id="doc-vld-msg">
        <div  style="margin-top: 10px;">
            <label class="txt">机器编号：</label>
            <label>
                <input type="text"  value ="<?php echo $data['dispenser_code']; ?>" class ="form-control" style="display: inline-block;width: 150px;" id="dispenser_code">
            </label>

        </div>
        <div  style="margin-top: 10px;">
            <label class="txt">销售商代码：</label>
            <label>
                <input type="text"  value ="<?php echo $data['vender_id']; ?>" class ="form-control" style="display: inline-block;width: 150px;" id="vender_id">
            </label>

        </div>
        <div  style="margin-top: 10px;">
            <label class="txt">智魔方机子代码：</label>
            <label>
                <input type="text"  value ="<?php echo $data['sn_code']; ?>" class ="form-control" style="display: inline-block;width: 150px;" id="sn_code">
            </label>
        </div>
        <input type="hidden" name="ticket_dispenser_id" value="<?php echo $data['ticket_dispenser_id']; ?>">
        <div>
            <?php

            use yii\helpers\Html;

$type = [
                "0" => "请选择",
                "1" => "手工出票",
                "2" => "自动出票"
            ];
            echo Html::label("出票类型：", "", ["style" => "margin-left:0px;", "class" => "txt"]);
            echo Html::dropDownList("type", $data['type'], $type, ["class" => "form-control", "id" => "type", "style" => "width:90px;display:inline;margin-left:12px;margin-bottom:5px;"]);
            ?>
        </div>
        <div>
            <label class="txt">预出票数：</label>
            <label>
                <input type="text"  value ="<?php echo $data['pre_out_nums']; ?>" class ="form-control" style="display: inline-block;width: 150px;" id="pre_out_nums">
            </label>
        </div>
        <div>
            <label class="txt">剩余出票数：</label>
            <label>
                <input type="text"  value ="<?php echo $data['mod_nums']; ?>" class ="form-control" style="display: inline-block;width: 150px;" id="mod_nums">
            </label>
        </div>
        <div>
            <label class="txt">可出彩种：</label>
            <label>
                <?php foreach ($data['sale_lottery'] as $key => $val) : ?>
                    <div class="action">
                        <input type="checkbox" name="<?php echo $key; ?>" id="code_<?php echo $key; ?>" class="select" value=<?php echo $key; ?>   <?php if (in_array($key, $data['out_lottery'])) : ?> checked="checked" <?php endif; ?>> <?php echo $val; ?>
                    </div>
                <?php endforeach; ?>
            </label>
        </div>
        <button type="button" class="am-btn am-btn-primary" id="addSubmit" style="margin-left: 50px;">提交</button>
        <button type="button" class="am-btn am-btn-primary" id="closeBtn" style="margin-left: 5px;">关闭</button>
</div>
<script>
    $("#closeBtn").click(function () {
        closeMask();
    })
    $("#addSubmit").click(function () {
        var codes = [];
        $.each($(".select"), function () {
            if ($(this).is(':checked')) {
                codes.push($(this).val());
            }
        });
        var type = $("#type").val();
        var dispenser_code = $("#dispenser_code").val();
        var ticket_dispenser_id = $("input[name=ticket_dispenser_id]").val();
        var pre_out_nums = parseInt($("#pre_out_nums").val());
        var mod_nums = parseInt($("#mod_nums").val());
        var vender_id = $("#vender_id").val();
        var sn_code = $("#sn_code").val();
        if (type == 0) {
            msgAlert("请选择出票类型")
        } else if (dispenser_code == "") {
            msgAlert("请填写机器编号")
        } else if (pre_out_nums <= 0 || isNaN(pre_out_nums)) {
            msgAlert("输入有误，预出票数不能小于0")
        } else {
            $.ajax({
                url: '/agents/ticket/edit-dispenser',
                async: false,
                type: 'POST',
                data: {ticket_dispenser_id: ticket_dispenser_id, type: type, dispenser_code: dispenser_code, pre_out_nums: pre_out_nums, mod_nums: mod_nums, vender_id: vender_id, sn_code: sn_code, codes:codes},
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            });
        }
    });
//    $(".select").click(function () {
//        var id = $(this).attr("id");
//        if ($(this).is(':checked')) {
//
//        } else if (!$(this).is(':checked')) {
//            $("input[data-id = 'authid_" + val + "']").prop("checked", false);
//
//        }
//    }
//    });
</script>