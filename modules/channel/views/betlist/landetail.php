
<div>
</div>
<script type="text/javascript">
    $(function () {
        var offset = 10;
        var total =<?php echo $detailCount; ?>;
        var lottery_order_id =<?php echo $_GET["lottery_order_id"]; ?>;
        if (offset >= total) {
            $("#getDetailMore").hide();
        }
        $("#getDetailMore").click(function (){
            if (offset < total) {
                var data = {offset: offset, lottery_order_id: lottery_order_id};
                $.ajax({
                    url: "/channel/betlist/get-deatail-list",
                    async: false,
                    type: 'POST',
                    data: data,
                    dataType: "json",
                    success: function (json) {
                        if (json["code"] == 600) {
                            var html = "";
                            $.each(json["result"], function (k, v) {
                                html += '<tr data-key="' + offset + '" ' + (v["status"] == 4 ? ('style="color:red;"') : "") + '><td>' + (++offset) + '</td><td>' + v["bet"] + '</td><td>' + v["play_name"] + '</td><td>1 注 ' + v["bet_double"] + ' 倍</td><td>' + v["bet_money"] + ' 元</td><td>' + v["statusName"] + ' </td><td>' + v["win_amount"] + '</td></tr>';
                            });
                            $("#detailList tbody").append(html);
                            if (offset >= total) {
                                $("#getDetailMore").hide();
                            }
                        } else {
                            alert(json["msg"]);
                        }
                    }
                });
            }
        });
        $(".orderImg").bigShow();
    });
</script>

