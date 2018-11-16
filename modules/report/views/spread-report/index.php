<ul class="am-nav am-nav-tabs" id="statusArr" style="margin-bottom:10px;">
    <li role="presentation" class="am-active" flag="un_settle_list"><a onclick="statusArrClick($(this));">未结算</a></li>
    <li role="presentation" flag="settle_list"><a onclick="statusArrClick($(this));">已结算 </a></li>
</ul>
<div>
    <form class="myForm" id="filterForm">
        <ul class="third_team_ul">
            <li>
                <label style="margin-left:15px;" >推广人信息:</label>
                <input type="text" id="user_info" class="form-control" placeholder="推广人编号、手机号、名称" style="width:250px;display: inline-block;" >
            </li>
            <li>
                <label style="margin-left:15px;" >上级信息:</label>
                <input type="text" id="agent_info" class="form-control" placeholder="上级编号、手机号、名称" style="width:250px;display: inline-block;" >
            </li>
            <li style="display:none" id="date">
                <label>出票年份:</label>
                <select  class="form-control" id="years" style="width: 100px;display: inline;margin-left:5px;">
                    <option><?php echo date("Y"); ?></option>
                    <option><?php echo date("Y") - 1; ?></option>
                </select>
                <label>出票月份:</label>
                <select  class="form-control" id="months" style="width: 100px;display: inline;margin-left:5px;">
                    <?php
                    $Ary=["01","02","03","04","05","06","07","08","09","10","11","12"];
                    foreach ($Ary as $v){
                        if(date("m")-1==$v){
                            echo '<option selected>'.$v.'</option>';
                        }else{
                            echo '<option >'.$v.'</option>';
                        }
                    }
                    ?>
                </select>
            </li>
            <li>
                <input type="button" class="am-btn am-btn-primary" id="filterButton" value="统计" style="margin-left: 21px">
                <input type="reset" class="am-btn am-btn-primary" id="btnExport" value="导出">
                <input type="reset" class="am-btn am-btn-primary" id="resetDay" value="重置">
            </li>
        </ul>
    </form>
    <table class="table" id="pwTable">
        <thead>
            <tr>
                <th style="text-align: center;">月份</th>
                <th style="text-align: center;">推广人编号</th>
                <th style="text-align: center;">手机号</th>
                <th style="text-align: center;">上级编号</th>
                <th style="text-align: center;">上级手机号</th>
                <th style="text-align: center;">返点%</th>
                <th style="text-align: center;">购彩量</th>
                <th style="text-align: center;">提成</th>
                <th style="text-align: center;">提成状态</th>
                <th style="text-align: center;">是否需要发放</th>
                <th style="text-align: center;">操作</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    getReport({type:'un_settle_list'});
    //页面切换选择
    function statusArrClick(_this) {
        var flag = _this.parent().attr("flag");
        $("#statusArr").find("li").removeClass("am-active");
        _this.parent("li").addClass("am-active");
        getReport({type:flag});
        if(flag=='un_settle_list'){
            $("#date").css("display","none");
        }else if(flag=='settle_list'){
            $("#date").css("display","block");
        }
    }
    //点击统计
    $("#filterButton").click(function () {
        $("#statusArr").children().each(function () {
            if($(this).attr('class')=='am-active'){
                var flag = $(this).attr('flag');
                getReport({type:flag});
            }
        })
    })
//    获取未结算统计数据
    function getReport(options) {
        $("#pwTable tbody").html("");
        var user_info = $("#user_info").val();
        var agent_info = $("#agent_info").val();
        var years = $("#years").val();
        var months = $("#months").val();
        var data = $.extend({user_info:user_info, agent_info:agent_info, years:years, month:months},options);
        $.ajax({
            url: "/report/spread-report/get-spread-report",
            type: "POST",
            data: data,
            async: false,
            dataType: "json",
            success: function (json) {
                //不发放提成的用户数组
                var noSendAry= new Array();
                noSendAry[0] = 'comorange';//林中
                noSendAry[1] = 'gl00002324';//启炜
                noSendAry[2] = 'gl00001051';//许总
                noSendAry[3] = 'gl00018214';//雅娜
                noSendAry[4] = 'gl00003295';
                noSendAry[5] = 'gl00026327';

                if (json["result"] == "") {
                    $("#pwTable tbody").html("暂无此项统计数据");
                    return false;
                }
                var html = "";
                var totalAmount = 0;
                var amount = 0;
                $.each(json["result"], function (key, val) {
                    totalAmount += eval(val.total_amount);
                    amount += eval(val.amount);
                    html += "<tr styly='text-align: center'>"
                    html += "<td style='text-align: center'><a>" + val.settle_month + "</a></td>"
                    html += "<td style='text-align: center'>" + val.cust_no + "</td>"
                    html += "<td style='text-align: center'>" + val.user_tel + "</td>"
                    html += "<td style='text-align: center'>" + val.agent_code + "</td>"
                    html += "<td style='text-align: center'>" + val.agent_tel + "</td>"
                    html += "<td style='text-align: center'>" + val.rate + "</td>"
                    html += "<td style='text-align: center'>" + val.total_amount + "</td>"
                    html += "<td style='text-align: center'>" + val.amount + "</td>"
                    html += "<td style='text-align: center'>" + (val.IsTC==1?"已发放":"未发放") + "</td>"
                    html += "<td style='text-align: center'>" + ($.inArray(val.cust_no, noSendAry)>=0?"否":"是") + "</td>"
                    html += "<td style='text-align: center' >"+(val.IsTC==0&&val.settleid!=''&&$.inArray(val.cust_no, noSendAry)<0?"<a onclick='awardAmount("+val.amount+","+val.user_tel+",\""+val.settleid+"\")'> 发放提成 |</a>":"")+(val.settleid!=''?"<a onclick='location.href =\"/report/spread-report/detail?settleid=" + val.settleid +"\"'> 查看会员详情 </a><a onclick='location.href=\"/report/spread-report/store-detail?settleid="+val.settleid+"\"'> | 查看门店详情 </a><a onclick='location.href=\"/report/spread-report/day-detail?settleid="+val.settleid+"\"'> | 查看每天详情 </a>":"<a onclick='location.href = \"/report/spread-report/detail?cust_no=" + val.cust_no +"\"'> 查看会员详情</a>")+"</td>"
                    html += "</tr>"
                });
                html += "<tr style='font-weight:bold;background-color:#E9ECF3'><td style='text-align: center;font-size:16px'>统计</td><td></td><td></td><td> </td><td></td><td></td><td style='text-align: center;font-size:16px'>" +totalAmount.toFixed(2) + "</td><td style='text-align: center;font-size:16px'>" + amount.toFixed(2)+ "</td><td></td><td></td><td></td></tr>"
                if (html == '') {
                    html = '<div style="width:100%;text-align:center;">没找到数据</div>';
                }
                $("#pwTable tbody").html(html);
            }
        })
    }
    //重置
    // $("#resetDay").click(function(){
        // location.href="/report/spread-report/index";

    // })
    // 点击发放提成
    function awardAmount(money,tel,id) {
        // location.href='/report/spread-report/award-amount?tel='+tel+'&settleid='+id;
        modDisplay({title: '发放提成', url:'/report/spread-report/award-amount?money='+money+'&tel='+tel+'&settleid='+id, height: 300, width: 600});
    }
    //导出表格
    $("#btnExport").click(function(){
        msgConfirm('提醒',"确定导出报表?",function(){
            $('#pwTable').tableExport({
                type:'excel',
                escape:'false',
                fileName: '推广分润统计'
            });
        })

    });
</script>

