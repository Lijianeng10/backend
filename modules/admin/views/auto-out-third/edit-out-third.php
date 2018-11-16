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
            <label class="txt">出票方编号：</label>
            <label>
                <input type="text"  value ="<?php echo $data['third_code']; ?>" class ="form-control" style="display: inline-block;width: 150px;" id="third_code" disabled="disabled">
            </label>
        </div>
        <div style="margin-top: 10px;">
            <label class="txt">出票方名称：</label>
            <label>
                <input type="text"  value ="<?php echo $data['third_name']; ?>" class ="form-control" style="display: inline-block;width: 150px;" id="third_name" >
            </label>
        </div>
        <input type="hidden" name="auto_out_third_id" id="auto_out_third_id"  value="<?php echo $data['auto_out_third_id']; ?>">
        <div style="margin-top: 10px;">
            <?php
                use yii\helpers\Html;
                $type = [
                    "" => "请选择",
                    "1" => "流量单",
                    "2" => "自营单",
                    '3' => '全部'
                ];
                echo Html::label("出票类型：", "", ["style" => "margin-left:0px;", "class" => "txt"]);
                echo Html::dropDownList("out_type", $data['out_type'], $type, ["class" => "form-control", "id" => "out_type", "style" => "width:90px;display:inline;margin-left:12px;margin-bottom:5px;"]);
            ?>
        </div>
        <div>
            <label class="txt">可出彩种：</label>
            <label>
                <?php foreach ($data['auto_lottery'] as $key => $val) : ?>
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
        var out_type = $("#out_type").val();
        var auto_out_third_id = $('#auto_out_third_id').val();
        var thirdName = $('#third_name').val();
        if (out_type == 0) {
            msgAlert("请选择出票类型")
            return false;
        } else {
            $.ajax({
                url: '/admin/auto-out-third/edit-third',
                async: false,
                type: 'POST',
                data: {auto_out_third_id: auto_out_third_id, out_type: out_type, codes: codes, third_name: thirdName},
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