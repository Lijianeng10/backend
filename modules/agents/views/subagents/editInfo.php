<?php

use yii\helpers\Html;

$agents_type = [
    "1" => "总部",
    "2" => "地推",
    "3" => "体彩店",
    "4" => "福彩店",
    "5" => "便利店",
    "6" => "个人",
];
$pass_status = [
    "1" => "未认证",
    "2" => "审核中",
    "3" => "已通过",
    "4" => "未通过",
];
$use_status = [
    "1" => "使用",
    "2" => "禁用",
];
echo '<form>';
echo "<ul class='double_team_ul'>";
echo '<li style="width:100%;">';
echo Html::label("代理商基本信息", "", ["class" => "hr_label"]);
echo Html::tag("hr", "", ["class" => "resultPage"]);
echo Html::input("hidden", "agents_id", $data["agents_id"],["id"=>"agents_id"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商账户  ", "", ["style" => "margin-left:45px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "agents_account", $data['agents_account'], ["class" => "form-control need","disabled"=>"true","placeholder" => "代理商账户", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("上级代理商编号  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "upagents_code", $data['upagents_code'], ["class" => "form-control need","id"=>"upagents_code", "placeholder" => "上级代理商编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商名称  ", "", ["style" => "margin-left:42px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "agents_name", $data['agents_name'], ["class" => "form-control need","id"=>"agents_name", "placeholder" => "代理商名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("上级代理商名称  ", "", ["style" => "margin-left:15px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "upagents_name", $data['upagents_name'], ["class" => "form-control need","id"=>"upagents_name", "placeholder" => "上级代理商名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商APPID  ", "", ["style" => "margin-left:33px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "agents_appid", $data['agents_appid'], ["class" => "form-control need","disabled"=>"true", "placeholder" => "代理商APPID", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商类型", "", ["style" => "margin-left:43px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("agents_type", $data['agents_type'], $agents_type, ["class" => "form-control need","id"=>"agents_type", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("代理商简码  ", "", ["style" => "margin-left:42px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "agents_code", $data['agents_code'], ["class" => "form-control need","id"=>"agents_code", "placeholder" => "代理商简码", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("跳转URL  ", "", ["style" => "margin-left:61px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::input("input", "to_url", $data['to_url'], ["class" => "form-control need","id"=>"to_url", "placeholder" => "跳转URL", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("认证状态", "", ["style" => "margin-left:55px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("pass_status", $data['pass_status'], $pass_status, ["class" => "form-control need", "disabled"=>"true","style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("使用状态", "", ["style" => "margin-left:57px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::dropDownList("use_status", $data['use_status'], $use_status, ["class" => "form-control need", "disabled"=>"true","style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("开户时间  ", "", ["style" => "margin-left:54px;"]) . Html::tag("span", "*", ["class" => "requiredIcon"]);
echo Html::input("input", "check_time", $data['check_time'], ["class" => "form-control need","disabled"=>"true", "placeholder" => "开户时间", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="display:block;clear:both;width:60%">';
echo Html::label("代理商备注  ", "", ["style" => "margin-left:43px;"]) . Html::tag("span", " ", ["class" => "requiredIcon"]);
echo Html::tag("textarea", $data['agents_remark'], ["class" => "form-control need","id"=>"agents_remark", "style" => "width:300px;height:100px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="width:90%;padding-left:100px;">';
echo Html::tag("span", "修改 ", ["class" => "search am-btn am-btn-primary", "id" => "editBtn", "style" => "margin-left:45px;margin-top:20px;"]);
echo Html::tag("span", "返回", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:30px;margin-top:20px;", "onclick" => "location.href = '/agents/subagents/index'"]);
echo '</li>';
echo '</ul>';
echo '</form>';
?>
<script>

$("#editBtn").click(function(){
   var name="<?php echo $data->agents_name; ?>";
   var agents_id=$("#agents_id").val();
   var agents_name=$("#agents_name").val();
   var agents_code=$("#agents_code").val();
   var upagents_name=$("#upagents_name").val();
   var upagents_code=$("#upagents_code").val();
   var to_url=$("#to_url").val();
   var agents_type=$("#agents_type").val();
   var agents_remark=$("#agents_remark").val();
   if(agents_name==""||upagents_name==""||upagents_code==""||agents_type==""){
       msgAlert("请将带*的必选项填写完整");
   }else{
        $.ajax({
            url: "/agents/subagents/edit-agents-info",
            async: false,
            dataType: "json",
            type: "POST",
            data: {agents_id: agents_id, agents_name: agents_name,upagents_name:upagents_name,upagents_code:upagents_code,agents_type:agents_type,agents_remark:agents_remark,agents_code:agents_code,to_url:to_url},
            success: function (json) {
                if (json["code"] == 600) {
                    msgAlert(json["msg"], function () {
                        location.reload();
                    });
                } else {
                    msgAlert(json["msg"]);
                }
            }
        })
   }
})
</script>

