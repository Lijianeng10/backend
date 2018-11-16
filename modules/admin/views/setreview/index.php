<div>
    <form class="am-form" id="doc-vld-msg">
        <button class="am-btn am-btn-secondary" id="addSubmit" ><?php echo $data['is_open'] == 1 ? '开启屏蔽' : '关闭屏蔽'?></button>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>

<script>
    $(function () {
        $('#addSubmit').click(function () {
            document.forms[0].target = "rfFrame";
            $.ajax({
                url: '/admin/setreview/do-set',
                async: false,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(data['msg'], function () {
                             location.reload();
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