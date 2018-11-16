<?php

use yii\helpers\Html;

echo '<form action="/admin/paysetting/" method="post" id="pay_setting" enctype="multipart/form-data">';
echo '<ul class="child_margin_top_5">';
echo '<li>';
echo Html::tag("label", "支付宝", ["style" => "margin-top:10px;font-size: 20px;font-weight:700;"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo '</li>';
echo '<li class="switch">';
echo Html::label("是否开启  ", "", ["style" => "margin-left:15px;"]);
echo '<div style="max-width:80%;display:inline-block;margin-left:5px;">
    <input type="hidden" name="ali_switch" class="switch_value" value="' . $data["ali_switch"] . '"/>
    <div class="switch1">
        <div class="switch2"></div></div></div>';
echo '</li>';
echo '<li>';
echo Html::label("APPID  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "ali_app_id", $data["ali_app_id"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("商户私钥  ", "", ["style" => "margin-left:15px;"]);
echo Html::textarea("ali_merchant_private_key", $data["ali_merchant_private_key"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;height:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("编码格式  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "ali_charset", $data["ali_charset"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("签名方式  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "ali_sign_type", $data["ali_sign_type"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("支付宝网关  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "ali_gatewayUrl", $data["ali_gatewayUrl"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("支付宝公钥  ", "", ["style" => "margin-left:15px;"]);
echo Html::textarea("ali_alipay_public_key", $data["ali_alipay_public_key"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;height:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::tag("label", "微信公众号", ["style" => "margin-top:10px;font-size: 20px;font-weight:700;"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo '</li>';
echo '<li class="switch">';
echo Html::label("是否开启  ", "", ["style" => "margin-left:15px;"]);
echo '<div style="max-width:80%;display:inline-block;margin-left:5px;">
    <input type="hidden" name="wx_switch" class="switch_value" value="' . $data["wx_switch"] . '"/>
    <div class="switch1">
        <div class="switch2"></div></div></div>';
echo '</li>';
echo '<li>';
echo Html::label("APPID  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "wx_APPID", $data["wx_APPID"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("商户号  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "wx_MCHID", $data["wx_MCHID"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("商户支付密钥  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "wx_KEY", $data["wx_KEY"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("公众帐号secert  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "wx_APPSECRET", $data["wx_APPSECRET"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("证书cert  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("file", "wx_SSLCERT", $data["wx_SSLCERT"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("证书key  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("file", "wx_SSLKEY", $data["wx_SSLKEY"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::tag("label", "微信APP", ["style" => "margin-top:10px;font-size: 20px;font-weight:700;"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo '</li>';
echo '<li class="switch">';
echo Html::label("是否开启  ", "", ["style" => "margin-left:15px;"]);
echo '<div style="max-width:80%;display:inline-block;margin-left:5px;">
    <input type="hidden" name="wxapp_switch" class="switch_value" value="' . $data["wxapp_switch"] . '"/>
    <div class="switch1">
        <div class="switch2"></div></div></div>';
echo '</li>';
echo '<li>';
echo Html::label("APPID  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "wxapp_APPID", $data["wxapp_APPID"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("商户号  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "wxapp_MCHID", $data["wxapp_MCHID"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("商户支付密钥  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "wxapp_KEY", $data["wxapp_KEY"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("证书cert  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("file", "wxapp_SSLCERT", $data["wxapp_SSLCERT"], ["class" => "form-control", "placeholder" => "", "style" => "width:80%;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("证书key  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("file", "wxapp_SSLKEY", $data["wxapp_SSLKEY"], [ "class" => "form-control", "placeholder" => "", "style" => "width:100px;display:inline;margin-left:5px;"]);
echo '</li>';

echo '<li>';
echo Html::submitButton("提交", ["class" => "am-btn am-btn-primary", "onclick" => "submitform();"]);
//echo Html::button("提交", ["class" => "am-btn am-btn-primary", "onclick" => "submitform();"]);
echo '</li>';

echo '</ul>';
echo '</form>';
?>
<script type="text/javascript">
//    function submitform() {
//        msgConfirm('提醒', '确定修改？', function () {
//            var data = $("#pay_setting").serialize();
//            $.ajax({
//                url: "/admin/paysetting/",
//                type: "POST",
//                async: false,
//                data: data,
//                dataType: "json",
////                processData: false,
////                contentType: false,
//                success: function (data) {
//                    if (data["code"] != 0) {
//                        msgAlert(data["msg"]);
//                    } else {
//                        msgAlert(data['msg'], function () {
//                            location.reload();
//                        });
//                    }
//                }
//            });
//        })
//    }
    $(function () {
        $.each($(".switch"), function () {
            switchinit(this)
        });
        $(".switch").click(function () {
            switchfun(this)
        });
        function switchfun(_this) {
            var v = $(_this).find(".switch_value").val();
            var s1 = $(_this).find(".switch1");
            var s2 = $(_this).find(".switch2");
            if (v == "1") {
                s1.removeClass("open1");
                s1.addClass("close1");
                s2.removeClass("open2");
                s2.addClass("close2");
                $(_this).find(".switch_value").val('0');
            } else {
                s1.removeClass("close1");
                s1.addClass("open1");
                s2.removeClass("close2");
                s2.addClass("open2");
                $(_this).find(".switch_value").val('1');
            }
        }
        function switchinit(_this) {
            var v = $(_this).find(".switch_value").val();
            var s1 = $(_this).find(".switch1");
            var s2 = $(_this).find(".switch2");
            if (v == "1") {
                s1.removeClass("close1");
                s1.addClass("open1");
                s2.removeClass("close2");
                s2.addClass("open2");
                $(_this).find(".switch_value").val('1');
            } else {
                s1.removeClass("open1");
                s1.addClass("close1");
                s2.removeClass("open2");
                s2.addClass("close2");
                $(_this).find(".switch_value").val('0');
            }
        }
    });
</script>

