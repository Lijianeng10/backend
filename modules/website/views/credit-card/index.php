<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\modules\common\helpers\PublicHelpers;

?>
<div style="font-size:14px;">
    <form action="/website/credit-card/index">
        <ul class="third_team_ul">
            <?php
            echo '<li>';
            echo Html::label("信用卡名称", "", ["style" => "margin-left:32px;"]);
            echo Html::input("input", "cardName", isset($get["cardName"]) ? $get["cardName"] : "", [ "class" => "form-control", "placeholder" => "标题名称", "style" => "width:200px;display:inline;margin-left:10px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("状态  ", "", ["style" => "margin-left:15px;"]);
            echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $status, ["class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
            echo Html::button("新增", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addCard();"]);
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
            'label' => '信用卡名称',
            'value' => 'card_name'
        ],[
            'label' => '取现额度',
            'value' => 'cash_quota'
        ],[
            'label' => '免息期',
            'value' => 'free_periods'
        ], [
            'label' => '缩略图',
            'format' => 'raw',
            'value' => function($model) {
                if (!empty($model['pic_url'])) {
                    return Html::img($model['pic_url'], ['width' => '40px', 'height' => '40px', "class" => "loanImg"]);
                } else {
                    return "";
                }
            }
         ], [
            'label' => '链接地址',
            'value' => 'jump_url'
        ],  [
            'label' => '序号',
            'value' => 'sort'
        ], [
            'label' => '状态',
            'value' => function($model) {
                $str = $model['status'] == 1 ? '启用' : '禁用';
                return $str;
            }
        ], [
            'label' => '创建时间',
            'value' => 'create_time'
        ], [
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                return '<div class="am-btn-toolbar">
                    <div class="am-btn-group am-btn-group-xs">' .
                        ($model['status'] == 1 ? '<span class="handle pointer" onclick="editSta(' . $model['credit_card_id'] . ',0);"> 禁用 |</span>' : '<span class="handle pointer" onclick="editSta(' . $model['credit_card_id'] . ',1);"> 启用 |</span>' )
                       . '<span class="handle pointer" onclick="editCard(' . $model['credit_card_id'] . ');"> 编辑</span></div></div>';
            }
        ]
        ],
    ]);
        $this->title = 'Lottery';
        ?>
<script>
    
    //新增
    function addCard() {
//        location.href ='/website/loan/add-loan'
        modDisplay({title: '新增贷款', url: '/website/credit-card/add', height: 600, width: 800});
    }
    
    function editCard(id) {
        modDisplay({title: '编辑贷款', url: '/website/credit-card/edit?id=' + id, height: 600, width: 800});
    }
    //重置
    function goReset() {
        location.href = '/website/found/index';
    }
    
    //修改广告状态
    function editSta(id, status) {
        var str = "";
        if (status == 1) {
            str = "您确定要启用该信用卡吗?";
        } else {
            str = "您确定要禁用该信用卡吗?";
        }
        msgConfirm('提醒', str, function () {
            $.ajax({
                url: "/website/credit-card/edit-status",
                type: "POST",
                async: false,
                data: {cardId: id, status: status},
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

