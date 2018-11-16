<?php
use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\widgets\DetailView;
$this->title = 'AddLottery';
?>
<?php
echo '<form id="addlottery">';
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => '彩种编码<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'lottery_code','',['class'=>'form-control need', 'id'=>'l_code']);
            }
        ], [
            'label' => '彩种名称<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                return Html::input('text', 'lottery_name','',['class'=>'form-control need', 'id'=>'l_name']);
            }
        ],  [
            'label' => '所属分类<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                return Html::dropDownList("category_id", "0", $model, ['class' => 'form-control', 'id'=>'l_category']) ;
            }
        ],[
            'label' => '彩种排序',
            'format' => 'raw',
            'value' => function($model) {
                return Html::input('text', 'sort','99', ['class' => 'form-control', 'id' => 'sort']);
            }
        ],  [
            'label' => '缩略图<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function() {
                $html = "<div id='preview' >";
                $html.=Html::img("/image/u1529.png", ["id" => "showimg", "style" => "filter:;width:80px;height:80px;border:2px inset #EEE"]);
                $html .= "</div>";
                $html .= "<div>";
                $html.=Html::tag("a", "上传", ["class" => "buttomspan", "onclick" => "$('#lottery_pic').click();"]);
                $html.=Html::tag("a", "| 删除", ["class" => "buttomspan", "onclick" => "javascript:$('#lottery_pic').val('');$('#showimg').attr('src', '/image/u1529.png');"]);
                $html .= "</div>";
                $html.=Html::tag("span", "图片尺寸限制为108X108，大小限制为200KB以下", ["class" => "buttomspan", "style" => "color:#bbbbbb;"]);
                $html.=Html::fileInput("race_img", "", ["class" => "imgupload", "id" => "lottery_pic", "onchange" => "previewImage(this);"]);
                return $html;
            }
        ],[
            'label' => '备注',
            'format' => 'raw',
            'value' => function() {
                return Html::textarea('lottery_des', '' ,['class'=>'form-control', 'id'=>'l_descript']);
            }
        ], [
            'label' => '',
            'format' => 'raw',
            'value' => function() {
                $html = "<div class = 'error_msg'></div>";
                return $html . Html::button('提交', ['class'=>'am-btn am-btn-primary', 'id'=>'addSubmit']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class'=>'am-btn am-btn-primary', 'id'=>'reback']);
            }
        ]
    ]
]);    
echo '</form>';
?>

<script type="text/javascript">
    function previewImage(file)
    {
//        if (file.files && file.files[0])
//        {
//            var reader = new FileReader();
//            reader.onload = function (evt) {
//                $("#showimg").attr("src", evt.target.result);
//            }
//            reader.readAsDataURL(file.files[0]);
//            return false;
//        } else //兼容IE
//        {
//            var sFilter = 'filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
//            file.select();
//            var src = document.selection.createRange().text;
//            $("#showimg").attr("style", sFilter + src + '")');
//            $("#showimg").attr("src", src);
//        }
    }
    $(function () {
        $('#lottery_pic').change(function() {
            var file = this.files[0];
            var scr = $('#showimg').attr("src");
            var objUrl = getObjectURL(this.files[0]);
            if(objUrl){
              $('#showimg').attr("src",objUrl);
            }
            if(!/.(jpg|jpeg|png)$/.test(file.name)){ 
                msgAlert("图片类型必须是.jpeg,jpg,png中的一种");
                $('#showimg').attr("src",scr);     
            }else if(file.size > 200 * 1024){
                msgAlert('图片大小不可超过200KB');
                $('#showimg').attr("src",scr); 
            }
            
           
        });
        
        $('#addSubmit').click(function () {
            err = 0;
            var formData= new FormData();
            var data =$("#addlottery").serializeArray();
            formData.append("upfile", $("#lottery_pic").get(0).files[0]);
            $.each(data,function(i, field){
                formData.append(field.name,field.value);
            });
            $(".need").each(function(i){
            var text = $(this).val();
            if(text ==""){
                   err++;
                   $(this).focus();
                   $("#msg").empty();
                   h = '<span id="msg" style="color:red;">请填写此字段</span>';
                   $(this).after(h);
                   return false;
            }
            });
            if(err != 0){
                return false;
            }
            $.ajax({
                url: '/lottery/lottery/addlottery',
                async: false,
                type: 'POST',
                processData: false,
            contentType: false,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        $("#msg").empty();
                        console.log(data.result)
                        h = '<span id="msg" style="color:red;">' + data['msg'] + '</span>';
                        $(".error_msg").prepend(h);
                        $("input[type=text]").each(function(){
                            if($(this).val()=='') {
                                $(this).focus();
                                return false
                            }
                        });
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
//                            location.href = '/lottery/lottery/index';
                        });
                    }
                }
            });
        });
        
        $("#reback").click(function () {
            closeMask();
//            location.href = '/lottery/lottery/index';
        });
    });
    
    function getObjectURL(file) {
         var url = null ; 
         if (window.createObjectURL!=undefined) { // basic
          url = window.createObjectURL(file) ;
         } else if (window.URL!=undefined) { // mozilla(firefox)
          url = window.URL.createObjectURL(file) ;
         } else if (window.webkitURL!=undefined) { // webkit or chrome
          url = window.webkitURL.createObjectURL(file) ;
         }
         return url ;
    }
    
</script>