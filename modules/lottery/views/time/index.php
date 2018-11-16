<?php

use yii\grid\GridView;
use yii\helpers\Html;

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
            'label' => '开奖频率',
            'value' => 'rate'
        ],[
            'label' => '星期',
            'format' => 'raw',
            'value' => function ($model) {
               $str = '';
               foreach ($model['open_time'] as $val){
                   $trStr = '<div>' . $val['week'] . '</div>';
                   $str .= $trStr;
               }        
               return $str;
            }
        ],[
            'label' => '场次',
            'value' => 'changci'
        ],[
            'label' => '开奖开始时间',
            'value' => 'start_time'
        ],[
            'label' => '开奖结束时间',
            'value' => 'stop_time'
        ],[
            'label' => '投注截止',
            'value' => 'limit_time'
        ],[
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <a href="/lottery/time/setting?lotterycode='.$model["lottery_code"].'" class="handle pointer">设置</a>
                            </div>
                        </div>';
            }
        ]
    ],
]);
$this->title = 'LotteryTime';
?>

<script>
    function search() {
        var search_category_id = $("#category_code").val();
        var code = $("#lottery_code").val();
        
        if(search_category_id == 0 && code == ''){
            location.href = '/lottery/time/index';
        }else if(search_category_id == 0 && code != '') {
            location.href = '/lottery/time/index?code=' + code ;
        }else if(search_category_id != 0 && code == ''){
            location.href = '/lottery/time/index?category=' + search_category_id ;
        }else {
            location.href = '/lottery/time/index?category=' + search_category_id + '&code=' + code;
        }
    }
</script>