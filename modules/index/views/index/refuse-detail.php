<input type="hidden" value="<?php echo isset($_GET['type']) ? $_GET['type'] : "" ?>"  id="type">
<input type="button" class="am-btn am-btn-primary" style="margin-left:2px;" value="返回" onclick="goBack()">
<table class="table" id="pwTable">
    <thead>
        <tr>
            <th style="text-align: center;">门店</th>
            <th style="text-align: center;">订单号</th>
            <th style="text-align: center;">拒绝原因</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="M-box"></div>
<script type="text/javascript">
    $(function () {
        getRefuseDetail()
        // 获取订单明细
        function getRefuseDetail(options) {
            var type=$("#type").val();
            var data = $.extend({type:type,page: 1}, options);
            $.ajax({
                url: "/index/index/get-refuse-order-detail",
                type: "POST",
                data: data,
                async: false,
                dataType: "json",
                success: function (json) {
                    if (json["result"]["list"] == "") {
                        $("#pwTable tbody").html("暂无此项统计数据");
                        return false;
                    }
                    var html = "";
                    $.each(json["result"]["list"], function (key, val) {
                        html += "<tr styly='text-align: center'>"
                        html += "<td style='text-align: center'>" + val.store_name+ "</td>"
                        html += "<td style='text-align: center'>" + val.lottery_order_code + "</td>"
                        html += "<td style='text-align: center'>" + val.refuse_reason + "</td>"
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
                    getRefuseDetail({page: api.getCurrent()});
                }
            });
        }
    })
</script>
