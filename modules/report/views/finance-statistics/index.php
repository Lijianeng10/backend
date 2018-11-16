<?php if ($loginType == 0) : ?>
    <ul class="am-nav am-nav-tabs" id="statusArr" style="margin-bottom:10px;">
        <li role="presentation" class="am-active" flag="0"><a onclick="statusArrClick($(this));">未结算</a></li>
        <li role="presentation" flag="1"><a onclick="statusArrClick($(this));">已结算</a></li>
        <!--<li role="presentation" flag="2"><a  onclick="statusArrClick($(this));">按彩种</a></li>-->
    </ul>
<?php endif; ?>
<div>
    <form class="myForm" id="filterForm1">
        <ul class="third_team_ul" >
            <li>
                <?php if ($loginType == 0): ?>
                    <label>渠道方:</label>
                    <select id="channelInfo"  style="width:100px;display:inline;margin-left:5px;">
                        <?php foreach ($channelData as $key => $val) : ?>
                            <option value="<?php echo $key; ?>"><?php echo $val['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                <label>统计时间:</label>
<!--                <select id="infoType"  style="width:100px;display:inline;margin-left:5px;">
                    <option value="1">出票时间</option>
                    <option value="2">下单时间</option>
                </select>-->
                <select  class="form-control" id="years" style="width: 100px;display: inline;margin-left:5px;">
                    <option><?php echo date("Y"); ?></option>
                    <option><?php echo date("Y") - 1; ?></option>
                </select>
                <select  class="form-control" id="months" style="width: 100px;display: inline;margin-left:5px;">
                    <?php
                    $Ary = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
                    foreach ($Ary as $v) {
                        if ($loginType == 1) {
                            if (date("m", strtotime('-1 month')) == $v) {
                                echo '<option selected>' . $v . '</option>';
                            } else {
                                echo '<option >' . $v . '</option>';
                            }
                        } else {
                            if (date("m") == $v) {
                                echo '<option selected>' . $v . '</option>';
                            } else {
                                echo '<option >' . $v . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </li>
            <li  style="width: 60%">
                <input type="button" class="am-btn am-btn-primary" id="sendYears" value="统计">
                <input type="button" class="am-btn am-btn-primary" id="btnExcelMonth" value="导出">
                <span id = "tcTitle" style="color:#618FFC"></span>
                <?php if ($loginType == 0): ?>
                                                <!--<input type="button" class="am-btn am-btn-primary" id="settle" value="结算">-->
                    <input type="number" id="tcAmount" style="display: none;width: 100px">
                    <input type='button' class='am-btn am-btn-primary' id='grantTc' style="display: none;" value='发放提成'>
                    <input type='button' class='am-btn am-btn-primary' id='unline_grantTc' style="display: none;" value='线下发放'>
                <?php endif; ?>
                <input type="hidden" id="tabType" value="<?php if ($loginType == 1): ?>1<?php else : ?> 0<?php endif; ?>">
                <input type="hidden" id="loginType" value="<?php echo $loginType; ?>">

            </li>
        </ul>
    </form>

    <table class="table" id="pwTable">
        <thead>
            <tr>
                <th style="text-align: center;">日期</th>
                <th style="text-align: center;">中奖(下单时间)</th>
                <th style="text-align: center;">未派奖(下单时间)</th>
                <th style="text-align: center;">已派奖(下单时间)</th>
                <th style="text-align: center;">起始余额</th>
                <th style="text-align: center;background-color:#FF0000;color: #F0F0F0">充值</th>
                <th style="text-align: center;">中奖(派奖时间)</th>
                <th style="text-align: center;background-color:#FF0000;color: #F0F0F0">兑奖</th>
                <th style="text-align: center;background-color:#FF0000;color: #F0F0F0">提成</th>
                <th style="text-align: center;background-color:#FF0000;color: #F0F0F0">总收入</th>
                <th style="text-align: center;background-color:#618FFC;color: #F0F0F0">销量</th>
                <th style="text-align: center;background-color:#618FFC;color: #F0F0F0">提现</th>
                <th style="text-align: center;background-color:#618FFC;color: #F0F0F0">总支出</th>
                <th style="text-align: center;background-color:#52AD8B;color: #F0F0F0">其他</th>
                <th style="text-align: center;">账户余额</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
</div>
<script type="text/javascript">
    $(function () {
        getMonthReport();
        $("#sendYears").click(function () {
            getMonthReport();
        })
    })
    //页面切换选择
    function statusArrClick(_this) {
        var statusArr = _this.data("val");
        $("#statusArr").find("li").removeClass("am-active");
        _this.parent("li").addClass("am-active");
        if (_this.parent("li").attr("flag") == 1 || _this.parent("li").attr("flag") == 0) {
            $("#filterForm").css("display", "none");
            $("#filterForm1").css("display", "block");
            $("#filterForm2").css("display", "none");
            $("#filterForm4").css("display", "none");
            $($("#pwTable thead th")[0]).html("月份");
            $($("#pwTable thead th")[1]).css("display", "block");
            $("#pwTable tbody").html("");
            if (_this.parent("li").attr("flag") == 0) {
                $("#tabType").val(0);
                var strMonth = new Date().getMonth() + 1;
                if (strMonth < 10) {
                    strMonth = '0' + strMonth;
                }
                $("#months").val(strMonth)
            } else {
                $("#tabType").val(1);
                var strMonth = new Date().getMonth();
                if (strMonth < 10) {
                    strMonth = '0' + strMonth;
                }
                $("#months").val(strMonth)
            }
            getMonthReport()
        }
    }
//    获取月统计数据
    function getMonthReport() {
        var years = $("#years").val();
        var months = $("#months").val();
        var sourceNo = $("#channelInfo").val();
        var tabType = $("#tabType").val();
        $("#tcTitle").empty();
        $("#grantTc").hide();
        $("#unline_grantTc").hide();
        $("tcAmount").val('')
        $("#tcAmount").hide();
        var str = '';
        $.ajax({
            url: "/report/finance-statistics/get-statistic",
            type: "POST",
            data: {years: years, months: months, sourceNo: sourceNo, tabType: tabType},
            async: false,
            dataType: "json",
            success: function (json) {
                if (json['code'] != 600) {
                    msgAlert(json["msg"]);
                    return false;
                }
                if (json['result'] == false) {
//                    msgAlert(json["msg"]);
                    return false;
                }
                if (json['result'] == true) {
                    msgAlert(json["msg"]);
                }
                if (json["result"]["data"] == "") {
                    $("#pwTable tbody").html("暂无此项统计数据");
                    return false;
                }
                var html = "";
                var sumCz = 0;
                var sumAward = 0;
                var sumSr = 0;
                var sumTz = 0;
                var sumTx = 0;
                var sumZc = 0;
                var sumTc = 0;
                var sumWin = 0;
                var sumTzWin = 0;
                var sumTzStayAward = 0;
                var sumTzAlreadyAward = 0;
                var sumOrder = 0;
                $.each(json["result"]["data"], function (key, val) {
                    sumCz += eval(val.cz_amount);
                    sumAward += eval(val.award_amount);
                    sumSr += eval(val.sr_amount);
                    sumTz += eval(val.tz_amount);
                    sumTx += eval(val.tx_amount);
                    sumZc += eval(val.zc_amount);
                    sumTc += eval(val.tc_amount);
                    sumWin += eval(val.win_amount);
                    sumTzWin += eval(val.tz_win_money);
                    sumTzStayAward += eval(val.stay_award_money);
                    sumTzAlreadyAward += eval(val.already_award_money);
                    sumOrder += eval(val.order_amount);
                    html += "<tr styly='text-align: center'>"
                    html += "<td style='text-align: center'>" + val.statistics_date + "</td>"
                    html += "<td style='text-align: center'>" + val.tz_win_money + "</td>"
                    html += "<td style='text-align: center'>" + val.stay_award_money + "</td>"
                    html += "<td style='text-align: center'>" + val.already_award_money + "</td>"
                    html += "<td style='text-align: center'>" + val.begin_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.cz_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.win_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.award_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.tc_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.sr_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.tz_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.tx_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.zc_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.order_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.ye_amount + "</td>"
                    html += "</tr>"
                });
                html += "<tr style='font-weight:bold;background-color:#E9ECF3'><td style='text-align: center;font-size:16px'>统计</td><td style='text-align: center;font-size:16px'>" + sumTzWin.toFixed(2) +"</td><td style='text-align: center;font-size:16px'>"+ sumTzStayAward.toFixed(2) +"</td><td style='text-align: center;font-size:16px'>" + sumTzAlreadyAward.toFixed(2) + "</td><td style='text-align: center;font-size:16px'></td><td style='text-align: center;font-size:16px'>" + sumCz.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>" + sumWin.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>" + sumAward.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>" + sumTc.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>" + sumSr.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>" + sumTz.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>" + sumTx.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>" + sumZc.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>"+ sumOrder.toFixed(2)+"</td><td style='text-align: center;font-size:16px'></td></tr>"
                if (html == '') {
                    html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                }
                $("#pwTable tbody").html(html);
                if (tabType == 1) {
                    var h = "";
                    if (json["result"]["tcData"]['deal_status'] == 1) {
                        h = "&nbsp&nbsp&nbsp&nbsp提成发放状态：<span style='color:#F50000'>未发放</span>&nbsp&nbsp&nbsp&nbsp佣金比例：<span style='color:#F50000'>7%</span>&nbsp&nbsp&nbsp&nbsp待发放总金额为：" + json["result"]["tcData"]['month_tc'] + "&nbsp&nbsp&nbsp&nbsp";
                        $("#grantTc").show();
                        $("#unline_grantTc").show();
                        if ($("#loginType").val() == 0) {
                            $("#tcAmount").val(json["result"]["tcData"]['month_tc']);
                            $("#tcAmount").show();
                        }
                    } else if (json["result"]["tcData"]['deal_status'] == 2) {
                        if(json["result"]["tcData"]['grant_type'] == 1) {
                            str = '线上发放';
                        }else {
                            str = '线下发放';
                        }
                        h = "&nbsp&nbsp&nbsp&nbsp提成发放状态：<span style='color:#F50000'>已发放</span>&nbsp&nbsp("+ str +")&nbsp&nbsp&nbsp&nbsp佣金比例：<span style='color:#F50000'>7%</span>&nbsp&nbsp&nbsp&nbsp已发放总金额为：<span style='color:#F50000'>" + json["result"]["tcData"]['grant_tc'] + "</span>&nbsp&nbsp&nbsp&nbsp发放时间：" + json["result"]["tcData"]['grant_time'];
                    }
                    $("#tcTitle").html(h);
                }
            }
        })
    }
    //监测时间框select的值变化
    $("#timer").change(function () {
        if ($("#timer").val() == "") {
            $("#filterForm4").css("display", "inline-block");
        } else {
            $("#filterForm4").css("display", "none");
        }
    })

    $("#btnExcelMonth").click(function () {
        msgConfirm('提醒', "确定导出报表?", function () {
            $('#pwTable').tableExport({
                type: 'excel',
                escape: 'false',
                fileName: '订单月统计'
            });
        })
    });

    $("#settle").click(function () {
        msgConfirm('提醒', "确定要结算此月份的吗?", function () {
            document.forms[0].target = "rfFrame"; 
            var years = $("#years").val();
            var months = $("#months").val();
            var sourceNo = $("#channelInfo").val();
            $.ajax({
                url: "/report/finance-statistics/do-settle",
                type: "POST",
                data: {years: years, months: months, sourceNo: sourceNo},
                async: false,
                dataType: "json",
                success: function (json) {
                    if (json["code"] != 600) {
                        msgAlert(json["msg"]);
                    } else {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    }
                }
            })
        })
    })

    $("#grantTc").click(function () {
        msgConfirm('提醒', "确定要发放提成吗?", function () {
            document.forms[0].target = "rfFrame"; 
            var years = $("#years").val();
            var months = $("#months").val();
            var sourceNo = $("#channelInfo").val();
            var tcAmount = $("#tcAmount").val();
            $.ajax({
                url: "/report/finance-statistics/grant-tc",
                type: "POST",
                data: {years: years, months: months, sourceNo: sourceNo, tcAmount: tcAmount, grantType:1},
                async: false,
                dataType: "json",
                success: function (json) {
                    if (json["code"] != 600) {
                        msgAlert(json["msg"]);
                    } else {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    }
                }
            })
        })
    })
    
    $("#unline_grantTc").click(function () {
        msgConfirm('提醒', "确定要发放提成吗?", function () {
            document.forms[0].target = "rfFrame"; 
            var years = $("#years").val();
            var months = $("#months").val();
            var sourceNo = $("#channelInfo").val();
            var tcAmount = $("#tcAmount").val();
            $.ajax({
                url: "/report/finance-statistics/grant-tc",
                type: "POST",
                data: {years: years, months: months, sourceNo: sourceNo, tcAmount: tcAmount, grantType:2},
                async: false,
                dataType: "json",
                success: function (json) {
                    if (json["code"] != 600) {
                        msgAlert(json["msg"]);
                    } else {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    }
                }
            })
        })
    })


</script>

