<style>
    .action{
        width: 40%;
        text-align: left;
        padding-left: 0px;
        padding-top: 10px;
        display: inline-block;
    }
    .stxt{
        display: inline-block;
        width: 40%;
    }
    .select{
        display: inline-block;
        width: 18px;
        height: 18px;
    }
</style>
<form class="am-form" id="doc-vld-msg">
    <div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
        <label style="margin-top: 20px;font-size: 16px;">代理商：</label>
        <select id="agents"  style="width:140px;display:inline;margin-left:5px;">
            <?php foreach ($agents as $key => $item): ?>
                <option value="<?php echo $key; ?>"><?php echo $item; ?></option>
            <?php endforeach; ?>
        </select>
<!--        <label style="margin-top: 20px;font-size: 16px;">所在地区：</label>-->
<!--        <div id="city_china"  style="display:inline-block;margin-left:6px;">-->
<!--            <select class="province"  style="width:140px;display:inline;margin-left:5px;" id="province">-->
<!---->
<!--            </select>-->
<!--        </div>-->
        <button class='am-btn am-btn-secondary' id='searchSubmit'  style='margin-left:5px;'>搜索</button>
        <button class='am-btn am-btn-secondary' id='addSubmit'  style='margin-left:5px;'>设置</button>

    </div>
    <div id="storeInfo">

    </div>
</form>
<iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
<script src="/js/jquery.cxselect.min.js"></script>
<script type="text/javascript">
    //省份
    $('#city_china').cxSelect({
        url: '/js/cityData.min.json',
        selects: ['province'],
        emptyStyle: 'none'
    });
    $(function () {
        //点击搜索代理商信息
        $("#searchSubmit").click(function () {
            document.forms[0].target = "rfFrame";
            var id = $("#agents").val();
            var province = $("#province").val();
            // var province ="";
            if(id!=""){
                $.ajax({
                    url: '/agents/weight/get-weight-info',
                    async: false,
                    type: 'POST',
                    data: {id: id,province:province},
                    dataType: 'json',
                    success: function (json) {
                        if (json['code'] != 600) {
                            msgAlert(json['msg']);
                        } else {
                            $("#storeInfo").html("");
                            var res = json["result"];
                            var html="";
                            if(res==""){
                                msgAlert("无门店数据");
                            }else{
                                var storeType =new Array();
                                storeType[0]="未知";
                                storeType[1]="个体自营店";
                                storeType[2]="个体转让店";
                                storeType[3]="咕啦自营店";
                                storeType[4]="贵人鸟加盟店";
                                for(var i=0;i<res.length;i++){
                                    html +="<p class='stxt'><input type='checkbox' value='"+res[i]["store_code"]+"' class='select' id='"+res[i]["store_code"]+"'>"+res[i]["province"]+"-"+res[i]["city"]+"-"+res[i]["store_name"]+"("+storeType[res[i]["store_type"]]+")"+"<input value='"+res[i]["weight"]+"' class='form-input'></p>";
                                }
                                $("#storeInfo").html(html);
                                for(var i=0;i<res.length;i++){
                                    if(res[i]["check"]==1){
                                        $("#"+res[i]["store_code"]).prop("checked",true)
                                    }
                                }
                            }
                        }
                    }
                });
            }else{
                msgAlert("请先选择代理商");
            }
        })
        $('#addSubmit').click(function () {
            document.forms[0].target = "rfFrame";
            var agents = $('#agents').val();
            var weightArr = [];
            $.each($(".select"), function () {
                if ($(this).is(':checked')) {
                    store = $(this).val();
                    weight =$(this).next("input").val();
                    weightArr.push(store + ':' + weight);
                }
            });
            // if(weightArr.length==0){
            //     msgAlert("没选中门店,请重新选择");
            //     return false;
            // }else{
                $.ajax({
                    url: '/agents/weight/set-out-lot-weight',
                    async: false,
                    type: 'POST',
                    data: {weightData: weightArr, agents: agents},
                    dataType: 'json',
                    success: function (result) {
                        if (result['code'] != 600) {
                            msgAlert(result['msg']);
                        } else {
                            msgAlert(result['msg'], function () {
                                location.reload();
                            });
                        }
                    }
                });
            // }

        })

    })
</script>
