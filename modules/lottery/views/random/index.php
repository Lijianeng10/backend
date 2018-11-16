<div>
    <form class="am-form am-form-horizontal" id="random_bet">
        <div class="am-form-group">
            <label class="am-u-sm-2 am-form-label"></label>
            <div class="am-u-sm-10">
            </div>
        </div>
        <div class="am-form-group">
            <label class="am-u-sm-2 am-form-label">彩种</label>
            <div class="am-u-sm-10" id="lot_code">
                <select class="lottery" style="width: 200px">
                    <option value=0>请选择</option>
                    <?php foreach ($data['lottery'] as $key => $val) : ?>
                        <option value=<?php echo $key ?>><?php echo $val; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="am-form-group">
            <label class="am-u-sm-2 am-form-label">玩法</label>
            <div class="am-u-sm-10" id="play_code">
                <select class="play" style="width: 200px" disabled="disabled">
                    <option  value=0>请选择</option>
                    <?php foreach ($data['play'] as $val) : ?>
                        <option value=<?php echo $val['lottery_play_code'] ?> class="code_0 code_<?php echo $val['lottery_code'] ?>" style="display:none"><?php echo $val['lottery_play_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="am-form-group">
            <label class="am-u-sm-2 am-form-label">订单数</label>
            <div class="am-u-sm-10">
                <select class="order" disabled="disabled" style="width: 200px">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        <div class="am-form-group">
            <label class="am-u-sm-2 am-form-label">订单注数</label>
            <div class="am-u-sm-10" id="order">
                <select class="order_count" disabled="disabled" style="width: 200px">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
        </div>
        <div class="am-form-group" id="trace">
            <label class="am-u-sm-2 am-form-label">追号</label>
            <div class="am-u-sm-10">
                <input id="bet_trace" disabled="disabled" value="1"/>
            </div>
        </div>
        <!--        <div class="am-form-group" id="add_bet">
                    <label class="am-u-sm-2 am-form-label">追加</label>
                    <div class="am-u-sm-10">
                        <input disabled="disabled" id="add_nums" value="0"/>
                    </div>
                </div>-->
        <!--        <div class="am-form-group">
                    <label class="am-u-sm-2 am-form-label">号码</label>
                    <div class="am-u-sm-10">
                        <textarea disabled="disabled" id="bet_nums" wrap="virtual" rows="1" style="word-wrap:" ></textarea>
                    </div>
                </div>-->
        <div class="am-form-group" id="bet">
            <label class="am-u-sm-2 am-form-label">注数/金额</label>
            <div class="am-u-sm-10">
                <input disabled="disabled" id="bet_count" value="0"/>
                <input disabled="disabled" id="bet_total" value="0"/>
            </div>
        </div>
        <div class="am-form-group">
            <label class="am-u-sm-2 am-form-label">完成度</label>
            <div class="am-u-sm-10">
                <progress id="pro"></progress>
                <span id="degree">0</span>% 
            </div>
        </div>
        <div class="am-form-group">
            <div class="am-u-sm-10 am-u-sm-offset-2" id="button_1">
                <!--<button type="button" class="am-btn am-btn-primary tpl-btn-bg-color-success " id="generate">生成随机号码</button>-->
                <input type="reset"class="am-btn am-btn-primary tpl-btn-bg-color-success " id="reset" value="重置">
                <button type="button" class="am-btn am-btn-primary tpl-btn-bg-color-success " id="addSubmit">投注</button>
            </div>
        </div>
    </form>
</div>

<script>

    $(function () {
        $(".lottery").change(function () {
            var code = $(this).val();
            $("#error_msg1").remove();
            if (code == 0) {
                $(".play").attr("disabled", true);
                $("#add_nums").attr("disabled", true);
                $(".order").attr("disabled", true);
                $(".order_count").attr("disabled", true);
//                $("#bet_trace").attr("disabled", true);
                var h1 = "<span style='color:red' id='error_msg1'>请选择彩种</span>";
                $('#lot_code').append(h1);
                return false;
            }
            if (code == '2001') {
                $("#add_nums").attr("disabled", false);
            } else {
                $("#add_nums").attr("disabled", true);
            }
            $(".order").attr("disabled", false);
            $(".order_count").attr("disabled", false);
//            $("#bet_trace").attr("disabled", false);
            $(".play").attr("disabled", false);
            $(".code_0").css('display', 'none');
            $(".code_" + code).css('display', 'block');
        });

        $('#generate').click(function () {
//            $("#error_msg1").remove();
//            $("#error_msg2").remove();
//            $("#error_msg3").remove();
//
//            var lottery = $(".lottery option:selected"); 
//            var play = $(".play option:selected");
//            var order = $(".order option:selected");
//            var order_count = $(".order_count option:selected");
//            var l_code = lottery.val();
//            var p_code = play.val();
//            var order_nums = order.val();
//            var count_nums = order_count.val();
//            if (l_code == 0) {
//                var h1 = "<span style='color:red' id='error_msg1'>请选择彩种</span>";
//                $('#lot_code').append(h1);
//                return false;
//            }
//            if (p_code == 0) {
//                var h2 = "<span style='color:red' id='error_msg2'>请选择玩法</span>";
//                $('#play_code').append(h2);
//                return false;
//            }
//            $(".lottery").attr("disabled", true);
//            $(".play").attr("disabled", true);
//            $(".order").attr("disabled", true);
//            $(".order_count").attr("disabled", true);
//            $("#addSubmit").attr("disabled", false);
//            $("#bet_trace").attr("disabled", false);
////            bet = $("#bet_nums").text();
//            bet_count = parseInt($("#bet_count").val());
//            bet_total = parseInt($("#bet_total").val());
//            trace = parseInt($("#bet_trace").val());
//            var i = 0;
//            if (l_code == '1001') {
//                if (p_code == '100101') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            reds = getRandom(6, 1, 33);
//                            blues = getRandom(1, 1, 16);
//                            redStr = getZero(reds);
//                            blueStr = getZero(blues);
//                            bet += redStr + '|' + blueStr + '^';
//                            redCount = getBetcount(redStr, 6);
//                            blueCount = getBetcount(blueStr, 1);
//                            count = redCount * blueCount;
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else if (p_code == '100102') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            reds = getRandom(6 + j, 1, 33);
//                            blues = getRandom(1, 1, 16);
//                            redStr = getZero(reds);
//                            blueStr = getZero(blues);
//                            bet += redStr + '|' + blueStr + '^';
//                            redCount = getBetcount(redStr, 6);
//                            blueCount = getBetcount(blueStr, 1);
//                            count = redCount * blueCount;
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                }
////                $("#bet_nums").text(bet)
//                $("#bet_count").val(bet_count);
//                $("#bet_total").val(bet_total);
//            } else if (l_code == '1002') {
//                if (p_code == '100201') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            baiStr = getRandom(1, 0, 9);
//                            shiStr = getRandom(1, 0, 9);
//                            geStr = getRandom(1, 0, 9);
//                            bet += baiStr + "|" + shiStr + "|" + geStr + '^';
//                            baiCount = getBetcount(baiStr, 1);
//                            shiCount = getBetcount(shiStr, 1);
//                            geCount = getBetcount(geStr, 1);
//                            count = baiCount * shiCount * geCount;
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else if (p_code == '100211') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            baiStr = getRandom(1 + j, 0, 9);
//                            shiStr = getRandom(1 + j, 0, 9);
//                            geStr = getRandom(1 + j, 0, 9);
//                            bet += baiStr + "|" + shiStr + "|" + geStr + '^';
//                            baiCount = getBetcount(baiStr, 1);
//                            shiCount = getBetcount(shiStr, 1);
//                            geCount = getBetcount(geStr, 1);
//                            count = baiCount * shiCount * geCount;
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else if (p_code == '100221') {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                } else if (p_code == '100202') {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                } else if (p_code == '100212') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            sanStr = getRandom(2 + j, 0, 9);
//                            bet += sanStr + '^';
//                            count = getBetcount(sanStr, 2);
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else if (p_code == '100222') {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                } else if (p_code == '100203') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            sixStr = getRandom(3, 0, 9);
//                            bet += sixStr + '^';
//                            count = getBetcount(sixStr, 3);
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else if (p_code == '100213') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            sixStr = getRandom(3 + j, 0, 9);
//                            bet += sixStr + '^';
//                            count = getBetcount(sixStr, 3);
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                }
////                $("#bet_nums").text(bet)
//                $("#bet_count").val(bet_count);
//                $("#bet_total").val(bet_total);
//            } else if (l_code == '1003') {
//                if (p_code == '100301') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            reds = getRandom(7, 1, 30);
//                            redStr = getZero(reds);
//                            bet += p_code + ':' + redStr + '^';
//                            count = getBetcount(redStr, 7);
//                            total = count * 2 * trace;
//                            bet_count = count;
//                            bet_total = total;
//                        }
//                        i++;
//                    }
//                } else if (p_code == '100302') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            reds = getRandom(7 + j, 1, 30);
//                            redStr = getZero(reds);
//                            bet += redStr + '^';
//                            count = getBetcount(redStr, 7);
//                            total = count * 2 * trace;
//                            bet_count = count;
//                            bet_total = total;
//                        }
//                        i++;
//                    }
//                } else {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                }
////                $("#bet_nums").text(bet)
//                $("#bet_count").val(bet_count);
//                $("#bet_total").val(bet_total);
//            } else if (l_code == '2001') {
//                if (p_code == '200101') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            reds = getRandom(5, 1, 35);
//                            blues = getRandom(2, 1, 12);
//                            redStr = getZero(reds);
//                            blueStr = getZero(blues);
//                            bet += redStr + '|' + blueStr + '^';
//                            redCount = getBetcount(redStr, 5);
//                            blueCount = getBetcount(blueStr, 2);
//                            count = redCount * blueCount;
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else if (p_code == '200102') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            reds = getRandom(5 + j, 1, 35);
//                            blues = getRandom(2, 1, 12);
//                            redStr = getZero(reds);
//                            blueStr = getZero(blues);
//                            bet += redStr + '|' + blueStr + '^';
//                            redCount = getBetcount(redStr, 5);
//                            blueCount = getBetcount(blueStr, 2);
//                            count = redCount * blueCount;
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                }
////                $("#bet_nums").text(bet)
//                $("#bet_count").val(bet_count);
//                $("#bet_total").val(bet_total);
//            } else if (l_code == '2002') {
//                if (p_code == '200201') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            baiStr = getRandom(1, 0, 9);
//                            shiStr = getRandom(1, 0, 9);
//                            geStr = getRandom(1, 0, 9);
//                            bet += baiStr + "|" + shiStr + "|" + geStr + '^';
//                            baiCount = getBetcount(baiStr, 1);
//                            shiCount = getBetcount(shiStr, 1);
//                            geCount = getBetcount(geStr, 1);
//                            count = baiCount * shiCount * geCount;
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//
//                } else if (p_code == '200211') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            baiStr = getRandom(1 + j, 0, 9);
//                            shiStr = getRandom(1 + j, 0, 9);
//                            geStr = getRandom(1 + j, 0, 9);
//                            bet += baiStr + "|" + shiStr + "|" + geStr + '^';
//                            baiCount = getBetcount(baiStr, 1);
//                            shiCount = getBetcount(shiStr, 1);
//                            geCount = getBetcount(geStr, 1);
//                            count = baiCount * shiCount * geCount;
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else if (p_code == '200221') {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                } else if (p_code == '200202') {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                } else if (p_code == '200212') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            sannums = parseInt(Math.random() * (10 - 2) + 1 + 2);
//                            sanStr = getRandom(sannums, 0, 9);
//                            bet += sanStr + '^';
//                            count = getBetcount(sanStr, 2);
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//
//                } else if (p_code == '200222') {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                } else if (p_code == '200203') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            sixStr = getRandom(3, 0, 9);
//                            bet += p_code + ":" + sixStr + '^';
//                            count = getBetcount(sixStr, 3);
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//
//                } else if (p_code == '200213') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            sixStr = getRandom(3 + j, 0, 9);
//                            bet += sixStr + '^';
//                            count = getBetcount(sixStr, 3);
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else {
//                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
//                    $('#play_code').append(h2);
//                    return false;
//                }
//
////                $("#bet_nums").text(bet)
//                $("#bet_count").val(bet_count);
//                $("#bet_total").val(bet_total);
//            } else if (l_code == '2003') {
//                if (p_code == '200301') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            wanStr = getRandom(1, 0, 9);
//                            qianStr = getRandom(1, 0, 9);
//                            baiStr = getRandom(1, 0, 9);
//                            shiStr = getRandom(1, 0, 9);
//                            geStr = getRandom(1, 0, 9);
//                            bet += wanStr + "|" + qianStr + "|" + baiStr + "|" + shiStr + "|" + geStr + '^';
//                            wanCount = getBetcount(wanStr, 1);
//                            qianCount = getBetcount(qianStr, 1);
//                            baiCount = getBetcount(baiStr, 1);
//                            shiCount = getBetcount(shiStr, 1);
//                            geCount = getBetcount(geStr, 1);
//                            count = wanCount * qianCount * baiCount * shiCount * geCount;
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                } else if (p_code == '200302') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            wanStr = getRandom(1 + j, 0, 9);
//                            qianStr = getRandom(1 + j, 0, 9);
//                            baiStr = getRandom(1 + j, 0, 9);
//                            shiStr = getRandom(1 + j, 0, 9);
//                            geStr = getRandom(1 + j, 0, 9);
//                            bet += wanStr + "|" + qianStr + "|" + baiStr + "|" + shiStr + "|" + geStr + '^';
//                            wanCount = getBetcount(wanStr, 1);
//                            qianCount = getBetcount(qianStr, 1);
//                            baiCount = getBetcount(baiStr, 1);
//                            shiCount = getBetcount(shiStr, 1);
//                            geCount = getBetcount(geStr, 1);
//                            count = wanCount * qianCount * baiCount * shiCount * geCount;
//                            total = count * 2 * trace;
//                            bet_count += count;
//                            bet_total += total;
//                        }
//                        i++;
//                    }
//                }
//
////                $("#bet_nums").text(bet)
//                $("#bet_count").val(bet_count);
//                $("#bet_total").val(bet_total);
//            } else if (l_code == '2004') {
//                if (p_code == '200401') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            baiwanStr = getRandom(1, 0, 9);
//                            shiwanStr = getRandom(1, 0, 9);
//                            wanStr = getRandom(1, 0, 9);
//                            qianStr = getRandom(1, 0, 9);
//                            baiStr = getRandom(1, 0, 9);
//                            shiStr = getRandom(1, 0, 9);
//                            geStr = getRandom(1, 0, 9);
//                            bet += baiwanStr + "|" + shiwanStr + "|" + wanStr + "|" + qianStr + "|" + baiStr + "|" + shiStr + "|" + geStr + '^';
//                            baiwanCount = getBetcount(baiwanStr, 1);
//                            shiwanCount = getBetcount(shiwanStr, 1);
//                            wanCount = getBetcount(wanStr, 1);
//                            qianCount = getBetcount(qianStr, 1);
//                            baiCount = getBetcount(baiStr, 1);
//                            shiCount = getBetcount(shiStr, 1);
//                            geCount = getBetcount(geStr, 1);
//                            count = baiwanCount * shiwanCount * wanCount * qianCount * baiCount * shiCount * geCount;
//                            total = count * 2 * trace;
//                            bet_count = (bet_count + count);
//                            bet_total = (bet_total + total);
//                        }
//                        i++;
//                    }
//                } else if (p_code == '200402') {
//                    while (i < order_nums) {
//                        for (j = 0; j < count_nums; j++) {
//                            baiwanStr = getRandom(1 + j, 0, 9);
//                            shiwanStr = getRandom(1 + j, 0, 9);
//                            wanStr = getRandom(1 + j, 0, 9);
//                            qianStr = getRandom(1 + j, 0, 9);
//                            baiStr = getRandom(1 + j, 0, 9);
//                            shiStr = getRandom(1 + j, 0, 9);
//                            geStr = getRandom(1 + j, 0, 9);
//                            bet += baiwanStr + "|" + shiwanStr + "|" + wanStr + "|" + qianStr + "|" + baiStr + "|" + shiStr + "|" + geStr + '^';
//                            baiwanCount = getBetcount(baiwanStr, 1);
//                            shiwanCount = getBetcount(shiwanStr, 1);
//                            wanCount = getBetcount(wanStr, 1);
//                            qianCount = getBetcount(qianStr, 1);
//                            baiCount = getBetcount(baiStr, 1);
//                            shiCount = getBetcount(shiStr, 1);
//                            geCount = getBetcount(geStr, 1);
//                            count = baiwanCount * shiwanCount * wanCount * qianCount * baiCount * shiCount * geCount;
//                            total = count * 2 * trace;
//                            bet_count = (bet_count + count);
//                            bet_total = (bet_total + total);
//                        }
//                        i++;
//                    }
//                }
////                $("#bet_nums").text(bet)
//                $("#bet_count").val(bet_count);
//                $("#bet_total").val(bet_total);
//            } else {
//                $(".lottery").attr("disabled", false);
//                $("#addSubmit").attr("disabled", true);
//                $(".play").attr("disabled", true);
//                var h1 = "<span style='color:red' id='error_msg1'>此彩种暂未开放投注</span>";
//                $('#lot_code').append(h1);
//                return false;
//            }
//
//            $("#generate").attr("disabled", true)
//            $('textarea').each(function () {
//                this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
//            }).on('input', function () {
//                this.style.height = 'auto';
//                this.style.height = (this.scrollHeight) + 'px';
//            });
        });
        ;
        $('#reset').click(function () {
//            $("#bet_nums").text('')
            $(".lottery").attr("disabled", false);
            $(".play").attr("disabled", true);
            $("#bet_count").val(0);
            $("#bet_total").val(0);
            $("#pro").attr('max','');
            $("#pro").attr('value','0');
        })

        $("#addSubmit").click(function () {
            $("#error_msg1").remove();
            $("#error_msg2").remove();
            $("#error_msg3").remove();
            $("#pro").attr('max','');
            $("#pro").attr('value','0');
            $("#degree").html(0);
            $("#bet_count").val(0);
            $("#bet_total").val(0);
            var lottery = $(".lottery option:selected"); 
            var play = $(".play option:selected");
            var order = $(".order option:selected");
            var order_count = $(".order_count option:selected");
            var l_code = lottery.val();
            var p_code = play.val();
            var l_name = lottery.text();
            var p_name = play.text();
            var order_nums = order.val();
            var count_nums = order_count.val();
            $("#pro").attr('max',order_nums);
            if (l_code == 0) {
                var h1 = "<span style='color:red' id='error_msg1'>请选择彩种</span>";
                $('#lot_code').append(h1);
                return false;
            }
            if (p_code == 0) {
                var h2 = "<span style='color:red' id='error_msg2'>请选择玩法</span>";
                $('#play_code').append(h2);
                return false;
            }
            $(".lottery").attr("disabled", true);
            $(".play").attr("disabled", true);
            $(".order").attr("disabled", true);
            $(".order_count").attr("disabled", true);
//            bet = $("#bet_nums").text();
            bet_count = parseInt($("#bet_count").val());
            bet_total = parseInt($("#bet_total").val());
            trace = parseInt($("#bet_trace").val());
            var i = 0;
            if (l_code == '1001') {
                if (p_code == '100101') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            reds = getRandom(6, 1, 33);
                            blues = getRandom(1, 1, 16);
                            redStr = getZero(reds);
                            blueStr = getZero(blues);
                            bet += redStr + '|' + blueStr + '^';
                            redCount = getBetcount(redStr, 6);
                            blueCount = getBetcount(blueStr, 1);
                            count = redCount * blueCount;
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        degree = ((i+1)/order_nums)*100;
                        $("#degree").html(degree);
                        $("#pro").attr('value',i+1);
                        i++;
                    }
                } else if (p_code == '100102') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            reds = getRandom(6 + j, 1, 33);
                            blues = getRandom(1, 1, 16);
                            redStr = getZero(reds);
                            blueStr = getZero(blues);
                            bet += redStr + '|' + blueStr + '^';
                            redCount = getBetcount(redStr, 6);
                            blueCount = getBetcount(blueStr, 1);
                            count = redCount * blueCount;
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                }
//                $("#bet_nums").text(bet)
                $(".play").attr("disabled", false);
                $("#bet_count").val(bet_count);
                $("#bet_total").val(bet_total);
            } else if (l_code == '1002') {
                if (p_code == '100201') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            baiStr = getRandom(1, 0, 9);
                            shiStr = getRandom(1, 0, 9);
                            geStr = getRandom(1, 0, 9);
                            bet += baiStr + "|" + shiStr + "|" + geStr + '^';
                            baiCount = getBetcount(baiStr, 1);
                            shiCount = getBetcount(shiStr, 1);
                            geCount = getBetcount(geStr, 1);
                            count = baiCount * shiCount * geCount;
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '100211') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            baiStr = getRandom(1 + j, 0, 9);
                            shiStr = getRandom(1 + j, 0, 9);
                            geStr = getRandom(1 + j, 0, 9);
                            bet += baiStr + "|" + shiStr + "|" + geStr + '^';
                            baiCount = getBetcount(baiStr, 1);
                            shiCount = getBetcount(shiStr, 1);
                            geCount = getBetcount(geStr, 1);
                            count = baiCount * shiCount * geCount;
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '100221') {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                } else if (p_code == '100202') {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                } else if (p_code == '100212') {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                    while (i < order_nums) {
                        for (j = 0; j < count_nums; j++) {
                            sanStr = getRandom(2 + j, 0, 9);
                            bet += sanStr + '^';
                            count = getBetcount(sanStr, 2);
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '100222') {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                } else if (p_code == '100203') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            sixStr = getRandom(3, 0, 9);
                            bet += sixStr + '^';
                            count = getBetcount(sixStr, 3);
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '100213') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            sixStr = getRandom(3 + j, 0, 9);
                            bet += sixStr + '^';
                            count = getBetcount(sixStr, 3);
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                }
//                $("#bet_nums").text(bet)
                $(".play").attr("disabled", false);
                $("#bet_count").val(bet_count);
                $("#bet_total").val(bet_total);
            } else if (l_code == '1003') {
                if (p_code == '100301') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            reds = getRandom(7, 1, 30);
                            redStr = getZero(reds);
                            bet += p_code + ':' + redStr + '^';
                            count = getBetcount(redStr, 7);
                            total = count * 2 * trace;
                            bet_count = count;
                            bet_total = total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '100302') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            reds = getRandom(7 + j, 1, 30);
                            redStr = getZero(reds);
                            bet += redStr + '^';
                            count = getBetcount(redStr, 7);
                            total = count * 2 * trace;
                            bet_count = count;
                            bet_total = total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                }
//                $("#bet_nums").text(bet)
                $(".play").attr("disabled", false);
                $("#bet_count").val(bet_count);
                $("#bet_total").val(bet_total);
            } else if (l_code == '2001') {
                if (p_code == '200101') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            reds = getRandom(5, 1, 35);
                            blues = getRandom(2, 1, 12);
                            redStr = getZero(reds);
                            blueStr = getZero(blues);
                            bet += redStr + '|' + blueStr + '^';
                            redCount = getBetcount(redStr, 5);
                            blueCount = getBetcount(blueStr, 2);
                            count = redCount * blueCount;
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '200102') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            reds = getRandom(5 + j, 1, 35);
                            blues = getRandom(2, 1, 12);
                            redStr = getZero(reds);
                            blueStr = getZero(blues);
                            bet += redStr + '|' + blueStr + '^';
                            redCount = getBetcount(redStr, 5);
                            blueCount = getBetcount(blueStr, 2);
                            count = redCount * blueCount;
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                }
//                $("#bet_nums").text(bet)
                $("#bet_count").val(bet_count);
                $("#bet_total").val(bet_total);
                $(".play").attr("disabled", false);
            } else if (l_code == '2002') {
                if (p_code == '200201') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            baiStr = getRandom(1, 0, 9);
                            shiStr = getRandom(1, 0, 9);
                            geStr = getRandom(1, 0, 9);
                            bet += baiStr + "|" + shiStr + "|" + geStr + '^';
                            baiCount = getBetcount(baiStr, 1);
                            shiCount = getBetcount(shiStr, 1);
                            geCount = getBetcount(geStr, 1);
                            count = baiCount * shiCount * geCount;
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '200211') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            baiStr = getRandom(1 + j, 0, 9);
                            shiStr = getRandom(1 + j, 0, 9);
                            geStr = getRandom(1 + j, 0, 9);
                            bet += baiStr + "|" + shiStr + "|" + geStr + '^';
                            baiCount = getBetcount(baiStr, 1);
                            shiCount = getBetcount(shiStr, 1);
                            geCount = getBetcount(geStr, 1);
                            count = baiCount * shiCount * geCount;
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '200221') {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                } else if (p_code == '200202') {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                } else if (p_code == '200212') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            sannums = parseInt(Math.random() * (10 - 2) + 1 + 2);
                            sanStr = getRandom(sannums, 0, 9);
                            bet += sanStr + '^';
                            count = getBetcount(sanStr, 2);
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '200222') {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                } else if (p_code == '200203') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            sixStr = getRandom(3, 0, 9);
                            bet += p_code + ":" + sixStr + '^';
                            count = getBetcount(sixStr, 3);
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '200213') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            sixStr = getRandom(3 + j, 0, 9);
                            bet += sixStr + '^';
                            count = getBetcount(sixStr, 3);
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else {
                    var h2 = "<span style='color:red' id='error_msg2'>此玩法暂未开放</span>";
                    $('#play_code').append(h2);
                    return false;
                }

//                $("#bet_nums").text(bet)
                $("#bet_count").val(bet_count);
                $("#bet_total").val(bet_total);
                $(".play").attr("disabled", false);
            } else if (l_code == '2003') {
                if (p_code == '200301') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            wanStr = getRandom(1, 0, 9);
                            qianStr = getRandom(1, 0, 9);
                            baiStr = getRandom(1, 0, 9);
                            shiStr = getRandom(1, 0, 9);
                            geStr = getRandom(1, 0, 9);
                            bet += wanStr + "|" + qianStr + "|" + baiStr + "|" + shiStr + "|" + geStr + '^';
                            wanCount = getBetcount(wanStr, 1);
                            qianCount = getBetcount(qianStr, 1);
                            baiCount = getBetcount(baiStr, 1);
                            shiCount = getBetcount(shiStr, 1);
                            geCount = getBetcount(geStr, 1);
                            count = wanCount * qianCount * baiCount * shiCount * geCount;
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '200302') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            wanStr = getRandom(1 + j, 0, 9);
                            qianStr = getRandom(1 + j, 0, 9);
                            baiStr = getRandom(1 + j, 0, 9);
                            shiStr = getRandom(1 + j, 0, 9);
                            geStr = getRandom(1 + j, 0, 9);
                            bet += wanStr + "|" + qianStr + "|" + baiStr + "|" + shiStr + "|" + geStr + '^';
                            wanCount = getBetcount(wanStr, 1);
                            qianCount = getBetcount(qianStr, 1);
                            baiCount = getBetcount(baiStr, 1);
                            shiCount = getBetcount(shiStr, 1);
                            geCount = getBetcount(geStr, 1);
                            count = wanCount * qianCount * baiCount * shiCount * geCount;
                            total = count * 2 * trace;
                            bet_count += count;
                            bet_total += total;
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                }
//                $("#bet_nums").text(bet)
                $("#bet_count").val(bet_count);
                $("#bet_total").val(bet_total);
                $(".play").attr("disabled", false);
            } else if (l_code == '2004') {
                if (p_code == '200401') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            baiwanStr = getRandom(1, 0, 9);
                            shiwanStr = getRandom(1, 0, 9);
                            wanStr = getRandom(1, 0, 9);
                            qianStr = getRandom(1, 0, 9);
                            baiStr = getRandom(1, 0, 9);
                            shiStr = getRandom(1, 0, 9);
                            geStr = getRandom(1, 0, 9);
                            bet += baiwanStr + "|" + shiwanStr + "|" + wanStr + "|" + qianStr + "|" + baiStr + "|" + shiStr + "|" + geStr + '^';
                            baiwanCount = getBetcount(baiwanStr, 1);
                            shiwanCount = getBetcount(shiwanStr, 1);
                            wanCount = getBetcount(wanStr, 1);
                            qianCount = getBetcount(qianStr, 1);
                            baiCount = getBetcount(baiStr, 1);
                            shiCount = getBetcount(shiStr, 1);
                            geCount = getBetcount(geStr, 1);
                            count = baiwanCount * shiwanCount * wanCount * qianCount * baiCount * shiCount * geCount;
                            total = count * 2 * trace;
                            bet_count = (bet_count + count);
                            bet_total = (bet_total + total);
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                } else if (p_code == '200402') {
                    while (i < order_nums) {
                        var p_codes = '';
                        var p_names = '';
                        var bet = '';
                        for (j = 0; j < count_nums; j++) {
                            baiwanStr = getRandom(1 + j, 0, 9);
                            shiwanStr = getRandom(1 + j, 0, 9);
                            wanStr = getRandom(1 + j, 0, 9);
                            qianStr = getRandom(1 + j, 0, 9);
                            baiStr = getRandom(1 + j, 0, 9);
                            shiStr = getRandom(1 + j, 0, 9);
                            geStr = getRandom(1 + j, 0, 9);
                            bet += baiwanStr + "|" + shiwanStr + "|" + wanStr + "|" + qianStr + "|" + baiStr + "|" + shiStr + "|" + geStr + '^';
                            baiwanCount = getBetcount(baiwanStr, 1);
                            shiwanCount = getBetcount(shiwanStr, 1);
                            wanCount = getBetcount(wanStr, 1);
                            qianCount = getBetcount(qianStr, 1);
                            baiCount = getBetcount(baiStr, 1);
                            shiCount = getBetcount(shiStr, 1);
                            geCount = getBetcount(geStr, 1);
                            count = baiwanCount * shiwanCount * wanCount * qianCount * baiCount * shiCount * geCount;
                            total = count * 2 * trace;
                            bet_count = (bet_count + count);
                            bet_total = (bet_total + total);
                            p_codes += p_code + ',';
                            p_names += p_name + ',';
                        }
                        pro_value = createOrder(l_code,p_codes,l_name,p_names,bet,trace,count,total);  
                        if(pro_value == 1){
                            degree = ((i+1)/order_nums)*100;
                            $("#degree").html(degree);
                            $("#pro").attr('value',i+1);
                        }else{
                            var h2 = "<span style='color:red' id='error_msg2'>提交错误</span>";
                            $('#button_1').append(h2);
                            return false;
                        }
                        i++;
                    }
                }
//                $("#bet_nums").text(bet)
                $("#bet_count").val(bet_count);
                $("#bet_total").val(bet_total);
                $(".play").attr("disabled", false);
            } else {
                $(".lottery").attr("disabled", false);
                $("#addSubmit").attr("disabled", true);
                $(".play").attr("disabled", true);
                var h1 = "<span style='color:red' id='error_msg1'>此彩种暂未开放投注</span>";
                $('#lot_code').append(h1);
                return false;
            }
        });
    })

    function createOrder(l_code,p_code,l_name,p_name,bet,trace,bet_count,bet_total) {
        var sta;
        $.ajax({
            url: '/lottery/random/addorder',
            async: false,
            type: 'POST',
            data: {
                l_code:l_code,
                p_code:p_code,
                l_name:l_name,
                p_name:p_name,
                bet_nums:bet,
                trace:trace,
                bet_count:bet_count,
                bet_total:bet_total
            },
            dataType: 'json',
            success: function (data) {
                if (data['code'] != 1) {
                    console.log(data.msg)
                   sta = 0;
                } else {
                   sta = 1;
                }
            }
        });
        return sta;
    }

    function getRandom(nums, min, max) {
        var arr = [];
        for (i = min; i < max + 1; i++) {
            arr.push(i);
        }
        var out = [];
        while (out.length < nums) {
                    var temp = (Math.random() * arr.length) >> 0;
            randoms = arr.splice(temp, 1);
                    out.push(randoms.join());
        }
        return out.join();
    }

    function getBetcount(numStr, m) {
        numArr = numStr.split(',');
        n = numArr.length;
        c = getCombination(n, m);
        return c;
    }

    function getZero(numStr) {
        numArr = numStr.split(',');
        arr = [];
        $.each(numArr, function (i, val) {
            if (val < 10) {
                a = '0' + val;
            } else {
                a = val;
            }
            arr.push(a);
        })
        return arr.join();
    }

    function getCombination(n, m) {
        if (n < m) {
            return 0;
        }
        a = getArrange(n, m);
        f = getFactorial(m);
        comb = a / f;
        return comb;
    }

    function getArrange(n, m) {
        f1 = getFactorial(n);
        f2 = getFactorial(n - m);
        arrange = f1 / f2;
        return arrange;
    }

    function getFactorial(n) {
        if (n <= 1) {
            f = 1;
        } else {
            f = n * arguments.callee(n - 1);
        }
        ;
        return f;
    }
</script>

