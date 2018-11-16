<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ Confirm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */
?>

<ol class="am-breadcrumb">
  <li><a href="/admin/role">角色管理</a></li>
  <li class="am-active">用户查看</li>
</ol>

<?php

echo GridView::widget([
    'dataProvider' => $provider,
    'columns' => [
        [
            'label' => '管理员名',
            'value' => 'admin_name'
        ],[
            'label' => '状态',
            'value' => function ($model) {
                return $model['status'] == 1 ? '启用' : '停用';
            }
        ],[
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                return '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs">
                                <button class="am-btn am-btn-default am-btn-xs am-text-danger am-hide-sm-only del" onclick="delAdminrole(' . $model['admin_id'] . ',' . $model['role_id'] . ');">删除</button>
                            </div>
                        </div>';
            }
        ]
    ],
]);
$this->title = 'Adminrole';
?>
<button class="am-btn am-btn-primary"  id="reback">返回</button>
<script>
    function delAdminrole(adminId,roleId) {
        msgConfirm ('提醒','确定要删除此角色吗？',function(){
            $.ajax({
                url: "/admin/role/deladminrolebyid",
                type: "POST",
                async: false,
                data: {type: "admin_role_delete_by_id", adminId: adminId, roleId:roleId},
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
        });
        
    }
    
    $(function(){
        $("#reback").click(function () {
             history.go(-1);
        });
    })
</script>