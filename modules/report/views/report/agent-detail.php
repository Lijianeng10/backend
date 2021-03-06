
<form class="myForm" id="mxForm">
    <input type="hidden" value="<?php echo isset($_GET['sNo']) ? $_GET['sNo'] : "" ?>"  id="sNo">
    <input type="hidden" value="<?php echo isset($_GET['type']) ? $_GET['type'] : "" ?>"  id="type">
    <input type="hidden" value="<?php echo isset($_GET['lottery']) ? $_GET['lottery'] : "" ?>"  id="lottery">
    <input type="hidden" value="<?php echo isset($_GET['star']) ? $_GET['star'] : "" ?>"  id="star">
    <input type="hidden" value="<?php echo isset($_GET['end']) ? $_GET['end'] : "" ?>"  id="end">
    <input type="hidden" value="<?php echo isset($_GET['days']) ? $_GET['days'] : "" ?>"  id="days">
    <input type="hidden" value="<?php echo isset($_GET['months']) ? $_GET['months'] : "" ?>"  id="months">
    <label style="margin-left:15px;" for="">店铺/彩种:</label>
    <input type="text" id="name"  class="form-control" style="width:150px;display: inline-block;" value="<?php
    if(isset($_GET['store'])){
        echo $_GET['store'];
    }elseif(isset($_GET['lottery_name'])){
        echo $_GET['lottery_name'];
    }else{
        echo "";
    }
    ?>" disabled="true">
    <label>统计时间:</label>
    <input type="text" class='form-control' style="width: 100px;display: inline;margin-left:5px;"  value="<?php echo isset($_GET['type'])&&$_GET['type']==1 ? "出票时间" : "投注时间" ?>"  disabled="true"/>
    <input type="text" class='form-control' style="width: 100px;display: inline;margin-left:5px;"  value="<?php
    if(isset($_GET['days'])){
        echo $_GET['days'];
    }elseif(isset($_GET['star'])){
        echo $_GET['star'];
    }else{
        echo "";
    }
    ?>"  disabled="true"/>
<!--    <label>出票月份:</label>-->
    <input type="text" class='form-control' style="width: 100px;display: inline;margin-left:5px;"  value="<?php
    if(isset($_GET['months'])){
        echo $_GET['months'];
    }elseif(isset($_GET['end'])){
        echo $_GET['end'];
    }else{
        echo "";
    }
    ?>"  disabled="true"/>
    <label style="margin-top: 15px;">订单来源:</label>
    <select id="platFrom"  style="width:100px;display:inline;margin-left:5px;" class="form-control">
        <?php foreach ($platFrom as $key => $item): ?>
            <option value="<?php echo $key; ?>"><?php echo $item; ?></option>
        <?php endforeach; ?>
    </select>
    <select id="from" style="display: none; width:100px;margin-left:5px;" class="form-control">
    </select>
    <select id="from_user" style="display: none;width:100px;margin-left:5px;" class="form-control">
    </select>
    <input type="button" class="am-btn am-btn-primary" style="margin-left:10px" id="filterButton" value="统计">
    <input type="button" class="am-btn am-btn-primary" style="margin-left:2px;" value="返回" onclick="goBack()">
    <input type="button" class="am-btn am-btn-primary" id="btnExport" value="导出">

</form>

<table class="table" id="pwTable">
    <thead>
        <tr>
            <th style="text-align: center;">日期/彩种</th>
            <th style="text-align: center;">订单平台</th>
            <th style="text-align: center;">名称</th>
            <th style="text-align: center;">下单人数</th>
            <th style="text-align: center;">订单数</th>
            <th style="text-align: center;">待开奖订单数</th>
            <th style="text-align: center;">待派奖订单数</th>
            <th style="text-align: center;">订单总金额</th>
            <th style="text-align: center;">出票扣款</th>
            <th style="text-align: center;">实际收入</th>
            <th style="text-align: center;">中奖金额</th>
            <th style="text-align: center;">派奖金额</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="M-box"></div>
<script type="text/javascript">
    $(function () {
        getSaleDetail()
        //搜索
        $("#filterButton").click(function () {
            var platFrom =$("#platFrom").val();
            var from =$("#from").val();
            var from_user =$("#from_user").val();
            getSaleDetail({platFrom:platFrom,from:from,from_user:from_user});
        })
        // 获取订单明细
        function getSaleDetail(options) {
            var name = $("#name").val();
            var sNo=$("#sNo").val();
            var timer = $("#days").val();
            var months = $("#months").val();
            var type=$("#type").val();
            var lottery=$("#lottery").val();
            var star = $("#star").val();
            var end = $("#end").val();
            var data = $.extend({sNo:sNo,timer: timer, months: months,type:type,lottery:lottery, star: star,end:end}, options);
            $.ajax({
                url: "/report/report/get-agent-detail",
                type: "POST",
                data: data,
                async: false,
                dataType: "json",
                success: function (json) {
                    if (json["result"] == "") {
                        $("#pwTable tbody").html("暂无此项统计数据");
                        return false;
                    }
                    var html = "";
                    var counts = 0;
                    var ordernums = 0;
                    var noopen = 0;
                    var noaward = 0;
                    var salemoneys = 0;
                    var paymoneys = 0;
                    var winmoneys = 0;
                    var awardmoneys = 0;
                    var platForm = new Array();
                    platForm[1] = "咕啦";
                    platForm[2] = "代理商";
                    platForm[3] = "渠道";
                    platForm[4] = "推广";
                    $.each(json["result"], function (key, val) {
                        counts += eval(val.count);
                        ordernums += eval(val.ordernum);
                        noopen += eval(val.stayopen);
                        noaward += eval(val.stayaward);
                        salemoneys += eval(val.salemoney);
                        paymoneys += eval(val.paymoney);
                        winmoneys += eval(val.winmoney);
                        awardmoneys +=eval(val.award_amount);
                        html += "<tr styly='text-align: center'>"
                        html += "<td style='text-align: center'>" + (timer!=""?timer:(months!=""?months:name)) + "</td>"
                        html += "<td style='text-align: center'>" + platForm[val.order_platform] + "</td>"
                        html += "<td style='text-align: center'>" + (val.agents_name?val.agents_name:"") + "</td>"
                        html += "<td style='text-align: center'>" + val.count + "</td>"
                        html += "<td style='text-align: center'>" + val.ordernum + "</td>"
                        html += "<td style='text-align: center'>" + val.stayopen + "</td>"
                        html += "<td style='text-align: center'>" + val.stayaward + "</td>"
                        html += "<td style='text-align: center'>" + val.salemoney + "</td>"
                        html += "<td style='text-align: center'>" + (val.paymoney!=null?val.paymoney:"0.00")+ "</td>"
                        html += "<td style='text-align: center'>" + eval(val.salemoney - val.paymoney) + "</td>"
                        html += "<td style='text-align: center'>" + val.winmoney + "</td>"
                        html += "<td style='text-align: center'>" + (val.award_amount!=null?val.award_amount:"0.00") + "</td>"
                        html += "</tr>"
                    });
                    html += "<tr style='font-weight:bold;background-color:#E9ECF3'><td style='text-align: center;font-size:16px'>统计</td><td style='text-align: center;font-size:16px'>平台</td><td style='text-align: center;font-size:16px'>名称</td><td style='text-align: center;font-size:16px'>" + counts + "</td><td style='text-align: center;font-size:16px'>" + ordernums + "</td><td style='text-align: center;font-size:16px'>" + noopen + "</td><td style='text-align: center;font-size:16px'>" + noaward + "</td><td style='text-align: center;font-size:16px'>" + salemoneys + "</td><td style='text-align: center;font-size:16px'>" + paymoneys.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>" + eval(salemoneys - paymoneys) + "</td><td style='text-align: center;font-size:16px'>" + winmoneys.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>"+ awardmoneys.toFixed(2) + "</td><td style='text-align: center;font-size:16px'></td></tr>"
                    if (html == '') {
                        html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                    }
                    $("#pwTable tbody").html(html);
                }
            })
        }
        //订单来源变化
        $("#platFrom").change(function () {
            $("#from_user").empty();
            $('#from_user').css("display","none");
            var from = $(this).val();
            if (from != 0) {
                $.ajax({
                    url: '/report/report/get-plat-from',
                    async: false,
                    type: 'POST',
                    data: {from: from},
                    dataType: 'json',
                    success: function (json) {
                        if (json['code'] != 600) {
                            msgAlert(json['msg']);
                        } else {
                            $("#from").empty();
                            var res = json["result"];
                            var html = "<option value=0>请选择</option>"
                            switch (from) {
                                case '1' :
                                    $.each(res, function (index, value) {
                                        html += "<option value=" + index + ">" + value + "</option>";
                                    });
                                    break;
                                case '2' :
                                    $.each(res, function (index, value) {
                                        html += "<option value=" + index + ">" + value["agents_name"] + "</option>";
                                    });
                                    break;
                                case '3' :
                                    $.each(res, function (index, value) {
                                        html += "<option value=" + index + ">" + value['name'] + "</option>";
                                    })
                                    break;
                                case '4' :
                                    $.each(res, function (index, value) {
                                        html += "<option value=" + index + ">" + value['user_name'] + "</option>";
                                    })
                                    break;
                            }
                            $('#from').css("display","inline-block");
                            $('#from').append(html);

                        }
                    }
                });
            }else {
                $("#from").empty();
                $("#from").append('<option value=0>请选择</option>');
            }
        })
        //订单来源:咕啦平台
            $("#from").change(function () {
                var platFrom = $("#platFrom").val();
                var from_type = $(this).val();
                if (platFrom == 1&&from_type==2) {
                    $.ajax({
                        url: '/report/report/get-spread-user',
                        async: false,
                        type: 'POST',
                        data: {},
                        dataType: 'json',
                        success: function (json) {
                            if (json['code'] != 600) {
                                msgAlert(json['msg']);
                            } else {
                                $("#from_user").empty();
                                var res = json["result"];
                                var html = "<option value=0>请选择</option>"
                                $.each(res, function (index, value) {
                                    html += "<option value=" + index + ">" + value["user_name"] + "</option>";
                                });
                                html += "</select>";
                                $('#from_user').css("display","inline-block");
                                $('#from_user').append(html);
                            }
                        }
                    });
                }else {
                    $("#from_user").empty();
                    $('#from_user').css("display","none");
                }
            })
        //导出表格
        $("#btnExport").click(function(){
            msgConfirm('提醒',"确定导出报表?",function(){
                $('#pwTable').tableExport({
                    type:'excel',
                    escape:'false',
                    fileName: '门店销售通道详情统计'
                });
            })

        });
    })
</script>
