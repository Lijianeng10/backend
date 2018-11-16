<div>
    <form class="am-form authen" id="doc-vld-msg">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="user_authen" id="user_authen">
        <div class="am-form-group">
            <label for="doc-vld-name-2-1">审核说明</label>
            <textarea  name="authen_remark" rows="5" style="width:380px" maxlength="250"></textarea>
            <span style="color:#8A8A91">审核说明不可超过250个字符</span>
        </div>
        <div class="am-form-group">
        <button class="am-btn am-btn-secondary" id="agree" >审核通过</button>
        <button class="am-btn am-btn-secondary" id="disagree" >审核不通过</button>
        <button class="am-btn am-btn-secondary" id="backSubmit" >返回</button>
        <label id="error_msg"> </label>
        </div>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>
<script>
    $(function(){
        $('#agree').click(function(){
            document.forms[1].target = "rfFrame";
            $('#user_authen').val(1);
            formData = $('.authen').serialize();
//            console.log(formData);
            $.ajax({
                url: '/member/list/review-member',
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
                             location.href = '/member/list/index';
                        });
                    }
                }
            });
        })
        $('#disagree').click(function(){
            document.forms[1].target = "rfFrame";
            $('#user_authen').val(3);
            formData = $('.authen').serialize();
//            console.log(formData);
            $.ajax({
                url: '/member/list/review-member',
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
                             location.href = '/member/list/index';
                        });
                    }
                }
            });
        })
        $("#backSubmit").click(function(){
            location.href = '/member/list/index';
        })
    })
</script>