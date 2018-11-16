<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\helpers\Constants;
?>
<div style="font-size:14px;">
    <form action="/website/access/index">
        <?php
        echo "<ul class='third_team_ul'>";
        if($_SESSION['type']==0){
            echo '<li>';
            echo Html::label("类型  ", "", ["style" => "margin-left:46px;"]);
            echo Html::dropDownList("type", isset($get["type"]) ? $get["type"] : "", $type, ["id" => "type", "class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
            echo '</li>';
        }
        echo '<li>';
        echo Html::label("访问日期  ", "", ["style" => "margin-left:15px;"]);
        echo Html::input("input", "startdate", isset($get["startdate"]) ? $get["startdate"] : "", ["id" => "startdate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo "-";
        echo Html::input("input", "enddate", isset($get["enddate"]) ? $get["enddate"] : "", ["id" => "enddate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
        echo Html::button("重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
        echo Html::button("导出", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "id" => "btnExport"]);
        echo '</li>';
        echo "</ul>";
        ?>
    </form>
</div>
<div id="pwTable">
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
//        [
//            'label' => '',
//            'format' => 'raw',
//            'value' => function($model) {
//                return Html::input('checkbox', 'delSelect', $model["admin_id"], ["class" => 'delSelect']);
//            }
//                ], 
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '访问日期',
            'value' => function($model){
                $str = explode(' ',$model['date_time']);
                return $str[0];
            }
        ],
        [
            'label' => '浏览量',
            'value' => 'pv'
        ], [
            'label' => '独立用户',
            'value' => 'uv'
        ],[
            'label' => '独立ip',
            'value' => 'iv',
        ],[
            'label' => '访问次数',
            'value' => 'vv'
        ],[
            'label' => '所属平台',
            'value' => function($model){
                $type = Constants::ACCESS_TYPE;
                return $type[$model['info_type']];
        }],
    ]
]);
                
        ?>
</div>
<script>
    function goReset(){
        location.href = '/website/access/index';
    }
    //导出表格
    $("#btnExport").click(function(){
        msgConfirm('提醒',"确定导出报表?",function(){
            $('#pwTable').tableExport({
                type:'excel',
                escape:'false',
                fileName: '访问统计报表'
            });
        })

    });
</script>