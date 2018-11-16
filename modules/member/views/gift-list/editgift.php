<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>
<style>
    .am-form-group{
        width:60%;
    }
    .infoSpan{
        width: 90px;
        text-align: right;
    }
    /*    #couponsTime{
            margin-left: 25px;
        }*/
    #couponsStart{
        width:145px;
        display:inline;
    }
    #couponsEnd{
        width:145px;
        display:inline;
        margin-left:15px;
    }
</style>
<div>
    <legend>编辑礼品</legend>
    <!--实品礼品表单-->
    <form class="add_gift" id="giftType3"">
    <input type="hidden" value="<?php echo $data["gift_id"]; ?>" name="gift_id">
    <?php
    echo '<li style="margin-top:10px">';
    echo Html::label("礼品类型  ","",["style"=>"margin-left:10px"]) . Html::tag("span", "*", ["class" => "requiredIcon infoSpan"]);
    echo Html::dropDownList("giftType",$data["type"], $giftType, ["class" => "need form-control","disabled"=>"true", "id" => "giftType", "style" => "width:120px;display:inline;margin-left:12px;margin-bottom:10px;"]);
    echo Html::tag("span", "新增礼品请先选择礼品类型，勿先填写礼品信息", ["style" => "color:#aaa;font-size:14px;padding-left:10px"]);
    ?>
<!--    <div class="am-form-group" id="coupons" style="display:none">-->
<!--        <label class="infoSpan">优惠批次<span style="color: red">*</span>&nbsp;&nbsp;</label>-->
<!--        <label ><select  class="need form-control" name="batch"  id="couponsBatch">--><?php //foreach ($batch as $key => $val): ?><!--<option value="--><?php //echo $val; ?><!--">--><?php //echo $val ?><!--</option>--><?php //endforeach; ?><!--</select></label>-->
    </div>
    <div class="am-form-group" id="couponsTime" style="display:none">
        <label class="infoSpan">有效期&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <label><input type="text"  id="couponsStart" class=" form-control" /> - <input type="text"  id="couponsEnd" class=" form-control"></label>
    </div>
    <div class="am-form-group">
        <label class="infoSpan">礼品名称<span style="color: red">*</span>&nbsp;&nbsp;</label>
        <label><input type="text" name="gift_name" id="gift_name" readonly="readonly" class="need form-control" value="<?php echo $data["gift_name"];?>" placeholder="礼品名称" style="width: 300px;" required/></label>
    </div>
    <div class="am-form-group">
        <label class="infoSpan">礼品副标题<span style="color: red"> </span>&nbsp;&nbsp;</label>
        <label><input type="text" name="subtitle" id="subtitle" readonly="readonly" class=" form-control" value="<?php echo $data["subtitle"];?>" placeholder="礼品副标题" style="width: 300px;"/></label>
    </div>

    <div class="am-form-group">
        <label class="infoSpan">库存数量<span style="color: red">*</span>&nbsp;&nbsp;</label>
        <label><input type="text" name="in_stock" id="in_stock" readonly="readonly" class="need form-control" value="<?php echo $data["in_stock"];?>" placeholder="库存" data-validation-message="请填写正确的正整数" pattern="^\d+$" required/></label>
    </div>

    <!--        <div class="am-form-group">
                <label for="doc-vld-name-2-1">礼品简码<span style="color: red">*</span>&nbsp;&nbsp;</label>
                <label><input type="text" id="doc-vld-528" class="need form-control" name="gift_code" data-validation-message="请填写正确的编码,只能含有数字或字母" placeholder="礼品简码" required/></label>
            </div>-->

    <div class="am-form-group">
        <label class="infoSpan">礼品分类<span style="color: red">*</span>&nbsp;&nbsp;</label>
        <?php
        echo Html::dropDownList("gift_cate",$data["gift_category"], $cateList, ["class" => "need form-control","disabled"=>"true", "id" => "gift_cate", "style" => "width:150px;display:inline;margin-left:12px;margin-bottom:10px;"]);
        ?>
    </div>
    <?php
    echo '<li style="margin-bottom:10px">';
    echo Html::label("活动时间  ", "",["style"=>"margin-left:10px"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
    echo Html::input("text", "startdate",$data["start_date"], ["id" => "startdate","disabled"=>"true", "class" => " need form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:150px;display:inline;margin-left:15px;"]);
    echo " - ";
    echo Html::input("text", "enddate", $data["end_date"], ["id" => "enddate", "disabled"=>"true","class" => "need form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:150px;display:inline;margin-left:5px;"]);
    echo '</li>';
    ?>
    <div class="am-form-group">
        <label class="infoSpan">所需等级<span style="color: red">*</span>&nbsp;&nbsp;</label>
        <?php
        echo Html::dropDownList("gift_leave",$data["gift_level"], $levelAry, ["class" => "need form-control","disabled"=>"true", "id" => "gift_leave", "style" => "width:120px;display:inline;margin-left:12px;margin-bottom:10px;"]);
        ?>
    </div>

    <div class="am-form-group">
        <label class="infoSpan">所需咕币<span style="color: red"> </span>&nbsp;&nbsp;</label>
        <label><input type="text" name="gift_glcoin" readonly="readonly" class="form-control" value="<?php echo $data["gift_glcoin"];?>" placeholder="兑换所需咕币" data-validation-message="请填写正确的正整数" pattern="^\d+$" required/></label>
    </div>
    <div class="am-form-group">
        <label class="infoSpan">电信积分<span style="color: red"> </span>&nbsp;&nbsp;</label>
        <label><input type="text" name="gift_integral" readonly="readonly" class="form-control" value="<?php echo $data["gift_integral"];?>" placeholder="兑换所需电信积分" data-validation-message="请填写正确的正整数" pattern="^\d+$" required/></label>
    </div>

    <div class="am-form-group">
        <label class="infoSpan">缩略图<span style="color: red">*</span>&nbsp;&nbsp;</label>
        <label>
            <div>
                <a class="buttomspan" onclick="$('#picture').click();">上传</a>
                <a class="buttomspan" onclick="javascript:$('#picture').val('');$('#showimg').attr('src', '/image/u1529.png');">| 删除</a>
            </div>
        </label>
        <img src="<?php echo $data["gift_picture"];?>" id="showimg" style="width: 140px;height: 140px" class="am-img-thumbnail">
        <span style="font-size:14px;">图片尺寸限制为306*216，大小限制为1M以下.</span>
        <input type="file" id="picture" class="imgupload" name="gift_img" required>
    </div>

    <div class="am-form-group">
        <label class="infoSpan">详情图<span style="color: red">*</span>&nbsp;&nbsp;</label>
        <label>
            <div>
                <a class="buttomspan" onclick="$('#picture2').click();">上传</a>
                <a class="buttomspan" onclick="javascript:$('#picture').val('');$('#showimg2').attr('src', '/image/u1529.png');">| 删除</a>
            </div>
        </label>
        <img src="<?php echo $data["gift_picture2"];?>" id="showimg2" style="width: 250px;height: 200px" class="am-img-thumbnail">
        <span style="font-size:14px;">图片尺寸限制为720*510，大小限制为1M以下.</span>
        <input type="file" id="picture2" class="imgupload" name="gift_img2" required>
    </div>

    <div class="am-form-group">
        <label class="infoSpan">备注<span style="color: red">*</span>&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <textarea id="editor_id" style="width:550px;height:500px;">
            <?php echo $data["gift_remark"];?>
        </textarea>
    </div>
    <button type="button" class="am-btn am-btn-secondary" id="addSubmit" >提交</button>
    <input  class="am-btn am-btn-secondary" type="reset" />
    <button class="am-btn am-btn-secondary" id="backSubmit" >返回</button>
    <label id="error_msg"> </label>
    </form>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>
<script charset="utf-8" src="/kindeditor/kindeditor-all.js"></script>
<script charset="utf-8" src="/kindeditor/lang/zh_CN.js"></script>
<script>
    $(function () {
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
        //选择礼品类型
        $("#giftType").change(function () {
            var type = $(this).val();
            if (type == 1) {
                $("#coupons").css("display", "block");
            } else {
                $("#couponsBatch").val("请选择");
                $("#coupons").css("display", "none");
                $("#couponsTime").css("display", "none");
                $("#in_stock").val("");
                $("#gift_name").val("");
                $("#in_stock").attr("readonly", false);
                $("#gift_name").attr("readonly", false);
            }
        })
        //获取所选优惠券批次信息
        $("#couponsBatch").change(function () {
            var batch = $(this).val();
            if (batch != "请选择") {
                $.ajax({
                    url: '/member/gift-list/get-coupons-detail',
                    async: false,
                    type: 'POST',
                    data: {batch: batch},
                    dataType: 'json',
                    success: function (data) {
                        if (data['code'] == 600) {
                            $("#couponsTime").css("display", "block");
                            $("#couponsStart").attr("readonly", true);
                            $("#couponsEnd").attr("readonly", true);
                            $("#couponsStart").val(data["result"]["start_date"]);
                            $("#couponsEnd").val(data["result"]["end_date"]);
                            $("#in_stock").val("");
                            $("#gift_name").val("");
                            $("#in_stock").attr("readonly", true);
                            $("#gift_name").attr("readonly", true);
                            $("#in_stock").val(data["result"]["numbers"] - data["result"]["send_num"]);
                            $("#gift_name").val(data["result"]["coupons_name"]);
                        } else {
                            msgAlert(data["msg"]);
                        }
                    }
                });
            } else {
                $("#couponsStart").val("");
                $("#couponsEnd").val("");
                $("#in_stock").val("");
                $("#gift_name").val("");
                $("#in_stock").attr("readonly", false);
                $("#gift_name").attr("readonly", false);
            }
        })
        //缩略图
        $('#picture').change(function () {
            var file = this.files[0];
            var scr = $('#showimg').attr("src");
//            console.log(this.files);
            var objUrl = getObjectURL(this.files[0]);
            if (objUrl) {
                $('#showimg').attr("src", objUrl);
            }
            if (!/.(jpg|jpeg|png)$/.test(file.name)) {
                $("#msg").empty();
                h = '<label id="msg" style="color:red;">图片类型必须是.jpeg,jpg,png中的一种</label>';
                $(".showing").after(h);
                $('#showimg').attr("src", scr);
                return false;
            }
            if (file.size > 1024 * 1024 * 1) {
                $("#msg").empty();
                h = '<label id="msg" style="color:red;">图片大小不可超过1M</label>';
                $(".showing").after(h);
                $('#showimg').attr("src", scr);
                return false;
            }
        });
        //详情图
        $('#picture2').change(function () {
            var file = this.files[0];
            var scr = $('#showimg2').attr("src");
            var objUrl = getObjectURL(this.files[0]);
            if (objUrl) {
                $('#showimg2').attr("src", objUrl);
            }
            if (!/.(jpg|jpeg|png)$/.test(file.name)) {
                $("#msg").empty();
                h = '<label id="msg" style="color:red;">图片类型必须是.jpeg,jpg,png中的一种</label>';
                $(".showing").after(h);
                $('#showimg2').attr("src", scr);
                return false;
            }
            if (file.size > 1024 * 1024 * 1) {
                $("#msg").empty();
                h = '<label id="msg" style="color:red;">图片大小不可超过1M</label>';
                $(".showing").after(h);
                $('#showimg2').attr("src", scr);
                return false;
            }
        })
        $('#giftType3').validator({
            onValid: function (validity) {
                $(validity.field).closest('.am-form-group').find('.am-alert').hide();
            },
            onInValid: function (validity) {
                var $field = $(validity.field);
                var $group = $field.closest('.am-form-group');
                var $alert = $group.find('.am-alert');
                // 使用自定义的提示信息 或 插件内置的提示信息
                var msg = $field.data('validationMessage') || this.getValidationMessage(validity);

                if (!$alert.length) {
                    $alert = $('<div class="am-alert am-alert-danger"></div>').hide().appendTo($group);
                }
                $alert.html(msg).show();
            }
        });
        //添加礼品
        $('#addSubmit').click(function () {
            editor.sync();
            var filterContent  =$("#editor_id").val();
            if (filterContent=="") {
                msgAlert("文章内容不得空");
                return false;
            }
            document.forms[0].target = "rfFrame";
            a = 0;
            var formData = new FormData();
            var data = $(".add_gift").serializeArray();
            formData.append("upfile", $("#picture").get(0).files[0]);
            formData.append("upfile2", $("#picture2").get(0).files[0]);
            formData.append("gift_remark", filterContent);
            $.each(data, function (i, field) {
                formData.append(field.name, field.value);
            });
            var cate = $("#gift_cate option:selected");
            if (cate.val() == 0) {
                $(this).focus();
//                msgAlert("请选择有效类别");
                $("#msg").empty();
                h = '<label id="msg" style="color:red;"> 请选择有效类别</label>';
                $(".cate_sel").after(h);
                a++
            }
            if ($('#showimg').attr("src") == '/image/u1529.png') {
                $(this).focus();
//                msgAlert("请上传图片");
                $("#msg").empty();
                h = '<label id="msg" style="color:red;"> 请上传缩略图</label>';
                $("#showimg").after(h);
                a++
            }
            if ($('#showimg2').attr("src") == '/image/u1529.png') {
                $(this).focus();
//                msgAlert("请上传图片");
                $("#msg").empty();
                h = '<label id="msg" style="color:red;"> 请上传详情图</label>';
                $("#showimg2").after(h);
                a++
            }
            $(".need").each(function (i) {
                var text = $(this).val();
                if (text == "") {
                    a++;
                    $(this).focus();
                    return false;
                }
            });
            // $(".am-alert-danger").each(function (i) {
            //     if ($(this).is(":visible")) {
            //         a++;
            //     }
            // })
            if (a != 0) {
                msgAlert("请将带*的参数填写完整")
                return false;
            }
            if ($("#giftType").val() == 1) {
                if ($("#couponsBatch").val() == "请选择") {
                    msgAlert("请选择优惠券批次")
                    return false;
                }
            }
            $.ajax({
                url: '/member/gift-list/editgift',
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
                        $("#msg").empty();
                        msgAlert(data['msg'], function () {
                            location.href = '/member/gift-list/index';
                        });
                    }
                }
            });
        })
        $("#backSubmit").click(function () {
            location.href = '/member/gift-list/index';
        })
    })

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
</script>