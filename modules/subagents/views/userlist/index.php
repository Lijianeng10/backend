<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>
<div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
    <form action="/subagents/userlist/index">
        <?php
        echo "<ul class='third_team_ul'>";
        echo '<li>';
        echo Html::label("会员信息", "user_info", ["style" => "margin-left:15px;"]);
        echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["class" => "form-control", "placeholder" => "会员编号、名称、手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("代理商信息", "agents_info");
        echo Html::input("input", "agents_info", isset($get["agents_info"]) ? $get["agents_info"] : "", ["class" => "form-control", "placeholder" => "上级代理商编号、名称", "style" => "width:200px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("开户日期", "", ["style" => "margin-left:15px;"]);
        echo Html::input("text", "startdate", isset($get["startdate"]) ? $get["startdate"] : "", ["id" => "startdate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
        echo "-";
        echo Html::input("text", "enddate", isset($get["enddate"]) ? $get["enddate"] : "", ["id" => "enddate", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "日期", "style" => "width:80px;display:inline;margin-left:5px;"]) . '</br>';
        echo '</li>';
        echo '<li>';
        echo Html::label("认证状态  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("authen_status", isset($get["authen_status"]) ? $get["authen_status"] : "", $authen, ["id" => "status", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
        echo '</li>';
        echo '<li>';
        echo Html::label("会员等级  ", "", ["style" => "margin-left:15px;"]);
        echo Html::dropDownList("user_level", isset($get["user_level"]) ? $get["user_level"] : "0", $levels, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
        echo '</li>';
//        echo '<li>';
//        echo Html::label("微信绑定  ", "", ["style" => "margin-left:15px;"]);
//        echo Html::dropDownList("vxstatus", isset($get["vxstatus"]) ? $get["vxstatus"] : "", $vxstatus, ["id" => "vxstatus", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
//        echo '</li>';
//        echo '<li>';
//        echo Html::label("会员咕币  ", "", ["style" => "margin-left:15px;"]);
//        echo Html::dropDownList("glcoin", isset($get["glcoin"]) ? $get["glcoin"] : ">=", $compar, ["id" => "status", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
//        ;
//        echo Html::input("input", "user_glcoin", isset($get["user_glcoin"]) ? $get["user_glcoin"] : "", ["class" => "form-control", "style" => "width:80px;display:inline;margin-left:5px;"]);
//        echo '</li>';
//        echo '<li>';
//        echo Html::label("会员余额  ", "", ["style" => "margin-left:15px;"]);
//        echo Html::dropDownList("balance", isset($get["balance"]) ? $get["balance"] : ">=", $compar, ["id" => "status", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
//        echo Html::input("input", "balance_val", isset($get["balance_val"]) ? $get["balance_val"] : "", ["class" => "form-control", "style" => "width:80px;display:inline;margin-left:5px;"]);
//        echo '</li>';
        echo '<li>';
        echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;"]);
        echo Html::input("reset", '', '重置', ["class" => "am-btn am-btn-primary", "id" => "reset", "style" => "margin-left:5px;"]);
        echo '</li>';
        echo "</ul>";
        echo '</form>';
        ?>
    </form>
</div>
<?php
echo GridView::widget([
    'dataProvider' => $data,
    'columns' => [
        [
            'label' => '',
            'format' => 'raw',
            'value' => function ($model) {
                return \yii\bootstrap\Html::input('checkbox', 'box', $model["user_id"], ["class" => 'delSel']);
            },
                    'headerOptions' => ['style' => 'min-width:1px']
                ],
                [ 'class' => 'yii\grid\SerialColumn'],
                [
                    'label' => '会员编号',
                    'value' => 'cust_no'
                ], [
                    'label' => '会员昵称',
                    'value' => 'user_name'
                ], [
                    'label' => '上级代理商编号',
                    'value' => 'agent_code'
                ], [
                    'label' => '上级代理商名称',
                    'value' => 'agent_name'
                ], [
                    'label' => '子代理商名称',
                    'value' => 'user_remark'
                ], [
                    'label' => '手机号',
                    'value' => 'user_tel'
                ], 
//                    [
//                    'label' => '总金额',
//                    'value' => 'all_funds',
//                ], [
//                    'label' => '可用余额 ',
//                    'value' => 'able_funds',
//                ], [
//                    'label' => '冻结余额',
//                    'value' => 'ice_funds',
//                ], [
//                    'label' => '不可提现金额',
//                    'value' => 'no_withdraw',
//                ], [
//                    'label' => '可提现金额',
//                    'value' => function($model) {
//                        return sprintf('%.2f', $model["able_funds"] - $model["no_withdraw"]);
//                    },
//                ],  [
//                    'label' => '咕币',
//                    'value' => 'user_glcoin',
//                ],[
//                    'label' => '礼品卡',
//                    'value' =>  function($model) {
//                        return '0';
//                    },
//                ], [
//                    'label' => '微信绑定',
//                    'value' => function($model) {
//                        return (isset($model["third_uid"])&&!empty($model["third_uid"])?"已绑定":"未绑定");
//                    },
//                ], 
                            [
                    'label' => '会员等级',
                    'value' => 'level_name',
                    'headerOptions' => ['style' => 'min-width:1px']
                ],
                [
                    'label' => '开户时间',
                    'value' => 'create_time'
                ], [
                    'label' => '认证状态',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model['authen_status'] == 1 ? '已通过' : ($model['authen_status'] == 2 ? '审核中' : ($model['authen_status'] == 3 ? '审核失败' : '未认证'));
                    }
                ], 
                [
                    'label' => '使用状态',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model['status'] == 1 ? '正常' : ($model['status'] == 2 ? '禁用' : '挂失');
                    },
                    'headerOptions' => ['style' => 'min-width:1px']
                ], [
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => function ($model) {
//                        '.
//                               (in_array($model['authen_status'], ['', 0, 2, 3])?'<span class="handle pointer" onclick="reviewMember(' . $model['user_id'] . ');">审核 |</span>':'').'
//                        ($model['status'] == 1 ? '<span class="handle pointer" onclick="editSta(' . $model['user_id'] . ');"> 禁用 |' : '<span class="handle pointer" onclick="editSta(' . $model['user_id'] . ');"> 启用 |' ) . '
//                                <span class="handle pointer" onclick="delMember(' . $model['user_id'] . ');"> 删除</span>
                        $str = '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                              <span class="handle pointer" onclick="viewMember(' . $model['user_id'] . ');">查看 </span>'.'
                               
                            </div>
                        </div>';
                        return $str;
                    }
                ]
            ],
        ]);
        $this->title = 'Lottery';
        ?>
<script>
    $(function () {
        $('#addMember').click(function () {
            location.href = '/member/list/add-member';
        });

        $('#reset').click(function () {
            location.href = '/member/list';
        });
    });
//    function editMember(id) {
//        location.href = '/member/list/edit-member?user_id=' + id;
//    }
    function viewMember(id) {
        location.href = '/subagents/userlist/view-member?user_id=' + id;
    }
    function reviewMember(id) {
        modDisplay({title: '审核', url: '/member/list/review-member?user_id=' + id, height: 280, width: 450});
    }
     //会员启用禁用
    function editSta(id){
        $.ajax({
            url: "/member/list/edit-status",
            type: "POST",
            async: false,
            data: {user_id: id},
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
    }
    function changeUserType(id) {
        $.ajax({
            url: "/member/list/change-user-type",
            type: "POST",
            async: false,
            data: {user_id: id},
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
    }
    function delMember(id) {
        msgConfirm('提醒', '确定要删除此会员吗？', function () {
            $.ajax({
                url: "/member/list/delete-member",
                type: "POST",
                async: false,
                data: {user_id: id},
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
