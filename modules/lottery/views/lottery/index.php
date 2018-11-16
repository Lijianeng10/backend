<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\common\helpers\Constants;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <?php
    echo Html::label("彩种信息", "order_code", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "lottery", (isset($_GET["lottery"]) ? $_GET["lottery"] : ""), ["id" => "lottery", "class" => "form-control", "placeholder" => "输入彩种编号或彩种名称", "style" => "width:200px;display:inline;margin-left:5px;"]);

    echo Html::label("所属分类  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("category_type", (isset($_GET["category_type"]) ? $_GET["category_type"] : "0"), $category, ["id" => "category_type", "class" => "form-control", "style" => "width:140px;display:inline;margin-left:5px;"]);

    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "search();"]);
    echo Html::button("重置", ["class" => "am-btn am-btn-primary inputLimit",  "onclick" => "reset();"]);
    echo Html::button("新增类别", ["class" => "am-btn am-btn-primary inputLimit", "id" => "add"]);
    echo Html::button("新增彩种", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addLottery"]);
    ?>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $result,
    'columns' => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '彩种图片',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::img($model['lottery_pic'], ['width' => '40px', 'height' => '40px']);
            }
                ], [
                    'label' => '编号',
                    'value' => 'lottery_code'
                ], [
                    'label' => '彩种名称 ',
                    'value' => 'lottery_name'
                ], [
                    'label' => '所属类别 ',
                    'value' => function ($model){
                        $type=Constants::LOTTERY_TYPE;
                        return (isset($type[$model["lottery_category_id"]])?$type[$model["lottery_category_id"]]:"");
                    }
                ], [
                    'label' => '备注',
                    'value' => 'description'
                ], [
                    'label' => '状态',
                    'value' => function ($model) {
                        return $model['status'] == 1 ? '启用' : '停用';
                    }
                ], [
                    'label' => '是否停售',
                    'value' => function ($model) {
                        return $model['sale_status'] == 1 ? '在售' : '停售';
                    }
                ], [
                    'label' => '结果显示',
                    'value' => function ($model) {
                        return $model['result_status'] == 1 ? '显示' : '停显';
                    }
                ], [
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $sta = $model['status'] == 1 ? '停用' : '启用';
                        $sale = $model['sale_status'] == 1 ? '停售' : '开售';
                        $isResult = $model['result_status'] == 1 ? '停显' : '显示';
                        return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <span class="handle pointer" onclick="editLottery(' . $model['lottery_id'] . ');">编辑</span>    
                                <span class="handle pointer" onclick="doSta(' . $model['lottery_id'] . ',' . $model['status'] . ');">| ' . $sta . '</span>
                                <span class="handle pointer" onclick="doSale(' . $model['lottery_id'] . ',' . $model['sale_status'] . ');">| ' . $sale . '</span>
                                <span class="handle pointer" onclick="doResult(' . $model['lottery_id'] . ',' . $model['result_status'] . ');">| ' . $isResult . '</span>
                                <a href="/lottery/lottery/readlottery?lottery_id=' . $model['lottery_id'] . '" class="handle pointer" style="display:none">| 查看</a>
                                <span class="handle pointer" onclick="delLottery(' . $model['lottery_id'] . ');">| 删除</span>
                            </div>
                        </div>';
                    }
                ]
            ],
        ]);
        $this->title = 'Lottery';
        ?>
<script>
    $(function () {
        $('#addLottery').click(function () {
            modDisplay({title: '新增彩种', url: '/lottery/lottery/addlottery', height: 500});
        });
    });
    //新增彩种类别
    $("#add").click(function(){
        modDisplay({title: '新增类别', url: '/lottery/lottery/add-category',width:400, height: 250});
    })
        
    
    function editLottery(id) {
        modDisplay({title: '新增彩种', url: '/lottery/lottery/edit?lottery_id=' + id, height: 550});
    }

    function delLottery(ids) {
        msgConfirm('提醒', '确定要删除此彩种吗？', function () {
            $.ajax({
                url: "/lottery/lottery/dellottery",
                type: "POST",
                async: false,
                data: {id: ids},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != "1") {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert("删除成功", function () {
                            location.reload();
                        });
                    }
                }
            });
        })
    }

    function doSta(id, sta) {
        msgConfirm('提醒', '确定要修改此彩种的状态吗？', function () {
            if (sta == 1) {
                sta = 0;
            } else {
                sta = 1;
            }
            $.ajax({
                url: "/lottery/lottery/editsta",
                type: "POST",
                async: false,
                data: {id: id, status: sta},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != "1") {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert("修改成功", function () {
                            location.reload();
                        });
                    }
                }
            });
        })
    }

    function doSale(id, sale) {
        msgConfirm('提醒', '确定要修改此彩种的出售状态吗？', function () {
            if (sale == 1) {
                sale = 0;
            } else {
                sale = 1;
            }
            $.ajax({
                url: "/lottery/lottery/editsale",
                type: "POST",
                async: false,
                data: {id: id, sale_status: sale},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != "1") {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert("修改成功", function () {
                            location.reload();
                        });
                    }
                }
            });
        })
    }
    
    function doResult(id, isResult) {
        msgConfirm('提醒', '确定要修改此彩种的开奖结果显示状态吗？', function () {
            if (isResult == 1) {
                isResult = 0;
            } else {
                isResult = 1;
            }
            $.ajax({
                url: "/lottery/lottery/edit-result",
                type: "POST",
                async: false,
                data: {id: id, result_status: isResult},
                dataType: "json",
                success: function (data) {
                    if (data["code"] != "1") {
                        msgAlert(data["msg"]);
                    } else {
                        msgAlert("修改成功", function () {
                            location.reload();
                        });
                    }
                }
            });
        })
    }
    //搜索
    function search() {
        var lotteryInfo = $("#lottery").val();
        var category_type = $("#category_type").val();
        location.href = '/lottery/lottery/index?lotteryInfo=' + lotteryInfo+'&category_type=' + category_type;
    }
    //重置
    function reset(){
        location.href ="/lottery/lottery/index";
    }

</script>