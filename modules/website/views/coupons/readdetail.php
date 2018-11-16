<style>
    .word{
        width: 100px;
        text-align: right;
    }
    #backSubmit{
        margin-left: 25px;
    }
</style>
<div>
    <div>
        <label  for="doc-vld-name-2-1" class="word">批次号：</label>
        <label class="doc-read"><?php echo $data['coupons_batch']; ?></label>
    </div>

    <div>
        <label for="doc-vld-name-2-1" class="word">优惠券编号：</label>
        <label class="doc-read"><?php echo $data['coupons_no']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">优惠券名称：</label>
        <label class="doc-read"><?php echo $data['coupons']['coupons_name']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">优惠券面额：</label>
        <label class="doc-read"><?php echo $data['coupons']['type']==1?$data['coupons']['reduce_money']."元":($data['coupons']['discount']/10)."折"; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">优惠券状态：</label>
        <label class="doc-read"><?php echo $data['status']==1?"激活":"锁定"; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">发放用户：</label>
        <label class="doc-read"><?php echo !empty($data['send_user'])?$data['send_user']:""; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">发放时间：</label>
        <label class="doc-read"><?php echo $data['send_time']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">生效时间：</label>
        <label class="doc-read"><?php echo $data['coupons']['start_date']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">使用状态：</label>
        <label class="doc-read"><?php echo $data["use_status"]==0?"未领取":($data["use_status"]==1?"未使用":"已使用"); ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">使用单号：</label>
        <label class="doc-read"><?php echo $data['use_order_code']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">使用时间：</label>
        <label class="doc-read"><?php echo $data['use_time']; ?></label>
    </div>
    
   
    <button class="am-btn am-btn-secondary" id="backSubmit" >关闭</button>
</div>
<script src="/js/jquery.cxselect.min.js"></script>
<script>
    $(function () {
        $("#backSubmit").click(function () {
           closeMask();
        })
    })
</script>
