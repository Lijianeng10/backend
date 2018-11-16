<div>
    <form class="am-form edit_type" id="doc-vld-msg">
        <fieldset>
            <div class="am-form-group">
                <label>充值类型&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                <label class="name">
                    <input type="text" id="ccz_type" name="ccz_type" value="<?php echo $cczType['cz_type'];?>" disabled="true"/>
                </label>
            </div>
            <div class="am-form-group">
                <label>充值类型名<span style="color: red">*</span></label>
                <label class="name">
                    <input type="text" id="ccz_type_name" name="ccz_type_name" value="<?php echo $cczType['cz_type_name'];?>" required/>
                </label>
            </div>
            <div class="am-form-group">
                <label>支付价格<span style="color: red">*&nbsp;&nbsp;&nbsp;&nbsp;</span></label>
                <label class="name">
                    <input type="text" id="cz_money" name="cz_money" value="<?php echo $cczType['cz_money'];?>" required/>
                </label>
            </div>
            <div class="am-form-group">
                <label>对应咕币<span style="color: red">*&nbsp;&nbsp;&nbsp;&nbsp;</span></label>
                <label class="name">
                    <input type="text" id="cz_coin" name="cz_coin" value="<?php echo $cczType['cz_coin'];?>" required/>
                </label>
            </div>
            <div class="am-form-group">
                <label>福利类型<span style="color: red">*&nbsp;&nbsp;</span></label>
                <label class="name">
                    <label><select id="weal_type" name="weal_type"><?php foreach ($wealType as $key => $val): ?><option value="<?php echo $key; ?>" <?php if($key == $cczType['weal_type']):?>selected="true"<?php endif;?>><?php echo $val; ?>&nbsp;&nbsp;&nbsp;&nbsp;</option><?php endforeach; ?></select></label>
                </label>
            </div>
            <div class="am-form-group" id="wealValue" <?php if($cczType['weal_type'] == 1): ?>style="display: none" <?php endif;?>>
                <label>福利值<span id="baifenbi" <?php if($cczType['weal_type'] != 2 && $cczType['weal_type'] != 3): ?>style="display: none" <?php endif;?>>(%)</span><span id="geshu" <?php if($cczType['weal_type'] != 4): ?>style="display: none" <?php endif;?>>(个)</span><span style="color: red">*&nbsp;&nbsp;&nbsp;</span></label>
                <label class="name">
                    <input type="text" id="weal_value" name="weal_value" value="<?php echo $cczType['weal_value'];?>" required/>
                </label>
            </div>
            <input type="hidden" name="cz_type_id" value="<?php echo $cczType['coin_cz_type_id'];?>">
            <div class="am-form-group" id="wealTime" <?php if($cczType['weal_type'] != 4): ?>style="display: none" <?php endif;?>>
                <label>福利有效期<span style="color: red">*</span></label>
                <label class="name">
                    <input type="text" id="weal_time" name="weal_time" value="<?php echo $cczType['weal_time'];?>" required/>
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
            console.log(wealType);
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
            var formData = $(".edit_type").serialize();
            console.log(formData)
            $.ajax({
                url: '/member/coin-cztype/edit-cztype',
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