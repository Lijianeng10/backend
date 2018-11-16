<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\member\helpers\Constants;
?>
<style>
    .show-span{
        padding-left: 30px;
    }
</style>
<div style="margin-bottom: 5px;margin-left: 10px;font-size: 14px;height:105px">
<form action="/website/coupons/view-detail">
    <ul class="third_team_ul">
<?php
echo '<li>';
echo Html::input("hidden", "batch", isset($get["batch"]) ? $get["batch"] : "",["id"=>"batch"]);
echo Html::label("优惠券信息", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "coupons_no", isset($get["coupons_no"]) ? $get["coupons_no"] : "", ["class" => "form-control", "placeholder" => "优惠券号、兑换码", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("发送对象", "", ["style" => "margin-left:15px;"]);
echo Html::input("input", "send_user", isset($get["send_user"]) ? $get["send_user"] : "", ["class" => "form-control", "placeholder" => "会员编号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("发送状态", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("send_status", isset($get["send_status"]) ? $get["send_status"] : "",$send_status, ["class" => "form-control", "placeholder" => "发送类型", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("优惠券状态", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "",$status, ["class" => "form-control",  "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("使用状态", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("use_status", isset($get["use_status"]) ? $get["use_status"] : "", $use_status, [ "class" => "form-control", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("发送时间", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "start_date", isset($get["start_date"]) ? $get["start_date"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "end_date", isset($get["end_date"]) ? $get["end_date"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]) ;
echo '</li>';
echo '<li>';
echo Html::label("使用时间", "", ["style" => "margin-left:28px;"]);
echo Html::input("text", "start", isset($get["start"]) ? $get["start"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "end", isset($get["end"]) ? $get["end"] : "", ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]) ;
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:20px;"]);
echo Html::input("reset", '', '重置',  ["class" => "am-btn am-btn-primary", "onclick" => "goReset();","style" => "margin-left:5px;"]);
echo Html::input("reset", '', '返回',  ["class" => "am-btn am-btn-primary", "onclick" => "goBack();","style" => "margin-left:5px;"]);
echo '</li>';
?>
    </ul>
    </form>
</div>
<!--显示当前批次优惠券详细信息-->
<div style="font-size: 14px;">
    <h6 style="padding-left: 30px;">优惠券信息</h6>
    <p>
        <span class="show-span">名称:</span><span><?php echo $coupons["coupons_name"];?></span>
        <span class="show-span">类型:</span><span><?php
            $yh_type=Constants::YH_TYPE;
            echo $yh_type[$coupons["type"]];?>
        </span>
        <span class="show-span">最低消费:</span><span><?php echo $coupons["less_consumption"]."元";?></span>
        <span class="show-span">优惠金额:</span><span><?php echo $coupons["reduce_money"]."元";?></span>
        <span class="show-span">单日限用:</span><span><?php
            if($coupons["days_num"]==0){
                echo "不限";
            }else{
                echo $coupons["days_num"]."张";
            }

            ?>
        </span>
        <span class="show-span">预发数量:</span><span><?php echo $coupons["numbers"];?></span>
        <span class="show-span">已发数量:</span><span><?php echo $coupons["send_num"];?></span>
        <span class="show-span">已用数量:</span><span><?php echo $coupons["use_num"];?></span>
        <span class="show-span">有效期限:</span><span><?php echo $coupons["start_date"].' 至 '.$coupons["end_date"];?></span>
    </p>
</div>
<?php
echo GridView::widget([
"dataProvider" => $data,
"columns" => [
    [ 'class' => 'yii\grid\SerialColumn'],
    [
        'label' => '优惠券号',
        'value' => 'coupons_no'
    ], [
        'label' => '优惠券兑换码',
        'value' => 'conversion_code'
    ],[
        'label' => '所属批次',
        'value' => 'coupons_batch'
    ], [
        'label' => '发送状态',
        'value' => function($model){
            return $model["send_status"]==1?"未发送":"已发送";
        }
    ], [
        'label' => '使用状态',
        'value' => function($model){
             return $model["use_status"]==0?"未领取":($model["use_status"]==1?"未使用":"已使用");
        }
    ],[
        'label' => '优惠券状态',
        'value' => function($model){
             return $model["status"]==1?"激活":"锁定";
        }
    ],[
        'label' => '发送对象',
        'value' => 'send_user',
    ],[
        'label' => '发送时间',
        'value' => 'send_time',
    ],[
        'label' => '使用时间',
        'value' => 'use_time',
    ],[
        'label' => '订单来源',
        'value' => function($model){
            $source =[
                "1"=>"自购",
                "2"=>"追号",
                "3"=>"赠送",
                "4"=>"合买",
                "5"=>"分享",
                "6"=>"计划",
            ];
            return isset($model["use_order_source"])?$source[$model["use_order_source"]]:"";
        },
    ],[
        'label' => '使用订单号',
        'value' => 'use_order_code',
    ],[
        'label' => '操作',
        'format' => 'raw',
        'value' => function($model){
             return '<div class="am-btn-toolbar"><div class="am-btn-group am-btn-group-xs">'.($model["status"]==1?'<span class="handle pointer" onclick="edit('."'".$model['coupons_no']."'".',2)">锁定 |</span>':'<span class="handle pointer" onclick="edit('."'".$model['coupons_no']."'".',1)">激活 |</span>').
                     '<span class="handle pointer" onclick="readDetail('."'".$model['coupons_no']."'".')"> 查看 </span></div></div>';
        },
    ],
]
])
?>

<script>
    //重置
    function goReset(){
        var batch= $("#batch").val();
        location.href = '/website/coupons/view-detail?batch='+batch;
    }
    //返回
    function goBack(){
        location.href = '/website/coupons/index';
    }
    //修改状态
    function edit(id,status){
        msgConfirm ('提醒','确定要修改该优惠券状态吗？',function(){
            $.ajax({
                url: "/website/coupons/editstatus",
                data: {id:id,status:status},
                type: "POST",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"],function(){
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        })
    }
    //查看优惠券详细
    function readDetail(coupons_no){
        modDisplay({title: '优惠券详情', url:"/website/coupons/read-detail?coupons_no="+coupons_no, height: 450, width: 600});
    }
    
</script>

