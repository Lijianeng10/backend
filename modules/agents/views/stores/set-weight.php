<div>
    <form class="am-form set-weight" id="doc-vld-msg">
        <fieldset>
            <div class="am-form-group">
                <label>门店名称：<?php echo $data['store_name']?><span style="color: red"></span></label>
            </div>
            <input type="hidden" name="store_code" value="<?php echo $data['store_code'];?>">
            <div class="am-form-group">
                <label>权重<span style="color: red">*</span></label>
                <label class="name">
                    <input type="text" id="cate_name" name="store_weight" value="<?php echo $data['weight'];?>" required/>
                </label>
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
            var formData = $(".set-weight").serialize();
            console.log(formData)
            var cateName = $("#store_weight").val();
            if(cateName == ''){
                $("#msg").empty();
                h = '<label id="msg" style="color:red;"> 请填写此字段</label>';
                $(".name").after(h);
                return false;
            }
            $.ajax({
                url: '/agents/stores/set-weight',
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