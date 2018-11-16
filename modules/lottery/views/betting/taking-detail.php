<span>出票张数：<?php echo count($takingDetail['data']);?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span>投注场次数：<?php echo $takingDetail['count_sche']?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span>投注玩法：<?php echo $takingDetail['play_str']?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span>投注金额：<?php echo $takingDetail['order_money']?></span>
<table class='table table-striped table-bordered modalTable'>
    <tr style="text-align:center">
        <th>票号</th>
        <th>选项</th>
        <th>玩法</th>
        <th>倍数</th>
        <th>金额</th>
    </tr>
    <?php foreach ($takingDetail['data'] as $key => $val):?>
    <tr style="text-align:center">
        <td><?php echo $key + 1;?></td>
        <td style="text-align:left">
            <?php foreach ($val['bet_val'] as $b):?>
                <?php echo $b . '<br>'?>
            <?php endforeach;?>
        </td>
        <td>
            <?php foreach ($val['play_name'] as $p):?>
                <?php echo $p . '<br>'?>
            <?php endforeach;?>
        </td>
        <td><?php echo $val['bet_double'];?></td>
        <td><?php echo $val['bet_money']?></td>
    </tr>    
    <?php endforeach; ?>
</table>

