<div>
    <form class="am-form edit_cate" id="doc-vld-msg">
        <fieldset>
            <div class="am-form-group">
                <label>所属类别<span style="color: red">*</span></label>
                <label><select  name="cate_parent" <?php if($cate_data['parent_id'] == 0):?>disabled="true"<?php endif;?>><?php foreach ($cate_list as $key => $val): ?><option value="<?php echo $key; ?>" <?php if($key == $cate_data['parent_id']):?>selected="true"<?php endif;?>><?php echo $val; ?>&nbsp;&nbsp;&nbsp;&nbsp;</option><?php endforeach; ?></select></label>
            </div>
            <input type="hidden" name="cate_id" value="<?php echo $cate_data['gift_category_id'];?>">
            <div class="am-form-group">
                <label>类别名称<span style="color: red">*</span></label>
                <label class="name">
                    <input type="text" id="cate_name" name="cate_name" value="<?php echo $cate_data['category_name'];?>" required/>
                </label>
            </div>
            <div class="am-form-group">
                <label>类别备注</label>
                <label><textarea  name="cate_remark" rows="2" style="width:380px" maxlength="250"><?php echo $cate_data['category_remark'];?></textarea></label>
            </div>
            
            <button type="button" class="am-btn am-btn-secondary" id="addSubmit" >提交</button>
            <label id="error_msg"> </label>
        </fieldset>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>

<script>

    $(function () {
        $('#addSubmit').click(function () {
            document.forms[0].target = "rfFrame"; 
            var formData = $(".edit_cate").serialize();
            console.log(formData)
            var cateName = $("#cate_name").val();
            if(cateName == ''){
                $("#msg").empty();
                h = '<label id="msg" style="color:red;"> 请填写此字段</label>';
                $(".name").after(h);
                return false;
            }
            $.ajax({
                url: '/member/gift-category/editcate',
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