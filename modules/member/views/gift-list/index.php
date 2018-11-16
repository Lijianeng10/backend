<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\helpers\Constants;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/member/gift-list/index">
        <ul class="third_team_ul">
    <?php
    echo '<li>';
    echo Html::label("礼品名称", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "gift_name", isset($get["gift_name"]) ? $get["gift_name"] : "", ["class" => "form-control", "placeholder" => "礼品名称、简码", "style" => "width:150px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("礼品类型", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("gift_type", (isset($_GET["gift_type"]) ? $_GET["gift_type"] : ""), $gift_type, ["class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("类别名称", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("cate_name", (isset($_GET["cate_name"]) ? $_GET["cate_name"] : "0"), $cateList, ["class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("兑换状态", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("status", (isset($_GET["status"]) ? $_GET["status"] : ""), $status, ["class" => "form-control", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:21px;"]);
    echo Html::button("重置", ["class" => "am-btn am-btn-primary inputLimit", "id" => "resetBtn" ]);
    echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addGift" ]);
    echo '</li>';
    ?>
        </ul>
    </form>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $dataList,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '礼品名称',
            'value' => 'gift_name'
        ], [
            'label' => '礼品简码',
            'value' => 'gift_code'
        ], [
            'label' => '所属类别',
            'value' => function($model){
                $type = Constants::GIFT_TYPE;
                return $type[$model["type"]];
            }
        ],
//        [
//            'label' => '所属分类',
//            'value' => 'category_name'
//        ],
        [
            'label' => '优惠券批次',
            'value' => 'batch'
        ],[
            'label' => '所需咕币',
            'value' => 'gift_glcoin'
        ],[
            'label' => '所需积分',
            'value' => 'gift_integral'
        ], [
            'label' => '缩略图',
            'format'=> 'raw',
            'value' => function($model){
                $html = '';
                if(!empty($model['gift_picture'])){
                    $html = '<img data-magnify="gallery" data-src="'.$model["gift_picture"].'" src="'.$model["gift_picture"].'" style="width:40px;height:40px">';
                }
                return $html;
               
            }
        ], 
//                [
//            'label' => '详情图',
//            'format'=> 'raw',
//            'value' => function($model){
//                if(!empty($model['gift_picture2'])){
//                    return Html::img($model['gift_picture2'],['width'=>'40px','height'=>'40px']);
//                }else{
//                    return "";
//                }
//               
//            }
//        ], 
                [
            'label' => '库存',
            'value' => 'in_stock'
        ], [
            'label' => '已兑换数量',
            'value' => 'exchange_nums'
        ], 
//                [
//            'label' => '所属上级',
//            'value' => 'agent_name'
//        ], 
                [
            'label' => '等级限制',
            'value' => function($model){
                $userAry=[
                    "0"=>"无等级限制",
                    "1"=>"初出茅庐",
                    "2"=>"蒙猜大虾",
                    "3"=>"江湖半仙",
                    "4"=>"神机妙算",
                    "5"=>"未卜先知",
                    "6"=>"你就是神",
                    
                ];
                return $userAry[$model["gift_level"]];
                        
            }
        ],[
            'label' => '活动时间',
            'value' => function($model){
//                return explode(" ", $model["start_date"])[0]."-".explode(" ", $model["end_date"])[0];
                return $model["start_date"]." - ".$model["end_date"];
                        
            }
        ], [
            'label' => '活动状态',
            'value' => function($model){
               $timer = date("Y-m-d H:i:s");
               if(strtotime($timer)>strtotime($model["end_date"])){
                   return "已过期";
               }else{
                   return "活动中";
               }
            }
        ],
//                [
//            'label' => '礼品备注',
//            'value' => 'gift_remark', 
//            'headerOptions' => ['style' => 'width:350px']
//        ],
                [
            'label' => '兑换状态',
            'value' => function($model){
                $status = [
                    "1"=>"在线",
                    "2"=>"下线"
                ];
                return $status[$model["status"]];
                        
            }
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                return  '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs"><span class="handle pointer" onclick="readGift('.$model['gift_id'].');"> 查看详情 |</span> <span class="handle pointer" onclick="editGift('.$model['gift_id'].');"> 编辑 | </span><span class="handle pointer" onclick="delGift('.$model['gift_id'].');"> 删除 |</span>'.
                                ($model['status'] == 1 ? '<span class="handle pointer" onclick="editSta(' . $model['gift_id'] .',2);"> 下线 ' : '<span class="handle pointer" onclick="editSta(' . $model['gift_id'] . ',1);"> 上线 ' ) .
                                '
                            </div>
                        </div>';
            }
        ]
    ],
]);
$this->title = 'Lottery';
?>
<script>
    $(function() {
        $(".giftImg").bigShow();
        $('#addGift').click(function() {
            location.href = '/member/gift-list/addgift';
        });
        
    });
    function editGift(id){
        location.href = '/member/gift-list/editgift?gift_id=' + id;
    }
    function delGift(id){
        msgConfirm ('提醒','确定要删除此礼品吗？',function(){
            $.ajax({
                url: "/member/gift-list/delgift",
                type: "POST",
                async: false,
                data: {gift_id: id},
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
        })
    }
    //重置
    $("#resetBtn").click(function(){
         location.href = '/member/gift-list/index';
    })
    //修改礼品状态
    function editSta(id,status){
        var str="";
        if(status==1){
            str="您确定要上线该礼品吗?";
        }else{
            str="您确定要下线该礼品吗?";
        }
        msgConfirm ('提醒',str,function(){
            $.ajax({
                url: "/member/gift-list/edit-status",
                type: "POST",
                async: false,
                data: {gift_id: id,status:status},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != 600) {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.reload();
                        });
                    }
                }
            });   
        })
    }
    //查看详情
    function readGift(id){
        modDisplay({title: '礼品详情', url:"/member/gift-list/read-gift?gift_id=" + id, height: 600, width: 600});
    }
</script>