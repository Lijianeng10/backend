<ul class="am-nav am-nav-tabs" id="statusArr" style="margin-bottom:10px;">
    <li role="presentation" class="am-active" flag="1"><a onclick="statusArrClick($(this));">事件整体数据</a></li>
    <li role="presentation" flag="2"><a onclick="statusArrClick($(this));">参数明细统计 </a></li>
</ul>
<div>
    <form class="myForm" id="filterForm">
        <ul class="third_team_ul">
            <li>
                <label>访问时间:</label>
                <input type="text" class='ECalendar form-control' id="startdate" style="width: 100px;display: inline;margin-left:5px;"  value="<?php echo date('Y-m-d',strtotime('-7 days')) ?>" placeholder="开始时间"/>
                -
                <input type="text"class='ECalendar form-control' id="enddate" style="width: 100px;display: inline;"  value="<?php echo date('Y-m-d') ?>" placeholder="结束时间"/>
            </li>
            <li>
                <input type="button" class="am-btn am-btn-primary" id="filterButton" value="统计">
                <input type="button" class="am-btn am-btn-primary" id="btnExport" value="导出">
            </li>
        </ul>
    </form>
    <form class="myForm" id="filterForm1" style="display:none">
        <ul class="third_team_ul" >
            <li>
                <label>访问时间:</label>
                <input type="text" class='ECalendar form-control' id="start_date" style="width: 100px;display: inline;margin-left:5px;"  value="<?php echo date('Y-m-d',strtotime('-7 days')) ?>" placeholder="开始时间"/>
                -
                <input type="text"  class='ECalendar form-control' id="end_date" style="width: 100px;display: inline;"  value="<?php echo date('Y-m-d') ?>" placeholder="结束时间"/>
            </li>
            <li>
                <input type="button" class="am-btn am-btn-primary" id="sendYears" value="统计">
<!--                 <input type="button" class="am-btn am-btn-primary" id="resetYears" value="重置">-->
                <input type="button" class="am-btn am-btn-primary" id="btnExportYears" value="导出">
            </li>
        </ul>
    </form>
    <table class="table" id="pwTable">
        <thead>
            <tr>
                <th style="text-align: center;">访问日期</th>
                <th style="text-align: center;">浏览量</th>
                <th style="text-align: center;">独立用户</th>
                <th style="text-align: center;">独立ip</th>
                <th style="text-align: center;">访问次数</th>
<!--                <th style="text-align: center;">所属平台</th>-->
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(function () {
        getReport();
        //时间框插件弹窗
        $("#startdate").ECalendar({
            type: "date", //模式，time: 带时间选择; date: 不带时间选择;
            stamp: false, //是否转成时间戳，默认true;
            offset: [0, 10], //弹框手动偏移量;
            format: "yyyy-mm-dd", //时间格式 默认 yyyy-mm-dd hh:ii;
            skin: 3, //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
            step: 10, //选择时间分钟的精确度;
            callback: function (v, e) {
            } //回调函数
        });
        $("#enddate").ECalendar({
            type: "date", //模式，time: 带时间选择; date: 不带时间选择;
            stamp: false, //是否转成时间戳，默认true;
            offset: [0, 10], //弹框手动偏移量;
            format: "yyyy-mm-dd", //时间格式 默认 yyyy-mm-dd hh:ii;
            skin: 3, //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
            step: 10, //选择时间分钟的精确度;
            callback: function (v, e) {
            } //回调函数
        })
        $("#start_date").ECalendar({
            type: "date", //模式，time: 带时间选择; date: 不带时间选择;
            stamp: false, //是否转成时间戳，默认true;
            offset: [0, 10], //弹框手动偏移量;
            format: "yyyy-mm-dd", //时间格式 默认 yyyy-mm-dd hh:ii;
            skin: 3, //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
            step: 10, //选择时间分钟的精确度;
            callback: function (v, e) {
            } //回调函数
        });
        $("#end_date").ECalendar({
            type: "date", //模式，time: 带时间选择; date: 不带时间选择;
            stamp: false, //是否转成时间戳，默认true;
            offset: [0, 10], //弹框手动偏移量;
            format: "yyyy-mm-dd", //时间格式 默认 yyyy-mm-dd hh:ii;
            skin: 3, //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
            step: 10, //选择时间分钟的精确度;
            callback: function (v, e) {
            } //回调函数
        })
        $("#filterButton").click(function () {
            getReport();
        })

        $("#sendYears").click(function () {
            getMonthReport();
        })

    })
//    事件整体数据
    function getReport() {
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var type = $("#type").val();
        var timestamp2 = Date.parse(new Date(startdate));
        var timestamp1 = Date.parse(new Date(enddate));
        if (timestamp1 < timestamp2) {
            alert("请选择正确时间")
        } else {
            $.ajax({
                url: "/website/loan-access/get-access",
                type: "POST",
                data: {startdate: dateChange(startdate), enddate:dateChange(enddate),type:type},
                async: false,
                dataType: "json",
                success: function (json) {
                    if (json["result"] == "") {
                        $("#pwTable tbody").html("暂无此项统计数据");
                        return false;
                    }
                    var html = "";
                    var pvs = 0;
                    var uvs = 0;
                    var ivs = 0;
                    var vvs = 0;
                    $.each(json["result"], function (key, val) {
                        pvs += eval(val.pv);
                        uvs += eval(val.uv);
                        ivs += eval(val.iv);
                        vvs += eval(val.vv);
                        html += "<tr styly='text-align: center'>"
                        var timeAry =val.date_time.split(' ');
                        html += "<td style='text-align: center'><a>" + timeAry[0] + "</a></td>"
                        html += "<td style='text-align: center'>" + val.pv + "</td>"
                        html += "<td style='text-align: center'>" + val.uv + "</td>"
                        html += "<td style='text-align: center'>" + val.iv + "</td>"
                        html += "<td style='text-align: center'>" + val.vv + "</td>"
                        html += "</tr>"
                    });
                    html += "<tr style='font-weight:bold;background-color:#E9ECF3'><td style='text-align: center;font-size:16px'>统计</td><td style='text-align: center;font-size:16px'>"+pvs+"</td><td style='text-align: center;font-size:16px'>" + uvs + "</td><td style='text-align: center;font-size:16px'>" + ivs + "</td><td style='text-align: center;font-size:16px'>" + vvs + "</td></tr>"
                    if (html == '') {
                        html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                    }
                    $("#pwTable tbody").html(html);
                }
            })
        }

    }
//    获取月统计数据
    function getMonthReport() {
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var type = $("#type").val();
        var timestamp2 = Date.parse(new Date(start_date));
        var timestamp1 = Date.parse(new Date(end_date));
        if (timestamp1 < timestamp2) {
            alert("请选择正确时间")
        } else {
            $.ajax({
                url: "/website/loan-access/get-access-detail",
                type: "POST",
                data: {start_date: dateChange(start_date), end_date:dateChange(end_date),type: type},
                async: false,
                dataType: "json",
                success: function (json) {
                    if (json["result"] == "") {
                        $("#pwTable tbody").html("暂无此项统计数据");
                        return false;
                    }
                    var html = "";
                    var timenums = 0;
                    var peoplenums = 0;
                    $.each(json["result"], function (key, val) {
                        timenums += eval(val.numbers_time);
                        peoplenums += eval(val.numbers_people);
                        html += "<tr styly='text-align: center'>"
                        var timeAry =val.date_time.split(' ');
                        html += "<td style='text-align: center'><a>" + timeAry[0] + "</a></td>"
                        html += "<td style='text-align: center'>" + val.param + "</td>"
                        html += "<td style='text-align: center'>" + val.value + "</td>"
                        html += "<td style='text-align: center'>" + val.numbers_time + "</td>"
                        html += "<td style='text-align: center'>" + val.numbers_people + "</td>"
                        html += "</tr>"
                    });
                    html += "<tr style='font-weight:bold;background-color:#E9ECF3'><td style='text-align: center;font-size:16px'>统计</td><td style='text-align: center;font-size:16px'>参数</td><td style='text-align: center;font-size:16px'>取值</td><td style='text-align: center;font-size:16px'>" + timenums + "</td><td style='text-align: center;font-size:16px'>" + peoplenums + "</td></tr>"
                    if (html == '') {
                        html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                    }
                    $("#pwTable tbody").html(html);
                }
            })
        }
    }
    //页面切换选择
    function statusArrClick(_this) {
        var statusArr = _this.data("val");
        $("#statusArr").find("li").removeClass("am-active");
        _this.parent("li").addClass("am-active");
        if (_this.parent("li").attr("flag") == 1) {
            $("#filterForm").css("display", "block");
            $("#filterForm1").css("display", "none");
            $("#pwTable tbody").html("");
            getReport()
        } else if (_this.parent("li").attr("flag") == 2) {
            $("#filterForm").css("display", "none");
            $("#filterForm1").css("display", "block");
            $($("#pwTable thead th")[1]).html("参数");
            $($("#pwTable thead th")[2]).html("取值");
            $($("#pwTable thead th")[3]).html("触发次数");
            $($("#pwTable thead th")[4]).html("触发用户数");
            $("#pwTable tbody").html("");
            getMonthReport()
        }

    }

    //获取在售彩种
    function getSaleLottery() {
        $.ajax({
            url: "/report/order-statistics/get-lottery",
            type: "POST",
            data: {},
            async: false,
            dataType: "json",
            success: function (json) {
                if (json["code"] != 600) {
                    alert(json["msg"]);
                    return false;
                } else {
                    var html = '<option value="0">全部</option>';
                    $.each(json["result"], function (k, val) {
                        html += '<option value="' + val["lottery_code"] + '">' + val["lottery_name"] + '</option>';
                    });
                    $("#lottery_code").html(html);
                }
            }
        });
    }
    //重置
    // $("#resetDay").click(function(){
    //     location.href="/report/report/index";
    // })
    //日期格式转换
    function dateChange($date){
        var dateAry = $date.split("-");
        var month = dateAry["1"];
        var day = dateAry["2"];
        if(month<10){
            month = "0"+month;
        }
        if(day<10){
            day = "0"+day;
        }
        var newDate = dateAry["0"]+"-"+month+"-"+day;
        return newDate;
    }
    //导出表格
    $("#btnExport").click(function(){
        msgConfirm('提醒',"确定导出报表?",function(){
            $('#pwTable').tableExport({
                type:'excel',
                escape:'false',
                fileName: '事件整体数据统计'
            });
        })

    });
    $("#btnExportYears").click(function(){
        msgConfirm('提醒',"确定导出报表?",function(){
            $('#pwTable').tableExport({
                type:'excel',
                escape:'false',
                fileName: '参数明细统计'
            });
        })
    });

</script>

