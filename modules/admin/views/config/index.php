<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\modules\common\helpers\Constants;
?>
<div style="font-size:14px;">
    <form action="/admin/config/index">
        <ul class="third_team_ul">
            <?php
            echo '<li>';
            echo Html::label("参数名称", "", ["style" => "margin-left:32px;"]);
            echo Html::input("input", "param", isset($get["param"]) ? $get["param"] : "", [ "class" => "form-control", "placeholder" => "参数名称", "style" => "width:200px;display:inline;margin-left:10px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("所属类别", "", ["style" => "margin-left:15px;"]);
            echo Html::dropDownList("type", isset($get["type"]) ? $get["type"] : "", $configType, ["class" => "form-control", "style" => "width:100px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("状态  ", "", ["style" => "margin-left:15px;"]);
            echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $configStatus, ["class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
            echo Html::button("新增", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addParam();"]);
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
            'label' => '系统配置编号',
            'value' => 'code'
        ],
       [
            'label' => '参数名称',
            'value' => 'name'
        ], [
            'label' => '参数值',
            'value' => 'value'
        ],[
            'label' => '所属类别',
            'value' => function($model){
                $confType = Constants::SYS_CONF_TYPE;
                return $confType[$model["type"]]??"";
            }
        ], [
            'label' => '使用状态',
            'value' => function($model) {
                $bankStatus = Constants::CONFIG_STATUS;
                return $bankStatus[$model["status"]]??"";
            }
        ], [
            'label' => '备注',
            'value' => 'remark'
        ],[
                'label' => '操作',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<div class="am-btn-toolbar">
                        <div class="am-btn-group am-btn-group-xs"><span class="handle pointer" onclick="edit(' . $model['id'] . ');">编辑 |</span><span class="handle pointer" onclick="del(' . $model['id'] . ');"> 删除 |</span>'.($model['status'] == 0 ? '<span class="handle pointer" onclick="editSta(' . $model['id'] . ',1);"> 启用</span>' :"" ) .($model['status'] == 1 ? '<span class="handle pointer" onclick="editSta(' . $model['id'] . ',0);"> 禁用</span>' :"" ) .'</div></div>';
                }
            ]
            ],
        ]);
        ?>
<script>
    //新增
    function addParam() {
       modDisplay({title: '新增参数', url: '/admin/config/add-param', height:400, width: 500});
    }
    //重置
    function goReset() {
        location.href = '/admin/config/index';
    }
    //删除参数
    function del(id) {
        msgConfirm('提醒', "您确定要删除该参数吗", function () {
            $.ajax({
                url: "/admin/config/del-param",
                type: "POST",
                async: false,
                data: {id: id},
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
    //编辑
    function edit(id){
       modDisplay({title: '编辑参数信息', url: '/admin/config/edit-param?id='+id, height: 400, width: 500});
    }
    //修改参数状态
    function editSta(id, status) {
        var str = "";
        if (status == 1) {
            str = "您确定要启用该参数吗?";
        } else {
            str = "您确定要禁用该参数吗?";
        }
        msgConfirm('提醒', str, function () {
            $.ajax({
                url: "/admin/config/edit-status",
                type: "POST",
                async: false,
                data: {id: id, status: status},
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

