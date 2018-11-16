<style>
    .form-word{
        width: 90px;
        text-align: right;
    }
    
</style>
<div>
    <form class="am-form edit_growth" id="doc-vld-msg">
        <fieldset>
            <div class="am-form-group" >
                <label for="doc-vld-name-2-1" class="form-word">成长值来源<span style="color: red">*</span></label>
                <label id="growth_source">
                    <select id="source" name="growth_source">
                        <option class="growth_source" value="0">请选择</option>
                        <?php foreach ($source as $key => $val) :?>
                        <option class="growth_source" value="<?php echo $key;?>" <?php if($val == $data['growth_source']): ?>selected="true"<?php endif;?>><?php echo $val;?></option>
                        <?php endforeach;?>
                    </select>
                </label>
            </div>
            <input type="hidden" name="growth_id" value="<?php echo $data['user_growth_id'];?>">
            <div class="am-form-group">
                <label for="doc-vld-email-2-1" class="form-word">成长类型<span style="color: red">*</span></label>
                <label id="value_type">
                    <select id="growth_type" name="growth_type">
                        <option value="0">请选择&nbsp;&nbsp;&nbsp;&nbsp;</option>
                        <?php foreach ($type as $key => $val) :?>
                        <option value="<?php echo $key;?>" <?php if($val == $data['growth_type']): ?>selected="true"<?php endif;?>><?php echo $val;?></option>
                        <?php endforeach;?>
                    </select>
                </label>
            </div>
            <div class="am-form-group">
                <label for="doc-vld-age-2-1" class="form-word">成长值<span style="color: red">*</span></label>
                <label>
                    <input type="text" class="need" name="growth_value"  value="<?php echo $data['growth_value']; ?>" placeholder="成长值" data-validation-message="请填写正确的正整数0-∞" pattern="^\d+$" min="0" required/>
                </label>
            </div>
            <div class="am-form-group" id="growth_need">
                <label for="doc-vld-age-2-1" class="form-word">成长机制<span style="color: red">*</span></label>
                <label>
                    <textarea class="need" name="growth_remark" rows="2" style="width:350px;height:70px;" required><?php echo $data['growth_remark']; ?></textarea>
                </label>
            </div>
            
            <button class="am-btn am-btn-secondary" id="editSubmit" style="margin-left:100px;">提交</button>
            <input  class="am-btn am-btn-secondary" type="reset" />
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
            var formData = $(".edit_growth").serialize();
            var source = $("#source option:selected");
            var type = $('#growth_type option:selected');
            if(source.val() == 0){
                $(this).focus();
                $("#s_msg").empty();
                h = '<label id="s_msg" style="color:red;"> 请选择有效来源</label>';
                $("#growth_source").after(h);
                a++
            }
            if(type.val() == 0){
                console.log(type.val())
                $(this).focus();
                $("#t_msg").empty();
                h = '<label id="t_msg" style="color:red;"> 请选择有效类型</label>';
                $("#value_type").after(h);
                a++
            }
            $(".need").each(function(i){
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
                url: '/member/growing/editgrowing',
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