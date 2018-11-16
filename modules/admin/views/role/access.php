<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ Confirm;
use yii\db\Query;
?>
<style>
    p{
        padding: 0;
        margin: 0;
    }
    .action{
        width: 18%;
        text-align: left;
        padding-left: 40px;
        display: inline-block;
    }
    .bigModule{
        background:#ccc;
    }
    .modules{
        background:#E9ECF3;
    }
    .main{
        border: 1px solid #ccc;
        margin-top: 15px;
    }
    .select{
        width:17px;
        height:17px;
    }
</style>
<ol class="am-breadcrumb">
  <li><a href="/admin/role">角色管理</a></li>
  <li class="am-active">权限配置</li>
</ol>

<?php
echo '<form id="upauth" style="width:100%;">';
echo Html::input('hidden', 'role_id',$_GET['role_id'], ['id'=>"roleId"]);
?>

<div>
    <p>
        <button id="clearIp" type="button" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-star"></span> 清空</button>
        <button id="ipSelected" type="button" class="am-btn am-btn-default am-btn-secondary"><span class="am-icon-star"></span> 全选/反选</button>
    </p>
    <?php foreach ($provider as $val) : ?>
        <ul class="main">
            <p class="bigModule">
                <input type="checkbox" name="authIds" class="select" id="authid_<?php echo $val['auth_id'] ;?>" value=<?php echo $val['auth_id'] ; ?>  <?php if($val['ischecked'] == 1) :?> checked="checked" <?php endif;?> /><?php echo $val['auth_name'];?></p>
            <?php if(!empty($val['child'])) :?>
                <?php foreach ($val['child'] as $item) :?>
                    <div class="modules">
                        &nbsp;&nbsp;&nbsp;<input type="checkbox" name="pid_<?php echo $item['auth_pid'] ; ?>" data-id="authid_<?php echo $item['auth_id']; ?>" class="select" value=<?php echo $item['auth_id']?>  <?php if($item['ischecked'] == 1) :?> checked="checked" <?php endif;?>><?php echo $item['auth_name'];?>
                    </div>
                    <?php if(!empty($item['child'])) :?>
                        <?php foreach ($item['child'] as $item3) :?>
                            <div class="controller">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="pid_<?php echo $item['auth_pid']; ?>" data-id="authid_<?php echo $item3['auth_pid']; ?>" class="select" value=<?php echo $item3['auth_id']?>  <?php if($item3['ischecked'] == 1) :?> checked="checked" <?php endif;?>><?php echo $item3['auth_name'];?>
                            </div>
                            <?php if(!empty($item3['child'])) :?>
                                    <?php foreach ($item3['child'] as $item4) :?>
                                        <div class="action">
                                           <input type="checkbox" name="pid_<?php echo $item['auth_pid']; ?>" ff_id="ffid_<?php echo $item3['auth_pid']; ?>" data-id="authid_<?php echo $item4['auth_pid']; ?>" class="select" value=<?php echo $item4['auth_id'];?>   <?php if($item4['ischecked'] == 1) :?> checked="checked" <?php endif;?>> <?php echo $item4['auth_name'];?>
                                        </div>
                                    <?php endforeach;?>
                            <?php endif;?>
                        <?php endforeach;?>
                    <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
        </ul>
        
    <?php endforeach;?>
</div>

<?php
echo '<p style="clear:both;">';
echo '<button type="button" class="am-btn am-btn-primary" id="refer" style="margin-left:40%;margin-top:20px;">提交</button>';
echo ' <button type="button" class="am-btn am-btn-primary" id="reback" style="margin-top:20px;">返回</button>';
echo '</p>';
echo '</form>';
$this->title = 'Access';
?>


<script type="text/javascript">
    $(function () {
         //清空
        $("#clearIp").click(function () {
            $.each($(".select"), function () {
                if ($(this).is(':checked')) {
                    $(this).prop("checked", false);
                }
            });
        });
        //权限反选
        $("#ipSelected").click(function () {
            $.each($(".select"), function () {
                if ($(this).is(':checked')) {
                    $(this).prop("checked", false);
                } else {
                    $(this).prop("checked", true);
                }
            });
        });
        
        $(".select").click(function(){
            var name = $(this).attr("name");
            if(name == "authIds"){
                var idname = $(this).attr("id");
                var id = idname . substr(7,idname.lenght);
                if(!$(this).is(':checked')) {
                    $("input[name = 'pid_" + id + "']").prop("checked", false);
                }else if($(this).is(':checked')){
//                    $("input[name = 'pid_" + id + "']").prop("checked", true);
                }
            }else {
                var idname = $(this).attr("name");
                var ppid = $(this).attr("data-id");
                var ffid = $(this).attr("ff_id");
                var val = $(this).val();
                if(ppid){  
                    var id = ppid . substr(7,ppid.lenght);
                }
                if(ffid){  
                    var fid = ffid . substr(5,ffid.lenght);
                }
                var pid = idname .substr(4,idname.lenght);
                if($(this).is(' :checked')){
                    $("input[value = '" + id + "']").prop("checked", true);
                    $("input[id = 'authid_" + pid + "']").prop("checked", true);
                    $("input[value = '" + fid + "']").prop("checked", true);
//                    $("input[data-id = 'authid_" + val + "']").prop("checked", true);
                }else if(!$(this).is(':checked')){
                    $("input[data-id = 'authid_" + val + "']").prop("checked", false);
                    
                }
            }
        })
        
        $('#refer').click(function () {
            var authIds = [];
            $.each($(".select"), function () {
                if ($(this).is(':checked')) {
                    authIds.push($(this).val());
                }
            });
            var roleId = $("#roleId").val();
            //console.log(authIds)
            $.ajax({
                url: '/admin/role/upauth',
                async: false,
                type: 'POST',
                data: {
                    role_id: roleId,
                    authIds: JSON.stringify(authIds)
                },
                dataType: 'json',
                success: function (data) {
                    if (1 != data['code']) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.href = '/admin/role/index';
                        });
                    }
                }
            });
        });
        
        $("#reback").click(function () {
            location.href = '/admin/role/index';
        });
    });
</script>

                               