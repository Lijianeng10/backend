<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div style="text-align: left;margin-bottom: 5px;margin-top: 10px;margin-left: 10px;font-size: 14px;" id="user">
    <form id="userIn">
        <?php
        echo Html::label("请输入会员信息:", "", ["style" => "margin-left:15px;"]);
        echo Html::input("input", "user_inform", isset($userInform) ? $userInform : "", ["id" => "user_inform", "class" => "form-control", "placeholder" => "会员编号/名称/手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);

        echo Html::submitButton("搜索", ["class" => "searchUser am-btn am-btn-primary", "style" => "margin-left:5px;"]);
        echo '<span style="color:#ccc">请输入会员编号/名称/手机号，点击查询即可定位会员！</span>';
        ?>
    </form>
</div>
<div id="read">

</div>

<legend></legend>
<div>
    <label>兑换日期：</label>
    <label id="ex_time"><?php echo $exData['ex_time']; ?></label>
</div>
<div>
    <label>兑换单号：</label>
    <label id="ex_order"><?php echo $exData['ex_order']; ?></label>
</div>
<div>
    <label>操作人员：</label>
    <label id="opt_name"><?php echo $exData['opt_name'] ?></label>
</div>
<legend></legend>
<div style="width:100%">
    <label style="width:45%;">
        <div>
            <div style="height:10%;text-align:center;margin-bottom: 0.7rem;" >
                <form>
                    <?php
                    echo Html::label("礼品名称或简码：", "", ["style" => "margin-left:15px;"]);
                    echo Html::input("input", "gift_inform", isset($giftInform) ? $giftInform : "", ["id" => "gift_inform", "class" => "form-control", "placeholder" => "礼品名称/简码", "style" => "width:200px;display:inline;margin-left:5px;"]);

                    echo Html::submitButton("搜索", ["class" => "searchGift am-btn am-btn-primary", "style" => "margin-left:5px;"]);
                    echo Html::submitButton("重置", ["class" => "ressetGift am-btn am-btn-primary", "style" => "margin-left:5px;"]);
                    ?>
                </form>
            </div>
            <div style="text-align:center;" >
                <table border="1" style="width: 80%;margin:auto;">
                    <thead >
                        <tr text-align="center">
                            <th style="text-align: center">礼品名称</th>
                            <th style="text-align: center">礼品简码</th>
                            <th style="text-align: center">所需咕币</th>
                            <th style="text-align: center">库存</th>
                            <th style="text-align: center">操作</th>
                        </tr>
                    </thead>
                    <tbody id="gift">
                        <?php foreach ($gift as $val): ?>
                            <tr id="gift_<?php echo $val['gift_id']; ?>">
                                <td><?php echo $val['gift_name']; ?></td>
                                <td><?php echo $val['gift_code']; ?></td>
                                <td><?php echo $val['gift_glcoin']; ?></td>
                                <td><?php echo $val['in_stock']; ?></td>
                                <td><input type="checkbox" class="chose" value="<?php echo $val['gift_id']; ?>" style="width:15px;height:15px"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align:center;color:#ccc" >
                点击列表中的商品即可轻松选定，同时也可以通过简码、名称进行搜索或条码直接定位！
            </div>
        </div>
    </label>
    <label style="width:45%;">
        <div >
            <div style="height:10%;text-align:center;">
                <h2>兑换明细</h2>
            </div>
            <div style="text-align:center;" >
                <table border="1" style="width: 80%;margin:auto;">
                    <thead >
                        <tr>
                            <th style="text-align: center">礼品名称</th>
                            <th style="text-align: center">礼品简码</th>
                            <th style="text-align: center">所需咕币</th>
                            <th style="text-align: center">兑换数量</th>
                            <th style="text-align: center">扣除咕币</th>
                            <th style="text-align: center">操作</th>
                        </tr>
                    </thead>
                    <tbody id="exgift">
                    </tbody>
                </table>
            </div>
        </div>
        <div >
            <button class="am-btn am-btn-secondary" id="addSubmit" style="margin-left:77px;margin-top: 10px;">提交</button>
        </div>
    </label>
</div>
<iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>

<script>
    $(function () {
        $('.searchUser').click(function () {
            document.forms[0].target = "rfFrame";
            $('#read').html('');
            userIn = $('#user_inform').val();
            if (userIn == '') {
                msgAlert(" 请填写相关信息！");
            } else {
                $.ajax({
                    url: '/member/redeem/search',
                    async: false,
                    type: 'POST',
                    data: {user_inform: userIn},
                    dataType: 'json',
                    success: function (data) {
                        if (data['code'] == 600) {
                            result = data.result;
                            console.log(result);
                            var html = '<div id="inform"><legend>会员基础信息</legend>' + '<div>' +
                                    ' <label  for="doc-vld-name-2-1">会员编号：</label>' +
                                    ' <label class="doc-read">' + result.cust_no + '</label>' +
                                    '<label for="doc-vld-name-2-1">会员姓名：</label>' +
                                    ' <label class="doc-read">' + result.user_name + '</label>' +
                                    ' </div><div>' +
                                    '<label for="doc-vld-name-2-1">会员余额：</label>' +
                                    '<label class="doc-read">' + result.all_funds + '</label>' +
                                    '<label for="doc-vld-name-2-1">会员咕币：</label>' +
                                    ' <label class="doc-read">' + result.user_glcoin + '</label>' +
                                    '</div><div>' +
                                     '<label for="doc-vld-name-2-1">累计消费：</label>' +
                                    '<label class="doc-read">' + result.paytotal["pay"] + '</label>' +
                                    '<label for="doc-vld-name-2-1">手机号码：</label>' +
                                    '<label class="doc-read">' + result.user_tel + '</label>' +
                                    '</div><div>' +
                                    '<label for="doc-vld-name-2-1">会员状态：</label>' +
                                    '<label class="doc-read">' + (result.user_type==1?"正常":"禁用")+ '</label>' +
                                    '<label for="doc-vld-name-2-1">所属上级：</label>' +
                                    '<label class="doc-read">' + result.agent_name + '</label>' +
                                    '</div><div>' +
//                                    '<label for="doc-vld-name-2-1">会员生日：</label>' +
//                                    '<label class="doc-read">' + result.province + result.city + result.area + result.address + '</label>' +
                                     '<label for="doc-vld-name-2-1">会员等级：</label>' +
                                    '<label class="doc-read">' + result.level_name + '</label>' +
                                    ' </div><div>' +
//                                    '<label for="doc-vld-name-2-1">会员性别：</label>' +
//                                    '<label class="doc-read">' + result.province + result.city + result.area + result.address + '</label>' +
                                    '</div><legend></legend><div>' +
                                    '<button class="am-btn am-btn-secondary" id="putaway" >收起</button>' +
                                    '<button class="am-btn am-btn-secondary" id="laydown" style="display:none;" >展示</button>' +
                                    '</div><input type="hidden" name="user_id" id="user_id" value="' + result.user_id + '">'
                            $('#read').html(html)
                        } else {
                            msgAlert(data["msg"]);
                        }
                    }
                })
            }
        })
        //礼品搜索
        $('.searchGift').click(function (){
            document.forms[1].target = "rfFrame";
            $('#gift').html('');
            giftIn = $('#gift_inform').val();
            $.ajax({
                url: '/member/redeem/search',
                async: false,
                type: 'POST',
                data: {
                    gift_inform: giftIn
                },
                dataType: 'json',
                success: function (data) {
                    if (data['code'] == 600) {
                        result = data.result;
                        if (result == null) {
                            return false;
                        }
                        var html = '';
                        $(result).each(function (i, val) {
                            html += '<tr id="gift_' + val.gift_id + '"><td>' + val.gift_name + '</td><td>' + val.gift_code + '</td><td>' + val.gift_glcoin +
                                    '</td><td>' + val.in_stock + '</td><td><input type="checkbox" class="chose" value="' + val.gift_id + '" style="width:15px;height:15px"></td></tr>';
                        });
                        $('#gift').html(html);
                    } else {
                        msgAlert('操作错误');
                    }
                }
            })
        })
        //礼品重置
        $(".ressetGift").click(function(){
            $('#gift_inform').val("");
             $.ajax({
                url: '/member/redeem/search',
                async: false,
                type: 'POST',
                data: {gift_inform: ""},
                dataType: 'json',
                success: function (data) {
                    if (data['code'] == 600) {
                        result = data.result;
                        if (result == null) {
                            return false;
                        }
                        var html = '';
                        $(result).each(function (i, val) {
                            html += '<tr id="gift_' + val.gift_id + '"><td>' + val.gift_name + '</td><td>' + val.gift_code + '</td><td>' + val.gift_glcoin +
                                    '</td><td>' + val.in_stock + '</td><td><input type="checkbox" class="chose" value="' + val.gift_id + '"style="width:15px;height:15px"></td></tr>';
                        });
                        $('#gift').html(html);
                    } else {
                        msgAlert('操作错误');
                    }
                }
            })
        })
        //展开、收起
        $("#read").on('click', "#putaway", function () {
            $("#inform").css('display', 'none');
            $("#putaway").css('display', 'none');
            $("#laydown").css('display', 'block');
        })
        $("#read").on('click', "#laydown", function () {
            $("#inform").css('display', 'block');
            $("#putaway").css('display', 'block');
            $("#laydown").css('display', 'none');
        })
        //选中礼品
        $("#gift").on("click", ".chose", function () {
            if ($(this).is(':checked')) {
                var concent = [];
                idstr = 'gift_' + $(this).val();
                tr = $('#' + idstr).html();
                $(tr).each(function () {
                    if (concent.length == 3) {
                        return false;
                    }
                    if (this.innerHTML != undefined) {
                        concent.push(this.innerHTML);
                    }
                })
                html = '<tr id="exgift_' + $(this).val() + '"><td>' + concent[0] + '</td><td>' + concent[1] + '</td><td>' + concent[2] +
                        '</td><td><input type="text" class="ex_nums" data-id="' + $(this).val() + '" data-int="' + concent[2] + '" style="width:50%;border:1px solid" value="1"></td><td id="ex_nums_' + $(this).val() + '">' + concent[2] + '</td><td><a class="del">删除</a></td></tr>';
                $('#exgift').append(html)
            }  
        });
        //兑换数量变化
        $("#exgift").on("change", ".ex_nums", function () {
            var nums = $(this).val();
            if (nums > 0 && nums % 1=== 0) {
                var idstr = $(this).attr('data-id');
                var pirce = $(this).attr('data-int');
                $('#ex_nums_' + idstr).html(nums * pirce);
            } else {
                msgAlert("请输入正确的正整数")
            }
        })
        //删除兑换明细
        $("#exgift").on("click", '.del', function () {
            $(this).parent().parent().remove();
        })
        //提交兑换信息
        $("#addSubmit").click(function () {
            var exgift = [];
            var i = 0;
            var a = 0;
            var exTime = $('#ex_time').text();
            var exOrder = $('#ex_order').text();
            var  optName = $('#opt_name').text();
            var userId = $('#user_id').attr('value');
            if (userId == undefined) {
                msgAlert("请先确认要申请兑换的会员");
                return false;
            }
            if ( $('#exgift tr').length == 0) {
                msgAlert("请先选择要兑换的礼品");
                return false;
            }
            $('#exgift tr').each(function () {
                var exgiftObj = new Object();
                var content = [];
                tr = this.innerHTML;
                var nums = $(this).find("input[class='ex_nums']").val();
                if (nums > 0 && nums % 1=== 0) {
                    content.push(nums);
                } else {
                    msgAlert("请填入正确的兑换数量");
                    return false;
                }
                $(tr).each(function () {
                    if (this.innerHTML != undefined) {
                        content.push(this.innerHTML);
                    }
                    exgiftObj.ex_nums = content[0];
                    exgiftObj.gift_name = content[1];
                    exgiftObj.gift_code = content[2];
                    exgiftObj.need_int = content[3];
                    exgiftObj.all_int = content[5];
                })
                exgift.push(exgiftObj);
            })
            if (exgift.length == $('#exgift tr').length ) {
                 $.ajax({
                    url: '/member/redeem/add-exgift',
                    async: false,
                    type: 'POST',
                    data: {
                        user_id: userId,
                        ex_time: exTime,
                        ex_order: exOrder,
                        opt_name: optName,
                        ex_gift: exgift
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data['code'] != 600) {
                            msgAlert(data['msg']);
                        } else {
                            msgAlert(data['msg'], function () {
                                location.reload();
                            });
                        }
                    }
                })
            }
        })
    })
</script>
