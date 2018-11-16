<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\modules\common\helpers\PublicHelpers;
?>
<div style="font-size:14px;">
    <form action="/expert/source/get-source-list">
        <ul class="third_team_ul">
            <?php
            echo '<li>';
            echo Html::label("来源信息", "", ["style" => "margin-left:32px;"]);
            echo Html::input("input", "sourceName", isset($get["sourceName"]) ? $get["sourceName"] : "", [ "class" => "form-control", "placeholder" => "来源信息", "style" => "width:200px;display:inline;margin-left:10px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
            echo Html::button("新增", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addSource();"]);
            echo Html::button("重置", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
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
            'label' => '来源名称',
            'value' => 'source_name'
        ], [
            'label' => '会员编号',
            'value' => 'cust_no',
         ],  [
            'label' => '会员电话',
            'value' => 'user_tel',
         ],  [
            'label' => '状态',
            'value' => function($model) {
                return $model['status'] == 0 ? '禁用' : '启用';
            }
        ], [
            'label' => '创建时间',
            'value' => 'create_time'
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
               $str = $model['status'] == 0 ? '启用' : '禁用';
               $status = $model['status'] == 0 ? 1 : 0;
                return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <span class="handle pointer" onclick="editSource(' . $model['source_id'] . ');">编辑</span>' .
                                (empty($model['user_id']) ? '<span class="handle pointer" onclick="editSta('. $model['source_id'] . ',' . $status .');"> | ' . $str . ' </span>' : "") .
                            '</div>
                        </div>';
            }
        ]
        ],
    ]);
        ?>
<script>
    //新增
    function addSource() {
        modDisplay({title: '新增来源', url: '/expert/source/add-base-source', height:180, width: 400});
    }
    //重置
    function goReset() {
        location.href = '/expert/source/get-source-list';
    }
    //编辑
    function editSource(id){
        modDisplay({title: '编辑来源', url: '/expert/source/edit-base-source?sourceId=' + id, height:200, width: 400});
    }
    
    function editSta(id, status) {
        var str = status == 0 ? '禁用' : '启用'; 
        msgConfirm('提醒', '确定要'+ str +'此会员吗？', function () {
            $.ajax({
                url: "/expert/source/edit-base-sta",
                type: "POST",
                async: false,
                data: {sourceId: id, status:status} ,
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
</script>

