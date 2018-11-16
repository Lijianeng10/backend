<?php

use yii\helpers\Html;

function getImgHtml($name, $body, $img = "/image/u1529.png") {
    if ($img == "") {
        $img = "/image/u1529.png";
    }
    $id = $name . "_show";
    $changeName = $name . "_change";
    $html = "<div style='float:left;display:inline-block;'>";
    $html .= "<div style='padding-left:8px;' data-magnify='gallery' href={$img} data-caption={$body}>";
    $html .=Html::img($img, ["id" => $id, "style" => "filter:;width:80px;height:80px;border:2px inset #EEE"]);
    $html .=Html::input("hidden", $changeName, "");
    $html .= "</div>";
    $html .= "<div>";
    $html.=Html::tag("a", "上传", ["class" => "buttomspan", "onclick" => "$('input[name=" . $name . "]').click();"]);
    $html.=Html::tag("a", "| 删除", ["class" => "buttomspan", "onclick" => "javascript:$('input[name=" . $name . "]').val('');$('#" . $id . "').attr('src', '/image/u1529.png');imgChange('" . $changeName . "');"]);
    $html .= "</div>";
    $html.=Html::tag("span", $body, ["class" => "buttomspan", "style" => "color:#bbbbbb;"]);
    $html.=Html::fileInput($name, "", ["class" => "imgupload", "onchange" => "previewImage(this, \$('#{$id}'));imgChange('" . $changeName . "');"]);
    $html .= "</div>";
    return $html;
}

$cert_status = [
    "" => "请选择",
    "1" => "未认证",
    "2" => "审核中",
    "3" => "已通过",
    "4" => "未通过"
];
$store_type = [
    "" => "请选择",
    "1" => "个人自营店",
    "2" => "个体转让店",
    "3" => "咕啦自营店",
    "4" => "贵人鸟加盟店"
];

echo '<form id="editstore">'; //action="/agents/stores/addstore" method="post"
echo "<ul class='double_team_ul'>";
echo '<li style="width:100%;">';
echo Html::label("门店基本信息", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo Html::input("hidden", "store_id", $data["store_id"]);
echo '</li>';
echo '<li>';
echo Html::label("经营类型", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("store_type", $data['store_type'], $store_type, ["class" => "form-control need", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("门店编号  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "store_code", $data['store_code'], ["class" => "form-control need", "placeholder" => "门店编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="store_type store_type_3">';
echo Html::label("代销者姓名  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "consignee_name", $data['consignee_name'], ["class" => "form-control need", "placeholder" => "代销者姓名", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="store_type  store_type_3">';
echo Html::label("代销者身份证号码  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "consignee_card", $data['consignee_card'], ["class" => "form-control need", "placeholder" => "代销者身份证号码", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="store_type store_type_3">';
echo Html::label("公司名称  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "company_name", $data['company_name'], ["class" => "form-control need", "placeholder" => "公司名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="store_type store_type_3">';
echo Html::label(" 营业执照号  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "business_license", $data['business_license'], ["class" => "form-control need", "placeholder" => "营业执照号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="store_type store_type_1">';
echo Html::label("运营者姓名  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "operator_name", $data['operator_name'], ["class" => "form-control ", "placeholder" => "运营者姓名", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="store_type store_type_1">';
echo Html::label("运营者证件  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "operator_card", $data['operator_card'], ["class" => "form-control ", "placeholder" => "运营者证件", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="store_type store_type_2">';
echo Html::label("原业主姓名  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "old_owner_name", $data['old_owner_name'], ["class" => "form-control ", "placeholder" => "原业主姓名", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="store_type store_type_2">';
echo Html::label("原业主身份证  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "old_owner_card", $data['old_owner_card'], ["class" => "form-control ", "placeholder" => "原业主身份证", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="store_type store_type_2">';
echo Html::label("现业主姓名  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "now_owner_name", $data['now_owner_name'], ["class" => "form-control ", "placeholder" => "现业主姓名", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li class="store_type store_type_2">';
echo Html::label("现业主身份证  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "now_owner_card", $data['now_owner_card'], ["class" => "form-control", "placeholder" => "现业主身份证", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("体彩代销编号  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "sports_consignee_code", $data['sports_consignee_code'], ["class" => "form-control", "placeholder" => "体彩代销编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("福彩代销编号  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "welfare_consignee_code", $data['welfare_consignee_code'], ["class" => "form-control", "placeholder" => "福彩代销编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("邮箱  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "email", $data['email'], ["class" => "form-control ", "placeholder" => "邮箱", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("手机号码  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "phone_num", $data['phone_num'], ["class" => "form-control need", "placeholder" => "手机号码", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("所在地区  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo '<div id="city_china" class="am-form-group" style="display:inline-block;">
            <label><select class="form-control province cxselect need"  name="province" style="width:200px;display:inline;" data-value="' . $data['province'] . '"></select></label>
            <label><select class="form-control city cxselect need"  name="city" style="min-width: 80px" data-value="' . $data['city'] . '"></select></label>
            <label><select class="form-control area cxselect need"  name="area" style="min-width: 80px" data-value="' . $data['area'] . '"></select></label>
            <label class="address_input"><input type="text" class="form-control need" id="address" name="address" value="' . $data['address'] . '" style="width:500px" placeholder="详细地址" data-validation-message="请填写详细地址" required/></label>
    </div>';
echo '</li>';
echo '<li>';
echo Html::label("门店名称  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "store_name", $data['store_name'], ["class" => "form-control need", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("支持代兑奖金  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "support_bonus", $data['support_bonus'], ["class" => "form-control", "style" => "width:200px;display:inline;margin-left:5px;"]) . "元以下";
echo '</li>';
echo '<li>';
echo Html::label("服务时间", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("text", "open_time", $data['open_time'], ["class" => "form-control inputTime need", "placeholder" => "开始", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "close_time", $data['close_time'], ["class" => "form-control inputTime need", "placeholder" => "结束", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("对外联系电话", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "telephone", $data['telephone'], ["class" => "form-control ", "placeholder" => "对外联系手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("合同开始日期", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("text", "contract_start_date", $data['contract_start_date'], ["class" => "form-control ", "data-am-datepicker" => "", "readonly" => "readonly", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("合同结束日期", "", ["style" => "margin-left:15px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("text", "contract_end_date", $data['contract_end_date'], ["class" => "form-control", "data-am-datepicker" => "", "readonly" => "readonly", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("备注", "", ["style" => "margin-left:15px;"]);
echo Html::textarea("remark", $data['remark'], ["class" => "form-control", "style" => "width:80%;display:inline;height:60px;margin-left:5px;"]);
echo '</li style="width:100%;">';
echo '<li style="width:100%;">';
echo Html::label("门店说明信息", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("体彩代销资质", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:150px;"]);
echo getImgHtml("consignee_img", "(仅支持jpg、png图片文件)", $data['consignee_img']);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("代销者身份证件  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:150px;"]);
echo getImgHtml("consignee_card_img1", "身份证正面", $data['consignee_card_img1']);
echo getImgHtml("consignee_card_img2", "身份证反面", $data['consignee_card_img2']);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("代销者手势身份证件  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:150px;"]);
echo getImgHtml("consignee_card_img3", "手持身份证正面", $data['consignee_card_img3']);
echo getImgHtml("consignee_card_img4", "手持身份证反面", $data['consignee_card_img4']);
echo '</li>';
echo '<li style="width:100%;" class="store_type store_type_2">';
echo Html::label("原业主身份证件  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:150px;"]);
echo getImgHtml("old_owner_card_img1", "身份证正面", $data['old_owner_card_img1']);
echo getImgHtml("old_owner_card_img2", "身份证反面", $data['old_owner_card_img2']);
echo '</li>';
echo '<li style="width:100%;" class="store_type store_type_3">';
echo Html::label("企业营业执照  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:150px;"]);
echo getImgHtml("business_license_img", "企业营业执照", $data['business_license_img']);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("店面  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:150px;"]);
echo getImgHtml("store_img", "门头+门店+店内照", $data['store_img']);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("票样上传", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo '</li>';
echo '<li style="min-width:300px;width:30%;">';
echo Html::label("竞彩", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:80px;"]);
echo getImgHtml("competing_img", "", $data['competing_img']);
echo '</li>';
echo '<li style="min-width:300px;width:30%;">';
echo Html::label("传统足球  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:80px;"]);
echo getImgHtml("football_img", "", $data['football_img']);
echo '</li>';
echo '<li style="min-width:300px;width:30%;">';
echo Html::label("体彩数字  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:80px;"]);
echo getImgHtml("sports_nums_img", "", $data['sports_nums_img']);
echo '</li>';
echo '<li style="min-width:300px;width:30%;">';
echo Html::label("体彩高频  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:80px;"]);
echo getImgHtml("sports_fre_img", "", $data['sports_fre_img']);
echo '</li>';
echo '<li style="min-width:300px;width:30%;">';
echo Html::label("北单  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:80px;"]);
echo getImgHtml("north_single_img", "", $data['north_single_img']);
echo '</li>';
echo '<li style="min-width:300px;width:30%;">';
echo Html::label("福彩数字  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:80px;"]);
echo getImgHtml("welfare_nums_img", "", $data['welfare_nums_img']);
echo '</li>';
echo '<li style="min-width:300px;width:30%;">';
echo Html::label("福彩高频  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:80px;"]);
echo getImgHtml("welfare_fre_img", "", $data['welfare_fre_img']);
echo '</li>';
echo '<li style="width:90%;padding-left:100px;">';
echo Html::tag("span", "修改 ", ["class" => "search am-btn am-btn-primary", "id" => "addSubmit", "style" => "margin-left:5px;margin-bottom:50px;"]);
echo Html::tag("span", "返回", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:20px;margin-bottom:50px;", "onclick" => "location.href = '/agents/stores/index'"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo Html::tag("span", "", ["class" => "error_msg"]);
?>
<script src="/js/jquery.cxselect.min.js"></script>
<script>
    $('#city_china').cxSelect({
        url: '/js/cityData.min.json',
        selects: ['province', 'city', 'area'],
        emptyStyle: 'none'
    });
    function previewImage(_this, _show) {
        var file = _this.files[0];
        var scr = _show.attr("src");
        var objUrl = getObjectURL(_this.files[0]);
        if (objUrl) {
            _show.attr("src", objUrl);
        }
        if (!/.(jpg|jpeg|png)$/.test(file.name)) { 
            msgAlert("图片类型必须是.jpeg,jpg,png中的一种");
            _show.attr("src", scr);     
        } else if (file.size > 108 * 108 * 1) {
            msgAlert('图片大小不可超过200KB');
            _show.attr("src", scr); 
        }
    }
    function imgChange(name) {
        $("input[name=" + name + "]").val("1");
    }
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
        $('.city').change(function () {
            $("#c_msg").empty();
            var code = $(this).val();
            if (code == 0) {
                $('#address').attr('disabled', true);
            } else {
                $('#address').attr('disabled', false);
            }
        });
        $("input[name=open_time]").setTime({s: false});
        $("input[name=close_time]").setTime({s: false});


        $('#addSubmit').click(function () {
            err = 0;
            var formData = new FormData();
            var data = $("#editstore").serializeArray();
            formData.append("consignee_img", $("input[name=consignee_img]").get(0).files[0]);
            formData.append("consignee_card_img1", $("input[name=consignee_card_img1]").get(0).files[0]);
            formData.append("consignee_card_img2", $("input[name=consignee_card_img2]").get(0).files[0]);
            formData.append("consignee_card_img3", $("input[name=consignee_card_img3]").get(0).files[0]);
            formData.append("consignee_card_img4", $("input[name=consignee_card_img4]").get(0).files[0]);
            formData.append("competing_img", $("input[name=competing_img]").get(0).files[0]);
            formData.append("football_img", $("input[name=football_img]").get(0).files[0]);
            formData.append("sports_nums_img", $("input[name=sports_nums_img]").get(0).files[0]);
            formData.append("sports_fre_img", $("input[name=sports_fre_img]").get(0).files[0]);
            formData.append("north_single_img", $("input[name=north_single_img]").get(0).files[0]);
            formData.append("welfare_nums_img", $("input[name=welfare_nums_img]").get(0).files[0]);
            formData.append("welfare_fre_img", $("input[name=welfare_fre_img]").get(0).files[0]);
            formData.append("store_img", $("input[name=store_img]").get(0).files[0]);
//            if ($("input[name=consignee_img]").data("change") == 1) {
//                formData.append("consignee_img", $("input[name=consignee_img]").get(0).files[0]);
//            }
//
//            if ($("input[name=consignee_card_img1]").data("change") == 1) {
//                formData.append("consignee_card_img1", $("input[name=consignee_card_img1]").get(0).files[0]);
//            }
//
//            if ($("input[name=consignee_card_img2]").data("change") == 1) {
//                formData.append("consignee_card_img2", $("input[name=consignee_card_img2]").get(0).files[0]);
//            }
//            if ($("input[name=consignee_card_img3]").data("change") == 1) {
//                formData.append("consignee_card_img3", $("input[name=consignee_card_img3]").get(0).files[0]);
//            }
//            if ($("input[name=consignee_card_img4]").data("change") == 1) {
//                formData.append("consignee_card_img4", $("input[name=consignee_card_img4]").get(0).files[0]);
//            }
//            formData.append("competing_img", $("input[name=competing_img]").get(0).files[0]);
//            if ($("input[name=football_img]").data("change") == 1) {
//                formData.append("football_img", $("input[name=football_img]").get(0).files[0]);
//            }
//            if ($("input[name=sports_nums_img]").data("change") == 1) {
//                formData.append("sports_nums_img", $("input[name=sports_nums_img]").get(0).files[0]);
//            }
//            if ($("input[name=sports_fre_img]").data("change") == 1) {
//                formData.append("sports_fre_img", $("input[name=sports_fre_img]").get(0).files[0]);
//            }
//            if ($("input[name=north_single_img]").data("change") == 1) {
//                formData.append("north_single_img", $("input[name=north_single_img]").get(0).files[0]);
//            }
//            if ($("input[name=welfare_nums_img]").data("change") == 1) {
//                formData.append("welfare_nums_img", $("input[name=welfare_nums_img]").get(0).files[0]);
//            }
//            if ($("input[name=welfare_fre_img]").data("change") == 1) {
//                formData.append("welfare_fre_img", $("input[name=welfare_fre_img]").get(0).files[0]);
//            }
//            if ($("input[name=store_img]").data("change") == 1) {
//                formData.append("store_img", $("input[name=store_img]").get(0).files[0]);
//            }
            $.each(data, function (i, field) {
                formData.append(field.name, field.value);
            });
            $(".need").each(function (i){
            var text = $(this).val();
            if (text == "") {
                    err++;
                    $(this).focus();
                    $("#msg").empty();
                    h = '<span id="msg" style="color:red;">请填写此字段</span>';
                    $(this).after(h);
                    return false;
                }
            });
            if (err != 0) {
                return false;
            }
            $.ajax({
                url: '/agents/stores/savestore',
                async: false,
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                dataType: 'json',
                success: function (data) {  
                    if (data['code'] != 0) {
                        msgAlert(data['msg']);
                        $("#msg").empty();
                        h = '<span id="msg" style="color:red;">' + data['msg'] + '</span>';
                        $(".error_msg").prepend(h);
                        $("input[type=text]").each(function () {
                            if ($(this).val() == '') {
                                $(this).focus();
                                return false
                            }
                        });
                    } else {
                        msgAlert(data['msg'], function (){
                             location.reload();
                        });
                    }
                }
            });
        });
        $("input[name=store_code]").change(function () {
            var store_code = $(this).val();
            var store_id = $("input[name=store_id]").val();
            var _this = $(this);
            $.ajax({
                url: "/agents/stores/validate",
                async: false,
                type: 'POST',
                data: {store_code: store_code, store_id: store_id},
                dataType: 'json',
                success: function (json) {
                    if (json != true) {
                        var html = '<span id="msg" style="color:red;">' + json['msg'] + '</span>';
                        $("#msg").remove();
                        _this.parent("li").append(html);
                    } else {
                        $("#msg").remove();
                    }
                }
            });
        });
        $("input[name=email]").change(function () {
            var RegEmail = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
            $("#msg").remove();
            if (!RegEmail.test($(this).val())) {
                $(this).focus();
                var h = '<span id="msg" style="color:red;">请正确输入邮箱</span>';
                $(this).after(h);
            }
        });
        $("input[name=phone_num]").change(function () {
            var patrn = /^0?1[3|4|5|8][0-9]\d{8}$/;
            $("#msg").remove();
            if (!patrn.test($(this).val())) {
                $(this).focus();
                var h = '<span id="msg" style="color:red;">请正确输入手机</span>';
                $(this).after(h);
            }
        });
        $("input[name=telephone]").change(function () {
            var patrn = /^0?1[3|4|5|8][0-9]\d{8}$/;
//            var patrn = /^0[\d]{2,3}-[\d]{7,8}$/;
            $("#msg").remove();
            if (!patrn.test($(this).val())) {
                $(this).focus();
                var h = '<span id="msg" style="color:red;">请正确输入电话</span>';
                $(this).after(h);
            }
        });
//        $("select[name=store_type]").change(function () {
//            var type = $(this).val();
//            $(".store_type").hide();
//            $(".store_type").find("input").removeClass("need");
//            $(".store_type_" + type).show();
//            $(".store_type_" + type).find("input").addClass("need");
//        });

//        var type = $("select[name=store_type]").val();
//        $(".store_type").hide();
//        $(".store_type").find("input").removeClass("need");
//        $(".store_type_" + type).show();
//        $(".store_type_" + type).find("input").addClass("need");
    })
</script>

