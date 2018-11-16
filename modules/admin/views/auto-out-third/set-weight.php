<div>
    <form class="am-form set-weight" id="doc-vld-msg">
        <fieldset>
            <div class="am-form-group">
                <label>出票方：<?php echo $data['third_name']?><span style="color: red"></span></label>
            </div>
            <input type="hidden" name="third_id" value="<?php echo $data['auto_out_third_id'];?>">
            <div class="am-form-group">
                <label>权重<span style="color: red">*</span></label>
                <label class="name">
                    <input type="text" id="third_weight" name="third_weight" value="<?php echo $data['weight'];?>" required/>
                </label>
            </div>
            <button type="button" class="am-btn am-btn-secondary" id="setWeight" >提交</button>
            <label id="error_msg"> </label>
        </fieldset>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>
<script>

    $(function () {
        $('#setWeight').click(function () {
            document.forms[0].target = "rfFrame"; 
            var formData = $(".set-weight").serialize();
            var thirdWeight = $("#third_weight").val();
            if(thirdWeight == ''){
                $("#msg").empty();
                h = '<label id="msg" style="color:red;"> 请填写此字段</label>';
                $(".name").after(h);
                return false;
            }
            $.ajax({
                url: '/admin/auto-out-third/set-weight',
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