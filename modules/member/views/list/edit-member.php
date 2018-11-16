<div>
    <legend>编辑会员</legend>
    <form class="am-form edit_user" id="doc-vld-msg">
        <input type="hidden" name="user_id" value="<?php echo $user_data['user_id']; ?>">
        <div class="am-form-group">
            <label for="doc-vld-name-2-1">会员名称<span style="color: red">*</span>&nbsp;&nbsp;</label>
            <label><input type="text" name="user_name" class="need" value="<?php echo $user_data['user_name'];?>" placeholder="会员名称" required/></label>
        </div>
        <div class="am-form-group">
            <label for="doc-vld-name-2-1">手机号码<span style="color: red">*</span>&nbsp;&nbsp;</label>
            <label><input type="text" id="doc-vld-528" class="js-pattern-mobile need" name="user_tel" value="<?php echo $user_data['user_tel']; ?>" pattern="^\d{11}$" data-validation-message="请填写正确的手机号" placeholder="手机号码" required/></label>
        </div>
        <div class="am-form-group">
            <label for="doc-vld-name-2-1">联系电话 &nbsp;&nbsp;&nbsp;</label>
            <label><input type="text" name="user_land" placeholder="联系电话" value="<?php echo $user_data['user_land']; ?>" data-validation-message="请填写正确的号码" pattern="^(\d{3}-|\d{4}-)(\d{8}|\d{7})$"/></label>
        </div>

        <div id="city_china" class="am-form-group">
            <label for="doc-vld-name-2-1">所属城市<span style="color: red">*</span>&nbsp;&nbsp;</label>
            <label><select class="province cxselect" data-value="<?php echo $user_data['province'];?>" disabled="disabled" name="province"></select></label>
            <label><select class="city cxselect" data-value="<?php echo $user_data['city'];?>" disabled="disabled" name="city" style="min-width: 80px"></select></label>
            <label><select class="area cxselect" data-value="<?php echo $user_data['area'];?>" disabled="disabled" name="area" style="min-width: 80px"></select></label>
            <label class="address_input"><input type="text" class="need" id="address" name="address" value="<?php echo $user_data['address'];?>"  style="width:500px" placeholder="详细地址" data-validation-message="请填写详细地址" required/></label>
        </div>

        <div class="am-form-group">
            <label for="doc-vld-name-2-1">会员性别&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <label><select  name="user_gender"><?php foreach ($gender as $key => $val): ?><option value="<?php echo $key; ?>" <?php if ($user_data['user_sex'] == $val): ?>selected="true"<?php endif;?>><?php echo $val; ?>&nbsp;&nbsp;&nbsp;&nbsp;</option><?php endforeach; ?></select></label>
        </div>
       
        <div>
            <label for="doc-vld-name-2-1">会员等级<span style="color: red">*</span>&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <label><select  name="user_level"><?php foreach ($levels as $key => $val): ?><option value="<?php echo $key; ?>" <?php if ($user_data['level_id'] == $key): ?>selected="true"<?php endif;?>><?php echo $val; ?>&nbsp;&nbsp;&nbsp;&nbsp;</option><?php endforeach; ?></select></label>
        </div>
        <div class="am-form-group">
            <label for="doc-vld-name-2-1">会员状态<span style="color: red">*</span>&nbsp;&nbsp;</label>
            <label><select class="user_sta" name="user_status"><?php foreach ($user_status as $key=>$val): ?><option value="<?php echo $key;?>" <?php if ($user_data['status'] == $key): ?>selected="true"<?php endif;?>><?php echo $val;?>&nbsp;&nbsp;&nbsp;&nbsp;</option><?php endforeach;?></select></label>
        </div>
        <div class="am-form-group">
            <label for="doc-vld-name-2-1">备注&nbsp;&nbsp;</label>
            <label><textarea  name="user_remark" rows="2" style="width:380px" ><?php echo $user_data['user_remark'];?></textarea></label>
        </div>
        <button class="am-btn am-btn-secondary" id="editSubmit" >提交</button>
        <button class="am-btn am-btn-secondary" id="backSubmit" >返回</button>
        <label id="error_msg"> </label>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>


<script src="/js/jquery.cxselect.min.js"></script>
<script>
    $.cxSelect.defaults.url = '/js/cityData.min.json';

    $('#city_china').cxSelect({
        selects: ['province', 'city', 'area']
    });
    
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
        $('.city').change(function(){
            $("#c_msg").empty();
            var code = $(this).val();
            if(code == 0){
                $('#address').attr('disabled',true);
            }else {
                $('#address').attr('disabled',false);
            }
        })
        $('#editSubmit').click(function () {
            document.forms[0].target = "rfFrame"; 
            a = 0;
            var formData = $(".edit_user").serialize();
           console.log(formData)
            var provice = $(".province option:selected");
            var city = $('.city option:selected');
            var sta = $('.user_sta option:selected');
            if(provice.val() == 0){
                $(this).focus();
                $("#msg").empty();
                h = '<label id="msg" style="color:red;"> 请选择省份</label>';
                $(".address_input").after(h);
                a++
            }
            if(city.val() == 0){
                $(this).focus();
                $("#msg").empty();
                h = '<label id="msg" style="color:red;"> 请选择城市</label>';
                $(".address_input").after(h);
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
            $(".am-alert-danger").each(function(i){
                if($(this).is(":visible")){
                    a++;
                }
            })
            if(a != 0){
                return false;
            }   
            $.ajax({
                url: '/member/list/edit-member',
                async: false,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        console.log(data.result)
                        $("#msg").empty();
                        h = '<span id="msg" style="color:red;">' + data['msg'] + '</span>';
                        $("#error_msg").prepend(h);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.href = '/member/list/index';
                        });
                    }
                }
            });
        })
        $("#backSubmit").click(function(){
            document.forms[0].target = "rfFrame"; 
            location.href = '/member/list/index';
        })
    })
</script>