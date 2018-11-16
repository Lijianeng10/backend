<?php

use yii\helpers\Html;
use yii\grid\GridView;

?>
<style>
    .edit_push {
        width: 95%;
        margin: 0 auto;
    }

    .w-e-text-container {
        height: 400px !important;
    }

    .infoSpan {
        width: 90px;
        /*text-align: right;*/
    }

    #couponsStart {
        width: 145px;
        display: inline;
    }

    #couponsEnd {
        width: 145px;
        display: inline;
        margin-left: 15px;
    }

    #addSubmit {
        margin-left: 20px;
    }
</style>
<div>
    <form class="edit_push">
        <input type="hidden" value="<?php echo $data["chat_push_id"]; ?>" name="chat_push_id">
        <div class="am-form-group">
            <div style="margin-top: 10px;">
                <span style="display: inline-block;" class="infoSpan">推送标题<span style="color: red">*</span></span>
                <input readonly="readonly" name="title" type="text" class="form-control need" placeholder="推送标题"
                       style="display:inline-block;width:300px" value="<?php echo $data["title"]; ?>">
                <!--                <span style="color: red;position: relative;left: -975px;top: -28px;float: right;" class="titleLen">0/27</span>-->
            </div>
            <div style="margin-top: 10px;" id="jump-type">
                <span  class="infoSpan">推送类型<span style="color: red">*</span>&nbsp;&nbsp;</span>
                <label>
                    <select readonly="readonly" id="type" class="need form-control" name="type" style="width:100px">
                        <?php foreach ($type as $key => $val) {
                            if ($key == $data["type"]) {
                                echo "<option selected = 'selected' value='" . $key . "'>" . $val . "</option>";
                            } else {
                                echo "<option  value='" . $key . "'>" . $val . "</option>";
                            }
                        } ?>
                    </select>
                </label>
            </div>
            <div style="margin-top: 10px;">
                <span class="infoSpan">跳转类型<span style="color: red">*</span>&nbsp;&nbsp;</span>
                <label>
                    <select  readonly="readonly" id="jumpType" class="need form-control" name="jumpType" style="width:100px">
                        <?php foreach ($picJump as $key => $val) {
                            if ($key == $data["jump_type"]) {
                                echo "<option selected = 'selected' value='" . $key . "'>" . $val . "</option>";
                            } else {
                                echo "<option  value='" . $key . "'>" . $val . "</option>";
                            }
                        } ?>
                    </select>
                </label>
            </div>
            <div style="margin-top: 10px;" id="jump">
                <span style="display: inline-block;" class="infoSpan">跳转地址<span style="color: red"></span></span>
                <input readonly="readonly" name="jump_url" type="text" class="form-control" placeholder="跳转地址"
                       style="display:inline-block;width:300px" value="<?php
                if ($data["jump_type"] == 1) {
                    echo "";
                } else {
                    echo $data["jump_url"];
                } ?>">
            </div>
            <div style="margin-top:5px" id="img">
                <span class="infoSpan">缩略图<span style="color: red">*</span>&nbsp;&nbsp;</span>
                <img src="<?php echo $data["img_url"] != "" ? $data["img_url"] : '/image/u1529.png'; ?>" id="showimg"
                     style="width: 140px;height: 140px;margin-left: 25px;" class="am-img-thumbnail">
            </div>
            <div id="adContent">
                <span style="display: inline-block;">推送内容<span style="color: red" readonly="readonly">*</span></span>
                <div>
                    <textarea id="editor_id" style="width:550px;height:200px;"><?php echo $data["content"]; ?></textarea>
                </div>
            </div>
        </div>
        <button class="am-btn am-btn-primary" id="backSubmit">返回</button>
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
    $("#backSubmit").click(function () {
        document.forms[0].target = "rfFrame";
        // location.href = '/website/chat-push/index';
        history.go(-1);
    })
</script>
