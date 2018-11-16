
<form id="mxForm" style="margin-bottom: 20px;">
    <input type="hidden" value="<?php echo isset($_GET['settleid']) ? $_GET['settleid'] : "" ?>"  id="settleid">
    <input type="hidden" value="<?php echo isset($_GET['cust_no']) ? $_GET['cust_no'] : "" ?>"  id="cust_no">
    <input type="reset" class="am-btn am-btn-primary" id="btnExport" value="导出" style="margin-left: 10px">
    <input type="button" class="am-btn am-btn-primary" style="width:80px;display: inline;margin-left:5px;" value="返回" onclick="window.history.back()">
</form>
<label style="display: inline-block;font-size: 16px;padding-left: 20px;color: #0f0f0f;font-weight: bold;">分润会员明细</label>
<table class="table" id="pwTable">
    <thead>
        <tr>
            <th style="text-align: center;">用户编号</th>
            <th style="text-align: center;">用户名称</th>
            <th style="text-align: center;">购彩量</th>
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
                url: "/report/spread-report/get-settle-detail",
                type: "POST",
                data: data,
                async: false,
                dataType: "json",
                success: function (json) {
                    var html = "";
                    $.each(json["result"]["list"], function (key, val) {
                        html += "<tr styly='text-align: center'>"
                        html += "<td style='text-align: center'><a>" + val.from_cust_no + "</a></td>"
                        html += "<td style='text-align: center'>" + val.user_name + "</td>"
                        html += "<td style='text-align: center'>" + val.total_amount + "</td>"
                        html += "<td style='text-align: center'>"+val.amount+"</td>"
                        html += "</tr>"
                    });
                    if (html == '') {
                        html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                    }

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
            msgConfirm ('提醒','确定需要打印该报表吗？',function() {
                var settleid=$("#settleid").val();
                var cust_no =$("#cust_no").val();
                location.href="/report/spread-report/print-report?settleid="+settleid+"&cust_no="+cust_no
                // $.ajax({
                //     url: "/report/spread-report/print-report",
                //     type: "POST",
                //     data: {settleid:settleid,cust_no: cust_no,page: 1},
                //     async: false,
                //     dataType: "json",
                //     success: function (json) {
                //         if(json["code"]==600){
                //               msgAlert("打印成功")
                //         }
                //     }
                // })
            })

        })
    })
</script>
