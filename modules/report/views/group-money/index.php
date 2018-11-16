<ul class="am-nav am-nav-tabs" id="statusArr" style="margin-bottom:10px;">
<!--    <li role="presentation" class="am-active" flag="0"><a onclick="statusArrClick($(this));">按日</a></li>-->
    <li role="presentation" flag="1"><a onclick="statusArrClick($(this));">按月 </a></li>
<!--    <li role="presentation" flag="2"><a  onclick="statusArrClick($(this));">按彩种</a></li>-->
</ul>
<div>
    <form class="myForm" id="filterForm">
        <ul class="third_team_ul" >
            <li>
                <label style="margin-left:15px;" for="">门店信息:</label>
                <input type="text" id="store" class="form-control" placeholder="运营者手机号、门店编码、店铺名称" style="width:250px;display: inline-block;" >
            </li>
            <li>
                <label>统计时间:</label>
                <select class="form-control" style="width:100px;display: inline-block" id="type2">
                    <option value="1">出票时间</option>
                    <option value="2">投注时间</option>
                </select>
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
                <th style="text-align: center;">省</th>
                <th style="text-align: center;">市</th>
                <th style="text-align: center;">店铺名称</th>
                <th style="text-align: center;">咕啦体育订单总金额</th>
                <th style="text-align: center;">糯米订单总金额</th>
                <th style="text-align: center;">推广订单总金额</th>
                <th style="text-align: center;">还信订单总金额</th>
                <th style="text-align: center;">自营订单总金额</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(function () {
        getMonthReport();
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
//    获取月统计数据
    function getMonthReport() {
        var store_name = $("#store").val();
        var years = $("#years").val();
        var months = $("#months").val();
        var type = $("#type2").val();
        $.ajax({
            url: "/report/group-money/get-month-report",
            type: "POST",
            data: {years: years,months:months,store_name: store_name,type:type},
            async: false,
            dataType: "json",
            success: function (json) {
                if (json["result"] == "") {
                    $("#pwTable tbody").html("暂无此项统计数据");
                    return false;
                }
                // console.log(json["result"]);
                // return false;
                var html = "";
                var allMoneys = 0;
                var nmMoneys = 0;
                var wqMoneys = 0;
                var hxMoneys = 0;
                var zyMoneys = 0;
                $.each(json["result"], function (key, val) {
                    allMoneys += eval(val.allMoney);
                    nmMoneys += eval(val.nmMoney);
                    wqMoneys += eval(val.wqMoney);
                    hxMoneys += eval(val.hxMoney);
                    zyMoneys += eval(val.zyMoney);
                    html += "<tr styly='text-align: center'>"
                    html += "<td style='text-align: center'><a>" + val.months + "</a></td>"
                    html += "<td style='text-align: center'>" + val.province + "</td>"
                    html += "<td style='text-align: center'>" + val.city + "</td>"
                    html += "<td style='text-align: center'>" + val.store_name + "</td>"
                    html += "<td style='text-align: center'>" + val.allMoney + "</td>"
                    html += "<td style='text-align: center'>" + val.nmMoney + "</td>"
                    html += "<td style='text-align: center'>" + val.wqMoney + "</td>"
                    html += "<td style='text-align: center'>" + val.hxMoney + "</td>"
                    html += "<td style='text-align: center'>" + val.zyMoney + "</td>"
                    html += "</tr>"
                });
                html += "<tr style='font-weight:bold;background-color:#E9ECF3'><td style='text-align: center;font-size:16px'>统计</td><td style='text-align: center;font-size:16px'></td><td style='text-align: center;font-size:16px'></td><td style='text-align: center;font-size:16px'>店铺</td><td style='text-align: center;font-size:16px'>" + allMoneys + "</td><td style='text-align: center;font-size:16px'>" + nmMoneys + "</td><td style='text-align: center;font-size:16px'>" + wqMoneys + "</td><td style='text-align: center;font-size:16px'>" + hxMoneys+ "</td><td style='text-align: center;font-size:16px'>" + zyMoneys + "</td></tr>"
                if (html == '') {
                    html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                }
                $("#pwTable tbody").html(html);
            }
        })
    }

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
    $("#btnExportYears").click(function(){
        msgConfirm('提醒',"确定导出报表?",function(){
            $('#pwTable').tableExport({
                type:'excel',
                escape:'false',
                fileName: '门店月销售金额统计'
            });
        })
    });

</script>

