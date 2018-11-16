<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>
<style>
    .add_push{
        width: 95%;
        margin: 0 auto;
    }
    .w-e-text-container{
        height: 400px !important;
    }
    .infoSpan{
        width: 90px;
        /*text-align: right;*/
    }
    #couponsStart{
        width:145px;
        display:inline;
    }
    #couponsEnd{
        width:145px;
        display:inline;
        margin-left:15px;
    }
    #addSubmit{
        margin-left: 20px;
    }
</style>
<div>
    <form class="add_push">
    <!--        <div class="am-form-group" style="margin-top:10px">
                <label class="infoSpan">图片名称<span style="color: red">*</span>&nbsp;&nbsp;</label>
                <label><input type="text" name="pic_name" id="pic_name" class="need form-control" placeholder="图片名称" style="width:200px" required/></label>
            </div>-->
    <div class="am-form-group">

        <div style="margin-top: 10px;">
            <span style="display: inline-block;" class="infoSpan">推送标题<span style="color: red">*</span></span>
            <input id="title"  type="text" class="form-control need" placeholder="文章标题" style="display:inline-block;width:300px">
        </div>
        <div style="margin-top: 10px;">
            <span class="infoSpan">推送类型<span style="color: red">*</span>&nbsp;&nbsp;</span>
            <label><select id="type" class="need form-control" name="type"  style="width:100px"><?php foreach ($type as $key => $val): ?><option value="<?php echo $key; ?>"><?php echo $val ?></option><?php endforeach; ?></select></label>
        </div>
        <!--            跳转类型-->
        <div style="margin: 10px 0;display: none" id="jump-type">
            <span class="infoSpan">跳转类型<span style="color: red">*</span>&nbsp;&nbsp;</span>
            <label>
                <select id="jumpType" class="need form-control"  style="width:100px">
                    <?php foreach ($picJump as $key => $val): ?><option value="<?php echo $key; ?>"><?php echo $val ?></option><?php endforeach; ?>
                </select>
            </label>
        </div>
<!--        <div style="margin-top: 10px;">-->
<!--            <span class="infoSpan">推送用户<span style="color: red">*</span>&nbsp;&nbsp;</span>-->
<!--            <label>-->
<!--                <select id="send_type" class="need form-control" name="send_type"  style="width:100px">-->
<!--                    --><?php //foreach ($sendType as $key => $val): ?><!--<option value="--><?php //echo $key; ?><!--">--><?php //echo $val ?><!--</option>--><?php //endforeach; ?>
<!--                </select>-->
<!--            </label>-->
<!--        </div>-->
        <div style="margin-top: 10px;display: none" id="jump">
            <span style="display: inline-block;" class="infoSpan">跳转地址<span style="color: red"></span></span>
            <input name="jump_url"  type="text" id="jump_url" class="form-control" placeholder="跳转地址" style="display:inline-block;width:60%">
        </div>
        <div style="margin-top:5px;display: none" id="img">
            <span class="infoSpan">缩略图<span style="color: red"></span>&nbsp;&nbsp;</span>
            <label>
                <div>
                    <a class="buttomspan" onclick="$('#picture').click();">上传</a>
                    <a class="buttomspan" onclick="javascript:$('#picture').val('');$('#showimg').attr('src', '/image/u1529.png');$('#showimg').css({ width:'140px',height:'140px',});">| 删除</a>
                </div>
            </label>
            <img src="/image/u1529.png" id="showimg" style="width: 140px;height: 140px" class="am-img-thumbnail">
            <span style="color:red;font-size:14px;">图片宽高最佳为680x300,大小限制为200k以下</span>
            <input type="file" id="picture" class="imgupload" name="picture" required>
        </div>
        <div style="display:none" id="adContent">
            <span style="display: inline-block;">推送内容<span style="color: red">*</span></span>
            <div>
                <textarea id="editor_id" name="content" style="width:550px;height:200px;"></textarea>
            </div>
        </div>

    </div>
    <button class="am-btn am-btn-primary" id="addSubmit" >确定</button>
    <button class="am-btn am-btn-primary" id="backSubmit" >返回</button>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>
<script charset="utf-8" src="/kindeditor/kindeditor-all.js"></script>
<script charset="utf-8" src="/kindeditor/lang/zh_CN.js"></script>
<script>
    //     关闭过滤模式，保留所有标签
    KindEditor.options.filterMode = false;
    KindEditor.ready(function(K) {
        window.editor = K.create('#editor_id',{
            minWidth : '550px',
            height:'550px',
            items:[
                'undo', 'redo', '|', 'preview', 'template', 'cut', 'copy', 'paste',
                '|', 'justifyleft', 'justifycenter', 'justifyright',
                'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                'superscript', 'clearhtml', 'quickformat', 'selectall', '|','/',
                'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
                'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|','image',
                'insertfile', 'table', 'hr', 'emoticons', 'pagebreak',
                'anchor', 'link', 'unlink', '|'
            ],
            allowImageRemote:true,
            uploadJson:"/tools/img/upload-img",
            afterUpload: function(){this.sync();}, //图片上传后，将上传内容同步到textarea中
            afterBlur: function(){this.sync();},   ////失去焦点时，将上传内容同步到textarea中
        });
        // 设置HTML内容
        editor.html();
    });
    //广告图片
    $('#picture').change(function () {
        var file = this.files[0];
        var scr = $('#showimg').attr("src");
        var objUrl = getObjectURL(this.files[0]);
        if (objUrl) {
            $('#showimg').attr("src", objUrl);
        }
        var imgType=["image/png","image/jpg","image/jpeg","image/gif"];
        if ($.inArray(file.type,imgType)=="-1") {
            msgAlert("图片类型必须是.jpeg,jpg,png,gif中的一种");
            $('#showimg').attr("src", "/image/u1529.png")
            return false;
        }
        if (file.size > 1024 * 200) {
            var now =  Math.ceil(file.size/1024);
            msgAlert("当前图片约为"+now+"k,图片大小不可超过200k");
            $('#showimg').attr("src", "/image/u1529.png");
            return false;
        }
        $('#showimg').css({
            width:"140px",
            height:"140px",
        })
    });
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
    //日期插件
    laydate.render({
        elem: '#pushTime',//指定元素
        type: 'datetime'
      });
    $("#addSubmit").click(function(){
        // 同步数据后可以直接取得textarea的value
        editor.sync();
        var title  = $("#title").val();
        var type  = $("#type").val();
        var filterContent  =$("#editor_id").val();
        var jumpType  = $("#jumpType").val();
        var jump_url  = $("#jump_url").val();
        if(title==""){
            msgAlert("请输入推送标题");
            return false;
        }
        if(type==""){
            msgAlert("请选择推送类型");
            return false;
        }
        if(type==23){
            if(jumpType==""){
                msgAlert("请选择跳转类型");
                return false;
            }
            //判断跳转类型
            if(jumpType==1){
                if (filterContent=="") {
                    msgAlert("推送内容不得为空");
                    return false;
                }
            }else if(jumpType==2){
                if (jump_url=="") {
                    msgAlert("跳转链接不得为空");
                    return false;
                }
            }
        }else {
            if (filterContent=="") {
                msgAlert("推送内容不得为空");
                return false;
            }
        }
        document.forms[0].target = "rfFrame";
        a = 0;
        var formData = new FormData();
        var data = $(".add_push").serializeArray();
        formData.append("title", title);
        formData.append("type", type);
        formData.append("jumpType", jumpType);
        formData.append("jump_url", jump_url);
        formData.append("content", filterContent);
        if(type==23){
            if ($('#showimg').attr("src") == '/image/u1529.png') {
                msgAlert("请上传缩略图")
                return false;
            }else{
                formData.append("upfile", $("#picture").get(0).files[0]);
            }
        }

        $.ajax({
            url: '/website/chat-push/add-chat-push',
            async: false,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data['code'] == 600) {
                    msgAlert(data['msg'], function () {
                        location.href = '/website/chat-push/index';
                    });
                } else {
                    msgAlert(data["msg"]);
                }
            }
        });
    })
    /**
     * 根据跳转类型显示隐藏
     */
    $("#jumpType").change(function () {
        var type = $(this).val();
        if(type==1){
            $("#jump").css("display","none")
            $("#adContent").css("display","block");
        }else if(type==2){
            $("#jump").css("display","block");
            $("#adContent").css("display","none");
        }else{
            $("#adContent").css("display","none");
            $("#jump").css("display","none");
        }
    })
    /**
     * 消息类型选择
     */
    $("#type").change(function () {
        var type = $(this).val();
        if(type==21){
            $("#img").css("display","none")
            $("#jump-type").css("display","none")
            $("#adContent").css("display","block");
        }else if(type==23){
            $("#img").css("display","block");
            $("#jump-type").css("display","block");
            $("#adContent").css("display","none");
        }else{
            $("#jump-type").css("display","none")
            $("#img").css("display","none");
            $("#jump-type").css("display","none");
        }
    })
    $("#backSubmit").click(function () {
        history.go(-1);
    })
</script>