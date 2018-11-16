<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>

<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <?php
    echo Html::label("彩种信息", "order_code", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "code", (isset($_GET["code"]) ? $_GET["code"] : ""), ["id" => "lottery_code", "class" => "form-control", "placeholder" => "输入彩种编号或彩种名称", "style" => "width:200px;display:inline;margin-left:5px;"]);

    echo Html::label("所属分类  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("lottery_code", (isset($_GET["category"]) ? $_GET["category"] : "0"), $category, ["id" => "category_code", "class" => "form-control", "style" => "width:110px;display:inline;margin-left:5px;"]);

    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "search();"]);
    echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "location.href='/lottery/awards/addawards'"]);
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
            'value' => 'lottery_category'
        ],[
            'label' => '奖级',
            'format' => 'raw',
            'value' => 'levels_code'
//            'value' => function ($model) {
//                $str = '';
//                foreach ($model['awards_code'] as  $val){
//                   $trStr = '<div>' . $val['l_code'] . '</div>';
//                   $str .= $trStr;
//               }       
//               
//               return $str;
//            }
        ],[
            'label' => '奖级名称',
            'format' => 'raw',
            'value' => 'levels_name'
//            'value' => function ($model) {
//                $str = '';
//                foreach ($model['awards_code'] as $key => $val){
//                   $trStr = '<div>' . $val['l_name'] . '</div>';
//                   $str .= $trStr;
//               }        
//               return $str;
//            }
        ],[
            'label' => '中奖条件',
            'format' => 'raw',
            'value' => function ($model) {
                $str = '中&nbsp' . $model['levels_red'] . '&nbsp红 + &nbsp' . $model['levels_blue'] .'&nbsp蓝';    
                return $str;
            }
        ],[
            'label' => '中奖说明',
            'format' => 'raw',
            'value' => 'levels_remark'
//            'value' => function ($model) {
//                $str = '';
//                foreach ($model['awards_code'] as $val){
//                    if($val['l_remark'] != null){
//                        $trStr = '<div>' . $val['l_remark'] . '</div>';
//                    } else {
//                         $trStr = '<div>' . '&nbsp' . '</div>';
//                    }
//                   $str .= $trStr;
//               }        
//               return $str;
//            }
        ],[
            'label' => '奖金(元/单注)',
            'format' => 'raw',
            'value' => 'levels_bonus'
//            'value' => function ($model) {
//                $str = '';
//                foreach ($model['awards_code'] as $val){
//                    if($val['levels_bonus'] != null){
//                        $trStr = '<div>' . $val['levels_bonus'] . '</div>';
//                    } else {
//                        $trStr = '<div>' . '&nbsp' . '</div>';
//                    }
//                   
//                   $str .= $trStr;
//               }        
//               return $str;
//            }
        ],[
            'label' => '奖金说明 ',
            'format' => 'raw',
            'value' => 'levels_bonus_category'
//            'value' => function ($model) {
//                $str = '';
//                foreach ($model['awards_code'] as $val){
//                    if($val['bonus_category'] != null){
//                        $trStr = '<div>' . $val['bonus_category'] . '</div>';
//                    } else {
//                         $trStr = '<div>' . '&nbsp' . '</div>';
//                    }
//                   $str .= $trStr;
//                }        
//               return $str;
//            }
        ],[
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) { 
                $str = '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <a href="/lottery/awards/edit?awards_id=' . $model['levels_id'] .'" class="handle pointer">编辑</a>
                                <span class="handle pointer" onclick="delAward(' .$model['levels_id']  . ');">| 删除</span>
                            </div>
                        </div>';
//                foreach ($model['awards_code'] as $val){
//                   $trStr = '<div class="am-btn-toolbar">
//                            <div class="am-btn-group am-btn-group-xs">
//                                <a href="/lottery/awards/edit?lottery_code=' . $model['lottery_code'] . '&levels_code=' . $val['l_code'] .'" class="handle pointer">编辑</a>
//                                <span class="handle pointer" onclick="delAward(' .$model['lottery_code'] . ',' . $val['l_code'] . ');">| 删除</span>
//                            </div>
//                        </div>';
//                   $str .= $trStr;
//                }        
               return $str;
            }
        ]
    ],
]);
$this->title = 'LotteryAwards';
?>

<script>
    function delAward(awardId) {
        msgConfirm ('提醒','确定要删除此奖级吗？',function(){
            $.ajax({
                url: "/lottery/awards/delaward",
                type: "POST",
                async: false,
                data: {
                    award_id: awardId
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
        })
    }
   
    function search() {
        var search_category_id = $("#category_code").val();
        var code = $("#lottery_code").val();
        
        if(search_category_id == 0 && code == ''){
            location.href = '/lottery/awards/index';
        }else if(search_category_id == 0 && code != '') {
            location.href = '/lottery/awards/index?code=' + code ;
        }else if(search_category_id != 0 && code == ''){
            location.href = '/lottery/awards/index?category=' + search_category_id ;
        }else {
            location.href = '/lottery/awards/index?category=' + search_category_id + '&code=' + code;
        }
    }
</script>