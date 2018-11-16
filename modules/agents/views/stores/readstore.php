<link href="/css/font-awesome.min.css" rel="stylesheet">
<?php

use yii\helpers\Html;
use app\modules\common\helpers\Constants;

function getImgHtml($name,$body, $img = "/image/u1529.png") {
    if ($img == "") {
        $img = "/image/u1529.png";
    }
    $id = $name . "_show";
    $html = "<div style='float:left;display:inline-block;'>";
    $html .= "<div style='padding-left:8px;' data-magnify='gallery' href={$img} data-caption={$body}>";
    $html .=Html::img($img, ["id" => $id,"class"=>"storeImg", "style" => "filter:;width:80px;height:80px;border:2px inset #EEE"]);
    $html .= "</div>";
    $html.=Html::tag("span", $body, ["class" => "buttomspan", "style" => "color:#bbbbbb;"]);
    $html.=Html::fileInput($name, "", ["class" => "imgupload"]);
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
$status = [
    "1" => "启用",
    "2" => "禁用",
];
echo '<form>';
echo "<ul class='double_team_ul'>";
echo '<li style="width:100%;">';
echo Html::label("门店基本信息", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo Html::input("hidden", "store_id", $data["store_id"]);
echo '</li>';

echo '<li>';
echo Html::label("门店编号  ", "", ["style" => "margin-left:33px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "store_code", $data['store_code'], ["class" => "form-control need", "placeholder" => "门店编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("门店名称  ", "", ["style" => "margin-left:68px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "store_name", $data['store_name'], ["class" => "form-control need", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("经营类型", "", ["style" => "margin-left:32px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("store_type", $data['store_type'], $store_type, ["class" => "form-control need", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("公司名称  ", "", ["style" => "margin-left:68px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "company_name", $data['company_name'], ["class" => "form-control need", "placeholder" => "公司名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("对外联系号码  ", "", ["style" => "margin-left:4px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "telephone", $data['telephone'], ["class" => "form-control need", "placeholder" => "对外联系号码", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("所在地区  ", "", ["style" => "margin-left:30px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo '<div id="city_china" class="am-form-group" style="display:inline-block;">
            <label><select class="form-control province cxselect need"  name="province" style="width:200px;display:inline;" data-value="' . $data['province'] . '"></select></label>
            <label><select class="form-control city cxselect need"  name="city" style="min-width: 80px" data-value="' . $data['city'] . '"></select></label>
            <label><select class="form-control area cxselect" name="area" style="min-width: 80px" data-value="' . $data['area'] . '"></select></label>
            <label class="address_input"><input type="text" class="form-control need" id="address" name="address" value="' . $data['address'] . '" style="width:280px" placeholder="详细地址" data-validation-message="请填写详细地址" required/></label>
    </div>';
echo '</li>';
echo '<li>';
echo Html::label("服务时间", "", ["style" => "margin-left:31px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("text", "open_time", $data['open_time'], ["class" => "form-control inputTime need", "placeholder" => "开始", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "close_time", $data['close_time'], ["class" => "form-control inputTime need", "placeholder" => "结束", "style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("支持代兑奖金  ", "", ["style" => "margin-left:42px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "support_bonus", $data['support_bonus'], ["class" => "form-control need", "style" => "width:200px;display:inline;margin-left:5px;"]) . "元以下";
echo '</li>';
echo '<li>';
echo Html::label("体彩代销编号  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "sports_consignee_code", $data['sports_consignee_code'], ["class" => "form-control", "placeholder" => "体彩代销编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("福彩代销编号  ", "", ["style" => "margin-left:51px;"]). Html::tag("span", " ");
echo Html::input("input", "welfare_consignee_code", $data['welfare_consignee_code'], ["class" => "form-control", "placeholder" => "福彩代销编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li >';
echo Html::label("运营者姓名  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "consignee_name", $data['consignee_name'], ["class" => "form-control need", "placeholder" => "运营者姓名", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("运营者身份证号码  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "consignee_card", $data['consignee_card'], ["class" => "form-control need", "placeholder" => "运营者身份证号码", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
//echo '<li class="store_type store_type_3">';
//echo Html::label(" 营业执照号  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
//echo Html::input("input", "business_license", $data['business_license'], ["class" => "form-control need", "placeholder" => "营业执照号", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
//echo '<li class="store_type store_type_3">';
//echo Html::label("运营者姓名  ", "", ["style" => "margin-left:57px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
//echo Html::input("input", "operator_name", $data['operator_name'], ["class" => "form-control need", "placeholder" => "运营者姓名", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
//echo '<li class="store_type store_type_3">';
//echo Html::label("运营者证件  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
//echo Html::input("input", "operator_card", $data['operator_card'], ["class" => "form-control need", "placeholder" => "运营者证件", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
//echo '<li class="store_type store_type_2">';
//echo Html::label("原业主姓名  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
//echo Html::input("input", "old_owner_name", $data['old_owner_name'], ["class" => "form-control need", "placeholder" => "原业主姓名", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
//echo '<li class="store_type store_type_2">';
//echo Html::label("原业主身份证  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
//echo Html::input("input", "old_owner_card", $data['old_owner_card'], ["class" => "form-control need", "placeholder" => "原业主身份证", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
//echo '<li class="store_type store_type_2">';
//echo Html::label("现业主姓名  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
//echo Html::input("input", "now_owner_name", $data['now_owner_name'], ["class" => "form-control need", "placeholder" => "现业主姓名", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
//echo '<li class="store_type store_type_2">';
//echo Html::label("现业主身份证  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
//echo Html::input("input", "now_owner_card", $data['now_owner_card'], ["class" => "form-control need", "placeholder" => "现业主身份证", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
//echo '<li>';
//echo Html::label("邮箱  ", "", ["style" => "margin-left:99px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
//echo Html::input("input", "email", $data['email'], ["class" => "form-control need", "placeholder" => "邮箱", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
//echo '<li>';
//echo Html::label("联系电话", "", ["style" => "margin-left:70px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
//echo Html::input("input", "telephone", $data['telephone'], ["class" => "form-control need", "placeholder" => "带区号如：0557-7456245", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
echo '<li>';
echo Html::label("认证状态", "", ["style" => "margin-left:28px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("cert_status", $data['cert_status'], $cert_status, ["class" => "form-control need", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("门店状态", "", ["style" => "margin-left:70px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("status", $data['status'], $status, ["class" => "form-control need","style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
//echo '<li>';
//echo Html::label("合同开始日期", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
//echo Html::input("text", "contract_start_date", $data['contract_start_date'], ["class" => "form-control need", "data-am-datepicker" => "", "readonly" => "readonly", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
//echo '<li>';
//echo Html::label("合同结束日期", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "", ["class" => "requiredIcon"]);
//echo Html::input("text", "contract_end_date", $data['contract_end_date'], ["class" => "form-control need", "data-am-datepicker" => "", "readonly" => "readonly", "style" => "width:200px;display:inline;margin-left:5px;"]);
//echo '</li>';
echo '<li style="width:75%;">';
echo Html::label("可售彩种", "", ["style" => "margin-left:34px;"]);
$lotteryName = Constants::LOTTERY;
$name = "";
if(!empty($data['sale_lottery'])){
    $lottery = explode(",",trim($data['sale_lottery']));
    foreach ($lottery as $v){
        if(isset($lotteryName[$v])){
           $name.=$lotteryName[$v].","; 
        }else{
           $name.="".",";
        }
        
    }
}
echo Html::input("text","",$name, ["class" => "form-control", "style" => "width:80%;display:inline;height:60px;margin-left:10px;"]);
echo '<li style="width:75%;">';
echo Html::label("备注", "", ["style" => "margin-left:63px;"]);
echo Html::textarea("remark", $data['remark'], ["class" => "form-control", "style" => "width:80%;display:inline;height:60px;margin-left:10px;"]);
echo '</li style="width:100%;">';
echo '<li style="width:100%;">';
echo Html::label("店员信息", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo '</li>';
echo '<li>';
echo '<table border=1px style="text-align:center;font-size:14px;margin-left:15px">';
echo '<tr style="background-color:#E4E4E4"><td>身份</td><td>编号</td><td>姓名</td><td>手机号</td><td>状态</td><td>备注</td><td>操作</td></tr>';
if (!empty($oldStore)) {
    foreach ($oldStore as $v) {
        echo '<tr><td>原运营者</td><td>' . $v["cust_no"] . '</td><td>' . $v["user_name"] . '</td><td>' . $v["user_tel"] . '</td><td>' . ($v["status"] == 1 ? "启用" : "禁用") . '</td><td>' . $v["nickname"] . '于' . $v["modify_time"] . '进行营运者更换操作</td><td></td></tr>';
    }
}
echo '<tr><td>现运营者</td><td>' . $data["cust_no"] . '</td><td>' . $data["consignee_name"] . '</td><td>' . $data["phone_num"] . '</td><td>' . ($data["status"] == 1 ? "启用" : "禁用") . '</td><td></td><td></td></tr>';
foreach ($operator as $val){
    echo '<tr><td>操作员</td><td>'.$val["cust_no"].'</td><td>'.$val["user_name"].'</td><td>'.$val["user_tel"].'</td><td>'.($val["status"]==1?"启用":"禁用").'</td><td></td><td>'.($val["status"]==1?"<a onclick=editStatus(".$val["store_operator_id"].",2)>禁用</a>":"<a onclick=editStatus(".$val["store_operator_id"].",1)>启用</a>").'</td></tr>';
}
echo '</table>';
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("门店说明信息", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("代销资质", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:150px;"]);
echo getImgHtml("consignee_img", "(代销资质图片1)", $data['consignee_img']);
echo getImgHtml("consignee_img", "(代销资质图片2)", $data['consignee_img2']);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("运营者身份证件  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:150px;"]);
echo getImgHtml("consignee_card_img1", "身份证正面", $data['consignee_card_img1']);
echo getImgHtml("consignee_card_img2", "身份证反面", $data['consignee_card_img2']);
echo '</li>';
echo '<li style="width:100%;">';
echo Html::label("运营者手持身份证件  ", "", ["style" => "margin-left:15px;float:left;display:inline-block;width:150px;"]);
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
echo Html::tag("span", "返回", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:50px;", "onclick" => "window.history.go(-1);"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo Html::tag("span", "", ["class" => "error_msg"]);
?>
<script src="/js/jquery.cxselect.min.js"></script>
<script>
    function editStatus(id,sta){
        msgConfirm("提示", "确定要改变该操作员当前状态吗?", function () {
            $.ajax({
                url: "/agents/stores/edit-operator-status",
                async: false,
                type: "POST",
                dataType: "json",
                data: {store_operator_id:id,status:sta},
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        })
    }
    $('#city_china').cxSelect({
        url: '/js/cityData.min.json',
        selects: ['province', 'city', 'area'],
        emptyStyle: 'none'
    });
    $(function () {
        var type = $("select[name=store_type]").val();
        $(".store_type").hide();
        $(".store_type").find("input").removeClass("need");
        $(".store_type_" + type).show();
        $(".store_type_" + type).find("input").addClass("need");
        $("input").attr("disabled", true);
        $("textarea").attr("disabled", true);
        $.each($("select"), function () {
            $(this).attr("disabled", true);
        });
    });
</script>

