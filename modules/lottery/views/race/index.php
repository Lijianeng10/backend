<?php

use yii\grid\GridView;
use yii\helpers\Html;

$items = ["请选择", "五大联赛", "其他"];
echo Html::label("联赛");
echo Html::input("text", "race_name", (isset($_GET["race_limit"]) ? $_GET["race_limit"] : ""), ["class" => "form-control inputLimit", "id" => 'race_limit', "placeholder" => "赛事编号，赛事简称"]);

echo Html::label("所属分类");
echo Html::dropDownList("category", (isset($_GET["category"]) ? $_GET["category"] : 0), $items, ["class" => "form-control inputLimit", "id" => 'category_limit','style'=>'width:90px;']);

echo Html::button("搜索", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "doSearch();"]);
echo Html::button("重置", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "reset();"]);

//echo Html::tag("br");
echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "location.href='/lottery/race/addrace'"]);


echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '联赛ID',
            'value' => 'league_id'
        ], [
            'label' => '联赛编码',
            'value' => 'league_code'
        ], [
            'label' => '联赛简称',
            'value' => 'league_short_name'
        ], [
            'label' => '赛事全称',
            'value' => 'league_long_name'
        ], [
            'label' => '所属分类',
            'value' => function ($model){
                return $model['league_category_id'] == 1 ? '五大联赛' : '其他';
            }
        ], [
            'label' => '备注',
            'headerOptions' => ['style' => 'width:55%'],
            'value' => 'league_remarks'
        ], [
            'label' => '状态',
            'value' => function ($model){
                return $model['league_status'] == 1 ? '启用' : '停用';
            }
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <a href="/lottery/race/league-team?race_id=' . $model['league_id'] .  '" class="handle pointer" >球队添加</a>
                            <a href="/lottery/race/editrace?race_id=' . $model['league_id'] .  '" class="handle pointer" >| 编辑</a>
                            <span class="handle pointer" onclick="statusRace(' . $model["league_id"] . ',' . $model['league_status'] . ');">| ' . ($model["league_status"] ? "禁用" : "启用") . '</span>
                            <span class="handle pointer" onclick="readRace(' . $model["league_id"] . ');" style="display:none">| 查看</span>
                            <span class="handle pointer" onclick="deleteRace(' . $model["league_id"] . ');">| 删除</span>
                        </div>';
            }
        ]
    ]
]);
?>
<script type="text/javascript">
    function doSearch() {
        var category_limit = $("#category_limit").val();
        var race_limit = $("#race_limit").val();
        if(race_limit == '' && category_limit == 0){
            location.href = '/lottery/race/index';
        }else if(category_limit == 0 && race_limit != '') {
            location.href = '/lottery/race/index?race_limit=' + race_limit ;
        }else if(category_limit != 0 && race_limit == ''){
            location.href = '/lottery/race/index?category=' + category_limit ;
        }else {
            location.href = '/lottery/race/index?category=' + category_limit + '&race_limit=' + race_limit;
        }
    }
    function reset(){
        $('#race_limit').val('');
        $('#category_limit').val(0);
    }
    
    function statusRace(raceId,status){
        msgConfirm ('提醒','确定要修改此赛事的状态吗？',function(){
            $.ajax({
                url: "/lottery/race/edit-status",
                type: "POST",
                async: false,
                data: {
                    id: raceId,
                    status: status
                },
                dataType: "json",
                success: function (data) {
                    if (data["code"] != "1") {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert("操作成功");
                        location.reload();
                    }
                }
            });
        })
    }
    function readRace(raceId){
        
    }
    function deleteRace(raceId){
        msgConfirm ('提醒','确定要删除此赛事吗？',function(){
            $.ajax({
                url: "/lottery/race/delete-race",
                type: "POST",
                async: false,
                data: {id: raceId},
                dataType: "json",
                success: function (data) {
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
</script>
