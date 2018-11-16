<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>
<div style="margin-top: 20px;margin-left: 10px;font-size: 14px;">
    <p>
        <lable class="form-span">活动主题：</lable>
        <?php
        echo Html::input("text", "activity_name","", ["id" => "activity_name", "class" => "form-control", "placeholder" => "活动主题", "style" => "width:150px;display:inline;margin-left:5px;"]);
        ?>
    </p>
    <p>
        <lable class="form-span">使用代理：</lable>
            <?php
            echo Html::dropDownList("use_agents","", $proplayform, ["class" => "form-control","id"=>"use_agents" , "style" => "width:120px;display:inline;margin-left:5px;"]);
            ?>
    </p>
    <p>
        <lable class="form-span">活动类型：</lable>
        <?php
        echo Html::dropDownList("type","", $ac_type, ["class" => "form-control","id"=>"type" , "style" => "width:120px;display:inline;margin-left:5px;"]);
        ?>
    </p>
    <p>
        <?php
        echo Html::label("有效期：", "",["class"=>"form-span"]);
        echo Html::input("text", "startdate","", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:150px;display:inline;margin-left:5px;"]);
        echo "-";
        echo Html::input("text", "enddate","", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:150px;display:inline;margin-left:5px;"]);
        ?>
    </p>
    <div style="text-align:center;" >
        <table border="1" style="width: 80%;margin:auto;">
            <thead >
            <tr text-align="center">
                <th style="text-align: center">操作</th>
                <th style="text-align: center">优惠券批次</th>
                <th style="text-align: center">优惠券名称</th>
                <th style="text-align: center">最低消费</th>
                <th style="text-align: center">优惠金额</th>
                <th style="text-align: center">赠送数量</th>
            </tr>
            </thead>
            <tbody id="coupons">
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 20px;">
    <button class="am-btn am-btn-primary" style="margin-left:104px;font-size: 14px;" id="addBtn">新增</button>
    <button  class="am-btn am-btn-primary" style="margin-left: 5px;font-size: 14px;" id="closeBtn">关闭</button>
</div>

<script>
    //日期插件
    laydate.render({
        elem: '#startdate',//指定元素
        type: 'datetime'
    });
    laydate.render({
        elem: '#enddate',//指定元素
        type: 'datetime'
    });
    //根据代理得到所属优惠券批次
    $("#use_agents").change(function () {
        var use_agents = $(this).val();
        if (use_agents != "") {
            $.ajax({
                url: "/website/activity/get-batch",
                data: {use_agents: use_agents,},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        var html ="";
                        $("#coupons").empty();
                        if(json["result"]!=""){
                            var d =json["result"];
                            for(var i = 0; i <=d.length; i++) {
                                var html = '<tr id="exCoupons_' + d[i]["batch"] + '">' +
                                    '<td><input type="checkbox" class="chose" value="' + d[i]["batch"]+ '"style="width:15px;height:15px"></td>' +
                                    '<td>' +d[i]["batch"] + '</td>' +
                                    '<td>' +d[i]["coupons_name"] +'</td>' +
                                    '<td>' +d[i]["less_consumption"] +'</td>' +
                                    '<td>' +d[i]["reduce_money"] +'</td>' +
                                    '<td><input type="text" class="ex_nums"  style="width:50%;border:1px solid;text-align: center" value="1"></td></tr>'
                                $('#coupons').append(html)
                            }
                        }else{
                            $("#batch").append(html);
                        }
                    }
                }
            });
        }
    })
    $("#addBtn").click(function(){
        var activityAry = [];
        $('.chose').each(function () {
            if ($(this).is(':checked')) {
                var exCouponsObj = new Object();
                exCouponsObj.batch = $(this).parent().next().html();
                exCouponsObj.send_num = $(this).parent().next().next().next().next().next().children().val();
                activityAry.push(exCouponsObj);
            }
        })
        var activity_name =$("#activity_name").val();
        var use_agents =$("#use_agents").val();
        // var send_num =$("#send_num").val();
        var type =$("#type").val();
        var start_date =$("#startdate").val();
        var end_date =$("#enddate").val();
        if(activityAry.length==0){
            msgAlert("请选择需要赠送的优惠券")
            return false;
        }else{
            $.ajax({
                url: "/website/activity/add-activity",
                data: {"activity_name":activity_name,"use_agents":use_agents,"start_date":start_date,"end_date":end_date,"type":type,"activityAry":activityAry},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.href = '/website/activity/index';
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            })
        }
    })
    $("#closeBtn").click(function(){
        closeMask();
    })
</script>


