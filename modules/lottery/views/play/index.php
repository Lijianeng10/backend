<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>

<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <?php
    echo Html::label("彩种编号", "order_code", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "code", (isset($_GET["code"]) ? $_GET["code"] : ""), ["id" => "lottery_code", "class" => "form-control", "placeholder" => "彩种编号", "style" => "width:200px;display:inline;margin-left:5px;"]);

    echo Html::label("所属分类  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("lottery_code", (isset($_GET["category"]) ? $_GET["category"] : "0"), $category, ["id" => "category_code", "class" => "form-control", "style" => "width:110px;display:inline;margin-left:5px;"]);

    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "search();"]);
    
    echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "location.href='/lottery/play/addplay'"]);
    ?>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $result,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '彩种编号',
            'value' => 'lottery_code'
            
        ],[
            'label' => '彩种名称 ',
            'value' => 'lottery_name'
        ],[
            'label' => '所属分类',
            'value' => 'category_name'
        ],[
            'label' => '玩法编号',
            'format' => 'raw',
            'value' => 'lottery_play_code'
//            'value' => function ($model) {
//               $str = '';
//               foreach ($model['play_mark'] as $val){
//                   $trStr = '<div>' . $val['play_code'] . '</div>';
//                   $str .= $trStr;
//               }        
//               return $str;
//            }
        ],[
            'label' => '玩法名称',
            'format' => 'raw',
            'value' => 'lottery_play_name'
//            'value' => function ($model) {
//               $str = '';
//               foreach ($model['play_mark'] as $val){
//                   $trStr = '<div>' . $val['play_name'] . '</div>';
//                   $str .= $trStr;
//               }        
//               return $str;
//            }
        ],[
            'label' => '号码格式示例',
            'format' => 'raw',
            'value' => 'example'
//            'value' => function ($model) {
//               $str = '';
//               foreach ($model['play_mark'] as $val){
//                   $trStr = '<div>' . $val['example'] . '</div>';
//                   $str .= $trStr;
//               }        
//               return $str;
//            }
        ],[
            'label' => '号码个数',
            'format' => 'raw',
            'value' => 'number_count'
//            'value' => function ($model) {
//               $str = 'num_count';
//               foreach ($model['play_mark'] as $val){
//                   $trStr = '<div>' . $val['num_count'] . '</div>';
//                   $str .= $trStr;
//               }        
//               return $str;
//            }
        ],[
            'label' => '格式说明',
            'format' => 'raw',
            'value' => 'format_remark'
//            'value' => function ($model) {
//               $str = '';
//               foreach ($model['play_mark'] as $val){
//                   $trStr = '<div>' . $val['remark'] . '</div>';
//                   $str .= $trStr;
//               }        
//               return $str;
//            }
        ],[
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) { 
                $str = '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <a href="/lottery/play/edit?play_id=' . $model['lottery_play_id'] .'" class="handle pointer">编辑</a>
                               <span class="handle pointer" onclick="delPlay(' .$model['lottery_play_id'] . ');">| 删除</span>
                           </div>
                       </div>';
//                $str = '';
//                foreach ($model['play_mark'] as $val){
//                   $trStr = '<div class="am-btn-toolbar">
//                            <div class="am-btn-group am-btn-group-xs">
//                                <a href="/lottery/play/edit?lottery_code=' . $model['lottery_play_id'] . '&play_code=' . $val['play_code'] .'" class="handle pointer">编辑</a>
//                                <span class="handle pointer" onclick="delPlay(' .$model['lottery_code'] . ',' . $val['play_code']  . ');">| 删除</span>
//                            </div>
//                        </div>';
//                   $str .= $trStr;
//                }        
               return $str;
            }
        ]
    ],
]);
$this->title = 'LotteryPlay';
?>


<script>    
    function delPlay(playId) {
        msgConfirm ('提醒','确定要删除此玩法吗？',function(){
            $.ajax({
                url: "/lottery/play/delplay",
                type: "POST",
                async: false,
                data: {
                    play_id: playId
                },
                dataType: "json",
                success: function (data) {
                    console.log(1111);
                    if (data["code"] != "1") {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert("删除成功");
                        location.reload();
                    }
                }
            });
        });
    }
    
    function search() {
        var search_category_id = $("#category_code").val();
        var code = $("#lottery_code").val();
        if(search_category_id == 0 && code == ''){
            location.href = '/lottery/play/index';
        }else if(search_category_id == 0 && code != '') {
            location.href = '/lottery/play/index?code=' + code ;
        }else if(search_category_id != 0 && code == ''){
            location.href = '/lottery/play/index?category=' + search_category_id ;
        }else {
            location.href = '/lottery/play/index?category=' + search_category_id + '&code=' + code;
        }
    }
</script>