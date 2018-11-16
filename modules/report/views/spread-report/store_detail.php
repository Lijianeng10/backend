
<form id="mxForm">
    <input type="hidden" value="<?php echo isset($_GET['settleid']) ? $_GET['settleid'] : "" ?>"  id="settleid">
    <input type="hidden" value="<?php echo isset($_GET['cust_no']) ? $_GET['cust_no'] : "" ?>"  id="cust_no">
    <input type="reset" class="am-btn am-btn-primary" id="btnExport" value="导出" style="margin-left: 10px">
    <input type="button" class="am-btn am-btn-primary" style="width:80px;display: inline;margin-left:5px;" value="返回" onclick="window.history.back()">
</form>
<table class="table" id="pwTable">
    <thead>
        <tr>
            <th style="text-align: center;">门店编号</th>
            <th style="text-align: center;">门店名称</th>
            <th style="text-align: center;">订单总金额</th>
            <th style="text-align: center;">提成</th>
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
                url: "/report/spread-report/get-store-detail",
                type: "POST",
                data: data,
                async: false,
                dataType: "json",
                success: function (json) {
                    var html = "";
                    var totalmoney = 0;
                    var amountmoney = 0;
                    $.each(json["result"]["data"], function (key, val) {
                        totalmoney += eval(val.total_amount);
                        amountmoney += eval(val.amount);
                        html += "<tr styly='text-align: center'>"
                        html += "<td style='text-align: center'><a>" + val.store_no + "</a></td>"
                        html += "<td style='text-align: center'>" + val.store_name + "</td>"
                        html += "<td style='text-align: center'>" + val.total_amount + "</td>"
                        html += "<td style='text-align: center'>"+val.amount+"</td>"
                        html += "</tr>"
                    });
                    if (html == '') {
                        html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                    }
                    html += "<tr style='font-weight:bold;background-color:#E9ECF3'><td style='text-align: center;font-size:16px'>统计</td><td style='text-align: center;font-size:16px'>店铺</td><td style='text-align: center;font-size:16px'>" + totalmoney.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>"+ amountmoney.toFixed(2) + "</td></tr>"
                    $("#pwTable tbody").html(html);
                    // total = json["result"]["pages"] > 0 ? json["result"]["pages"] : 1;
                    if (html == '') {
                        html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                    }
                    // page(data.page, total);
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
                $('#pwTable').tableExport({
                    type:'excel',
                    escape:'false',
                    fileName: '分润门店销售统计报表'
                });
            })
        })
    })
</script>
