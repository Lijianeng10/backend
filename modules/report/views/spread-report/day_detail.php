
<form id="mxForm">
    <input type="hidden" value="<?php echo isset($_GET['settleid']) ? $_GET['settleid'] : "" ?>"  id="settleid">
    <input type="hidden" value="<?php echo isset($_GET['cust_no']) ? $_GET['cust_no'] : "" ?>"  id="cust_no">
    <input type="reset" class="am-btn am-btn-primary" id="btnExport" value="导出" style="margin-left: 10px">
    <input type="button" class="am-btn am-btn-primary" style="width:80px;display: inline;margin-left:5px;" value="返回" onclick="window.history.back()">
</form>
<table class="table" id="pwTable">
    <thead>
        <tr>
            <th style="text-align: center">购彩日期</th>
            <th style="text-align: center;">用户编号</th>
            <th style="text-align: center;">手机号</th>
            <th style="text-align: center;">用户名称</th>
            <th style="text-align: center;">购彩量</th>
            <th style="text-align: center;">订单数</th>
<!--            <th style="text-align: center;">提成</th>-->
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="M-box"></div>
<script type="text/javascript">
    $(function () {
        getSaleDetail()
        // 获取订单明细
        function getSaleDetail(options) {
            var settleid=$("#settleid").val();
            var cust_no =$("#cust_no").val();
            var data = $.extend({settleid:settleid,cust_no: cust_no,page: 1}, options);
            $.ajax({
                url: "/report/spread-report/day-detail",
                type: "POST",
                data: data,
                async: false,
                dataType: "json",
                success: function (json) {
                    console.log(json);
                    // return;
                    var html = "";
                    var totalmoney = 0;
                    var amountmoney = 0;
                    var count = 0;
                    $.each(json["result"]["list"], function (key, val) {
                        totalmoney += eval(val.total_amount);
                        amountmoney += eval(val.amount);
                        count += eval(val.OrderCount);
                        html += "<tr styly='text-align: center'>"
                        html += "<td style='text-align: center'>" + val.create_date + "</td>"
                        html += "<td style='text-align: center'>" + val.from_cust_no + "</td>"
                        html += "<td style='text-align: center'>" + val.user_tel + "</td>"
                        html += "<td style='text-align: center'>" + val.user_name + "</td>"
                        html += "<td style='text-align: center'>" + val.total_amount + "</td>"
                        html += "<td style='text-align: center'>" + val.OrderCount + "</td>"
                        // html += "<td style='text-align: center'>"+val.amount+"</td>"
                        html += "</tr>"
                    });
                    if (html == '') {
                        html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                    }
                    html += "<tr style='font-weight:bold;background-color:#E9ECF3'><td style='text-align: center;font-size:16px'>统计</td><td style='text-align: center;font-size:16px'></td><td></td><td></td><td style='text-align: center;font-size:16px'>" + totalmoney.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>"+ count + "</td></tr>"
                    $("#pwTable tbody").html(html);
                    total = json["result"]["pages"] > 0 ? json["result"]["pages"] : 1;
                    if (html == '') {
                        html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                    }
                    page(data.page, total);
                }
            })
        }
        function page(current, setPageCount) {
            $('.M-box').paginations({
                pageCount: setPageCount,
                current: current,
                homePage: '首页',
                endPage: '末页',
                prevContent: '上一页',
                nextContent: '下一页',
                coping: true,
                callback: function (api) {
                    getSaleDetail({page: api.getCurrent()});
                }
            });
        }
        //导出表格
        $("#btnExport").click(function(){
            msgConfirm('提醒',"确定导出报表?",function(){
                var settleid=$("#settleid").val();
                var cust_no =$("#cust_no").val();
                location.href="/report/spread-report/print-day-cust-report?settleid="+settleid+"&cust_no="+cust_no
            })
        })
    })
</script>
