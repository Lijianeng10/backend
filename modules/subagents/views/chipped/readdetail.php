<style>
    #content{
        font-size: 14px;
    }
    p,hr{
        margin: 0;
        padding: 0;
    }
    .head p{
        margin: 5px 0;
    }
    .head span{
        display: inline-block;
        width: 100px;
        text-align: center;
    }
    .head_word span{
        color:#ccc;
    }
    .head_num span{
        color:red;
    }
    .content{
        margin-top: 12px;
    }
    table th,table td{
        text-align: center;
    }
</style>
<div id="content">
</div>
<script type="text/javascript">
    
    function getRes(code){
        var playHtml="";
        switch (code){
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
        return playHtml;
    }
    $(function (){
    var programmeCode = '<?php echo isset($_GET['programme_code']) ? $_GET['programme_code'] : "" ?>';
            $.ajax({
                    url: "/subagents/chipped/get-programme-detail",
                    data: {programme_code: programmeCode, list_type: 1},
                    type: "POST",
                    dataType: "json",
                    success: function (json) {
                        console.log(json)
                        if (json["code"] == 600) {
                        var data = json["result"];
                        var str = "";
                        var d1 = "<div class='head'><p>" + data["lottery_name"] + "</p><hr/>";
                        d1 += "<p class='head_num'><span>" + data["programme_speed"] + "%</span><span>" + data["bet_money"] + "元</span><span>" + data["bet_money"] / data["programme_all_number"] + "元</span><span>" + data["per_order_speed"] + "</span></p>";
                        d1 += "<p class='head_word'><span>当前进度</span><span>总金额</span><span>每份金额</span><span>预购</span></p>";
                        d1 += "<p style='background-color:#ccc;padding:5px;font-size:14px'>发起人</p>";
                        d1 += "<p>发起人：" + data["user_name"] + "</p>";
                        d1 += "<p>订单保底：" + data["minimum_guarantee"] + "</p>";
//                    d1+="<p><span>发起人:</span>"+res["user_name"]+"</p>";
                        d1 += "</div>";
                        var d2 = "<hr/><div class='content'><p><span>方案内容： </span>" + data["security_name"] + "</p>";
                        
                        if(data["contents"]!=null){
                        var strs = $.trim(data["contents"]["bet_val"]).split("^");
                        var playCodes = data["contents"]["play_code"].split(",");
                        var playNames = data["contents"]["play_name"].split(",");
                        if(['2001', '1001', '1002', '1003', '2002', '2003', '2004'].indexOf(data["lottery_code"]) != '-1'){
                            d2 += '<p><span>投注号码：</span>';
//                                d2 += '<tr><td style="vertical-align: middle;">投注号码</td><td style="padding: 2px;">';
                            betHtml="";
                            $.each(strs, function (strKey, str) {
                                if (str == "") {
                                    return true;
                                }
                                betHtml += '<div class="marginBottom2">';
                                if (data["lottery_code"] == '2001' || data["lottery_code"] == '1001') {
                                    var areas = str.split("|");
                                    var redBalls = areas[0].split(",");
                                    var blueBalls = areas[1].split(",");
                                    $.each(redBalls, function (k, v) {
                                        betHtml += '<span class="yuan_3">' + v + '</span>';
                                    });
                                    $.each(blueBalls, function (k, v) {
                                        betHtml += '<span class="yuan_4">' + v + '</span>';
                                    });
                                }
                                if (['100201', '100211', '200201', '200211', '200301', '200302', '200401', '200402'].indexOf(playCodes[strKey]) != '-1') {
                                    betHtml += '<span class="circlePrompt">' + playNames[strKey].substr(0, 2) + '</span>';
                                    var areas = str.split("|");
                                    $.each(areas, function (key, val) {
                                        var balls = val.split(",");
                                        $.each(balls, function (k, v) {
                                            if (key != 0 && k == 0) {
                                                betHtml += "<span class='areaBalls'>";
                                            } else {
                                                betHtml += "<span class='balls'>";
                                            }
                                            betHtml += '<span class="yuan_3">' + v + '</span>';
                                            betHtml += "</span>";
                                        });
                                    });
                                }else if(['100302', '100301',].indexOf(playCodes[strKey])!="-1"){
                                    betHtml += '<span class="circlePrompt">' + playNames[strKey].substr(0, 2) + '</span>';
                                    var areas = str.split(",");
                                    $.each(areas,function(k,v){
                                        betHtml += '<span class="yuan_3">' + v + '</span>';
                                    })
                                }else if (['100202', '100212', '100203', '100213', '200202', '200212', '200203', '200213'].indexOf(playCodes[strKey]) != '-1') {
                                    betHtml += '<span class="circlePrompt">' + playNames[strKey].substr(0, 2) + '</span>';
                                    var balls = str.split(",");
                                    $.each(balls, function (k, v) {
                                        betHtml += "<span class='balls'>";
//                                            if (resultBalls != undefined && resultBalls.length > 0 && resultBalls.indexOf(v) != '-1') {
//                                                betHtml += '<span class="yuan_0">' + v + '</span>';
//                                            } else {
                                            betHtml += '<span class="yuan_3">' + v + '</span>';
//                                            }
                                        betHtml += "</span>";
                                    });
                                }
                                betHtml += '</div>';
                            })
                            d2+=betHtml+"</p>";
                            d2+="<p>投注信息："+data["contents"]["count"]+"注"+data["contents"]["bet_double"]+"倍</p>"
                        } else if (['3006', '3007', '3008', '3009', '3010', '3011'].indexOf(data["lottery_code"]) != '-1') {
                                var competHtml = '<table class="table am-table am-table-bordered am-table-striped" style="margin: 0;font-size:12px">\n\
                                                    <tr><th style="text-align: center;">主队 VS 客队</th><th style="text-align: center;">赛果</th><th style="text-align: center;">投注内容</th></tr>';
                                $.each(data["contents"]["detail"], function (key, val) {
                                    var playHtml = '';
                                    var resHtml='';
                                    var has3006 = false;
                                    var playAry=[];
                                    $.each(val['lottery'], function (k, v) {
                                        playAry.push(v["play"]);
                                        if (val["schedule_result_" + v["play"]] == v["bet"]) {
                                            playHtml += "<span class='balls' style='color:#dc3b40;display:block;'>";
                                        } else {
                                            playHtml += "<span class='balls' style='display:block;'>";
                                        }
                                        if (v["play"] == '3006') {
                                            playHtml += '<span class="prompt3006">让</span>';
                                            has3006 = true;
                                        }
                                        playHtml += getResultName(v["play"], v["bet"]) + '(' + v["odds"] + ')';
                                        playHtml += "</span>";
                                    });
                                    $.unique(playAry.sort());  //赛程结果数组去重
                                    $.each(playAry,function(k,v){
                                        if(v=="3006"){
                                            if(val["schedule_result_3006"]!=""){
                                                resHtml+= '<span class="prompt3006">让</span>'+getResultName(v, val["schedule_result_3006"])+"<br/>";
                                            } 
                                        }
                                        if(v=="3007"){
                                            if(val["schedule_result_3007"]!=""){
                                                resHtml+= getResultName(v, val["schedule_result_3007"])+"<br/>";
                                            } 
                                        }
                                        if(v=="3008"){
                                            if(val["schedule_result_3008"]!=""){
                                                resHtml+=getResultName(v, val["schedule_result_3008"])+"<br/>";
                                            } 
                                        }
                                        if(v=="3009"){
                                            if(val["schedule_result_3009"]!=""){
                                                resHtml+=getResultName(v, val["schedule_result_3009"])+"<br/>";
                                            } 
                                        }
                                        if(v=="3010"){
                                            if(val["schedule_result_3010"]!=""){
                                                resHtml+=getResultName(v, val["schedule_result_3010"])+"<br/>";
                                            } 
                                        }
                                    })
                                    competHtml += '<tr><td style="text-align: center;"><span style="display:block;color:#999;">' + val['schedule_code'] + '</span>' + val['home_short_name'] + (has3006 ? '<span style="color:#0c89e1;font-size:8px;padding-left: 5px;">(' + val['rq_nums'] + ')</span>' : '') + (val['schedule_result_bf'] ? ('<span style="color:#dc3b40;"> ' + val['schedule_result_bf']) + ' </span>' : " VS ") + val['visit_short_name'] + '</td><td style="text-align: center;">' +resHtml+ '</td>\n\<td style="text-align: center;">' + playHtml + '</td></tr>';
                                });
                                competHtml += '</table>';
                                d2 += competHtml;
                                d2+="<p>过关方式："+data["contents"]["play_name"]+"</p>"
                                d2+="<p>投注信息："+data["contents"]["count"]+"注"+data["contents"]["bet_double"]+"倍</p>"
                            }else if(['3001', '3002', '3003', '3004', '3005'].indexOf(data["lottery_code"]) != '-1'){
                               var competHtml = '<table class="table am-table am-table-bordered am-table-striped" style="margin:0;font-size:12px;">\n\
                                        <tr><th style="text-align: center;">客队 VS 主队</th><th style="text-align: center;">赛果</th><th style="text-align: center;">投注内容</th></tr>';
                                $.each(data["contents"]["detail"], function (key, val) {
                                    var playHtml = "<tr>";
                                    if (val.hasOwnProperty("result_qcbf")){
                                        if (val.hasOwnProperty("rf_nums")) {
                                            if (val.hasOwnProperty("fen_cutoff")){
                                                playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_short_name + "</span><span style='color:red;'>" + val.result_qcbf + "</span><span>" + val.home_short_name + "&nbsp;</span><span style='color:blue'>(" + val.rf_nums + ")</span><span style='display:block;color:#999;'>预测总分" + val.fen_cutoff + "分</span></td>";
                                            } else {
                                                playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_short_name + "</span><span style='color:red;'>" + val.result_qcbf + "</span><span>" + val.home_short_name + "&nbsp;</span><span style='color:blue'>(" + val.rf_nums + ")</span></td>";
                                            }
                                        } else if (val.hasOwnProperty("fen_cutoff")) {
                                            playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_short_name + "</span>VS<span style='color:red;'>" + val.result_qcbf + "</span><span>" + val.home_short_name + "&nbsp;</span><span style='display:block;color:#999;'>预测总分" + val.fen_cutoff + "分</span></td>";
                                        } else {
                                            playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_short_name + "</span>VS<span style='color:red;'>" + val.result_qcbf + "</span><span>" + val.home_short_name + "&nbsp;</span></td>";
                                        }
                                    } else {
                                        if (val.hasOwnProperty("rf_nums")) {
                                            if (val.hasOwnProperty("fen_cutoff")) {
                                                playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_short_name + "</span><span> VS </span><span>" + val.home_short_name + "&nbsp;</span><span style='color:blue'>(" + val.rf_nums + ")</span><span style='display:block;color:#999;'>预测总分" + val.fen_cutoff + "分</span></td>";
                                            } else {
                                                playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_short_name + "</span><span> VS </span><span>" + val.home_short_name + "&nbsp;</span><span style='color:blue'>(" + val.rf_nums + ")</span></td>";
                                            }
                                        } else if (val.hasOwnProperty("fen_cutoff")) {
                                            playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_short_name + "</span><span> VS </span><span>" + val.home_short_name + "&nbsp;</span><span style='display:block;color:#999;'>预测总分" + val.fen_cutoff + "分</span></td>";
                                        } else {
                                            playHtml += "<td style='text-align: center;'><span style='display:block;color:#999;'>" + val.schedule_code + "</span><span>" + val.visit_short_name + "</span><span> VS </span><span>" + val.home_short_name + "</span></td>";
                                        }
                                    }
                                    //赛果
                                    playHtml += "<td>";
                                    if (val.hasOwnProperty("result_3001") && val.result_3001 == "0") {
                                        playHtml += "<span style='text-align: center;display:block;'>主负</span>"
                                    } else if (val.hasOwnProperty("result_3001") && val.result_3001 == "3"){
                                        playHtml += "<span style='text-align: center;display:block;'>主胜</span>"
                                    }
                                    if (val.hasOwnProperty("result_3002") && val.result_3002 == "0") {
                                        playHtml += "<span style='text-align: center;display:block;'>让分主负</span>"
                                    } else if (val.hasOwnProperty("result_3002") && val.result_3002 == "3"){
                                        playHtml += "<span style='text-align: center;display:block;'>让分主胜</span>"
                                    }
                                    if (val.hasOwnProperty("result_3003")){
                                        getRes(val.result_3003);
                                    }
                                    if (val.hasOwnProperty("result_3004") && val.result_3004 == "1") {
                                        playHtml += "<span style='text-align: center;display:block;'>大分</span>"
                                    } else if(val.hasOwnProperty("result_3004") && val.result_3004 == "2"){
                                        playHtml += "<span style='text-align: center;display:block;'>小分</span>"
                                    }
                                    playHtml += "</td>";
                                    //投注
                                    playHtml += "<td style='text-align: center;'>"
                                    $.each(val['lottery'], function (k, v){
                                        if (v.play == "3001") {
                                            if (v.bet == val.result_3001) {
                                                if (v.bet == 3) {
                                                    playHtml += "<span style='display:block;color:red;'>胜(" + v.odds[v.bet] + ")</span>";
                                                } else {
                                                    playHtml += "<span style='display:block;color:red;'>负(" + v.odds[v.bet] + ")</span>";
                                                }
                                            } else {
                                                if (v.bet == 3) {
                                                    playHtml += "<span style='display:block;'>胜(" + v.odds[v.bet] + ")</span>";
                                                } else {
                                                    playHtml += "<span style='display:block;'>负(" + v.odds[v.bet] + ")</span>";
                                                }
                                            }
                                        }
                                        if (v.play == "3002") {
                                            if (v.bet == val.result_3002) {
                                                if (v.bet == 3) {
                                                    playHtml += "<span style='display:block;color:red;'>让分主胜(" + v.odds[v.bet] + ")</span>";
                                                } else {
                                                    playHtml += "<span style='display:block;color:red;'>让分主负(" + v.odds[v.bet] + ")</span>";
                                                }
                                            } else {
                                                if (v.bet == 3) {
                                                    playHtml += "<span style='display:block;'>让分主胜(" + v.odds[v.bet] + ")</span>";
                                                } else {
                                                    playHtml += "<span style='display:block;'>让分主负(" + v.odds[v.bet] + ")</span>";
                                                }
                                            }
                                        }
                                        if (v.play == "3003") {
                                            if (v.bet == val.result_3003) {
                                                playHtml += "<span style='display:block;color:red;'>" +getRes(v.bet) + "(" + v.odds[v.bet] + ")</span>";
                                            } else {
                                                playHtml += "<span style='display:block;'>" + getRes(v.bet) + "(" + v.odds[v.bet] + ")</span>";
                                            }
                                        }
                                        if (v.play == "3004") {
                                            if (v.bet == val.result_3004) {
                                                if (v.bet == 2) {
                                                    playHtml += "<span style='display:block;color:red;'>小分(" + v.odds[v.bet] + ")</span>";
                                                } else {
                                                    playHtml += "<span style='display:block;color:red;'>大分(" + v.odds[v.bet] + ")</span>";
                                                }
                                            } else {
                                                if (v.bet == 2) {
                                                    playHtml += "<span style='display:block;'>小分(" + v.odds[v.bet] + ")</span>";
                                                } else {
                                                    playHtml += "<span style='display:block;'>大分(" + v.odds[v.bet] + ")</span>";
                                                }
                                            }
                                        }

                                    });
                                    playHtml += "</td></tr>";
                                    competHtml += playHtml;
                                });
                                competHtml += '</table>';
                                d2 += competHtml;
                                d2+="<p>过关方式："+data["contents"]["play_name"]+"</p>"
                                d2+="<p>投注信息："+data["contents"]["count"]+"注"+data["contents"]["bet_double"]+"倍</p>"
                            }
                        }
                        d2 += "</div>"; 
                        var d3 = "<hr/><div class='content'><p style='background-color:#ccc;padding:5px;font-size:14px'><span>跟单人数： </span>" + data["programme_peoples"] + "</p>";
                        d3 +="<table border=1 style='width:100%;margin:10px 0px'><tr><th>方案跟单编号</th><th>用户</th><th>认购份数</th><th>认购时间</th><th>奖金分配</th></tr>";
                        for(var i=0;i<data["withPeople"].length;i++){
                            d3+="<tr><td>"+data["withPeople"][i]["programme_user_code"]+"</td><td>"+data["withPeople"][i]["user_name"]+"</td><td>"+data["withPeople"][i]["buy_number"]+"</td><td>"+data["withPeople"][i]["create_time"]+"</td><td>"+(data["withPeople"][i]["win_amount"]?data["withPeople"][i]["win_amount"]:"")+"</td></tr>"
                        }
                        d3 +="</table>"
                        d3 += "<p style='background-color:#ccc;padding:5px;font-size:14px'>方案信息</p>";
                        d3 += "<p><span>方案编号：</span>" + data["programme_code"] + "</p>";
                        d3 += "<p><span>发起时间：</span>" + data["create_time"] + "</p>";
                        d3 += "<p><span>截止时间：</span>" + data["programme_end_time"] + "</p>";
                        d3 += "<p><span>推荐理由：</span>" + data["programme_reason"] + "</p>";
                        d3 += "<p><span>出票门店：</span>" + data["ticke_name"] + "(" + data["ticke_code"] + ")</p>";
                        d3 += "</div>";
                        str = d1 + d2 + d3 + "<button class='am-btn am-btn-primary' onclick='closeMask()' style='margin-top:10px'>关闭</button>";
                        $("#content").html(str);
                } else {
                    alert(json["msg"]);
                }
            }
        });
    })
       function getResultName(lotteryCode, res) {
        if (res == null || res == '') {
            return '';
        }
        var results = [];
        results["3010"] = [];
        results["3010"]["3"] = '胜';
        results["3010"]["1"] = '平';
        results["3010"][0] = '负';
        results["3006"] = [];
        results["3006"]["3"] = '胜';
        results["3006"]["1"] = '平';
        results["3006"]["0"] = '负';
        results["3008"] = [];
        results["3008"]["0"] = '总进0球';
        results["3008"]["1"] = '总进1球';
        results["3008"]["2"] = '总进2球';
        results["3008"]["3"] = '总进3球';
        results["3008"]["4"] = '总进4球';
        results["3008"]["5"] = '总进5球';
        results["3008"]["6"] = '总进6球';
        results["3008"]["7+"] = '总进7/7+球';
        if (lotteryCode == '3007') {
            if (res == '90') {
                return '胜其他';
            }
            if (res == '99') {
                return '平其他';
            }
            if (res == '09') {
                return '负其他';
            }
            return res.substr(0, 1) + ':' + res.substr(1, 2);
        }

        if (lotteryCode == '3009') {
            return results["3010"][res.substr(0, 1)] + results["3010"][res.substr(1, 1)];
        }
        return results[lotteryCode][res];
    }
</script>

