<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>
<style>
    .add_bananer{
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
    <form class="add_bananer" id="giftType3"">
<!--        <div class="am-form-group" style="margin-top:10px">
            <label class="infoSpan">图片名称<span style="color: red">*</span>&nbsp;&nbsp;</label>
            <label><input type="text" name="pic_name" id="pic_name" class="need form-control" placeholder="图片名称" style="width:200px" required/></label>
        </div>-->
        <div class="am-form-group">
            <span style="display: inline-block;" class="infoSpan">文章标题<span style="color: red">*</span></span>
            <div>
                <input name="articleTitle"  type="text" class="form-control need" placeholder="文章标题" style="width:34%">
<!--                <span style="color: red;position: relative;left: -975px;top: -28px;float: right;" class="titleLen">0/27</span>-->
            </div>
            <div style="margin-top:5px">
                <span class="infoSpan">广告图片<span style="color: red">*</span>&nbsp;&nbsp;</span>
                <label>
                    <div>
                        <a class="buttomspan" onclick="$('#picture').click();">上传</a>
                        <a class="buttomspan" onclick="javascript:$('#picture').val('');$('#showimg').attr('src', '/image/u1529.png');$('#showimg').css({ width:'140px',height:'140px',});">| 删除</a>
                    </div>
                </label>
                <img src="/image/u1529.png" id="showimg" style="width: 140px;height: 140px" class="am-img-thumbnail">
<!--                <span style="color:red;font-size:14px;">图片宽高最佳为720x95,大小限制为50k以下</span>-->
                <input type="file" id="picture" class="imgupload" name="picture" required>
            </div>
            <span style="display: inline-block;">广告内容<span style="color: red">*</span></span>
            <div>
                <textarea id="editor_id" style="width:550px;height:500px;"></textarea>
            </div>
        </div>
       
<!--        <div class="am-form-group">
            <label class="infoSpan">跳转地址<span style="color: red"> </span>&nbsp;&nbsp;</label>
            <label><input type="text" name="jump_url" id="jump_url" class="form-control" placeholder="跳转地址"  style="width:300px"/></label>
       </div>-->
<!--        <div class="am-form-group">
            <label class="infoSpan">适用于<span style="color: red">*</span>&nbsp;&nbsp;</label>
            <label><select id="type" class="need form-control" name="type"  style="width:100px"><?php foreach ($picType as $key => $val): ?><option value="<?php echo $key; ?>"><?php echo $val ?></option><?php endforeach; ?></select></label>
        </div>-->
        <button class="am-btn am-btn-primary" id="addSubmit" >提交</button>
        <input  class="am-btn am-btn-primary" type="reset" />
        <button class="am-btn am-btn-primary" id="backSubmit" >返回</button>
        <label id="error_msg"> </label>
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
                'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|',
                 'insertfile', 'table', 'hr', 'emoticons', 'pagebreak',
                'anchor', 'link', 'unlink', '|'
            ],
            allowImageRemote:true,
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
        // if (file.size > 1024 * 50) {
        //     var now =  Math.ceil(file.size/1024);
        //     msgAlert("当前图片约为"+now+"k,图片大小不可超过50k");
        //     $('#showimg').attr("src", "/image/u1529.png");
        //     return false;
        // }
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
    //添加幻灯片
    $('#addSubmit').click(function () {
        // 取得HTML内容
//        html = editor.html();
        // 同步数据后可以直接取得textarea的value
        editor.sync();
        var filterContent  =$("#editor_id").val();
        if (filterContent=="") {
            msgAlert("文章内容不得空");
            return false;
        }
        document.forms[0].target = "rfFrame"; 
        a = 0;
        var formData = new FormData();
        var data = $(".add_bananer").serializeArray();
        formData.append("upfile", $("#picture").get(0).files[0]);
        formData.append("content", filterContent);
        $.each(data, function (i, field){
            formData.append(field.name, field.value);
        });
        if ($('#showimg').attr("src") == '/image/u1529.png') {
            msgAlert("请上传广告图")
            a++
            return false;
        }
        $(".need").each(function (i) {
        var text=$(this).val();
        if (text == ""||text == 0) {
                a++;
                $(this).focus();
                return false;
            }
        });
        if (a != 0) {
            msgAlert("请将带*的参数填写完整")
            return false;
        }
        $.ajax({
            url: '/website/pc-bananer/add-bananer',
            async: false,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data['code'] == 600) {
                     msgAlert(data['msg'], function () {
                        location.href = '/website/pc-bananer/index';
                    });
                } else {
                    msgAlert(data["msg"]);
                }
            }
        });
    })
    $("#backSubmit").click(function () {
        history.go(-1);
    })
</script>
