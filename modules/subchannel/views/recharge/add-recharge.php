<style>
    .am-form-group p{
        margin-top: 8px;
    }
    .infoSpan{
        display: inline-block;
        width: 90px;
        text-align: right;
        font-size: 14px;
    }
    .infoInput{
        margin-left: 5px;
        border-radius: 4px;
    }
</style>
<div class="am-form-group">
    <?php 
    use yii\helpers\Html;
        if($_SESSION["type"]==0){
            echo '<p><span class="infoSpan">充值申请人</span>';
            echo Html::dropDownList('bussiness_id', isset($get['bussiness_id']) ? $get['bussiness_id'] : '', $channel, [ 'class' => 'form-input','id'=>'bussinessId']);
            echo '</p>';
        }else{
            echo '<p style="display:none"><span class="infoSpan">充值申请人</span>';
            echo Html::input('hidden', 'bussiness_id',"", ['class' => 'form-input','id'=>'bussinessId']);
            echo '</p>';
        }
    ?>
    <p>
        <span class="infoSpan">充值金额</span>
        <input type="input" placeholder="请输入充值金额" class="form-input" id="money">
    </p>
    <p>
        <div style="margin-top:5px">
            <span class="infoSpan">转账凭证</span>
            <label>
                <div>
                    <a class="buttomspan" onclick="$('#picture').click();">上传</a>
                    <a class="buttomspan" onclick="javascript:$('#picture').val('');$('#showimg').attr('src', '/image/u1529.png');$('#showimg').css({ width:'140px',height:'140px',});">| 删除</a>
                </div>
            </label>
            <img src="/image/u1529.png" id="showimg" style="width: 140px;height: 140px" class="am-img-thumbnail">
            <input type="file" id="picture" class="imgupload" name="picture" required>
        </div>
    </p>
    <p>
        <span class="infoSpan">备注</span>
        <textarea style="width:300px;height: 100px;" id="remark"></textarea>
    </p>
    <p>
        <button class="am-btn am-btn-primary" id="addSubmit" style="margin-left:30px" >提交</button>
        <button class="am-btn am-btn-primary" id="backSubmit" >返回</button>
    </p>
</div>
<script>
    //广告图片
    $('#picture').change(function () {
        var file = this.files[0];
        var scr = $('#showimg').attr("src");
        var objUrl = getObjectURL(this.files[0]);
        if (objUrl) {
            $('#showimg').attr("src", objUrl);
        }
        var imgType=["image/png","image/jpg","image/jpeg","image/gif"];
        if ($.inArray(file.type,imgType)=="-1") { 
            msgAlert("图片类型必须是.jpeg,jpg,png,gif中的一种");
            $('#showimg').attr("src", "/image/u1529.png")
            return false;     
        }
//        $('#showimg').css({
//            width:"720px",
//            height:"95px",
//        })
    });
    function getObjectURL(file) {
         var url = null;
         if (window.createObjectURL != undefined) { // basic
              url = window.createObjectURL(file);
         } else if (window.URL != undefined) { // mozilla(firefox)
              url = window.URL.createObjectURL(file);
         } else if (window.webkitURL != undefined) { // webkit or chrome
              url = window.webkitURL.createObjectURL(file);
         }
         return url;
    }   
    //添加转账申请
    $('#addSubmit').click(function () {
        var bussinessId = $('#bussinessId').val();
        var money = $("#money").val();
        var remark = $("#remark").val();
        if (bussinessId == "0") {
            msgAlert("请选择充值申请人")
            return false;
        }
         if(isNaN(parseInt(money))||money<=0){
            msgAlert("请输入合法的数字");
            return false;
        }
        if ($('#showimg').attr("src") == '/image/u1529.png') {
            msgAlert("请上传转账凭证")
            return false;
        }
        var formData = new FormData();
        formData.append("bussinessId", bussinessId);
        formData.append("upfile", $("#picture").get(0).files[0]);
        formData.append("money", money);
        formData.append("remark", remark);
        $.ajax({
            url: '/subchannel/recharge/add-recharge',
            async: false,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            success: function (data) {
                if (data['code'] == 600) {
                     msgAlert(data['msg'], function () {
                        location.href = '/subchannel/recharge/index';
                    });
                } else {
                    msgAlert(data["msg"]);
                }
            }
        });
    })
    $("#backSubmit").click(function () {
        closeMask();
    })
</script>

