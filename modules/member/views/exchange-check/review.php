<div>
    <form class="am-form authen" id="doc-vld-msg">
        <input type="hidden" name="exchange_id" value="<?php echo $exchange_id; ?>">
        <input type="hidden" name="ex_authen" id="ex_authen">
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
            $('#ex_authen').val(2);
            formData = $('.authen').serialize();
            $.ajax({
                url: '/member/exchange-check/review',
                async: false,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                       msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                             location.href = '/member/exchange-check/index';
                        });
                    }
                }
            });
        })
        $('#disagree').click(function(){
            document.forms[1].target = "rfFrame";
            $('#ex_authen').val(3);
            formData = $('.authen').serialize();
//            console.log(formData);
            $.ajax({
                url: '/member/exchange-check/review',
                async: false,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                             location.href = '/member/exchange-check/index';
                        });
                    }
                }
            });
        })
        $("#backSubmit").click(function(){
            location.href = '/member/exchange-check/index';
        })
    })
</script>