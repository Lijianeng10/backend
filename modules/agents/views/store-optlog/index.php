<style>
.opts{margin:0px auto;width:150px;height:30px;}    
.opts ul{margin:0px;padding:0px;}
.ul1 li{
     width:33px;
}
.ul2{
    z-index: 99;
}
.ul2 li{
    width: 80px;
    text-align: center;
}
.opts ul li{
    position:relative;
    float:left;  
    line-height:30px;
} 
.opts ul li ul{
    display:none;
} 
/*.menu ul li ul li{
    margin-top:1px
}*/
/*.menu ul li:hover{background:red;}*/
.opts ul li span:hover{
    color:#fff;
}
.opts ul li:hover ul{
    background:#ccc;
    display:block;
    position: absolute; 
    left: -45px; 
    top: 30px;
}
</style>
<?php

use yii\helpers\Html;
use yii\grid\GridView;

echo '<form action="/agents/store-optlog/index" method="get">';
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("门店信息", "", ["style" => "margin-left:30px;"]);
echo Html::input("input", "store_info", isset($_GET["store_info"]) ? $_GET["store_info"] : "", ["class" => "form-control", "placeholder" => "门店名称/编号、店主编号/名", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("查询日期  ", "", ["style" => "margin-left:30px;"]);
echo Html::input("text", "startdate", isset($_GET["startdate"]) ? $_GET["startdate"] : date('Y-m-d'), ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "enddate", isset($_GET["enddate"]) ? $_GET["enddate"] : date('Y-m-d'), ["class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::submitButton("搜索 ", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:21px;"]);
echo Html::tag("span", "重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "location.href = '/agents/store-optlog/index'"]);
echo '</li>';
echo "</ul>";
echo '</form>';
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '门店编号',
            'value' => 'store_code'
        ], [
            'label' => '门店名称',
            'value' => 'store_name'
        ], [
            'label' => '操作',
            'value' => 'content'
        ], [
            'label' => '操作时间',
            'value' => 'create_time'
        ], [
            'label' => '操作人',
            'value' => 'opt_name'
        ], 
    ]
]);
 ?>
<script src="/js/jquery.cxselect.min.js"></script>
<script>
    $('#city_china').cxSelect({
        url: '/js/cityData.min.json',
        selects: ['province'],
        emptyStyle: 'none'
    });
</script>

