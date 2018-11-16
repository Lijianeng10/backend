<?php
use app\modules\member\helpers\Constants;
?>
<style>
    .word{
        width: 100px;
        text-align: right;
       
    }
    #backSubmit{
        margin-left: 25px;
    }
    .doc-read{
        width: 70%;
    }
    .remark{
        width: 80%;
       white-space:normal;
       word-break:break-all;
       overflow:hidden; 
       vertical-align: top;
    }
    #couponsDetail p{
        margin: 0 ;
        padding: 0;
    }
</style>
<div>
    <div>
        <label  for="doc-vld-name-2-1" class="word">礼品名称：</label>
        <label class="doc-read"><?php echo $data['gift_name']; ?></label>
    </div>

    <div>
        <label for="doc-vld-name-2-1" class="word">礼品副标题：</label>
        <label class="doc-read"><?php echo $data['subtitle']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">礼品简码：</label>
        <label class="doc-read"><?php echo $data['gift_code']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">所属分类：</label>
        <label class="doc-read"><?php echo $data['category_name']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">所需咕币：</label>
        <label class="doc-read"><?php echo $data['gift_glcoin']; ?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">缩略图：</label>
        <label class="doc-read"><img class="slImg" src="<?php echo $data['gift_picture'];?>" width="40px" height="40px"></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">详情图：</label>
        <label class="doc-read"><img class="xqImg" src="<?php echo $data['gift_picture2'];?>" width="40px" height="40px"></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">库存：</label>
        <label class="doc-read"><?php echo $data['in_stock'];?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">已兑换数量：</label>
        <label class="doc-read"><?php echo $data['exchange_nums'];?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">所属上级：</label>
        <label class="doc-read"><?php echo $data['agent_name'];?></label>
    </div>
     <div>
        <label for="doc-vld-name-2-1" class="word">等级限制：</label>
        <label class="doc-read"><?php 
            $userAry=[
                    "0"=>"无等级限制",
                    "1"=>"初出茅庐",
                    "2"=>"蒙猜大虾",
                    "3"=>"江湖半仙",
                    "4"=>"神机妙算",
                    "5"=>"未卜先知",
                ]; 
            echo $userAry[$data['gift_level']];?>
        </label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">活动时间：</label>
        <label class="doc-read"><?php echo $data['start_date']." 至 ".$data['end_date'];?></label>
    </div>
    <div>
        <label for="doc-vld-name-2-1" class="word">礼品备注：</label>
        <label class="remark"><?php echo $data['gift_remark'];?></label>
    </div>
    <div id="couponsDetail">
        <?php
            if(!empty($data["batch"])){
                ?>
        <hr/>
        <p style=" font-weight: bold">优惠券信息</p>
        <p>
            <label for="doc-vld-name-2-1" class="word">批次：</label>
            <label class="doc-read"><?php echo $data['batch'];?></label>  
        </p>
         <p>
            <label for="doc-vld-name-2-1" class="word">适用彩种：</label>
            <label class="doc-read"><?php $use_range=Constants::LOTTERY_TYPE; echo $use_range[$data['coupons']['use_range']];?></label>  
        </p>
         <p>
            <label for="doc-vld-name-2-1" class="word">最低消费：</label>
            <label class="doc-read"><?php  echo $data['coupons']['less_consumption']."元";?></label>  
        </p>
        <p>
            <label for="doc-vld-name-2-1" class="word">优惠金额：</label>
            <label class="doc-read"><?php  echo $data['coupons']['reduce_money']."元";?></label>  
        </p>
        <p>
            <label for="doc-vld-name-2-1" class="word">单日限用：</label>
            <label class="doc-read"><?php  echo $data['coupons']['days_num']."张";?><span style="color:#ccc">(单日限用为0张表示没有张数限制)</span></label>
            
        </p>
        <p>
            <label for="doc-vld-name-2-1" class="word">是否可叠加：</label>
            <label class="doc-read"><?php $is_gift=Constants::IS_GIFT; echo $is_gift[$data['coupons']['stack_use']];?></label>  
        </p>
        <p>
            <label for="doc-vld-name-2-1" class="word">有效期：</label>
            <label class="doc-read"><?php  echo $data['coupons']['start_date']." 至 ".$data['coupons']['end_date'];?></label>  
        </p>
            <?php 
            }
        ?>
    </div>

    
   
    <button class="am-btn am-btn-primary" id="backSubmit" >关闭</button>
</div>
<script src="/js/jquery.cxselect.min.js"></script>
<script>
    $(function () {
        $(".xqImg").bigShow();
        $(".slImg").bigShow();
        $("#backSubmit").click(function () {
           closeMask();
        })
    })
</script>
