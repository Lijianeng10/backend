<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\modules\common\helpers\PublicHelpers;

?>
<div style="font-size:14px;">
    <form action="/website/loan/index">
        <ul class="third_team_ul">
            <?php
            echo '<li>';
            echo Html::label("标题名称", "", ["style" => "margin-left:32px;"]);
            echo Html::input("input", "title", isset($get["title"]) ? $get["title"] : "", [ "class" => "form-control", "placeholder" => "标题名称", "style" => "width:200px;display:inline;margin-left:10px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::label("状态  ", "", ["style" => "margin-left:15px;"]);
            echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $status, ["class" => "form-control", "style" => "width:150px;display:inline;margin-left:5px;"]);
            echo '</li>';
            echo '<li>';
            echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:37px;"]);
            echo Html::button("新增", ["class" => "am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "addLoan();"]);
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
            'label' => '贷款标题',
            'value' => 'title'
        ],[
            'label' => '贷款副标题',
            'value' => 'sub_title'
        ],[
            'label' => '额度范围',
            'value' => 'quota'
        ], [
            'label' => '利率说明',
            'value' => 'profit_remark'
        ],[
            'label' => '贷款期限',
            'value' => 'loan_periods'
        ],[
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
                        ($model['status'] == 1 ? '<span class="handle pointer" onclick="editSta(' . $model['loan_id'] . ',0);"> 禁用 |</span>' : '<span class="handle pointer" onclick="editSta(' . $model['loan_id'] . ',1);"> 启用 |</span>' )
                       . '<span class="handle pointer" onclick="editLoan(' . $model['loan_id'] . ');"> 编辑</span></div></div>';
            }
        ]
        ],
    ]);
        $this->title = 'Lottery';
        ?>
<script>
    
    //新增
    function addLoan() {
//        location.href ='/website/loan/add-loan'
        modDisplay({title: '新增贷款', url: '/website/loan/add-loan', height: 600, width: 800});
    }
    
    function editLoan(id) {
        modDisplay({title: '编辑贷款', url: '/website/loan/edit-loan?id=' + id, height: 600, width: 800});
    }
    //重置
    function goReset() {
        location.href = '/website/found/index';
    }
    
    //修改广告状态
    function editSta(id, status) {
        var str = "";
        if (status == 1) {
            str = "您确定要启用该条贷款吗?";
        } else {
            str = "您确定要禁用该条贷款吗?";
        }
        msgConfirm('提醒', str, function () {
            $.ajax({
                url: "/website/loan/edit-status",
                type: "POST",
                async: false,
                data: {loanId: id, status: status},
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
    function editBananer(id){
        location.href ='/website/found/edit-bananer?bananer_id='+id
//        modDisplay({title: '编辑广告', url: '/website/bananer/edit-bananer?bananer_id='+id, height: 780, width: 800});
    }
</script>

