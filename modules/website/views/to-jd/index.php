<div>
    <form class="am-form jdUrl" id="doc-vld-msg">
        <input type="hidden" name="jd_url_id" value="<?php echo $data['jd_url_id']; ?>">
        <div class="am-form-group">
            <label for="doc-vld-name-2-1">操作员</label>
            <input  name="opt_name" value="<?php echo $data['opt_name'] ?>" disabled="true">
        </div>
        <div class="am-form-group">
            <label for="doc-vld-name-2-1">跳转链接</label>
            <textarea  name="url" rows="10" style="width:500px" maxlength="250"><?php echo $data['url'] ?></textarea>
        </div>
        <div style="margin-top:2px;">
            <span class="infoSpan" style="display: inline-block;padding-left: 10px;">缩略图<span style="color: red">*</span>&nbsp;&nbsp;</span>
            <label>
                <div style="width: 140px;height: 140px; text-align: center;border: 1px solid #BECDD5;position: relative;float: left;padding: 2px" id="pic_up">
                    <img id="img_url" style="width: 100%; height: 100%" src="<?php echo empty($data['pic_url']) ? '/image/u1529.png' : $data['pic_url']; ?>">
                    <input type="file" style="width:100%;height: 100%;opacity: 0;position: absolute;top:0;left:0;" id="uploadPic" name="uploadPic">
                    <?php if (empty($data['pic_url'])): ?>
                        <a href="#" id="delPic" style="width:20px;height:20px;position:absolute;top:-6px;right:-6px;border-radius:100%;border:1px solid #fff;background-color:#C6C6C6;text-align:center;color:#fff;display: none" class="delPic">X</a> 
                    <?php else: ?>
                        <a href="#" id="delPic" style="width:20px;height:20px;position:absolute;top:-6px;right:-6px;border-radius:100%;border:1px solid #fff;background-color:#C6C6C6;text-align:center;color:#fff;" class="delPic">X</a> 
                    <?php endif; ?>
                </div>
            </label>
            <span style="color:red;font-size:14px;">图片大小限制为200k以下</span>
        </div>
        <div class="am-form-group">
            <button class="am-btn am-btn-secondary" id="urlBtn" >提交</button>
        </div>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>
<script>

    function getObjectURL(file) {
        var url = null;
        if (window.createObjectURL != undefined) { // basic
            url = window.createObjectURL(file);
        } else if (window.URL != undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file);
        } else if (window.webkitURL != undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file);
        }
        return url;
    }

    $(function () {
        $('#uploadPic').change(function () {
            var file = this.files[0];
            var scr = $('#img_url').attr("src");
            var objUrl = getObjectURL(this.files[0]);
            if (objUrl) {
                $('#img_url').attr("src", objUrl);
                $("#delPic").show();
            }
            if (!/.(jpg|jpeg|png)$/.test(file.name)) { 
                msgAlert("图片类型必须是.jpeg,jpg,png中的一种");
                $('#img_url').attr("src", scr); 
                $("#delPic").hide();    
            }
        });

        $('#pic_up').on('click', '#delPic', function () {
            $("#uploadPic").val("");
            $("#img_url").attr('src', '/image/u1529.png');
            $("#delPic").hide();
        });

        $('#urlBtn').click(function () {
            document.forms[0].target = "rfFrame";
            var formData = new FormData();
            var data = $(".jdUrl").serializeArray();
            formData.append("upfile", $("#uploadPic").get(0).files[0]);
            $.each(data, function (i, field) {
                formData.append(field.name, field.value);
            });
            $.ajax({
                url: '/website/to-jd/edit-url',
                async: false,
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(data['msg'], function () {
                            location.href = '/website/to-jd/index';
                        });
                    } else {
                        msgAlert(data['msg'], function () {
                            location.href = '/website/to-jd/index';
                        });
                    }
                }
            });
        })
    })
</script>