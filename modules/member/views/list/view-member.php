<link href="/css/font-awesome.min.css" rel="stylesheet">
<style>
    .infoSpan{
        width: 110px;
        text-align: right;
    }
    #storeInfo th,#storeInfo td{
        font-size: 14px;
        text-align: center;
    }
    #addBtn{
        margin: 5px 5px;
    }
    table th,td{
      border: 1px solid #ccc;
    }
</style>
<legend>查看会员</legend>
<div>
    <legend>会员基础信息</legend>
    <div>
        <label  class="infoSpan">会员编号：</label>
        <label class="doc-read" id="cust_no"><?php echo $user_data['cust_no']; ?></label>
        <label  class="infoSpan">会员昵称：</label>
        <label class="doc-read"><?php echo $user_data['user_name']; ?></label>
    </div>
    
     <div>
        <label class="infoSpan">所在城市：</label>
        <label class="doc-read"><?php echo $user_data['province'] . '- ' . $user_data['city'] . '- ' . $user_data['area'] . $user_data['address']; ?></label>
        <label  class="infoSpan">邀请码：</label>
        <label class="doc-read"><?php echo $user_data['invite_code']; ?></label></div>
    <div>
        <label class="infoSpan">手机号码：</label>
        <label class="doc-read"><?php echo $user_data['user_tel']; ?></label>
         <label class="infoSpan">用户等级：</label>
        <label class="doc-read"><?php echo $user_data["level_name"]." ( ".$user_data['user_growth']." ) "; ?></label>
    </div>
    
    <div>
        <label class="infoSpan">咕币：</label>
        <label class="doc-read"><?php echo $user_data['user_glcoin']; ?></label>
        <label class="infoSpan">礼品卡：</label>
        <label class="doc-read"><?php echo "0"; ?></label>
    </div>
    <div>
        <label class="infoSpan">使用状态：</label>
        <label class="doc-read"><?php echo $user_status[$user_data['status']]; ?></label>
        <label class="infoSpan">认证状态：</label>
        <label class="doc-read"><?php echo $authen[$user_data['authen_status']]; ?></label>
    </div>
    <div>
        <label class="infoSpan">实名状态：</label>
        <label class="doc-read"><?php echo !empty($user_data['javaStatus']['data']['checkStatus'])&&$user_data['javaStatus']['data']['checkStatus']==1?"已认证":"未认证"; ?></label>
        <label class="infoSpan">层级关系：</label>
        <label class="doc-read"><?php echo $user_data['p_tree']; ?></label>
    </div>
     <div>
        <label class="infoSpan">推广员：</label>
        <label class="doc-read"><?php echo  $user_data['spread_type']!=0?"是(".$user_data['spread_type']."级推广)":"否"; ?></label>
        <label class="infoSpan">推广返点：</label>
        <label class="doc-read"><?php echo $user_data['rebate']."%"; ?></label>
    </div>
    <div>
        <label class="infoSpan">邀请资格：</label>
        <label class="doc-read"><?php echo $user_data['is_inviter']?'是':'否'; ?></label>
        <label class="infoSpan">设备号：</label>
        <label class="doc-read"><?php echo $user_data['imei']; ?></label>
    </div>
    <legend></legend>
    <legend>会员结算账户</legend>
    <div>
        <label class="infoSpan">总金额：</label>
        <label class="doc-read"><?php echo  $user_data['all_funds']; ?></label>
        <label class="infoSpan">可用余额：</label>
        <label class="doc-read"><?php echo $user_data['able_funds']; ?></label>
    </div>
    <div>
        <label class="infoSpan">冻结金额：</label>
        <label class="doc-read"><?php echo  $user_data['ice_funds']; ?></label>
        <label class="infoSpan">不可提现金额：</label>
        <label class="doc-read"><?php echo $user_data['no_withdraw']; ?></label>
    </div>
    <div>
        <label class="infoSpan">可提现金额：</label>
        <label class="doc-read"><?php echo  $user_data['able_funds']-$user_data['no_withdraw']; ?></label>
    </div>
    <div>
        <label class="infoSpan">户名：</label>
        <label class="doc-read"><?php echo !empty($user_data['detail']['data'])?$user_data['detail']['data']['realName']:""; ?></label>
        <label class="infoSpan">账号：</label>
        <label class="doc-read"><?php echo !empty($user_data['detail']['data'])?$user_data['detail']['data']['bankNo']:""; ?></label>
    </div>
    <div>
        <label class="infoSpan">开户行：</label>
        <label class="doc-read"><?php echo !empty($user_data['detail']['data'])?$user_data['detail']['data']['depositBank']:""; ?></label>
        <label class="infoSpan">支行名称：</label>
        <label class="doc-read"><?php echo !empty($user_data['detail']['data'])?$user_data['detail']['data']['bankOutlets']:""; ?></label>
        
    </div>
    <legend></legend>
    <legend>会员说明信息</legend>
    <div>
        <label class="infoSpan">正面：</label>
        <label class="doc-read">
            <?php echo !empty($user_data['card']['data']['cardFrontImg'])?"<img  data-magnify='gallery' data-src='".$user_data['card']['data']['cardFrontImg']."' src='".$user_data['card']['data']['cardFrontImg']."' width=150px height=150px data-caption='身份证正面'>":""; ?>
        </label>
        <label class="infoSpan">反面：</label>
        <label class="doc-read">
            <?php echo !empty($user_data['card']['data']['cardBackImg'])?"<img  data-magnify='gallery' data-src='".$user_data['card']['data']['cardBackImg']."' src='".$user_data['card']['data']['cardBackImg']."' width=150px height=150px data-caption='身份证反面'>":""; ?>
        </label>
    </div>
    <div>
        <label class="infoSpan">手持身份证：</label>
        <label class="doc-read">
            <?php echo !empty($user_data['card']['data']['cardWithPeopleImg'])?"<img  data-magnify='gallery' data-src='".$user_data['card']['data']['cardWithPeopleImg']."' src='".$user_data['card']['data']['cardWithPeopleImg']."' width=150px height=150px data-caption='手持身份证'>":""; ?>
        </label>
        <label class="infoSpan">银行卡正面：</label>
        <label class="doc-read">
            <?php echo !empty($user_data['card']['data']['bankCardImg'])?"<img  data-magnify='gallery' data-src='".$user_data['card']['data']['bankCardImg']."' src='".$user_data['card']['data']['bankCardImg']."' width=150px height=150px data-caption='银行卡正面'>":""; ?>
        </label>
    </div>
    <legend></legend>
    <legend>会员绑定门店信息</legend>
    <div>
        <table  width="50%" id="storeInfo" style="border:1px solid #ccc;">
            <thead >
                <tr text-align="center" style="border:1px solid #ccc;">
                    <th>序号</th>
                    <th >门店名称</th>
                    <th>门店地址</th>
                    <th>状态</th>
                    <th>绑定时间</th>
                    <th >操作</th>
                </tr>
            </thead>
            <tbody id="gift" >
                <?php foreach ($storeInfo as $key=>$val): ?>
                    <tr style="border:1px solid #ccc;">
                        <td><?php echo $key + 1;?></td>
                        <td ><?php echo $val['store_name']; ?></td>
                        <td ><?php echo $val['province'].$val['city'].$val['area'].$val['address']; ?></td>
                        <td ><?php echo $val['default_status']==1?'系统默认':'会员自主默认'; ?></td>
                        <td ><?php echo $val['create_time']; ?></td>
                        <td class="am-btn am-btn-secondary" style="margin-left: 5px;" onclick="delFollow(<?php echo $val["user_follow_id"];?>)">解绑</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="am-btn am-btn-secondary" id="addBtn">新增</button>
    </div>
    <legend></legend>
    <legend>其他信息</legend>
    <div>
        <div>
            <label class="infoSpan">上级名称：</label>
            <label class="doc-read"><?php echo  $user_data['agent_name'];; ?></label>
        </div>
        <div>
            <label class="infoSpan">创建时间：</label>
            <label class="doc-read"><?php echo  $user_data['create_time'];; ?></label>
        </div>
        <div>
            <label class="infoSpan">最后登录时间：</label>
            <label class="doc-read"><?php echo  $user_data['last_login'];; ?></label>  
         </div>
    </div>
    <legend></legend>
    <button class="am-btn am-btn-secondary" id="backSubmit" >返回</button>
</div>


<script src="/js/jquery.cxselect.min.js"></script>
<script>
    $(function () {
        $("#backSubmit").click(function () {
            history.go(-1);
        })
        //新增绑定门店
        $("#addBtn").click(function(){
            var cust_no=$("#cust_no").html();
            if(cust_no==""){
                msgAlert("用户编号有误,操作错误")
            }else{
               modDisplay({title: '新增绑定门店', url: '/member/list/addstore?cust_no=' + cust_no, height: 280, width: 450}); 
            }
            
        })
    })
    //解绑
    function delFollow(id) {
        msgConfirm("提示","确定需要解绑该用户关注门店吗？",function () {
            $.ajax({
                url: "/member/list/del-user-follow",
                type: "POST",
                async: false,
                data: {id: id},
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
</script>