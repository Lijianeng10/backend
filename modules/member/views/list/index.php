<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\db\Query;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<style>
    .opts{margin:0px auto;width:150px;height:30px;}
    .opts ul{margin:0px;padding:0px;}
    .ul1 li{
        width:37px;
    }
    .ul2{
        z-index: 99;
    }
    .ul2 li{
        width: 80px;
        text-align: center;
    }
    .opts ul li{
        position:relative;
        float:left;
        line-height:30px;
    }
    .opts ul li ul{
        display:none;
    }
    /*.menu ul li ul li{
        margin-top:1px
    }*/
    /*.menu ul li:hover{background:red;}*/
    .opts ul li span:hover{
        color:#fff;
    }
    .opts ul li:hover ul{
        background:#ccc;
        display:block;
        position: absolute;
        left: -45px;
        top: 30px;
    }
</style>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/member/list/index" >
        <?php
        echo "<ul class='third_team_ul'>";
        echo '<li>';
        echo Html::label("会员信息", "user_info", ["style" => "margin-left:15px;"]);
        echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["class" => "form-control", "placeholder" => "会员编号、名称、手机号", "style" => "width:200px;display:inline;margin-left:5px;","id"=>"user_info"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("代理商信息", "agents_info");
        echo Html::input("input", "agents_info", isset($get["agents_info"]) ? $get["agents_info"] : "", ["class" => "form-control", "placeholder" => "上级代理商编号、名称", "style" => "width:200px;display:inline;margin-left:5px;","id"=>"agents_info"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("开户日期", "", ["style" => "margin-left:15px;"]);
        echo Html::input("text", "startdate", isset($get["startdate"]) ? $get["startdate"] : "", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo "-";
        echo Html::input("text", "enddate", isset($get["enddate"]) ? $get["enddate"] : "", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
//    echo Html::label("所属区域  ", "", ["style" => "margin-left:15px;"]);
//    echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", '$orderStatus', ["id" => "status", "class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
        echo Html::label("认证状态  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("authen_status", isset($get["authen_status"]) ? $get["authen_status"] : "", $authen, ["id" => "authen_status", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
//        echo Html::label("会员类型  ", "", ["style" => "margin-left:15px;"]);
//        echo Html::dropDownList("user_type", isset($get["user_type"]) ? $get["user_type"] : "", $user_type, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
        echo Html::label("会员等级  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("user_level", isset($get["user_level"]) ? $get["user_level"] : "0", $levels, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;","id"=>"user_level"]);

//
//    echo Html::label("认证宝见证状态  ", "", ["style" => "margin-left:15px;"]);
//    echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", '$orderStatus', ["id" => "status", "class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("微信绑定  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("vxstatus", isset($get["vxstatus"]) ? $get["vxstatus"] : "", $vxstatus, ["id" => "vxstatus", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("会员咕币  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("glcoin", isset($get["glcoin"]) ? $get["glcoin"] : ">=", $compar, ["id" => "glcoin", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
        echo Html::input("input", "user_glcoin", isset($get["user_glcoin"]) ? $get["user_glcoin"] : "", ["class" => "form-control", "style" => "width:80px;display:inline;margin-left:5px;","id"=>"user_glcoin"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("会员余额  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("balance", isset($get["balance"]) ? $get["balance"] : ">=", $compar, ["id" => "balance", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
        echo Html::input("input", "balance_val", isset($get["balance_val"]) ? $get["balance_val"] : "", ["class" => "form-control", "style" => "width:80px;display:inline;margin-left:5px;","id"=>"balance_val"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("推广员  ", "", ["style" => "margin-left:29px;"]);
        echo Html::dropDownList("spread_type", isset($get["spread_type"]) ? $get["spread_type"] : "", $spreadType, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;","id"=>"spread_type"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("邀请人  ", "", ["style" => "margin-left:29px;"]);
        echo Html::dropDownList("is_inviter", isset($get["is_inviter"]) ? $get["is_inviter"] : "", $inviteStatus, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;","id"=>"is_inviter"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("注册来源  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("register_from", isset($get["register_from"]) ? $get["register_from"] : "", $regform, ["class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;","id"=>"register_from"]);
        echo Html::dropDownList("from_id", isset($get["from_id"]) ? $get["from_id"] : "", $platform, ["class" => "form-control", "style" => "width:130px;display:inline;margin-left:5px;","id"=>"from_id"]);
        echo '</li>';
        echo '<li>';
//        echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;", "onclick" => "search();"]);
        echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;"]);
        echo Html::input("reset", '', '重置', ["class" => "am-btn am-btn-primary", "id" => "reset", "style" => "margin-left:5px;"]);
//        echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addMember"]);
        echo '</li>';
        echo "</ul>";
        echo '</form>';
        ?>
    </form>
</div>
<iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
//        [
//            'label' => '',
//            'format' => 'raw',
//            'value' => function ($model) {
//                return \yii\bootstrap\Html::input('checkbox', 'box', $model["user_id"], ["class" => 'delSel']);
//            },
//                ],
                [ 'class' => 'yii\grid\SerialColumn'],
                [
                    'label' => '会员编号',
                    'value' => 'cust_no'
                ],[
                    'label' => '手机号',
                    'value' => 'user_tel'
                ],  [
                    'label' => '会员昵称',
                    'value' => 'user_name',
                    'contentOptions' => ['style' => 'max-width:100px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']
                ], [
                    'label' => '上级代理编号',
                    'value' => 'agent_code'
                ], [
                    'label' => '上级代理名称',
                    'value' => function($model){
                        return $model["agentName"]??"咕拉体育";
                    }
                ], [
                    'label' => '总金额',
                    'value' => 'all_funds',
                ], [
                    'label' => '可用余额 ',
                    'value' => 'able_funds',
                ], [
                    'label' => '冻结余额',
                    'value' => 'ice_funds',
                ], [
                    'label' => '不可提现金额',
                    'value' => 'no_withdraw',
                ], [
                    'label' => '可提现金额',
                    'value' => function($model) {
                        return sprintf('%.2f', $model["able_funds"] - $model["no_withdraw"]);
                    },
                ], [
                    'label' => '咕币',
                    'value' => 'user_glcoin',
                ],
//        [
//                    'label' => '礼品卡',
//                    'value' => function($model) {
//                        return '0';
//                    },
//                ],
        [
                    'label' => '微信绑定',
                    'value' => function($model) {
                        return (isset($model["third_uid"]) && !empty($model["third_uid"]) ? "已绑定" : "未绑定");
                    },
                ], [
                    'label' => '会员等级',
                    'value' => 'level_name',
                ],
//        ], [
//            'label' => '会员类型',
//            'format' => 'raw',
//            'value' => function ($model){
//                return $model['user_type'] == 1 ? '地推' : ($model['user_type'] == 2 ? '体彩店' : ($model['user_type'] == 3 ? '福彩店' : ($model['user_type'] == 4 ? '便利店' : ($model['user_type'] == 5 ? '个人' : '未定义'))));
//            }
//        ], [
                [
                    'label' => '开户时间',
                    'value' => 'create_time'
                ],  [
            'label' => '注册来源',
             'contentOptions' => ['style' => 'max-width:150px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;'],
            'value' => function($model) {
                //注册来源
                $gl = [1,2,4];
                $reg= [
                    "1" => "APP注册",
                    "2" => "H5注册",
                    "3" => "彩店二维码注册",
                    "4" => "咕啦社区",
                ];
                $from=[
                    '5' =>'代理商',
                    '7'=>'推广人员',
                    '8' => '平台推广',
                ];
                $pt=[
                    'meitu' => '美图',
                    "GL"=>"咕啦体育",
                    "qj"=>"全景",
                    "txty"=>"腾讯体育",
                ];
                $str='';
                if(in_array($model["register_from"],$gl)){
                    if ($model["from_id"]>=10000){
                        $str.='咕啦自营-彩店二维码注册';
                    }else{
                        $str.='咕啦自营-'.$reg[$model["register_from"]];
                    }
                }elseif(isset($from[$model["register_from"]])){
                    $str.=$from[$model["register_from"]].'-';
                    //对注册来源为3的进行单独处理
                }else{
                    $type = \app\modules\common\models\Store::find()->select('store_type')->where(['store_code'=>$model["from_id"]])->asArray()->one();
                   switch ($type['store_type']){
                       case 1:
                       case 2:
                            $str.='个人自营-彩店二维码注册';
                            break;
                       case 3:
                           $str.='咕啦自营-彩店二维码注册';
                           break;
                       case 4:
                           $str.='贵人鸟加盟店-彩店二维码注册';
                           break;
                   }
                }
                if($model["register_from"]==8){
                    if(isset($pt[$model["from_id"]])){
                        $str.=$pt[$model["from_id"]];
                    }else{
                        $str.="咕啦体育";
                    }
                }elseif($model["register_from"]==5){
                    $agents = (new Query())->select("agents_name")->from("agents")->where(["agents_id"=>$model["from_id"]])->one();
                    $str.=$agents["agents_name"];
                }elseif($model["register_from"]==7){
                    $user = (new Query())->select("user_name")->from("user")->where(["user_id"=>$model["from_id"]])->one();
                    $str.=$user["user_name"];
                }
                return $str;
            },
        ],[
                    'label' => '认证状态',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model['authen_status'] == 1 ? '已通过' : ($model['authen_status'] == 2 ? '审核中' : ($model['authen_status'] == 3 ? '审核失败' : '未认证'));
                    }
                ], [
                    'label' => '提现状态',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model['withdraw_status'] == 1 ? '允许' : '禁止';
                    },
                ], [
                    'label' => '使用状态',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model['status'] == 1 ? '正常' : ($model['status'] == 2 ? '禁用' : '挂失');
                    },
                ], [
                    'label' => '购彩状态',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model['limit_lottery'] == 1 ? '不可购' : '可购彩';
                    },
                ], [
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => function ($model) {
                        
//                         <span class="handle pointer" onclick="editMember(' . $model['user_id'] . ');"> 编辑 |</span>'
                        $str = '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs opts"><ul class="ul1">' .
                                (in_array($model['authen_status'], ['', 0, 2, 3]) ? '<li><span class="handle pointer" onclick="reviewMember(' . $model['user_id'] . ');">审核 |</span></li>' : '') . '<li><span class="handle pointer" onclick="viewMember(' . $model['user_id'] . ');">查看 |</span></li><li><span class="handle pointer" onclick="readCoupons(\'' . $model['cust_no'].'\');"> 优惠券 </span></li>'.
                                '<li><span class="handle pointer">| 更多</span><ul class="ul2">'.($model['is_inviter'] == 0 ? '<li><span class="handle pointer" onclick="inviteChange('.$model['user_id'] . ');"> 成为邀请人 </span></li>' : '<li><span class="handle pointer" onclick="inviteChange('  . $model['user_id'] . ');"> 降级邀请资格 </span></li>').($model['spread_type'] == 0 ? '<li><span class="handle pointer" onclick="editType(' . $model['user_id'] . ',' . $model['rebate'] . ',1' . ');"> 成为推广员 </span></li>' : '<li><span class="handle pointer" onclick="editType(' . $model['user_id'] . ',0,0' . ');"> 降为普通会员 </span></li><li><span class="handle pointer" onclick="readUser(\'' . $model['cust_no'].'\');"> 我的推广 </span></li><li><span class="handle pointer" onclick="editSpread(' . $model['user_id'] . ');"> 修改返点 </span></li>' ) .($model['status'] == 1 ? '<li><span class="handle pointer" onclick="editSta(' . $model['user_id'] . ');"> 禁用 </span></li>' : '<li><span class="handle pointer" onclick="editSta(' . $model['user_id'] . ');"> 启用 </span></li>' ) . '<li><span class="handle pointer" onclick="delMember(' . $model['user_id'] . ');"> 删除 </span></li><li><span class="handle pointer" onclick="banWithdraw(' . $model['user_id']. ',' . $model['withdraw_status'] . ');"> ' . ($model['withdraw_status'] == 1 ? '禁止提现 ' : '允许提现 ').'</span></li><li><span class="handle pointer" onclick="changeLimitLot(' . $model['user_id'] . ');"> ' . ($model['limit_lottery'] == 1 ? '可购彩' : '不可购') . '</span></li>' . ($model['status'] == 1 ? '<li><span class="handle pointer" onclick="cancelCard(' . $model['user_id'] . ');">删帖封号 </span></li>' : '') .'</ul></li></ul></div></div>';
                        return $str;
                    }
                        ]
                    ],
                ]);
                $this->title = 'Lottery';
                ?>
<script>
    $(function () {
        $('#addMember').click(function () {
            location.href = '/member/list/add-member';
        });

        $('#reset').click(function () {
            location.href = '/member/list';
        });
    });
    function editMember(id) {
        location.href = '/member/list/edit-member?user_id=' + id;
    }
    function viewMember(id) {
        location.href = '/member/list/view-member?user_id=' + id;
    }
    function reviewMember(id) {
        modDisplay({title: '审核', url: '/member/list/review-member?user_id=' + id, height: 280, width: 450});
    }
    function readUser(custNo) {
        location.href = '/member/list/read-user?cust_no=' + custNo;
    }

    //会员启用禁用
    function editSta(id) {
        $.ajax({
            url: "/member/list/edit-status",
            type: "POST",
            async: false,
            data: {user_id: id},
            dataType: "json",
            success: function (data) {
                if (data["code"] != 600) {
                    msgAlert(data["msg"]);
                } else {
                    msgAlert(data['msg'], function () {
                        location.reload();
                    });
                }
            }
        });
    }
    function changeUserType(id) {
        $.ajax({
            url: "/member/list/change-user-type",
            type: "POST",
            async: false,
            data: {user_id: id},
            dataType: "json",
            success: function (data) {
                if (data["code"] != 600) {
                    msgAlert(data["msg"]);
                } else {
                    msgAlert(data['msg'], function () {
                        location.reload();
                    });
                }
            }
        });
    }
    function delMember(id) {
        msgConfirm('提醒', '确定要删除此会员吗？', function () {
            $.ajax({
                url: "/member/list/delete-member",
                type: "POST",
                async: false,
                data: {user_id: id},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            });
        })
    }
    //推广员、普通会员转换
    function editType(id, value, type) {
        var str = "";
        if (type == 1) {
            modDisplay({title: '设置推广类型', url: '/member/list/edit-spread-type?userId=' + id, height: 350, width: 450});
        } else {
            str = "确定将此会员降级为普通会员？"
            msgConfirm('提醒',str,function () {
                $.ajax({
                    url: "/member/list/edit-spread-type",
                    type: "POST",
                    async: false,
                    data: {user_id: id, type: type},
                    dataType: "json",
                    success: function (data) {
                        if (data["code"] != 600) {
                            msgAlert(data["msg"]);
                        } else {
                            msgAlert(data['msg'], function () {
                                location.reload();
                            });
                        }
                    }
                });
            })
        }
    }
    function banWithdraw(userId, banStatus) {
        var str = '';
        (banStatus == 1) ? str="禁止" : str="允许";
        msgConfirm('提醒', '确定要'+ str  + '此会员提现吗？', function () {
            $.ajax({
                url: "/member/list/ban-withdraw",
                type: "POST",
                async: false,
                data: {user_id: userId},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            });
        })
    }
    //查看我的优惠券
    function readCoupons(cust_no){
        location.href = '/member/list/read-user-coupons?cust_no=' + cust_no;
    }
    //修改返点信息
    function editSpread(id) {
        modDisplay({title: '修改返点信息', url: '/member/list/edit-spread?user_id=' + id, height: 300, width: 600});
    }
    //邀请人状态变更
    function inviteChange(id) {
        msgConfirm("提示", "确定更改用户邀请状态？", function () {
            $.ajax({
                url: "/member/list/invite-change",
                async: false,
                type: "POST",
                dataType: "json",
                data: {user_id: id},
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
    //注册来源
    $("#register_from").change(function () {
        var register_from =$(this).val();
        $("#from_id").empty();
        // var regAry = ['3','5','6','7','8'];
        // if($.inArray(register_from,regAry)>=0){
            $.ajax({
                url: '/member/list/get-register-from',
                async: false,
                type: 'POST',
                data: {register_from: register_from},
                dataType: 'json',
                success: function (json) {
                    if (json['code'] != 600) {
                        msgAlert(json['msg']);
                    } else {
                        var res =json["result"];
                        var html="";
                        if(res==""){
                            $("#from_id").append("<option value=''>请选择</option>");
                        }else{
                            for(var i= 0;i<res.length;i++){
                                html +="<option value='"+res[i]["id"]+"'>"+res[i]["name"]+"</option>"
                            }
                            $("#from_id").append(html)

                        }
                    }
                }
            });
        // }else{
        //     $("#from_id").append("<option value=''>请选择</option>");
        // }

    })

    //搜索
    function search() {
        document.forms[0].target = "rfFrame";
        var user_info = $("#user_info").val();
        var agents_info = $("#agents_info").val();
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var authen_status = $("#authen_status").val();
        var user_level = $("#user_level").val();
        var vxstatus = $("#vxstatus").val();
        var glcoin = $("#glcoin").val();
        var user_glcoin = $("#user_glcoin").val();
        var balance = $("#balance").val();
        var balance_val = $("#balance_val").val();
        var spread_type = $("#spread_type").val();
        var invite_user = $("#invite_user").val();
        var register_from = $("#register_from").val();
        var from_id = $("#from_id").val();
        var param = '?1=1';
        if (user_info != "") {
            param += "&user_info=" + user_info;
        }
        if (agents_info != "") {
            param += "&agents_info=" + agents_info;
        }
        if (startdate != "") {
            param += "&startdate=" + startdate;
        }
        if (enddate != "") {
            param += "&enddate=" + enddate;
        }
        if (authen_status != "") {
            param += "&authen_status=" + authen_status;
        }
        if (user_level !="" &&user_level !=0) {
            param += "&user_level=" + user_level;
        }
        if (vxstatus != "") {
            param += "&vxstatus=" + vxstatus;
        }
        if (glcoin != 0) {
            param += "&glcoin=" + glcoin;
        }
        if (user_glcoin != "") {
            param += "&user_glcoin=" + user_glcoin;
        }
        if (balance != 0) {
            param += "&balance=" + balance;
        }
        if (balance_val != "") {
            param += "&balance_val=" + balance_val;
        }

        if ( spread_type != "") {
            param += "&spread_type=" + spread_type;
        }
        if (invite_user != "") {
            param += "&invite_user=" + invite_user;
        }
        if (register_from != "") {
            param += "&register_from=" + register_from;
        }
        if (from_id != "") {
            param += "&from_id=" + from_id;
        }
        // if (source != "") {
        //     param += "&source=" + source;
        // }
        location.href = '/member/list/index' + param;
    }
    
    //邀请人状态变更
    function changeLimitLot(id) {
        msgConfirm("提示", "确定更改用户购彩状态？", function () {
            $.ajax({
                url: "/member/list/change-limit-lottery",
                async: false,
                type: "POST",
                dataType: "json",
                data: {user_id: id},
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
    
    //会员启用禁用
    function cancelCard(id) {
        $.ajax({
            url: "/member/list/edit-status",
            type: "POST",
            async: false,
            data: {user_id: id, active_type: 2},
            dataType: "json",
            success: function (data) {
                if (data["code"] != 600) {
                    msgAlert(data["msg"]);
                } else {
                    msgAlert(data['msg'], function () {
                        location.reload();
                    });
                }
            }
        });
    }
    
</script>
