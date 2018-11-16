<?php

use yii\helpers\Html;
?>
<div class="lotteryResult">
    <?php
    foreach ($data as $val) {
        ?>
        <div style="width: 100%;margin-top:6px; ">
            <?php
            echo Html::tag("span", Html::label($val["lottery_name"], "", ["style" => "margin-top:10px;font-size: 16px;font-weight:700;"])
                    . Html::tag("span", $val["periods"] . "期", ["class" => "buttomspan"])
                    . Html::tag("a", "往期开奖>>", ["class" => "buttomspan", "href" => "/lottery/result/list?lotterycode=" . $val["lottery_code"]]));
            if(isset($timeStrs[$val["lottery_code"]])){
               echo Html::tag("span", $timeStrs[$val["lottery_code"]], ["class" => "lotteryTime"]); 
            }
            echo Html::tag("hr", "", ["class" => "resultPage"]);
            $lists = explode("|", $val["lottery_numbers"]);
            if (is_array($lists) && count($lists) > 0) {
                foreach ($lists as $key => $list) { 
                    $vs = explode(",", $list);
                    if (is_array($vs) && count($vs) > 0) {
                        foreach ($vs as $v) {
                            echo Html::tag("span", $v, ["class" => "yuan_" . $key]);
                        }
                    }
                }
            }
            ?>
        </div>
        <?php
    }
    ?>
</div>

