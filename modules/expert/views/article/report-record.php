<?php
use yii\grid\GridView;
use yii\helpers\Html;
echo '<li>';
echo Html::button("返回", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "back();"]);
echo '</li>';    
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '方案编号',
            'value' => 'articles_code'
        ],
        [
            'label' => '举报人',
            'value' => function($model){
                return $model["user_name"]."(".$model["user_tel"].")";
            }
        ], [
            'label' => '咕啦编号',
            'value' => 'cust_no'
        ],[
            'label' => '举报原因',
            "format"=>"raw",
            'value' => function($model){
                $reasons =[
                    "1"=>"广告",
                    "2"=>"重复、旧闻",
                    "3"=>"低俗",
                    "4"=>"与事实不符",
                    "5"=>"内容质量差",
                    "6"=>"抄袭",
                ];
                $html ="";
                $res = explode(",",$model["report_reasons"]);
                foreach ($res as $k=>$v ){
                    $html.=($k+1)."、".$reasons[$v];
                    $html.="<br/>";
                }
                return $html;
            }
        ],  [
            'label' => '举报时间',
            'value' => 'create_time'
        ],
    ]
]);
        ?>
<script>
    function back(){
        history.go(-1);
    }
</script>
