<?php

use yii\grid\GridView;
use yii\helpers\Html;

$items = ["请选择", "五大联赛", "其他"];
echo Html::label("球队信息");
echo Html::input("text", "team_name", (isset($_GET["team_name"]) ? $_GET["team_name"] : ""), ["class" => "form-control inputLimit", 'id' => "team_name", "placeholder" => "球队编号，球队简称"]);

//echo Html::label("所属联赛");
//echo Html::dropDownList("team_name", "", $items, ["class" => "form-control inputLimit"]);

echo Html::button("搜索", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "doSearch();"]);
echo Html::button("重置", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "reset();"]);

//echo Html::tag("br");
echo Html::button("新增", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "location.href='/lottery/team/addteam'"]);


echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '球队编码',
            'value' => 'team_code'
        ], [
            'label' => '球队简称',
            'value' => 'team_short_name'
        ], [
            'label' => '球队全称',
            'value' => 'team_long_name'
        ],  
//        [
//            'label' => '所属国家',
//            'value' => 'country_name'
//        ],
        [
            'label' => '备注',
            'value' => 'team_remarks'
        ], [
            'label' => '状态',
            'value' => function ($model){
                return $model['team_status'] == 1 ? '启用' : '停用';
            }
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function($model) {
                return '<div class="am-btn-group am-btn-group-xs">
                            <a href="/lottery/team/editteam?team_id=' . $model['team_id'] .  '" class="handle pointer" >编辑</a>
                            <span class="handle pointer" onclick="statusTeam(' . $model["team_id"] . ',' . $model["team_status"]  . ');">| ' . ($model["team_status"] ? "禁用" : "启用") . '</span>
                            <span class="handle pointer" onclick="readTeam(' . $model["team_id"] . ');" style="display:none">| 查看</span>
                            <span class="handle pointer" onclick="deleteTeam(' . $model["team_id"] . ');">| 删除</span>
                        </div>';
            }
        ]
    ]
]);
?>
<script type="text/javascript">
    function doSearch() {
        var team_name = $("#team_name").val();
        if(team_name == '' ){
            location.href = '/lottery/team/index';
        }else if(team_name != '') {
            location.href = '/lottery/team/index?team_name=' + team_name ;
        }
    }
    function reset(){
        $("#team_name").val('');
    }
    function editTeam(teamId){
        
    }
    function statusTeam(teamId,status){
        console.log(teamId,status)
        msgConfirm ('提醒','确定要修改此球队的状态吗？',function(){
            $.ajax({
                url: "/lottery/team/status-team",
                type: "POST",
                async: false,
                data: {
                    id: teamId,
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
    function readTeam(teamId){
        
    }
    function deleteTeam(teamId){
        msgConfirm ('提醒','确定要删除此球队吗？',function(){
            $.ajax({
                url: "/lottery/team/delete-team",
                type: "POST",
                async: false,
                data: {id: teamId},
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
