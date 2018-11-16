<style>
    .txt{
        width: 121px;
        text-align: right;
    }
</style>
<div>
    <form id="doc-vld-msg">
        <div  style="margin-top: 10px;">
<!--            <div>
                <label class="txt">出票类型：</label>
                <label>
                    <select class ="form-control" style="display: inline-block;width:90px;" id="type">
                        <option value="0">请选择</option>
                        <option value="1">手工出票</option>
                        <option value="2">自动出票</option>
                    </select>
                </label>
            </div>-->
            <div>
                <label class="txt">兑换码类型：</label>
                <label>
                    <input type="number"   class ="form-control" style="display: inline-block;width: 150px;" id="ns">
                </label>
            </div>
            <div>
                <label class="txt">兑换码位数：</label>
                <label>
                    <input type="number"   class ="form-control" style="display: inline-block;width: 150px;" id="nums" placeholder="请输入1-32内正整数">
                </label>
            </div>
            <div>
                <label class="txt">预发行张数：</label>
                <label>
                    <input type="number"   class ="form-control" style="display: inline-block;width: 150px;" id="numbers" >
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
        var ns = $("#ns").val();
        var nums = $("#nums").val();
        var numbers = $("#numbers").val();
        if((/^(\+|-)?\d+$/.test(ns))&&ns>0||(/^(\+|-)?\d+$/.test(nums))&&nums>0||(/^(\+|-)?\d+$/.test(numbers))&&numbers>0){
            $.ajax({
                url: '/promote/record/add-code',
                async: false,
                type: 'POST',
                data: {ns:ns,nums:nums,numbers:numbers},
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
        }else{
            msgAlert("请输入正确的正整数值") 
        }
    })
</script>

