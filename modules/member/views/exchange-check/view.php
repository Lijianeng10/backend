
<div>
    <legend>订单基础信息</legend>
    <div>
        <label  for="doc-vld-name-2-1">兑换单号：</label>
        <label class="doc-read"><?php echo $data['exch_code']; ?></label>
    </div>

    <div>
        <label for="doc-vld-name-2-1">会员编号：</label>
        <label class="doc-read"><?php echo $data['cust_no']; ?></label>
        
    </div>
    <div>
        <label for="doc-vld-name-2-1">会员名字：</label>
        <label class="doc-read"><?php echo $data['user_name']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1">兑换数量：</label>
        <label class="doc-read"><?php echo $data['exch_nums']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1">兑换类型：</label>
        <label class="doc-read"><?php echo $exType[$data['exch_type']] ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1">申请时间：</label>
        <label class="doc-read"><?php echo $data['create_time']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1">所属上级代理商：</label>
        <label class="doc-read"><?php echo $data['agent_name']; ?></label>
    </div>
    <legend></legend>
    <legend>订单明细</legend>
    <div>
        <label for="doc-vld-name-2-1">联系电话：</label>
        <label class="doc-read"><?php echo $data['user_tel'];?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1">配送地址：</label>
        <label class="doc-read"><?php echo $data['province'] . $data['city'] . $data['area'] . $data['address']; ?></label>
    </div>
    <div>
        <p style="font-size: 14px;padding-left: 8px;">兑换详情：<p>
        <table border="1" style="font-size: 14px;margin-left: 8px;">
                    <thead >
                        <tr text-align="center">
                            <th style="text-align: center">序号</th>
                            <th style="text-align: center">名称</th>
                            <th style="text-align: center">兑换数量</th>
                            <th style="text-align: center">咕币</th>
                            <th style="text-align: center">咕币总计</th>
                            <th style="text-align: center">礼品编号</th>
                        </tr>
                    </thead>
                    <tbody id="gift">
                        <?php foreach ($detail as $key=>$val): ?>
                            <tr>
                                <td style="text-align: center"><?php echo $key + 1;?></td>
                                <td style="text-align: center"><?php echo $val['gift_name']; ?></td>
                                <td style="text-align: center"><?php echo $val['gift_nums']; ?></td>
                                <td style="text-align: center"><?php echo $val['exch_int']; ?></td>
                                <td style="text-align: center"><?php echo $val['all_int']; ?></td>
                                <td style="text-align: center"><?php echo $val['send_gift']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </p>
    </div>
    <legend></legend>
    
    <button class="am-btn am-btn-secondary" id="backSubmit" >返回</button>
</div>


<script src="/js/jquery.cxselect.min.js"></script>
<script>
    $(function () {
        $("#backSubmit").click(function () {
            location.href = '/member/exchange-check/index';
        })
    })
</script>

