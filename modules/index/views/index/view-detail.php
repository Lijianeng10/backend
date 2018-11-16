<input type="hidden" value="<?php echo isset($_GET['order_type']) ? $_GET['order_type'] : "" ?>"  id="order_type">
<input type="button" class="am-btn am-btn-primary" style="margin-left:2px;" value="返回" onclick="goBack()">
<table class="table" id="pwTable">
    <thead>
    <tr>
        <th style="text-align: center;">门店</th>
        <th style="text-align: center;">订单数</th>
        <th style="text-align: center;">总金额</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script>
    getSaleDetail();
    // 获取订单明细
    function getSaleDetail() {
        var order_type = $("#order_type").val();
        $.ajax({
            url: "/index/index/get-order-detail",
            type: "POST",
            data: {"order_type":order_type},
            dataType: "json",
            success: function (json) {
                if (json["result"] == "") {
                    $("#pwTable tbody").html("暂无此项统计数据");
                    return false;
                }
                var html = "";
                $.each(json["result"], function (key, val) {
                    html += "<tr styly='text-align: center'>"
                    html += "<td style='text-align: center'>" + val.store_name+ "</td>"
                    html += "<td style='text-align: center'>" + val.nums + "</td>"
                    html += "<td style='text-align: center'>" + val.moneys + "</td>"
                    html += "</tr>"
                });
                if (html == '') {
                    html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                }
                $("#pwTable tbody").html(html);
            }
        })
    }
</script>