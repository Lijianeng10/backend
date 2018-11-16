<?php
use yii\helpers\Html;
use yii\grid\GridView;

//var_dump($store);
echo '<ul>';
echo '<li style="margin:10px 0px">';
echo Html::label("用户编号", "", ["style" => "margin-left:5px;"]);
echo Html::input("text", "",$cust_no, ["class" => "form-control","id" => "cust","disabled"=>true ,"style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin:10px 0px">';
echo Html::label("绑定彩店", "", ["style" => "margin-left:5px;"]);
echo Html::dropDownList("store", "",$store, ["class" => "form-control","id" => "store","style" => "width:120px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li style="margin:20px 0px">';
echo Html::button("确定", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;","id" => "sureBtn"]);
echo Html::button("取消", ["class" => "am-btn am-btn-primary inputLimit", "id" => "closeBtn"]);
echo '</li>';
echo '</ul>';
?>
<script>
    $("#sureBtn").click(function(){
       var cust_no=$("#cust").val();
       var store=$("#store").val();
       if(store==0){
           msgAlert("请选择需要绑定的彩店")
       }else{
           $.ajax({
            url: "/member/list/addstore",
            type: "POST",
            async: false,
            data: {cust_no: cust_no,store:store},
            dataType: "json",
            success: function (data) {
                if (data["code"] != 600) {
                    msgAlert(data["msg"]);
                } else {
                    msgAlert(data['msg'], function (){
                        location.reload();
                    });
                }
            }
        });
       }
       
    })
     $("#closeBtn").click(function(){
       closeMask();
    })
</script>

