<div>
    <form class="am-form  edit_levels" id="doc-vld-msg">
        <fieldset>
            <legend>等级信息</legend>
            <div class="am-form-group">
                <label for="doc-vld-name-2-1">等级名称<span style="color: red">*</span>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                <label><input type="text" name="level_name" value="<?php echo $data['level_name'];?>" placeholder="等级名称" required/></label>
                <input type="hidden" name="level_id" value="<?php echo $data['user_level_id'];?>">
            </div>

            <div class="am-form-group">
                <label for="doc-vld-email-2-1">成长所需值<span style="color: red">*</span></label>
                <label>
                    <input type="text" name="growth_vale" value="<?php echo $data['level_growth'];?>" placeholder="成长值,只能填正整数" data-validation-message="请填写正确的正整数0-∞" pattern="^\d+$" min="0" required/>
                </label>
                <label><span style="color:#808080">成长值,只能填正整数</span></label>
            </div>

            <div class="am-form-group">
                <label for="doc-vld-url-2-1">升级机制</label>
                <label class="am-checkbox-inline">
                    <input type="checkbox" value="2" name="up_statue" <?php if ($data['up_status'] == 2):?> checked="true" <?php endif;?>> 等级锁定
                </label>
            </div>
            <legend>充值得咕币信息</legend>
            <div class="am-form-group">
                <label for="doc-vld-age-2-1">会员充值：<span style="color: red">*</span></label>
                <label>
                    <!--<input type="text" name="discount" value=""  placeholder="折扣百分比" pattern="^\d{1,3}$" data-validation-message="请填写正确的正整数0-100" min="0" max="100" required/>-->
                    <input type="text" name="multiple" value="<?php echo $data['multiple'];?>"  placeholder="赠送倍数" pattern="^\d{1,3}$" data-validation-message="请填写正确的正整数1-100" min="1" required/>
                </label>
                <label><span style="color:#808080">充值赠送咕啦倍数,只能填正整数</span></label>
            </div>
            <button class="am-btn am-btn-secondary" id="editSubmit" >提交</button>
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
        $('#editSubmit').click(function () {
            a = 0;
            document.forms[0].target = "rfFrame"; 
            var formData = $(".edit_levels").serialize();
            console.log(formData)
            $("input[type=text]").each(function(i){
            var text = $(this).val();
            if(text ==""){
                    a++;
                   $(this).focus();
                   return false;
            }
            });
            if(a != 0){
                return false;
            }
            $.ajax({
                url: '/member/grade/editlevels',
                async: false,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        $("#msg").empty();
                        h = '<span id="msg" style="color:red;">' + data['msg'] + '</span>';
                        $("#error_msg").prepend(h);
                        $("input[type=text]").each(function () {
                            if ($(this).val() == '') {
                                $(this).focus();
                                return false
                            }
                        });
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