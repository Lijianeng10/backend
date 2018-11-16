<form class="myForm" id="mxForm">
    <ul class="third_team_ul">
        <li class="third_team_ul">
            <label style="margin-left:29px;" >店铺:</label>
            <input type="text" class="form-control" id="storeName" style="width: 200px;display: inline;margin-left:5px;" placeholder="店铺名" />
        </li>

        <li class="third_team_ul">
            <label style="margin-left: 15px">订单号:</label> 
            <input type="text" class="form-control" id="lottery_order_code" style="width: 200px;display: inline;margin-left:5px;" placeholder="订单号" />
        </li>
        <li class="third_team_ul">
            <label>会员信息:</label> 
            <input type="text" class='form-control' id="user_info" id="days" style="width: 200px;display: inline;margin-left:5px;" placeholder="编号，手机号，名称"  />
        </li>
        <li class="third_team_ul">
            <label style="margin-left: 28px">彩种:</label> 
            <select  class="form-control" id="lottery_code" style="width: 100px;display: inline;margin-left:5px;">
            </select>
        </li>
        <li class="third_team_ul">
            <label>投注时间:</label> 
            <input type="text" name="start_time" class='ECalendar form-control' id="start_time" style="width: 100px;display: inline;margin-left:5px;"   placeholder="开始时间"/>
            -
            <input type="text" name="end_time"  class='ECalendar form-control' id="end_time" style="width: 100px;display: inline;"  placeholder="结束时间"/>
        </li>
        <li class="third_team_ul">
            <label>订单状态:</label>
            <select  class="form-control" id="status" style="width: 100px;display: inline;margin-left:5px;">
                <option flag="10">全部</option>
                <option flag="3">待开奖</option>
                <option flag="5">未中奖</option>
                <option flag="4">中奖</option>
            </select>
        </li>
        <li class="third_team_ul">
            <label>处理状态:</label>
            <select  class="form-control" id="deal_status" style="width: 100px;display: inline;margin-left:5px;">
                <option flag="10">全部</option>
                <option flag="1">已对奖</option>
                <option flag="3">派奖成功</option>
            </select>
            <input type="button" class="am-btn am-btn-primary" id="seachBtn" style="width:80px;display: inline;margin-left:20px;" value="搜索" >
        </li>
    </ul>
</form>

<table class="table" id="pwTable">
    <thead>
        <tr>
            <th style="text-align: center;">方案编号</th>
            <th style="text-align: center;">投注时间</th>
            <th style="text-align: center;">投注店铺</th>
            <th style="text-align: center;">过关方式</th>
            <th style="text-align: center;">彩种玩法</th>
            <th style="text-align: center;">注数</th>
            <th style="text-align: center;">倍数</th>
            <th style="text-align: center;">投注金额(元)</th>
            <th style="text-align: center;">中奖金额(元)</th>
            <th style="text-align: center;">手续费(元)</th>
            <th style="text-align: center;">实兑金额(元)</th>
            <th style="text-align: center;">投注会员</th>
            <th style="text-align: center;">会员手机号</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="M-box"></div>
<script type="text/javascript">
    $(function () {
        //时间框插件弹窗
        $("#start_time").ECalendar({
            type: "date", //模式，time: 带时间选择; date: 不带时间选择;
            stamp: false, //是否转成时间戳，默认true;
            offset: [0, 10], //弹框手动偏移量;
            format: "yyyy-mm-dd", //时间格式 默认 yyyy-mm-dd hh:ii;
            skin: 3, //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
            step: 10, //选择时间分钟的精确度;
            callback: function (v, e) {
            } //回调函数
        });
        $("#end_time").ECalendar({
            type: "date", //模式，time: 带时间选择; date: 不带时间选择;
            stamp: false, //是否转成时间戳，默认true;
            offset: [0, 10], //弹框手动偏移量;
            format: "yyyy-mm-dd", //时间格式 默认 yyyy-mm-dd hh:ii;
            skin: 3, //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
            step: 10, //选择时间分钟的精确度;
            callback: function (v, e) {
            } //回调函数
        })
        getSaleLottery();
        getSaleOrderList();
        $("#seachBtn").click(function () {
            getSaleOrderList();
        })
        // 获取销售明细列表
        function getSaleOrderList(options) {
            var store_name = $("#storeName").val();
            var lotteryOrderCode = $("#lottery_order_code").val();
            var userInfo = $("#user_info").val();
            var lotteryId = $("#lottery_code").find("option:selected").attr("value");
            var star = $("#start_time").val();
            var end = $("#end_time").val();
            var status = $("#status").find("option:selected").attr("flag");
            var dealStatus = $("#deal_status").find("option:selected").attr("flag");
            if (star != "" && end != "") {
                var timestamp2 = Date.parse(new Date(star));
                var timestamp1 = Date.parse(new Date(end));
                if (timestamp1 < timestamp2) {
                    alert("请选择正确时间")
                }
            }

            var data = $.extend({store_name:store_name,lotteryOrderCode: lotteryOrderCode, userInfo: userInfo, page: 1, lotteryId: lotteryId, star: star, end: end, status: status, dealStatus: dealStatus}, options);
            $.ajax({
                url: "/report/saleorder/get-sale-order-list",
                type: "POST",
                data: data,
                async: false,
                dataType: "json",
                success: function (json) {
                    console.log(json);
                    if (json["code"] != 100) {
                        $("#pwTable tbody").html("暂无此项统计数据");
                        return false;
                    }
                    var html = "";
                    $.each(json["result"]["result"], function (key, val) {
                        html += "<tr styly='text-align: center'>"
                        html += "<td style='text-align: center'><a>" + val.lottery_order_code + "</a></td>"
                        html += "<td style='text-align: center'>" + val.create_time + "</td>"
                        html += "<td style='text-align: center'>"+val.store_name+"</td>"
                        var newAry=val.play_name.split(",");
                        var str="";
                        if(newAry.length>4){
                            for(var i=0;i<=3;i++){
                                if(i<3){
                                    str+= newAry[i]+","
                                }else{
                                    str+=newAry[i]+"......" 
                                }
                            }
                        }else{
                            str+=val.play_name;
                        }
                        html += "<td style='text-align: center'>" +str+ "</td>"
                        html += "<td style='text-align: center'>" + val.lottery_name + "</td>"
                        html += "<td style='text-align: center'>" + val.count + "</td>"
                        html += "<td style='text-align: center'>" + val.bet_double + "</td>"
                        html += "<td style='text-align: center'>" + val.bet_money + "</td>"
                        html += "<td style='text-align: center'>" + val.win_amount + "</td>"
                        html += "<td style='text-align: center'>" + val.paymoney + "</td>"
                        if (val.award_amount != null) {
                            html += "<td style='text-align: center'>" + val.award_amount + "</td>"
                        } else {
                            html += "<td style='text-align: center'>0.00</td>"
                        }
                        html += "<td style='text-align: center'>" + val.cust_no + "</td>"
                        html += "<td style='text-align: center'>" + val.user_tel + "</td>"
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
                    getSaleOrderList({page: api.getCurrent()});
                }
            });
        }
        //获取在售彩种
        function getSaleLottery() {
            $.ajax({
                url: "/report/report/get-sale-lottery",
                type: "POST",
                data: {},
                async: false,
                dataType: "json",
                success: function (json) {
                    if (json["code"] != 600) {
                        alert(json["msg"]);
                        return false;
                    } else {
                        var html = '<option value="10">全部</option>';
                        $.each(json["result"], function (k, val) {
                            html += '<option value="' + val["lottery_code"] + '">' + val["lottery_name"] + '</option>';
                        });
                        $("#lottery_code").html(html);
                    }
                }
            });
        }
    })
</script>
