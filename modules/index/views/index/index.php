<style>
    .tableBox {
        display: flex;
        flex-wrap: wrap;
    }
    .tableDiv{
        width: 47%;
        margin-top: 10px;
        margin-left: 20px;
    }
    table tr th,table tr td{
        font-size: 14px !important;
        text-align: center !important;
    }
</style>
<body>
<!--<ul class="am-nav am-nav-tabs" id="statusArr" style="margin-bottom:10px;">-->
<!--    <li role="presentation" class="am-active" flag="1"><a onclick="statusArrClick($(this));">今日订单</a></li>-->
<!--    <li role="presentation" flag="2"><a onclick="statusArrClick($(this));">昨日订单</a></li>-->
<!--    <li role="presentation" flag="3"><a  onclick="statusArrClick($(this));">本月订单</a></li>-->
<!--    <li role="presentation" flag="4"><a  onclick="statusArrClick($(this));">上月订单</a></li>-->
<!--</ul>-->
<div class="tableBox">
    <div class="tableDiv">
        <h6>今日订单</h6>
        <div id="toDay" >

        </div>
    </div>
    <div class="tableDiv">
        <h6>昨日订单</h6>
        <div id="lastDay" >

        </div>
    </div>
    <div class="tableDiv">
        <h6>本月订单</h6>
        <div id="nowMonth" >

        </div>
    </div>
    <div class="tableDiv">
        <h6>上月订单</h6>
        <div id="lastMonth" >

        </div>
    </div>
</div>

<!--    <div id="lastDay" class="tableDiv" style="display: none">-->
<!--    </div>-->
<!--    <div id="nowMonth" class="tableDiv" style="display: none">-->
<!--    </div>-->
<!--    <div id="lastMonth" class="tableDiv" style="display: none">-->
<!--    </div>-->
</body>
<script>
    $(function () {
        getToDayReport();
        getLastDayReport()
        getNowMonthReport();
        getLastMonthReport();
    })

    //页面切换选择
    // function statusArrClick(_this) {
    //     var statusArr = _this.data("val");
    //     $("#statusArr").find("li").removeClass("am-active");
    //     _this.parent("li").addClass("am-active");
    //     if (_this.parent("li").attr("flag") == 1) {
    //         $("#toDay").css("display", "block");
    //         $("#lastDay").css("display", "none");
    //         $("#nowMonth").css("display", "none");
    //         $("#lastMonth").css("display", "none");
    //         getToDayReport();
    //     } else if (_this.parent("li").attr("flag") == 2) {
    //         $("#toDay").css("display", "none");
    //         $("#lastDay").css("display", "block");
    //         $("#nowMonth").css("display", "none");
    //         $("#lastMonth").css("display", "none");
    //         getLastDayReport()
    //     } else if (_this.parent("li").attr("flag") == 3){
    //         $("#toDay").css("display", "none");
    //         $("#lastDay").css("display", "none");
    //         $("#nowMonth").css("display", "block");
    //         $("#lastMonth").css("display", "none");
    //         getNowMonthReport();
    //     }else{
    //         $("#toDay").css("display", "none");
    //         $("#lastDay").css("display", "none");
    //         $("#nowMonth").css("display", "none");
    //         $("#lastMonth").css("display", "block");
    //         getLastMonthReport();
    //     }
    //
    // }
    //获取今日订单
    function getToDayReport() {
        $.ajax({
            type: "POST",
            url: "/index/index/get-order-report",
            data: {type:1},
            dataType: "json",
            success: function (data) {
                $("#toDay").html("");
                var orderPlatform =[];
                orderPlatform[1]="咕啦直营";
                orderPlatform[2]="代理商";
                orderPlatform[3]="渠道";
                orderPlatform[4]="推广";
                var timely = data["result"]["timely"];//及时订单
                var out = data["result"]["out"];//已出票
                var refuse = data["result"]["refuse"];//拒绝
                var outOrder = data["result"]["out"]["outOrder"];
                var html = "";
                html += "<table class='table-striped table-bordered modalTable'style='float:left;'>";
                html +="<tr><th style='width: 10%'>项目</th><th style='width: 15%'>订单数</th><th style='width: 15%'>总金额</th><th>操作</th></tr>";
                html +="<tr><td>待接单</td><td>"+timely["waitOrderNum"]+"</td><td>"+parseInt(timely["waitOrderMoney"])+"</td><td><a onclick='location.href=\"/index/index/get-order-detail?order_type=1\"'>查看详情</a></td></tr>";
                html +="<tr><td>待出票</td><td>"+timely["waitOutNum"]+"</td><td>"+parseInt(timely["waitOutMoney"])+"</td><td><a onclick='location.href=\"/index/index/get-order-detail?order_type=2\"'>查看详情</a></td></tr>";
                html +="<tr><td>待派奖</td><td>"+timely["waitAwardNum"]+"</td><td>"+parseInt(timely["waitAwardMoney"])+"</td><td><a onclick='location.href=\"/index/index/get-order-detail?order_type=3\"'>查看详情</a></td></tr>";
                html +="<tr><td>已出票</td><td>"+out["allOutNum"]+"</td><td>"+out["allOutMoney"]+"</td><td style='padding:0;margin:0;'><table class='table table-striped table-bordered modalTable' style='margin-bottom: 0;margin-top: 0;border:0;'>";
                //已出票循环数组
                if(outOrder!=""){
                    for(var i=0; i<outOrder.length;i++){
                        html +="<tr>";
                        html += "<td style='width:50%;border-left:0;" + (i == outOrder.length - 1 ? "border-bottom:0" : "")+ "'>"+orderPlatform[outOrder[i]["order_platform"]]+"</td>";
                        html += "<td style='width:20%;" + (i== outOrder.length - 1 ? "border-bottom:0 " : "" ) +"'>"+outOrder[i]["outNum"]+"</td>";
                        html += "<td style='width:30%;border-right:0;" + (i== outOrder.length - 1 ? "border-bottom:0 " : "" ) +"'>"+parseInt(outOrder[i]["outMoney"])+"</td>";
                        html += "</tr>"
                    }
                }
                html +="</table></td></tr>";
                html +="<tr><td>拒绝出票</td><td>"+refuse["allRefuseNum"]+"</td><td>"+refuse["allRefuseMoney"]+"</td><td style='padding:0;margin:0;'><table class='table table-striped table-bordered modalTable' style='margin-bottom: 0;margin-top: 0;border:0;'>";
                //拒绝出票
                html +="<tr>";
                html += "<td style='width:50%;border-left: 0;'><a onclick='location.href=\"/index/index/get-refuse-order-detail?type=1\"'>手工拒绝</a></td>";
                html += "<td style='width:20%;'>"+refuse["sgRefuseNum"]+"</td>";
                html += "<td style='width:30%;border-right: 0;'>"+parseInt(refuse["sgRefuseMoney"])+"</td>";
                // html += "<td><a onclick='location.href=\"/index/index/get-refuse-order-detail?type=1\"'>查看详情</a></td>";
                html += "</tr>"
                html +="<tr>";
                html += "<td style='width:50%;border-left: 0;border-bottom:0'>系统拒绝</td>";
                html += "<td style='width:20%;border-bottom:0'>"+refuse["zdRefuseNum"]+"</td>";
                html += "<td style='width:30%;border-bottom:0'>"+parseInt(refuse["zdRefuseMoney"])+"</td>";
                html += "</tr>"
                html +="</table></td></tr>";
                html +="</table>";
                $("#toDay").html(html);
            }
        })
    }
    //获取昨日订单
    function getLastDayReport() {
        $.ajax({
            type: "POST",
            url: "/index/index/get-order-report",
            data: {type:2},
            dataType: "json",
            success: function (data) {
                $("#lastDay").html("");
                var orderPlatform =[];
                orderPlatform[1]="咕啦直营";
                orderPlatform[2]="代理商";
                orderPlatform[3]="渠道";
                orderPlatform[4]="推广";
                var out = data["result"]["out"];//已出票
                var refuse = data["result"]["refuse"];//拒绝
                var outOrder = data["result"]["out"]["outOrder"];
                var html = "";
                html += "<table class='table table-striped table-bordered modalTable'style='float:left;'>";
                html +="<tr><th style='width: 10%'>项目</th><th style='width: 15%'>订单数</th><th style='width: 15%'>总金额</th><th>操作</th></tr>";
                html +="<tr><td>已出票</td><td>"+out["allOutNum"]+"</td><td>"+out["allOutMoney"]+"</td><td style='padding:0;margin:0;'><table class='table table-striped table-bordered modalTable' style='margin-bottom: 0;margin-top:0;border:0;'>";
                //已出票循环数组
                if(outOrder!=""){
                    for(var i=0; i<outOrder.length;i++){
                        html +="<tr>";
                        html += "<td style='width:50%;border-left:0;" + (i == outOrder.length - 1 ? "border-bottom:0" : "")+ "'>"+orderPlatform[outOrder[i]["order_platform"]]+"</td>";
                        html += "<td style='width:20%;" + (i== outOrder.length - 1 ? "border-bottom:0 " : "" ) +"'>"+outOrder[i]["outNum"]+"</td>";
                        html += "<td style='width:30%;border-right:0;" + (i== outOrder.length - 1 ? "border-bottom:0 " : "" ) +"'>"+parseInt(outOrder[i]["outMoney"])+"</td>";
                        html += "</tr>"
                    }
                }
                html +="</table></td></tr>";
                html +="<tr><td>拒绝出票</td><td>"+refuse["allRefuseNum"]+"</td><td>"+refuse["allRefuseMoney"]+"</td><td style='padding:0;margin:0;'><table class='table table-striped table-bordered modalTable' style='margin-bottom: 0;margin-top: 0;border: 0;'>";
                //拒绝出票
                html +="<tr>";
                html += "<td style='width:50%;border-left:0;'><a onclick='location.href=\"/index/index/get-refuse-order-detail?type=2 \"'>手工拒绝</a></td>";
                html += "<td style='width:20%;'>"+refuse["sgRefuseNum"]+"</td>";
                html += "<td style='width:30%;border-right: 0;'>"+parseInt(refuse["sgRefuseMoney"])+"</td>";
                // html += "<td style='width:30%;border-right: 0;'><a onclick='location.href=\"/index/index/get-refuse-order-detail?type=2\"'>查看详情</a></td>";
                html += "</tr>"
                html +="<tr>";
                html += "<td style='width:50%;border-left:0;border-bottom:0'>系统拒绝</td>";
                html += "<td style='width:20%;border-bottom:0'>"+refuse["zdRefuseNum"]+"</td>";
                html += "<td style='width:30%;border-bottom:0'>"+parseInt(refuse["zdRefuseMoney"])+"</td>";
                html += "</tr>"
                html +="</table></td></tr>";
                html +="</table>";
                $("#lastDay").html(html);
            }
        })
    }
    //获取本月订单
    function getNowMonthReport() {
        $.ajax({
            type: "POST",
            url: "/index/index/get-order-report",
            data: {type:3},
            dataType: "json",
            success: function (data) {
                $("#nowMonth").html("");
                var orderPlatform =[];
                orderPlatform[1]="咕啦直营";
                orderPlatform[2]="代理商";
                orderPlatform[3]="渠道";
                orderPlatform[4]="推广";
                var out = data["result"]["out"];//已出票
                var refuse = data["result"]["refuse"];//拒绝
                var outOrder = data["result"]["out"]["outOrder"];
                var html = "";
                html += "<table class='table table-striped table-bordered modalTable'style='float:left;'>";
                html +="<tr><th style='width: 10%'>项目</th><th style='width: 15%'>订单数</th><th style='width: 15%'>总金额</th><th>操作</th></tr>";
                html +="<tr><td>已出票</td><td>"+out["allOutNum"]+"</td><td>"+out["allOutMoney"]+"</td><td style='padding:0;margin:0;'><table class='table table-striped table-bordered modalTable' style='margin-bottom: 0;margin-top: 0;border:0;'>";
                //已出票循环数组
                if(outOrder!=""){
                    for(var i=0; i<outOrder.length;i++){
                        html +="<tr>";
                        html += "<td style='width:50%;border-left:0;" + (i == outOrder.length - 1 ? "border-bottom:0" : "")+ "'>"+orderPlatform[outOrder[i]["order_platform"]]+"</td>";
                        html += "<td style='width:20%;" + (i== outOrder.length - 1 ? "border-bottom:0 " : "" ) +"'>"+outOrder[i]["outNum"]+"</td>";
                        html += "<td style='width:30%;border-right:0;" + (i== outOrder.length - 1 ? "border-bottom:0 " : "" ) +"'>"+parseInt(outOrder[i]["outMoney"])+"</td>";
                        html += "</tr>"
                    }
                }
                html +="</table></td></tr>";
                html +="<tr><td>拒绝出票</td><td>"+refuse["allRefuseNum"]+"</td><td>"+refuse["allRefuseMoney"]+"</td><td style='padding:0;margin:0;'><table class='table table-striped table-bordered modalTable' style='margin-bottom: 0;margin-top: 0;border: 0;'>";
                //拒绝出票
                html +="<tr>";
                html += "<td style='width:50%;border-left:0;'><a onclick='location.href=\"/index/index/get-refuse-order-detail?type=3 \"'>手工拒绝</a></td>";
                html += "<td style='width:20%;'>"+refuse["sgRefuseNum"]+"</td>";
                html += "<td style='width:30%;border-right: 0;'>"+parseInt(refuse["sgRefuseMoney"])+"</td>";
                // html += "<td><a onclick='location.href=\"/index/index/get-refuse-order-detail?type=3\"'>查看详情</a></td>";
                html += "</tr>"
                html +="<tr>";
                html += "<td style='width:50%;border-left:0;border-bottom:0'>系统拒绝</td>";
                html += "<td style='width:20%;border-bottom:0'>"+refuse["zdRefuseNum"]+"</td>";
                html += "<td style='width:30%;border-bottom:0'>"+parseInt(refuse["zdRefuseMoney"])+"</td>";
                html += "</tr>"
                html +="</table></td></tr>";
                html +="</table>";
                $("#nowMonth").html(html);
            }
        })
    }
    //获取上月订单
    function getLastMonthReport() {
        $.ajax({
            type: "POST",
            url: "/index/index/get-order-report",
            data: {type:4},
            dataType: "json",
            success: function (data) {
                $("#lastMonth").html("");
                var orderPlatform =[];
                orderPlatform[1]="咕啦直营";
                orderPlatform[2]="代理商";
                orderPlatform[3]="渠道";
                orderPlatform[4]="推广";
                var out = data["result"]["out"];//已出票
                var refuse = data["result"]["refuse"];//拒绝
                var outOrder = data["result"]["out"]["outOrder"];
                var html = "";
                html += "<table class='table table-striped table-bordered modalTable'style='float:left;'>";
                html +="<tr><th style='width: 10%'>项目</th><th style='width: 15%'>订单数</th><th style='width: 15%'>总金额</th><th>操作</th></tr>";
                html +="<tr><td>已出票</td><td>"+out["allOutNum"]+"</td><td>"+out["allOutMoney"]+"</td><td style='padding:0;margin:0;'><table class='table table-striped table-bordered modalTable' style='margin-bottom: 0;margin-top: 0;border: 0;'>";
                //已出票循环数组
                if(outOrder!=""){
                    for(var i=0; i<outOrder.length;i++){
                        html +="<tr>";
                        html += "<td style='width:50%;border-left:0;" + (i == outOrder.length - 1 ? "border-bottom:0" : "")+ "'>"+orderPlatform[outOrder[i]["order_platform"]]+"</td>";
                        html += "<td style='width:20%;" + (i== outOrder.length - 1 ? "border-bottom:0 " : "" ) +"'>"+outOrder[i]["outNum"]+"</td>";
                        html += "<td style='width:30%;border-right:0;" + (i== outOrder.length - 1 ? "border-bottom:0 " : "" ) +"'>"+parseInt(outOrder[i]["outMoney"])+"</td>";
                        html += "</tr>"
                    }
                }
                html +="</table></td></tr>";
                html +="<tr><td>拒绝出票</td><td>"+refuse["allRefuseNum"]+"</td><td>"+refuse["allRefuseMoney"]+"</td><td style='padding:0;margin:0;'><table class='table table-striped table-bordered modalTable' style='margin-bottom: 0;margin-top: 0;border: 0;'>";
                //拒绝出票
                html +="<tr>";
                html += "<td style='width:50%;border-left:0;'><a onclick='location.href=\"/index/index/get-refuse-order-detail?type=4 \"'>手工拒绝</a></td>";
                html += "<td style='width:20%;'>"+refuse["sgRefuseNum"]+"</td>";
                html += "<td style='width:30%;border-right: 0;'>"+parseInt(refuse["sgRefuseMoney"])+"</td>";
                html += "</tr>"
                html +="<tr>";
                html += "<td style='width:50%;border-left:0;border-bottom:0'>系统拒绝</td>";
                html += "<td style='width:20%;border-bottom:0'>"+refuse["zdRefuseNum"]+"</td>";
                html += "<td style='width:30%;border-bottom:0'>"+parseInt(refuse["zdRefuseMoney"])+"</td>";
                html += "</tr>"
                html +="</table></td></tr>";
                html +="</table>";
                $("#lastMonth").html(html);
            }
        })
    }
</script>

