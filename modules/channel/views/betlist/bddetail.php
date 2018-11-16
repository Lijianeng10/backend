<input type="hidden" value=<?php echo $_GET["lotteryOrderCode"]; ?> id="lotteryOrderCode">
<div id="content">
</div>
<script type="text/javascript">
    $(function () {
        var lotteryOrderCode = $("#lotteryOrderCode").val();
        var offset = 10;
        //获取订单详情
        $.ajax({
            type: "POST",
            url: "/channel/betlist/bd-detail",
            data: {lotteryOrderCode: lotteryOrderCode},
            dataType: "json",
            success: function (json) {
                console.log(json);
                if (json["code"] != 600) {
                    alert(json["msg"]);
                    return false;
                }
                 var Array5001 = {
                    0:"负", 
                    1:"平", 
                    3:"胜", 
                };
                 var Array5002 = {
                    0:"0球", 
                    1:"0球", 
                    2:"2球", 
                    3:"3球", 
                    4:"4球", 
                    5:"5球", 
                    6:"6球", 
                    7:"7+球",
                };
                var Array5003 = {
                    33:"胜胜", 
                    31:"胜平", 
                    30:"胜负", 
                    13:"平胜", 
                    11:"平平", 
                    10:"平负", 
                    "03":"负胜", 
                    "01":"负平",
                    "00":"负负"
                };
                var Array5004 = {
                   1:"上单",
                   2:"上双",
                   3:"下单",
                   4:"下双",
                };
                var Array5005 = {
                   10:"1:0",
                   20:"2:0",
                   21:"2:1",
                   30:"3:0",
                   31:"3:1",
                   32:"3:2",
                   40:"4:0",
                   41:"4:1",
                   42:"4:2",
                   90:"胜其他",
                   "00":"0:0",
                   11:"1:1",
                   22:"2:2",
                   33:"3:3",
                   99:"平其他",
                   "01":"0:1",
                   "02":"0:2",
                   12:"1:2",
                   "03":"0:3",
                   13:"1:3",
                   23:"2:3",
                   "04":"0:4",
                   14:"1:4",
                   24:"2:4",
                   "09":"负其他",
                };
                var betHtml = "";
                var data = json["result"]["res"];
                var detial = json["result"]["content"]["result"]["data"];
                var payInfo = json["result"]["payInfo"];
                var t1 = "<table class='table table-striped table-bordered modalTable'style='width:45%;float:left;'>";
                t1 += "<tr><th>方案信息</th><td>" + data.lottery_name + "</td></tr>";
                t1 += "<tr><th>订单号</th><td>" + data.lottery_order_code + "</td></tr>";
                t1 += "<tr><th>会员编号</th><td>" + payInfo.cust_no + "</td></tr>";
                t1 += "<tr><th>接单门店</th><td>" + data.store_name + "(" + data.phone_num + ")</td></tr>";
                t1 += "<tr><th>订单状态</th><td>" + data.status_name + "</td></tr>";
                t1 += "<tr><th>投注时间</th><td>" + data.create_time + "</td></tr>";
                t1 += "<tr><th>出票手续费</th><td>" + data.pay_pre_money + "</td></tr>";
                t1 += "</table>";
                var t2 = "<table class='table table-striped table-bordered modalTable'style='width:45%;float:left;margin-left:10px;'>";
                t2 += "<tr><th>交易流水号</th><td>" + payInfo.pay_no + "</td></tr>";
                t2 += "<tr><th>第三方交易号</th><td>" + payInfo.outer_no + "</td></tr>";
                t2 += "<tr><th>退款号</th><td>" + payInfo.refund_no + "</td></tr>";
                t2 += "<tr><th>订单金额</th><td>" + data.bet_money + "元(实际支付:"+payInfo.pay_money+",卡券优惠:"+payInfo.discount_money+"元)</td></tr>";
                if (payInfo.pay_way == 1) {
                    t2 += "<tr><th>支付方式</th><td>支付宝支付</td></tr>";
                } else if (payInfo.pay_way == 2) {
                    t2 += "<tr><th>支付方式</th><td>微信支付</td></tr>";
                } else {
                    t2 += "<tr><th>支付方式</th><td>余额支付</td></tr>";
                }

                t2 += "<tr><th>支付时间</th><td>" + payInfo.pay_time + "</td></tr>";
                t2 += "</table>";
                var strs = data["bet_val"].split("^");
                var t3 = "<table class='table table-striped table-bordered modalTable'style='width:92%;'>";
                t3 += '<tr><td style="vertical-align: middle;">投注号码</td><td style="padding: 2px;">';
                if (['5000','5001','5002', '5003', '5004', '5005', '5006'].indexOf(data["lottery_id"]) != '-1'){
                    var competHtml = '<table class="table am-table am-table-bordered am-table-striped" style="margin: 0;">\n\
                                        <tr><th style="text-align: center;">主队 VS 客队</th><th style="text-align: center;">赛果</th><th style="text-align: center;">投注内容</th></tr>';
                    $.each(data["contents"], function (key, val) {
                        var playHtml = "<tr>";
                        if (val.hasOwnProperty("rq_nums")){
                            if(val.hasOwnProperty("schedule_result_bf")){
                               playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.mid + "</span><span>" + val.home_short_name + "</span><span style='color:blue;'>( " + val.rq_nums + " )</span><span style='color:red;'>" + val.schedule_result_bf + "</span><span> VS " + val.visit_short_name + "&nbsp;</span></td>"; 
                            }else{
                              playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.mid + "</span><span>" + val.home_short_name + "</span><span style='color:blue;'>( " + val.rq_nums + " )</span><span> VS " + val.visit_short_name + "&nbsp;</span></td>";   
                            }                        
                        } else {
                            if(val.hasOwnProperty("schedule_result_bf")){
                               playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.mid + "</span><span>" + val.home_short_name + "</span><span style='color:red;'>" + val.schedule_result_bf + "</span><span> VS " + val.visit_short_name + "&nbsp;</span></td>"; 
                            }else{
                               playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.mid + "</span><span>" + val.home_short_name + "</span><span> VS " + val.visit_short_name + "&nbsp;</span></td>";  
                            }
                            
                        }
                        //赛果
                        playHtml += "<td>";
                        if (val.result_status == 3) {
                            playHtml += "比赛取消";
                        } else {
                            if (val.hasOwnProperty("schedule_result_5001") && val.schedule_result_5001 != "") {
                                playHtml += "<span style='text-align: center;display:block;'>"+Array5001[val.schedule_result_5001]+"</span>"
                            } 
                            if (val.hasOwnProperty("schedule_result_5002") && val.schedule_result_5002 != "") {
                                playHtml += "<span style='text-align: center;display:block;'>"+Array5002[val.schedule_result_5002]+"</span>"
                            } 
                            if (val.hasOwnProperty("schedule_result_5003")&& val.schedule_result_5003 != "") {
                                playHtml += "<span style='text-align: center;display:block;'>"+Array5003[val.schedule_result_5003]+"</span>";
                            }
                            if (val.hasOwnProperty("schedule_result_5004") && val.schedule_result_5004 != "") {
                                playHtml += "<span style='text-align: center;display:block;'>"+Array5004[val.schedule_result_5004]+"</span>";
                            }
                            if (val.hasOwnProperty("schedule_result_5005")&& val.schedule_result_5005 != "") {
                                 playHtml += "<span style='text-align: center;display:block;'>"+Array5005[val.schedule_result_5005]+"</span>";
                            }
//                            if (val.hasOwnProperty("schedule_result_5006") && val.schedule_result_5006 == "0") {
//                                playHtml += "<span style='text-align: center;display:block;'>负</span>"
//                            } else if (val.hasOwnProperty("schedule_result_5006") && val.schedule_result_5006 == "3") {
//                                playHtml += "<span style='text-align: center;display:block;'>胜</span>"
//                            }
                        }   
                         playHtml += "</td>";
                         //投注
                        playHtml += "<td style='text-align: center;'>"
                        $.each(val['lottery'], function (k, v) {
                            if (v.play == "5001") {
                                if (v.bet == val.schedule_result_5001) {
                                    playHtml += "<span style='display:block;color:red;'>" + Array5001[v.bet]+"(" + v.odds + ")</span>";
                                } else {
                                    playHtml += "<span style='display:block;'>" + Array5001[v.bet]+"(" + v.odds + ")</span>"; 
                                }
                            }
                            if (v.play == "5002") {
                                if (v.bet == val.schedule_result_5002) {
                                    playHtml += "<span style='display:block;color:red;'>"+Array5002[v.bet]+"(" + v.odds + ")</span>";
                                } else {
                                    playHtml += "<span style='display:block;'>"+Array5002[v.bet]+"(" + v.odds + ")</span>";
                                }
                            }
                            if (v.play == "5003") {
                                if (v.bet == val.schedule_result_5003) {
                                    playHtml += "<span style='display:block;color:red;'>" + Array5003[v.bet]+ "(" + v.odds + ")</span>";
                                } else {
                                    playHtml += "<span style='display:block;'>" +Array5003[v.bet]+ "(" + v.odds + ")</span>";
                                }
                            }
                            if (v.play == "5004") {
                                if (v.bet == val.schedule_result_5004) {
                                    playHtml += "<span style='display:block;color:red;'>"+Array5004[v.bet]+"(" + v.odds + ")</span>";
                                 } else {
                                    playHtml += "<span style='display:block;'>"+Array5004[v.bet]+"(" + v.odds + ")</span>";
                                 }
                             }
                            if (v.play == "5005") {
                                if (v.bet == val.schedule_result_5005){
                                    playHtml += "<span style='display:block;color:red;'>"+Array5005[v.bet]+"(" + v.odds + ")</span>";
                                 } else {
                                    playHtml += "<span style='display:block;'>"+Array5005[v.bet]+"(" + v.odds + ")</span>";
                                 }
                             }

                        });
                        playHtml += "</td></tr>";
                         competHtml += playHtml;
                        });
                    competHtml += '</table>';
                    var newAry = {};
                    newAry[0] = "无奖金优化";
                    newAry[1] = "平均优化";
                    newAry[2] = "博热优化";
                    newAry[3] = "博冷优化";
                    competHtml += '<tr><td style="vertical-align: middle;">奖金优化</td><td>' + newAry[data.major_type] + '</td></tr>';
                    competHtml += '<tr><td style="vertical-align: middle;">中奖金额</td><td>' + data.win_amount + '元</td></tr>';
                    competHtml += '<tr><td style="vertical-align: middle;">派奖时间</td><td>' + data.award_time + '</td></tr>';
                    competHtml += '<tr><td style="vertical-align: middle;">投注期数</td><td>' + data.periods + '</td></tr>';
                    var buyinfo = "";
                    if (data.build_name != "") {
                        buyinfo = data.build_name + "&nbsp(" + data.play_name + ")";
                    } else {
                        buyinfo = data.play_name;
                    }
                    competHtml += '<tr><td style="vertical-align: middle;">投注信息</td><td>' + buyinfo + "&nbsp" + data.count + "注&nbsp" + data.bet_double + "倍(投注金额" + data.bet_money + '元)</td></tr>';
                    competHtml += '<tr><td style="vertical-align: middle;">出票人员</td><td>' + data.optInfo + '</td></tr>';
                    competHtml += '<tr><td style="vertical-align: middle;">出票照片</td>';
                    if (data.pic != "") {
                        var picAry = [];
                        picAry.push(data.pic.order_img1);
                        if (data.pic.order_img2 != null && data.pic.order_img2 != "") {
                            picAry.push(data.pic.order_img2);
                        }
                        if (data.pic.order_img3 != null && data.pic.order_img3 != "") {
                            picAry.push(data.pic.order_img3);
                        }
                        if (data.pic.order_img4 != null && data.pic.order_img4 != "") {
                            picAry.push(data.pic.order_img4);
                        }
                        competHtml += "<td>";
                        for (var i = 0; i < picAry.length; i++) {
                            competHtml += "<div style='display:inline-block' data-magnify='gallery' href="+ picAry[i] +" data-caption='出票照片'>";
                            competHtml+= "<img class='orderImg' src="+picAry[i]+" /></div>";
//                            competHtml += "<img class='orderImg' src='" + picAry[i] + "'>";
                        }
                        competHtml += "</td></tr>";
                    } else {
                        competHtml += "<td></td></tr>";
                    }
                    competHtml += '<tr><td style="vertical-align: middle;">出票时间</td><td>' + (data.out_time != "" ? data.out_time : "") + '</td></tr>';
                    var conHtml = "";
                    conHtml += competHtml;
//                    
                 }
                betHtml += t1 + t2 + t3 + conHtml;
                var t4 = "<table class='table table-striped table-bordered modalTable'style='width:92%;' id='detailList'>";
                t4 += "<tr><th  style='text-align: center;'>#</th><th  style='text-align: center;width:270px;'>场次</th><th style='text-align: center;'>过关方式</th><th  style='text-align: center;'>倍数</th><th  style='text-align: center;'>投注金额</th><th  style='text-align: center;'>状态</th><th  style='text-align: center;'>中奖金额</th></tr>";
                $.each(detial, function (key, val) {
                    t4 += "<tr style='text-align: center;'><td>" + eval(key + 1) + "</td>";
                    t4 += "<td>";
                    $.each(val.content, function (k, v) {
                        t4 += v.home_team_name+" VS "+v.visit_team_name + "<br/>(" + v.bet_play + "|" + v.bet_odds + ")<br/>";
                    })
                    t4 += "</td><td>" + val.play_name + "</td><td>" + val.bet_double + "</td><td>" + val.bet_money + "</td><td>" + val.status_name + "</td>";
                    if (val.win_amount != "0.00") {
                        t4 += "<td style='color:red'>" + val.win_amount + "</td>"
                    } else {
                        t4 += "<td>--</td>"
                    }
                    t4 += "</tr>";
                })
                t4 += "</table><span id='getDetailMore' style='width:92%;float:left;text-align:center;font-size: 14px;'><a>+加载更多</a></span><button class='am-btn am-btn-primary' onclick='closeMask()'>关闭</button>";
                betHtml += t4;
                $("#content").html(betHtml);
                var total = json["result"]["content"]["result"]["total"];
                if (offset >= total) {
                    $("#getDetailMore").css("display", "none");
                }
                }
        });
        //加载更多
        $("#content").on("click", "#getDetailMore", function () {
            var data = {offset: offset, lotteryOrderCode: lotteryOrderCode};
            $.ajax({
                url: "/channel/betlist/get-bd-more-detail",
                async: false,
                type: 'POST',
                data: data,
                dataType: "json",
                success: function (json) {
                    console.log(json);
                    if (json["code"] == 600) {
                        var html = "";
                        $.each(json["result"]["data"], function (k, val) {
                            html += "<tr style='text-align: center;'><td>" + (++offset) + "</td>";
                            html += "<td style='width:140px;'>";
                            $.each(val.content, function (key, v) {
                                html += v.home_team_name+" VS "+v.visit_team_name + "<br/>(" + v.bet_play + "|" + v.bet_odds + ")<br/>";
                            })
                            html += "</td><td>" + val.play_name + "</td><td>" + val.bet_double + "</td><td>" + val.bet_money + "</td><td>" + val.status_name + "</td>";
                            if (val.win_amount != "0.00") {
                                html += "<td style='color:red'>" + val.win_amount + "</td>"
                            } else {
                                html += "<td>--</td>"
                            }
                            html += "</tr>";
                        });
                        $("#detailList tbody").append(html);
                        var num = $("#detailList tbody").find("tr:last").find("td:first").html();
                        if (num == json["result"]["total"]) {
                            $("#getDetailMore").css("display", "none");
                        }
                    } else {
                        alert(json["msg"]);
                    }
                }
            });
        })
    });
</script>
