<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
?>
<div id="content-body">
    <table class="table">
        <tr>
            <th>标题</th>
            <th>内容</th>
            <th>操作</th>
        </tr>
        <tr>
            <td><?php echo $data["title"]; ?></td>
            <td><?php echo $data["content"]; ?></td>
            <td class="handle pointer" onclick="addTitle()">更新</td>
        </tr>
    </table> 
</div>
<script>
    //重置
    function goReset() {
        location.href = '/promote/record/index';
    }
    //新增兑换码
    function addTitle(){
        modDisplay({width: 500, height: 350, title: "更新标题", url: "/promote/title/add-title"});
    }
</script>
