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
    <div id="pollingType" style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
        <label style="margin-top: 20px;font-size: 16px;">轮循总类：</label>
        <select id="info"  style="width:140px;display:inline;margin-left:5px;">
            <?php foreach ($data as $key => $item): ?>
                <option value="<?php echo $key; ?>"><?php echo $item; ?></option>
            <?php endforeach; ?>
        </select>
        <label id='infoLabel' style='margin-top: 20px;font-size: 16px;'>轮循子类：</label>
        <select id='infoType' style='width:35%;display:inline;margin-left:5px'>
            <option value=0>请选择</option>
        </select>
        <!--<button class='am-btn am-btn-secondary' id='searchSubmit'  style='margin-left:5px;'>搜索</button>-->
        <button class='am-btn am-btn-secondary' id='addSubmit'  style='margin-left:5px;'>设置</button>
<!--        <div id="storeInfo">
            <input type='radio' name='polling'  value='1'>是
            <input type='radio' name='polling' value='2'>否
    </div>-->
    </div>
    
    <!--    -->
</form>
<iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
<script src="/js/jquery.cxselect.min.js"></script>
<script type="text/javascript">
    $(function () {
        $("#info").change(function () {
            infoType = $(this).val();
            if (infoType != 0) {
                $.ajax({
                    url: '/admin/polling/get-polling-info',
                    async: false,
                    type: 'POST',
                    data: {infoType: infoType},
                    dataType: 'json',
                    success: function (json) {
                        if (json['code'] != 600) {
                            msgAlert(json['msg']);
                        } else {
                            $("#infoType").empty();
//                            $("#infoLabel").empty();
                            var res = json["result"];
                            var html = "<option value=0>请选择</option>"
                            switch (infoType) {
                                case '1' :
                                    $.each(res, function (index, value) {
                                        html += "<option value=" + index + ">" + value + "</option>";
                                    });
                                    break;
                                case '2' :
                                    var storeType = new Array();
                                    storeType[0] = "未知";
                                    storeType[1] = "个体自营店";
                                    storeType[2] = "个体转让店";
                                    storeType[3] = "咕啦自营店";
                                    storeType[4] = "贵人鸟加盟店";
                                    $.each(res, function (index, value) {
                                        html += "<option value=" + index + ">" + value['store_name'] + "(" + storeType[value['store_type']] + ")</option>";
                                    })
                                    break;
                                case '3' :
                                    $.each(res, function (index, value) {
                                        html += "<option value=" + index + ">" + value['agents_name'] + "</option>";
                                    })
                                    break;
                                case '4' :
                                    $.each(res, function (index, value) {
                                        html += "<option value=" + index + ">" + value['name'] + "</option>";
                                    })
                            }
//                            html += "</select>";
                            $('#infoType').append(html);
//                            html.appendTo($('#pollingType'));
                        }
                    }
                });
            }else {
                $("#infoType").empty();
                $("#infoType").append('<option value=0>请选择</option>');
//                $("#infoLabel").remove();
            }
        })
        $("#infoType").change(function() {
            sourceCode = $(this).val();
            infoType = $("#info").find("option:selected").val();
            if (sourceCode != 0) {
                $.ajax({
                    url: '/admin/polling/get-set-info',
                    async: false,
                    type: 'POST',
                    data: {sourceCode: sourceCode, infoType:infoType},
                    dataType: 'json',
                    success: function (json) {
                        if (json['code'] != 600) {
                            msgAlert(json['msg']);
                        } else {
                            $("#setLabel").remove();
                            $("#polling").remove();
                            var res = json["result"];
                            var isCheck1 = '';
                            var isCheck2 = '';
                            if(res.length == 0) {
                                isCheck1 = '';
                                isCheck2 = '';
                            } else if(res['polling_type'] == 1) {
                                isCheck1 = 'checked';
                                isCheck2 = '';
                            }else{
                                isCheck1 = '';
                                isCheck2 = 'checked';
                            }
                            var html = "<label id='setLabel' style='margin-top: 20px;font-size: 16px;'>是否轮循：</label><label id='polling'><input type='radio' name='pollingType'"+ isCheck1 +" value='1'>是<input type='radio' name='pollingType' "+ isCheck2 +" value='2'>否</label>";
                            
                            $('#addSubmit').before(html)
//                            html.appendTo($('#pollingType'));
                        }
                    }
                });
            }else {
                $("#setLabel").remove();
                $("#polling").remove();
            }
        })

        $('#addSubmit').click(function () {
            document.forms[0].target = "rfFrame";
            sourceCode = $("#infoType").find("option:selected").val();
            infoType = $("#info").find("option:selected").val();
            isPolling = $('#polling input[name="pollingType"]:checked ').val();
            sourceName = $("#infoType").find("option:selected").text();
            console.log(sourceName);
            $.ajax({
                url: '/admin/polling/set-polling',
                async: false,
                type: 'POST',
                data: {sourceCode: sourceCode, infoType: infoType, isPolling:isPolling, sourceName:sourceName},
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
