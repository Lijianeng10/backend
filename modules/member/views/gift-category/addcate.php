<div>
    <form class="am-form add_cate" id="doc-vld-msg">
        <fieldset>
            <div class="am-form-group">
                <label>所属类别<span style="color: red">*</span></label>
                <label><select  name="cate_parent"><?php foreach ($cate_list as $key => $val): ?><option value="<?php echo $key; ?>"><?php echo $val; ?>&nbsp;&nbsp;&nbsp;&nbsp;</option><?php endforeach; ?></select></label>
            </div>
            <div class="am-form-group">
                <label>类别名称<span style="color: red">*</span></label>
                <label class="name">
                    <input type="text" id="cate_name" name="cate_name" required/>
                </label>
            </div>
            <div class="am-form-group">
                <label>类别名称</label>
                <label><textarea  name="cate_remark" rows="2" style="width:380px" maxlength="250"></textarea></label>
            </div>
            
            <button type="button" class="am-btn am-btn-secondary" id="addSubmit" >提交</button>
            <input  class="am-btn am-btn-secondary" type="reset" />
            <label id="error_msg"> </label>
        </fieldset>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>

<script>

    $(function () {
        $('#addSubmit').click(function () {
            document.forms[0].target = "rfFrame"; 
            var formData = $(".add_cate").serialize();
            var cateName = $("#cate_name").val();
            if(cateName == ''){
                $("#msg").empty();
                h = '<label id="msg" style="color:red;"> 请填写此字段</label>';
                $(".name").after(h);
                return false;
            }
            $.ajax({
                url: '/member/gift-category/addcate',
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