<div>
    <form class="am-form add_type" id="doc-vld-msg">
        <fieldset>
            <div class="am-form-group">
                <label>充值类型<span style="color: red">*</span>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                <label class="name">
                    <input type="text" id="ccz_type" name="ccz_type" required/>
                </label>
            </div>
            <div class="am-form-group">
                <label>充值类型名<span style="color: red">*</span></label>
                <label class="name">
                    <input type="text" id="ccz_type_name" name="ccz_type_name"  required/>
                </label>
            </div>
            <div class="am-form-group">
                <label>支付价格<span style="color: red">*&nbsp;&nbsp;&nbsp;&nbsp;</span></label>
                <label class="name">
                    <input type="text" id="cz_money" name="cz_money"  required/>
                </label>
            </div>
            <div class="am-form-group">
                <label>对应咕币<span style="color: red">*&nbsp;&nbsp;&nbsp;&nbsp;</span></label>
                <label class="name">
                    <input type="text" id="cz_coin" name="cz_coin"  required/>
                </label>
            </div>
            <div class="am-form-group">
                <label>福利类型<span style="color: red">*&nbsp;&nbsp;</span></label>
                <label class="name">
                    <label><select id="weal_type" name="weal_type"><?php foreach ($wealType as $key => $val): ?><option value="<?php echo $key; ?>"><?php echo $val; ?>&nbsp;&nbsp;&nbsp;&nbsp;</option><?php endforeach; ?></select></label>
                </label>
            </div>
            <div class="am-form-group" id="wealValue" style="display: none" >
                <label>福利值<span id="baifenbi" style="display: none">(%)</span><span id="geshu" style="display: none" >(个)</span><span style="color: red">*&nbsp;&nbsp;&nbsp;</span></label>
                <label class="name">
                    <input type="text" id="weal_value" name="weal_value"  required/>
                </label>
            </div>
            <div class="am-form-group" id="wealTime" style="display: none" >
                <label>福利有效期<span style="color: red">*</span></label>
                <label class="name">
                    <input type="text" id="weal_time" name="weal_time"  required/>
                </label>
            </div>
            <button type="button" class="am-btn am-btn-secondary" id="editSubmit" >提交</button>
            <label id="error_msg"> </label>
        </fieldset>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>

<script>

    $(function () {
        $('#doc-vld-msg').validator({
            onValid: function (validity) {
                $(validity.field).closest('.am-form-group').find('.am-alert').hide();
            },
            onInValid: function (validity) {
                var $field = $(validity.field);
                var $group = $field.closest('.am-form-group');
                var $alert = $group.find('.am-alert');
                // 使用自定义的提示信息 或 插件内置的提示信息
                var msg = $field.data('validationMessage') || this.getValidationMessage(validity);

                if (!$alert.length) {
                    $alert = $('<div class="am-alert am-alert-danger"></div>').hide().appendTo($group);
                }
                $alert.html(msg).show();
            }
        });
        $("#weal_type").change(function () {
            wealType = $(this).val();
            if(wealType == 1) {
                $("#wealValue").hide();
                $("#wealTime").hide();
            }else if(wealType == 2 || wealType == 3) {
                $("#wealValue").show();
                $("#wealTime").hide();
                $("#baifenbi").show();
                $("#geshu").hide();
            }else {
                $("#wealValue").show();
                $("#wealTime").show();
                $("#baifenbi").hide();
                $("#geshu").show();
            }
        });
        $('#editSubmit').click(function () {
            document.forms[0].target = "rfFrame"; 
            var formData = $(".add_type").serialize();
            console.log(formData)
            $.ajax({
                url: '/member/coin-cztype/add-cztype',
                async: false,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        $("#msg").empty();
                        h = '<span id="msg" style="color:red;">' + data['msg'] + '</span>';
                        $("#error_msg").prepend(h);
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