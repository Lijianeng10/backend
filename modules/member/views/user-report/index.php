<ul class="am-nav am-nav-tabs" id="statusArr" style="margin-bottom:10px;">
    <li role="presentation" class="am-active" flag="0"><a onclick="statusArrClick($(this));">按日</a></li>
    <li role="presentation" flag="1"><a onclick="statusArrClick($(this));">按月 </a></li>
<!--    <li role="presentation" flag="2"><a  onclick="statusArrClick($(this));">按彩种</a></li>-->
</ul>
<div>
    <form class="myForm" id="filterForm">
        <ul class="third_team_ul">
            <?php if($_SESSION['type']==0): ?>
            <li>
                <label style="margin-left:15px;" for="">注册渠道:</label>
                <select class="form-control" style="width:100px;display: inline-block" id="from_id">
                    <option value="">请选择</option>
                    <option value="GL">咕啦体育</option>
                    <option value="meitu">美图</option>
                    <option value="qj">全景</option>
                    <option value="txty">腾讯体育</option>
                </select>
            </li>
            <?php endif; ?>
            <li>
                <label>注册时间:</label>
<!--                <select class="form-control" style="width:100px;display: inline-block" id="type">-->
<!--                    <option value="1">出票时间</option>-->
<!--                    <option value="2">投注时间</option>-->
<!--                </select>-->
                <input type="text" name="start_date" class='ECalendar form-control' id="start_date" style="width: 100px;display: inline;margin-left:5px;"  value="<?php echo date('Y-m-d') ?>" placeholder="开始时间"/>
                -
                <input type="text" name="end_date" class='ECalendar form-control' id="end_date" style="width: 100px;display: inline;"  value=<?php echo date('Y-m-d') ?> placeholder="结束时间"/>
            </li>
            <li>
                <input type="button" class="am-btn am-btn-primary" id="filterButton" value="统计">
<!--                <input type="button" class="am-btn am-btn-primary" id="resetDay" value="重置">-->
                <input type="button" class="am-btn am-btn-primary" id="btnExport" value="导出">
            </li>
        </ul>
    </form>
    <form class="myForm" id="filterForm1" style="display:none">
        <ul class="third_team_ul" >
            <?php if($_SESSION['type']==0): ?>
            <li>
                <label style="margin-left:15px;" for="">注册渠道:</label>
                <select class="form-control" style="width:100px;display: inline-block" id="from_id_year">
                    <option value="">请选择</option>
                    <option value="GL">咕啦体育</option>
                    <option value="meitu">美图</option>
                    <option value="qj">全景</option>
                    <option value="txty">腾讯体育</option>
                </select>
            </li>
            <?php endif; ?>
            <li>
                <label>统计时间:</label>
<!--                <select class="form-control" style="width:100px;display: inline-block" id="type2">-->
<!--                    <option value="1">出票时间</option>-->
<!--                    <option value="2">投注时间</option>-->
<!--                </select>-->
<!--                <label>出票年份:</label>-->
                <select  class="form-control" id="years" style="width: 100px;display: inline;margin-left:5px;">
                    <option><?php echo date("Y"); ?></option>
                    <option><?php echo date("Y") - 1; ?></option>
                </select>
<!--                <label>出票月份:</label>-->
                <select  class="form-control" id="months" style="width: 100px;display: inline;margin-left:5px;">
                    <?php
                    $Ary=["01","02","03","04","05","06","07","08","09","10","11","12"];
                    foreach ($Ary as $v){
                        if(date("m")==$v){
                           echo '<option selected>'.$v.'</option>';  
                        }else{
                           echo '<option >'.$v.'</option>';  
                        }
                    }
                    ?>
                </select>
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
                <th style="text-align: center;">日期</th>
                <th style="text-align: center;">渠道名称</th>
                <th style="text-align: center;">注册人数</th>
<!--                <th style="text-align: center;">操作</th>-->
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
        $("#filterButton").click(function () {
            getReport();
        })
        $("#send").click(function () {
            getLotteryReport();
        })
        $("#sendYears").click(function () {
            getMonthReport();
        })

    })
//    获取日统计数据
    function getReport() {
        var from_id = $("#from_id").val();
        var star = $("#start_date").val();
        var end = $("#end_date").val();
        var timestamp2 = Date.parse(new Date(star));
        var timestamp1 = Date.parse(new Date(end));
        if (timestamp1 < timestamp2) {
            alert("请选择正确时间")
        } else {
            $.ajax({
                url: "/member/user-report/get-report",
                type: "POST",
                data: {start_date: star, end_date: end, from_id: from_id,},
                async: false,
                dataType: "json",
                success: function (json) {
                    if (json["result"] == "") {
                        $("#pwTable tbody").html("暂无此项统计数据");
                        return false;
                    }
                    var html = "";
                    var counts = 0;
                    $.each(json["result"], function (key, val) {
                        counts += eval(val.count);
                        html += "<tr styly='text-align: center'>"
                        html += "<td style='text-align: center'><a>" + val.days + "</a></td>"
                        html += "<td style='text-align: center'>" + val.platName + "</td>"
                        html += "<td style='text-align: center'>" + val.count + "</td>"
                        // html += "<td style='text-align: center' ><a onclick='location.href = \"/report/report/agent-detail?type="+type+"&days=" + val.days + "&store=" + val.store_name +"&sNo=" + val.store_no + "\"'>查看通道详情</a></td>"
                        html += "</tr>"
                    });
                    html += "<tr style='font-weight:bold;background-color:#E9ECF3'><td style='text-align: center;font-size:16px'>统计</td><td style='text-align: center;font-size:16px'>渠道</td><td style='text-align: center;font-size:16px'>" + counts + "</td></tr>"
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
        var from_id = $("#from_id_year").val();
        var years = $("#years").val();
        var months = $("#months").val();
        $.ajax({
            url: "/member/user-report/get-month-report",
            type: "POST",
            data: {years: years,months:months,from_id: from_id},
            async: false,
            dataType: "json",
            success: function (json) {
                if (json["result"] == "") {
                    $("#pwTable tbody").html("暂无此项统计数据");
                    return false;
                }
                var html = "";
                var counts = 0;
                $.each(json["result"], function (key, val) {
                    counts += eval(val.count);
                    html += "<tr styly='text-align: center'>"
                    html += "<td style='text-align: center'><a>" + val.months + "</a></td>"
                    html += "<td style='text-align: center'>" + val.platName + "</td>"
                    html += "<td style='text-align: center'>" + val.count + "</td>"
                    // html += "<td style='text-align: center' ><a onclick='location.href = \"/report/report/agent-detail?type="+type+"&days=" + val.days + "&store=" + val.store_name +"&sNo=" + val.store_no + "\"'>查看通道详情</a></td>"
                    html += "</tr>"
                });
                html += "<tr style='font-weight:bold;background-color:#E9ECF3'><td style='text-align: center;font-size:16px'>统计</td><td style='text-align: center;font-size:16px'>渠道</td><td style='text-align: center;font-size:16px'>" + counts + "</td></tr>"
                if (html == '') {
                    html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                }
                $("#pwTable tbody").html(html);
            }
        })
    }
    //页面切换选择
    function statusArrClick(_this) {
        var statusArr = _this.data("val");
        $("#statusArr").find("li").removeClass("am-active");
        _this.parent("li").addClass("am-active");
        if (_this.parent("li").attr("flag") == 0) {
            $("#filterForm").css("display", "block");
            $("#filterForm1").css("display", "none");
            $("#filterForm2").css("display", "none");
            $("#filterForm4").css("display", "none");
            $($("#pwTable thead th")[0]).html("日期");
            $($("#pwTable thead th")[1]).css("display", "block");
            $("#pwTable tbody").html("");
            getReport()
        } else if (_this.parent("li").attr("flag") == 1) {
            $("#filterForm").css("display", "none");
            $("#filterForm1").css("display", "block");
            $("#filterForm2").css("display", "none");
            $("#filterForm4").css("display", "none");
            $($("#pwTable thead th")[0]).html("月份");
            $($("#pwTable thead th")[1]).css("display", "block");
            $("#pwTable tbody").html("");
            getMonthReport()
        } else {
            $("#filterForm").css("display", "none");
            $("#filterForm1").css("display", "none");
            $("#filterForm2").css("display", "block");
            $($("#pwTable thead th")[0]).html("彩种");
            $($("#pwTable thead th")[1]).css("display", "none");
            // $($("#pwTable thead th")[8]).css("display", "none");
            $("#pwTable tbody").html("");
            getSaleLottery();
            getLotteryReport();
        }

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
                fileName: '会员注册日统计报表'
            });
        })

    });
    $("#btnExportYears").click(function(){
        msgConfirm('提醒',"确定导出报表?",function(){
            $('#pwTable').tableExport({
                type:'excel',
                escape:'false',
                fileName: '会员注册月统计报表'
            });
        })
    });

</script>

