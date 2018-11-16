<style>
    .txt{
        width: 121px;
        text-align: right;
    }
</style>
<div>
    <form id="doc-vld-msg">
        <div  style="margin-top: 10px;">
            <label class="txt">门店名称：</label><span style="padding-left: 8px;"><?php echo $data['store_name']?></span>
            </div>
            <input type="hidden" name="store_code" value="<?php echo $data['store_code'];?>">
            <div>
                <label class="txt">出票类型：</label>
                <label>
                    <select class ="form-control" style="display: inline-block;width:90px;" id="type">
                        <option value="0">请选择</option>
                        <option value="1">手工出票</option>
                        <option value="2">自动出票</option>
                    </select>
                </label>
            </div>
             <div>
                <label class="txt">机器编号：</label>
                <label>
                    <input type="text"   class ="form-control" style="display: inline-block;width: 150px;" id="dispenser_code">
                </label>
            </div>
            <div>
                <label class="txt">销售商代码：</label>
                <label>
                    <input type="text"   class ="form-control" style="display: inline-block;width: 150px;" id="vender_id">
                </label>
            </div>
            <div>
                <label class="txt">智魔方机子代码：</label>
                <label>
                    <input type="text"   class ="form-control" style="display: inline-block;width: 150px;" id="sn_code">
                </label>
            </div>
             <div>
                <label class="txt">预出票数：</label>
                <label>
                    <input type="text"   class ="form-control" style="display: inline-block;width: 150px;" id="pre_out_nums">
                </label>
            </div>
            <button type="button" class="am-btn am-btn-primary" id="addSubmit" style="margin-left: 50px;">提交</button>
            <button type="button" class="am-btn am-btn-primary" id="closeBtn" style="margin-left: 5px;">关闭</button>
</div>
<script>
    $("#closeBtn").click(function(){
        closeMask();
    })
    $("#addSubmit").click(function(){
        var type = $("#type").val();
        var dispenser_code = $("#dispenser_code").val();
        var store_no = $("input[name=store_code]").val();
        var pre_out_nums = parseInt($("#pre_out_nums").val()); 
        var vender_id = $("#vender_id").val();
        var sn_code = $("#sn_code").val();
        if(type==0){
            msgAlert("请选择出票类型")
        }else if(dispenser_code==""){
            msgAlert("请填写机器编号")
        }else if(pre_out_nums<=0 || isNaN(pre_out_nums)){
            msgAlert("预出票数有误，请检查")
        }else{
            $.ajax({
                url: '/agents/stores/add-dispenser',
                async: false,
                type: 'POST',
                data: {store_no:store_no,type:type,dispenser_code:dispenser_code,pre_out_nums:pre_out_nums,vender_id:vender_id,sn_code:sn_code},
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            });
        }
    })
</script>
