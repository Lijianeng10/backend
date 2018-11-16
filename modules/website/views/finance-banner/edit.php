<?php

use yii\helpers\Html;
use yii\grid\GridView;

?>
<style>
    .edit_bananer {
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
    <form class="edit_bananer">
        <input type="hidden" value="<?php echo $data["bananer_id"]; ?>" name="bananer_id">
        <div class="am-form-group">
            <div>
                <span style="display: inline-block;" class="infoSpan">标题</span>
                <label>
                    <input name="articleTitle" id="articleTitle" type="text" class="form-control"
                           placeholder="文章标题" style="width:200px" value="<?php echo $data["pic_name"]; ?>">
                    <!--                <span style="color: red;position: relative;left: -975px;top: -28px;float: right;" class="titleLen">0/27</span>-->
                </label>
            </div>
            <div style="margin-top:5px">
                <span class="infoSpan">APP图片<span style="color: red">*</span>&nbsp;&nbsp;</span>
                <label>
                    <div>
                        <a class="buttomspan" onclick="$('#picture').click();">上传</a>
                        <a class="buttomspan"
                           onclick="javascript:$('#picture').val('');$('#showimg').attr('src', '/image/u1529.png');$('#showimg').css({ width:'140px',height:'140px',})">|
                            删除</a>
                    </div>
                </label>
                <img src="<?php echo $data["pic_url"]; ?>" id="showimg" style="width: 140px;height: 140px"
                     class="am-img-thumbnail">
                <span style="color:red;font-size:14px;">图片宽高最佳为720x315,大小限制为200k以下</span>
                <input type="file" id="picture" class="imgupload" name="picture" required>
            </div>
<!--            <div style="margin-top:5px">
                <span class="infoSpan">P C图片<span style="color: red">*</span>&nbsp;&nbsp;</span>
                <label>
                    <div>
                        <a class="buttomspan" onclick="$('#picture2').click();">上传</a>
                        <a class="buttomspan"
                           onclick="javascript:$('#picture2').val('');$('#showimg2').attr('src', '/image/u1529.png');$('#showimg2').css({ width:'140px',height:'140px',})">|
                            删除</a>
                    </div>
                </label>
                <img src="<?php
//                if($data["pc_pic_url"]!=''){
//                    echo $data["pc_pic_url"];
//                }else{
//                    echo '/image/u1529.png';
//                };
                ?>" id="showimg2" style="width: 140px;height: 140px"
                     class="am-img-thumbnail">
                <span style="color:red;font-size:14px;">图片宽高最佳为1920x320</span>
                <input type="file" id="picture2" class="imgupload" name="picture2" required>
            </div>-->
<!--            <div style="margin: 10px 0;">-->
<!--                <span class="infoSpan">使用范围<span style="color: red">*</span>&nbsp;&nbsp;</span>-->
<!--                --><?php
//                echo Html::dropDownList("", $data["use_type"], $picUse, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;", "id" => "use_type"]);
//                ?>
<!--            </div>-->
            <!--            投放区域-->
<!--            <div style="margin: 10px 0;">-->
<!--                <span class="infoSpan">投放区域<span style="color: red">&nbsp;&nbsp;</span>&nbsp;&nbsp;</span>-->
<!--                --><?php
//                echo Html::dropDownList("", $data["area"], $picArea, ["class" => "form-control", "id" => "area", "style" => "width:120px;display:inline;margin-left:5px;"]);
//                ?>
<!--            </div>-->
            <!--            跳转类型-->
            <div style="margin: 10px 0;">
                <span class="infoSpan">跳转类型<span style="color: red">*</span>&nbsp;&nbsp;</span>
                <?php
                echo Html::dropDownList("", $data["jump_type"], $picJump, ["class" => "form-control", "id" => "jumpType", "style" => "width:120px;display:inline;margin-left:5px;"]);
                ?>
            </div>
            <div id="jump" style="display:none;">
                <div>
                    <span class="infoSpan">跳转标题</span>
                    <label><input type="text" id="jump_title" class="form-control"
                                  value="<?php echo $data["jump_title"]; ?>" style="width:300px"/></label>
                </div>
                <div>
                    <span class="infoSpan">跳转地址</span>
                    <label><input type="text" id="jump_url" class="form-control"
                                  value="<?php echo $data["jump_url"]; ?>" style="width:300px"/></label>
                    <span style="color:red;font-size:14px;">跳转地址开头请加上http或者https</span>
                </div>

            </div>
            <div id="adContent" style="display:none;">
                <span style="display: inline-block;">广告内容<span style="color: red">&nbsp;</span></span>
                <textarea id="editor_id" style="width:550px;height:500px;"><?php echo $data["content"]; ?></textarea>
                <!--                <span style="color: red;position: absolute;right: 35px;top: 133px;" class="contentLen">字数:0</span>
                                <div id="articleContent" style="margin-right:10px;margin-top:10px;font-size:14px;"></div>
                                <div class="img_limit" style="font-size: 14px;color: #9e9e9e;float: left;width: 100%;">注：发文字数不少于200字；建议上传图片宽高不超1000px, 大小不超1M</div>-->
            </div>
        </div>

        <!--        <div class="am-form-group">
            <label class="infoSpan">跳转地址<span style="color: red"> </span>&nbsp;&nbsp;</label>
            <label><input type="text"value="<?php echo $data["jump_url"]; ?>" name="jump_url" id="jump_url" class="form-control" placeholder="跳转地址"  style="width:200px"/></label>
        </div>-->
        <?php
        //        echo '<div class="am-form-group">';
        //        echo Html::label("适用于 ","",["style"=>"margin-left:-14px","class"=>"infoSpan"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
        //        echo Html::dropDownList("type", $data["type"], $picType, ["class" => "need form-control", "id" => "type", "style" => "width:100px;display:inline;margin-left:12px;margin-bottom:10px;"]);
        //        echo '</div >';
        //        echo '<div class="am-form-group">';
        //        echo Html::label("状态 ","",["style"=>"margin-left:-14px","class"=>"infoSpan"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
        //        echo Html::dropDownList("status", $data["status"], $picStatus, ["class" => "need form-control", "id" => "status", "style" => "width:100px;display:inline;margin-left:12px;margin-bottom:10px;"]);
        //        echo '</div >';
        ?>
        <button class="am-btn am-btn-primary" id="addSubmit">提交</button>
        <input class="am-btn am-btn-primary" type="reset"/>
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
    KindEditor.ready(function (K) {
        window.editor = K.create('#editor_id', {
            minWidth: '550px',
            height: '550px',
            items: [
                'undo', 'redo', '|', 'preview', 'template', 'cut', 'copy', 'paste',
                '|', 'justifyleft', 'justifycenter', 'justifyright',
                'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                'superscript', 'clearhtml', 'quickformat', 'selectall', '|', '/',
                'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
                'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image',
                'insertfile', 'table', 'hr', 'emoticons', 'pagebreak',
                'anchor', 'link', 'unlink', '|'
            ],
            allowImageRemote: true,
            uploadJson: "/tools/img/upload-img",
            afterUpload: function () {
                this.sync();
            }, //图片上传后，将上传内容同步到textarea中
            afterBlur: function () {
                this.sync();
            },   ////失去焦点时，将上传内容同步到textarea中
        });
        // 设置HTML内容
        editor.html();
    });
    $(function(){
        var jump = $("#jumpType").val();
        if (jump == 1) {
            $("#jump").hide();
            $("#adContent").show();
        } else if (jump == 2) {
            $("#jump").show();
            $("#adContent").hide();
        } else {
            $("#jump").hide();
            $("#adContent").hide();
        }
    })
    
    
    
    //广告图片
    $('#picture').change(function () {
        var file = this.files[0];
        var scr = $('#showimg').attr("src");
        var objUrl = getObjectURL(this.files[0]);
        if (objUrl) {
            $('#showimg').attr("src", objUrl);
        }
        var imgType = ["image/png", "image/jpg", "image/jpeg", "image/gif"];
        if ($.inArray(file.type, imgType) == "-1") {
            msgAlert("图片类型必须是.jpeg,jpg,png,gif中的一种");
            $('#showimg').attr("src", "/image/u1529.png")
            return false;
        }
        if (file.size > 1024 * 200) {
            var now = Math.ceil(file.size / 1024);
            msgAlert("当前图片约为" + now + "k,图片大小不可超过200k");
            $('#showimg').attr("src", "/image/u1529.png");
            return false;
        }
        $('#showimg').css({
            width: "140px",
            height: "140px",
        })
    });
    //PC图片
    $('#picture2').change(function () {
        var file = this.files[0];
        var scr = $('#showimg2').attr("src");
        var objUrl = getObjectURL(this.files[0]);
        if (objUrl) {
            $('#showimg2').attr("src", objUrl);
        }
        var imgType = ["image/png", "image/jpg", "image/jpeg", "image/gif"];
        if ($.inArray(file.type, imgType) == "-1") {
            msgAlert("图片类型必须是.jpeg,jpg,png,gif中的一种");
            $('#showimg2').attr("src", "/image/u1529.png")
            return false;
        }
        $('#showimg2').css({
            width: "140px",
            height: "140px",
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
        // 同步数据后可以直接取得textarea的value
        editor.sync();
        var filterContent = $("#editor_id").val();
        // var useType = $("#use_type").val();
        // var area = $("#area").val();
        var jumpType = $("#jumpType").val();
        var jump_url = $("#jump_url").val();
        var jump_title = $("#jump_title").val();
        // if (useType == "") {
        //     msgAlert("请选择使用范围");
        //     return false;
        // }
        if (jumpType == "") {
            msgAlert("请选择跳转类型");
            return false;
        }
        //咕啦自用才需要上传PC图片
        // if (useType == 1) {
        //     if ($('#showimg2').attr("src") == '/image/u1529.png') {
        //         msgAlert("请上传PC图片")
        //         return false;
        //     }
            // if (filterContent=="") {
            //     msgAlert("文章内容不得空");
            //     return false;
            // }
        // }
        //判断跳转类型
        if (jumpType == 1) {
            if (filterContent == "") {
                msgAlert("文章内容不得为空");
                return false;
            }
        } else if (jumpType == 2) {
            if (jump_url == "") {
                msgAlert("跳转链接不得为空");
                return false;
            }
        }
        if ($('#showimg').attr("src") == '/image/u1529.png') {
            msgAlert("请上传广告图");
            return false;
        }
        document.forms[0].target = "rfFrame";
        a = 0;
        var formData = new FormData();
        var data = $(".edit_bananer").serializeArray();
        formData.append("upfile", $("#picture").get(0).files[0]);
//        formData.append("upfile2", $("#picture2").get(0).files[0]);
        formData.append("content", filterContent);
        // formData.append("use_type", useType);
        formData.append("jump_url", jump_url);
        // formData.append("area", area);
        formData.append("jumpType", jumpType);
        formData.append("jump_title", jump_title);
        $.each(data, function (i, field) {
            formData.append(field.name, field.value);
        });
        $(".need").each(function (i) {
            var text = $(this).val();
            if (text == "") {
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
            url: '/website/finance-banner/edit',
            async: false,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data['code'] != 600) {
                    msgAlert(data["msg"]);
                } else {
                    msgAlert(data['msg'], function () {
                        location.href = '/website/finance-banner/index';
                    });
                }
            }
        });
    })
    $("#backSubmit").click(function () {
        history.go(-1);
    })
    $("#jumpType").change(function () {
        var type = $(this).val();
        if (type == 1) {
            $("#jump").css("display", "none")
            $("#adContent").css("display", "block");
        } else if (type == 2) {
            $("#jump").css("display", "block");
            $("#adContent").css("display", "none");
        } else {
            $("#adContent").css("display", "none");
            $("#jump").css("display", "none");
        }


    })
</script>
