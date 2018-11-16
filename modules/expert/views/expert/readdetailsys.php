<div style="font-size: 16px;font-weight: 700;margin: 10px 0 8px 0;">专家基本信息</div>
<ul class="am-g expertUl">
    <li class="am-u-sm-6">专家编号<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["userInfo"]["cust_no"]; ?></li><li class="am-u-sm-6">专家昵称<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["userInfo"]["user_name"]; ?></li>
    <li class="am-u-sm-6">手机号码<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["userInfo"]["user_tel"]; ?></li><li class="am-u-sm-6">协议状态<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["expertInfo"]["pactName"]; ?></li>
    <li class="am-u-sm-12">所属城市<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["realInfo"]["province"] . $data["realInfo"]["city"] . $data["realInfo"]["country"] . $data["realInfo"]["address"]; ?></li>
    <li class="am-u-sm-12">简<span style="width:28px;display: inline-block;"></span>介<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["expertInfo"]["introduction"]; ?></li>
</ul>
<div style="font-size: 16px;font-weight: 700;margin: 10px 0 8px 0;">专家结算账户</div>
<ul class="am-g expertUl">
    <li class="am-u-sm-6">总<span style="width:7px;display: inline-block;"></span>资<span style="width:7px;display: inline-block;"></span>产<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["fundsInfo"]["all_funds"]; ?></li><li class="am-u-sm-6">可用余额: <?php echo $data["fundsInfo"]["able_funds"]; ?></li>
    <li class="am-u-sm-12">冻结金额<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["fundsInfo"]["ice_funds"]; ?></li>
    <li class="am-u-sm-6">户<span style="width:28px;display: inline-block;"></span>名<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["bankInfo"]["realName"]; ?></li><li class="am-u-sm-6">账<span style="width:28px;display: inline-block;"></span>号<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["bankInfo"]["bankNo"]; ?></li>
    <li class="am-u-sm-6">支行名称<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["bankInfo"]["bankOutlets"]; ?></li><li class="am-u-sm-6">开<span style="width:7px;display: inline-block;"></span>户<span style="width:7px;display: inline-block;"></span>行: <?php echo $data["bankInfo"]["depositBank"]; ?></li>
</ul>
<label style="font-size: 16px;font-weight: 700;margin: 10px 0 8px 0;">专家说明信息</label><span style="font-size: 14px;">（咕啦实名认证<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["authStatusName"]; ?>）</span>
<ul class="am-g expertUl">
    <li class="am-u-sm-6">真实姓名<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["realInfo"]["realName"]; ?></li><li class="am-u-sm-6">身份证号<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["realInfo"]["cardNo"]; ?></li>
    <li class="am-u-sm-12">身<span style="width:7px;display: inline-block;"></span>份<span style="width:7px;display: inline-block;"></span>证<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><ul style="margin-left: 60px;margin-top: -16px;"><li style="display: inline-block;text-align: center;margin:0 10px 0 10px;"><?php echo "<img class='orderImg' style='display:block;' src='{$data["realInfo"]["cardFrontImg"]}' />"; ?>身份证正面</li><li style="display: inline-block;text-align: center;margin:0 10px 0 10px; "><?php echo "<img class='orderImg' style='display:block;' src='{$data["realInfo"]["cardBackImg"]}'  style='width:80px;height:80px;' />"; ?>身份证反面</li><li style="display: inline-block;text-align: center;margin:0 10px 0 10px;"><?php echo "<img class='orderImg' style='display:block;' src='{$data["realInfo"]["cardWithPeopleImg"]}'  style='width:80px;height:80px;' />"; ?>手持正面身份证</li><li style="display: inline-block;text-align: center;margin:0 10px 0 10px;"><?php echo "<img class='orderImg' style='display:block;' src='{$data["realInfo"]["bankCardImg"]}'  style='width:80px;height:80px;' />"; ?>本人银行卡正面</li></ul></li>
</ul>
<label style="font-size: 16px;font-weight: 700;margin: 10px 0 8px 0;">其它信息</label>
<ul class="am-g expertUl">
    <li class="am-u-sm-6">申请时间<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["expertInfo"]["create_time"]; ?></li><li class="am-u-sm-6">最后登录时间<span style="display: inline-block;margin: 0 5px 0 3px;">:</span><?php echo $data["userInfo"]["last_login"]; ?></li>
</ul>
<button type="button" class="am-btn am-btn-primary" onclick="closeMask();">返回</button>
<script type="text/javascript">
    $(function () {
        $(".orderImg").bigShow();
    });
</script>
