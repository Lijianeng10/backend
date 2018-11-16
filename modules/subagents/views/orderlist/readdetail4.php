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
            url: "/subagents/orderlist/lan-detail",
            data: {lotteryOrderCode: lotteryOrderCode},
            dataType: "json",
            success: function (json) {
                console.log(json);
                if (json["code"] != 600) {
                    alert(json["msg"]);
                    return false;
                }
                var betHtml = "";
                var data = json["result"]["res"];
                var detial = json["result"]["content"]["data"];
                var payInfo = json["result"]["payInfo"];
                var t1 = "<table class='table table-striped table-bordered modalTable'style='width:45%;float:left;'>";
                t1 += "<tr><th>方案信息</th><td>" + data.lottery_name + "</td></tr>";
                t1 += "<tr><th>订单号</th><td>" + data.lottery_order_code + "</td></tr>";
                t1 += "<tr><th>会员编号</th><td>" + data.cust_no + "</td></tr>";
//                t1 += "<tr><th>接单门店</th><td>" + data.store_name + "(" + data.phone_num + ")</td></tr>";
                t1 += "<tr><th>订单状态</th><td>" + data.status_name + "</td></tr>";
                t1 += "<tr><th>投注时间</th><td>" + data.create_time + "</td></tr>";
                t1 += "</table>";
                var t2 = "<table class='table table-striped table-bordered modalTable'style='width:45%;float:left;margin-left:10px;'>";
//                t2 += "<tr><th>交易流水号</th><td>" + payInfo.pay_no + "</td></tr>";
//                t2 += "<tr><th>第三方交易号</th><td>" + payInfo.outer_no + "</td></tr>";
//                t2 += "<tr><th>退款号</th><td>" + payInfo.refund_no + "</td></tr>";
//                t2 += "<tr><th>订单金额</th><td>" + data.bet_money + "元(实际支付:"+payInfo.pay_money+",卡券优惠:"+payInfo.discount_money+"元)</td></tr>";
                t2 += "<tr><th>订单金额</th><td>" + data.bet_money + "元</td></tr>";
                t2 +="<tr><th>支付信息</th><td>实际支付:"+payInfo.pay_money+",卡券优惠:"+payInfo.discount_money+"元</td></tr>";
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
                if (['3001', '3002', '3003', '3004', '3005'].indexOf(data["lottery_id"]) != '-1') {
                    var competHtml = '<table class="table am-table am-table-bordered am-table-striped" style="margin: 0;">\n\
                                        <tr><th style="text-align: center;">客队 VS 主队</th><th style="text-align: center;">赛果</th><th style="text-align: center;">投注内容</th></tr>';

                    $.each(data["contents"], function (key, val) {
                        var playHtml = "<tr>";
                        if (val.hasOwnProperty("result_qcbf")) {
                            if (val.hasOwnProperty("rf_nums")) {
                                if (val.hasOwnProperty("fen_cutoff")) {
                                    playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_team_name + "</span><span style='color:red;'>" + val.result_qcbf + "</span><span>" + val.home_team_name + "&nbsp;</span><span style='color:blue'>(" + val.rf_nums + ")</span><span style='display:block;color:#999;'>预测总分" + val.fen_cutoff + "分</span></td>";
                                } else {
                                    playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_team_name + "</span><span style='color:red;'>" + val.result_qcbf + "</span><span>" + val.home_team_name + "&nbsp;</span><span style='color:blue'>(" + val.rf_nums + ")</span></td>";
                                }
                            } else if (val.hasOwnProperty("fen_cutoff")) {
                                playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_team_name + "</span><span style='color:red;'>" + val.result_qcbf + "</span><span>" + val.home_team_name + "&nbsp;</span><span style='display:block;color:#999;'>预测总分" + val.fen_cutoff + "分</span></td>";
                            } else {
                                playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_team_name + "</span><span style='color:red;'>" + val.result_qcbf + "</span><span>" + val.home_team_name + "&nbsp;</span></td>";
                            }
                        } else {
                            if (val.hasOwnProperty("rf_nums")) {
                                if (val.hasOwnProperty("fen_cutoff")) {
                                    playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_team_name + "</span><span>&nbsp;VS&nbsp;</span><span>" + val.home_team_name + "&nbsp;</span><span style='color:blue'>(" + val.rf_nums + ")</span><span style='display:block;color:#999;'>预测总分" + val.fen_cutoff + "分</span></td>";
                                } else {
                                    playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_team_name + "</span><span>&nbsp;VS&nbsp;</span><span>" + val.home_team_name + "&nbsp;</span><span style='color:blue'>(" + val.rf_nums + ")</span></td>";
                                }
                            } else if (val.hasOwnProperty("fen_cutoff")) {
                                playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_team_name + "</span><span>&nbsp;VS&nbsp;</span><span>" + val.home_team_name + "&nbsp;</span><span style='display:block;color:#999;'>预测总分" + val.fen_cutoff + "分</span></td>";
                            } else {
                                playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_team_name + "</span><span>&nbsp;VS&nbsp;</span><span>" + val.home_team_name + "</span></td>";
                            }
                        }
                        //赛果
                        playHtml += "<td>";
                        if (val.result_status == 3) {
                            playHtml += "比赛取消";
                        } else {
                            if (val.hasOwnProperty("result_3001") && val.result_3001 == "0") {
                                playHtml += "<span style='text-align: center;display:block;'>主负</span>"
                            } else if (val.hasOwnProperty("result_3001") && val.result_3001 == "3") {
                                playHtml += "<span style='text-align: center;display:block;'>主胜</span>"
                            }
                            if (val.hasOwnProperty("result_3002") && val.result_3002 == "0") {
                                playHtml += "<span style='text-align: center;display:block;'>让分主负</span>"
                            } else if (val.hasOwnProperty("result_3002") && val.result_3002 == "3") {
                                playHtml += "<span style='text-align: center;display:block;'>让分主胜</span>"
                            }
                            if (val.hasOwnProperty("result_3003")) {
                                switch (val.result_3003) {
                                    case "01":
                                        playHtml += "<span style='text-align: center;display:block;'>主胜1-5</span>";
                                        break;
                                    case "02":
                                        playHtml += "<span style='text-align: center;display:block;'>主胜6-10</span>";
                                        break;
                                    case "03":
                                        playHtml += "<span style='text-align: center;display:block;'>主胜11-15</span>";
                                        break;
                                    case "04":
                                        playHtml += "<span style='text-align: center;display:block;'>主胜16-20</span>";
                                        break;
                                    case "05":
                                        playHtml += "<span style='text-align: center;display:block;'>主胜21-25</span>";
                                        break;
                                    case "06":
                                        playHtml += "<span style='text-align: center;display:block;'>主胜26+</span>";
                                        break;
                                    case "11":
                                        playHtml += "<span style='text-align: center;display:block;'>客胜1-5</span>";
                                        break;
                                    case "12":
                                        playHtml += "<span style='text-align: center;display:block;'>客胜6-10</span>";
                                        break;
                                    case "13":
                                        playHtml += "<span style='text-align: center;display:block;'>客胜11-15</span>";
                                        break;
                                    case "14":
                                        playHtml += "<span style='text-align: center;display:block;'>客胜16-20</span>";
                                        break;
                                    case "15":
                                        playHtml += "<span style='text-align: center;display:block;'>客胜21-25</span>";
                                        break;
                                    case "16":
                                        playHtml += "<span style='text-align: center;display:block;'>客胜26+</span>";
                                        break;
                                }

                            }
                            if (val.hasOwnProperty("result_3004") && val.result_3004 == "1") {
                                playHtml += "<span style='text-align: center;display:block;'>大分</span>"
                            } else if (val.hasOwnProperty("result_3004") && val.result_3004 == "2") {
                                playHtml += "<span style='text-align: center;display:block;'>小分</span>"
                            }
                        }

                        playHtml += "</td>";
                        //投注
                        playHtml += "<td style='text-align: center;'>"
                        $.each(val['lottery'], function (k, v) {
                            if (v.play == "3001") {
                                if (v.bet == val.result_3001) {
                                    if (v.bet == 3) {
                                        playHtml += "<span style='display:block;color:red;'>胜(" + v.odds + ")</span>";
                                    } else {
                                        playHtml += "<span style='display:block;color:red;'>负(" + v.odds + ")</span>";
                                    }
                                } else {
                                    if (v.bet == 3) {
                                        playHtml += "<span style='display:block;'>胜(" + v.odds + ")</span>";
                                    } else {
                                        playHtml += "<span style='display:block;'>负(" + v.odds + ")</span>";
                                    }
                                }
                            }
                            if (v.play == "3002") {
                                if (v.bet == val.result_3002) {
                                    if (v.bet == 3) {
                                        playHtml += "<span style='display:block;color:red;'>让分主胜(" + v.odds + ")</span>";
                                    } else {
                                        playHtml += "<span style='display:block;color:red;'>让分主负(" + v.odds + ")</span>";
                                    }
                                } else {
                                    if (v.bet == 3) {
                                        playHtml += "<span style='display:block;'>让分主胜(" + v.odds + ")</span>";
                                    } else {
                                        playHtml += "<span style='display:block;'>让分主负(" + v.odds + ")</span>";
                                    }
                                }
                            }
                            if (v.play == "3003") {
                                if (v.bet == val.result_3003) {
                                    playHtml += "<span style='display:block;color:red;'>" + v.bet_name + "(" + v.odds + ")</span>";
                                } else {
                                    playHtml += "<span style='display:block;'>" + v.bet_name + "(" + v.odds + ")</span>";
                                }
                            }
                            if (v.play == "3004") {
                                if (v.bet == val.result_3004) {
                                    if (v.bet == 2) {
                                        playHtml += "<span style='display:block;color:red;'>小分(" + v.odds + ")</span>";
                                    } else {
                                        playHtml += "<span style='display:block;color:red;'>大分(" + v.odds + ")</span>";
                                    }
                                } else {
                                    if (v.bet == 2) {
                                        playHtml += "<span style='display:block;'>小分(" + v.odds + ")</span>";
                                    } else {
                                        playHtml += "<span style='display:block;'>大分(" + v.odds + ")</span>";
                                    }
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
//                    competHtml += '<tr><td style="vertical-align: middle;">出票人员</td><td>' + data.optInfo + '</td></tr>';
//                    competHtml += '<tr><td style="vertical-align: middle;">出票照片</td>';
//                    if (data.pic != "") {
//                        var picAry = [];
//                        picAry.push(data.pic.order_img1);
//                        if (data.pic.order_img2 != null && data.pic.order_img2 != "") {
//                            picAry.push(data.pic.order_img2);
//                        }
//                        if (data.pic.order_img3 != null && data.pic.order_img3 != "") {
//                            picAry.push(data.pic.order_img3);
//                        }
//                        if (data.pic.order_img4 != null && data.pic.order_img4 != "") {
//                            picAry.push(data.pic.order_img4);
//                        }
//                        competHtml += "<td>";
//                        for (var i = 0; i < picAry.length; i++) {
//                            competHtml += "<img class='orderImg' src='" + picAry[i] + "'>";
//                        }
//                        competHtml += "</td></tr>";
//                    } else {
//                        competHtml += "<td></td></tr>"
//                    }
                    competHtml += '<tr><td style="vertical-align: middle;">出票时间</td><td>' + (data.out_time != "" ? data.out_time : "") + '</td></tr>';
                    var conHtml = "";
                    conHtml += competHtml;
                }
                betHtml += t1 + t2 + t3 + conHtml;
                var t4 = "<table class='table table-striped table-bordered modalTable'style='width:92%;' id='detailList'>";
                t4 += "<tr><th  style='text-align: center;'>#</th><th  style='text-align: center;'>场次</th><th style='text-align: center;'>过关方式</th><th  style='text-align: center;'>倍数</th><th  style='text-align: center;'>投注金额</th><th  style='text-align: center;'>状态</th><th  style='text-align: center;'>中奖金额</th></tr>";
                $.each(detial, function (key, val) {
                    t4 += "<tr style='text-align: center;'><td>" + eval(key + 1) + "</td>";
                    t4 += "<td style='width:140px;'>"
                    $.each(val.content, function (k, v) {
                        if (v.bet_play) {
                            t4 += v.schedule_code + "(" + v.bet_play + "|" + v.bet_odds + ")<br/>";
                        } else {
                            t4 += v.schedule_code + "(" + v.bet_code + "|" + v.bet_odds + ")<br/>";
                        }
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
                var total = json["result"]["content"]["total"];
                if (offset >= total) {
                    $("#getDetailMore").css("display", "none");
                }
                $(".orderImg").bigShow();
            },
        });
        //加载更多
        $("#content").on("click", "#getDetailMore", function () {
            var data = {offset: offset, lotteryOrderCode: lotteryOrderCode};
            $.ajax({
                url: "/subagents/orderlist/get-more-detail",
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
                                if (v.bet_play) {
                                    html += v.schedule_code + "(" + v.bet_play + "|" + v.bet_odds + ")<br/>";
                                } else {
                                    html += v.schedule_code + "(" + v.bet_code + "|" + v.bet_odds + ")<br/>";
                                }
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