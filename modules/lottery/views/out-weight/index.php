<style>
    .action{
        width: 25%;
        text-align: left;
        padding-left: 0px;
        padding-top: 10px;
        display: inline-block;
    }
</style>
<form class="am-form" id="doc-vld-msg">
    <div style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;">
        <label style="margin-top: 20px;font-size: 16px;">彩种：</label>
        <select id="lotteryArr"  style="width:140px;display:inline;margin-left:5px;">
            <?php foreach ($lotteryList as $key => $item): ?>
                <option value="<?php echo $key; ?>"><?php echo $item; ?></option>
            <?php endforeach; ?>
        </select>
        <?php foreach ($lotteryData as $k => $val) : ?>
            <div id="<?php echo $k; ?>" style="text-align: left;margin-bottom: 5px;margin-left: 10px;font-size: 14px;margin-top: 10px;display: none" class="out_third">
                <?php foreach ($val as $v): ?>
                    <div class="action">
                        <label><input type="checkbox" id="<?php echo $v['third_code']; ?>" class="select" value=<?php echo $v['third_code']; ?> <?php if (!empty($v['out_code'])) : ?> checked="checked" <?php endif; ?>><?php echo $v['third_name']; ?>：</label>
                        <label class="name">
                            <input type="number" id="out_<?php echo $k; ?>_<?php echo $v['third_code'] ?>" class="auto_third"  value=<?php echo empty($v['weight']) ? 0 : $v['weight']; ?>>
                        </label>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endforeach; ?>
        <button class="am-btn am-btn-secondary" id="addSubmit"  style="margin-left:5px;">设置</button>
    </div>
</form>
<iframe id="rfFrame" name="rfFrame" src="about:blank" style="display:none;"></iframe>
<script type="text/javascript">
    $(function () {
        $("#lotteryArr").change(function () {
            $(".out_third").hide();
            lotteryCode = $(this).val();
            $('#' + lotteryCode).show();
        });
        $('#addSubmit').click(function () {
            document.forms[0].target = "rfFrame";
            lcode = $('#lotteryArr').val();
            var weightArr = [];
            $.each($(".select"), function () {
                if ($(this).is(':checked')) {
                    thirdCode = $(this).attr('id');
                    weight = $('#out_' + lcode + '_' + thirdCode).val();
                    weightArr.push(thirdCode + ':' + weight);
                }
            });
            $.ajax({
                url: '/lottery/out-weight/set-out-lot-weight',
                async: false,
                type: 'POST',
                data: {weightData: weightArr, lotteryCode: lcode},
                dataType: 'json',
                success: function (result) {
                    if (result['code'] != 600) {
                        msgAlert(result['msg'], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(result['msg'], function () {
                            location.reload();
                        });
                    }
                }
            });
        })

    })
</script>