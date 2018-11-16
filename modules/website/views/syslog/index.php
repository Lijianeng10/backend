<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
?>
<div style="font-size:14px;">
    <?php
    $type=isset($get["type"]) ? $get["type"] : "";
    echo "<ul class='third_team_ul'>";
    echo '<li >';
    echo Html::label("日志类型", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "type", $type, ["id" => "type", "class" => "form-control", "placeholder" => "日志类型", "style" => "width:200px;display:inline;margin-left:10px;"]);
    echo '</li>';
    echo '<li >';
    echo Html::label("日志内容(匹配)", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "content", isset($get["content"]) ? $get["content"] : "", ["id" => "content", "class" => "form-control", "placeholder" => "日志内容", "style" => "width:200px;display:inline;margin-left:10px;"]);
    echo '</li>';
    echo '<li >';
    	echo Html::label("记录的日志类型 ", "", ["style" => "margin-left:15px;"]);
    	echo Html::dropDownList("", "", $logType, ["id" => "stype", "class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;",'ReadOnly'=>true]);
    echo '</li>';
    echo '<li>';
    echo Html::label("时间  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "startdate", isset($get["startdate"]) ? $get["startdate"] : "", ["id" => "startdate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("input", "enddate", isset($get["enddate"]) ? $get["enddate"] : "", ["id" => "enddate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;", "onclick" => "search();"]);
    echo Html::button("重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
    echo '</li>';
    echo "</ul>";
    ?>
</div>
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
            'label' => '日志类型',
            'value' => 'type'
        ], [
            'label' => '内容',
            'value' => 'data'
        ],
                 [
                    'label' => '日志记录时间',
                    'value'=>function($model){
                    	return  date('Y-m-d H:i:s',$model['c_time']);   //主要通过此种方式实现
                	},
                ]
//                        [
//                    'label' => '操作',
//                    'format' => 'raw',
//                    'value' => function($model) {
//                        return '<div class="am-btn-group am-btn-group-xs">
//                            <span class="handle pointer" onclick="readAdmin(' . $model["admin_id"] . ');">查看用户</span>
//                            <span class="handle pointer" onclick="editAdmin(' . $model["admin_id"] . ');">| 编辑</span>
//                            <span class="handle pointer" onclick="statusAdmin(' . $model["admin_id"] . ');">| '.($model["status"]?"停用":"启用").'</span>
//                            <span class="handle pointer" onclick="deleteAdmin(' . $model["admin_id"] . ');">| 删除</span>
//                        </div>';
//                    }
//                ]
            ]
        ]);
                
        ?>
<script>
    function search(){
        var  type = $("#type").val();
        var  startdate = $("#startdate").val();
        var  enddate = $("#enddate").val();
        var  content = $("#content").val();
        location.href = '/website/syslog/index?type='+type+"&startdate="+startdate+"&enddate="+enddate+"&content="+content;
    }
    function goReset(){
        location.href = '/website/syslog/index';
    }
    $('#stype').change(function(){
    	$("#type").val($('#stype').find("option:selected").text());
        });
        
</script>