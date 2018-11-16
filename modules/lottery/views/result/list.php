<?php

use yii\helpers\Html;
use yii\grid\GridView;

//echo Html::tag("span", $lotteryName . "开奖记录", ["style" => "font-size:20px;font-weight: bold;"]);
?>
<ol class="am-breadcrumb">
    <li><a href="/lottery/result/">开奖记录</a></li>
    <li class="am-active"><?php echo $lotteryName . "开奖记录"; ?></li>
</ol>
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [
            "label" => "期次",
            "value" => function($model) {
                return $model["periods"] . "期";
            }
        ], [
            "label" => "开奖号码",
            "format" => "raw",
            "value" => function($model) {
                $html = "";
                $areas = explode("|", $model["lottery_numbers"]);
                if (is_array($areas) && count($areas) > 0) {
                    foreach ($areas as $key => $area) {
                        $balls = explode(",", $area);
                        if (is_array($balls) && count($balls) > 0) {
                            foreach ($balls as $ball) {
                                $html.= Html::tag("span", $ball, ["class" => "yuan_" . $key]);
                            }
                        }
                    }
                }
                return $html;
            }
                ], 
//                        [
//                    "label" => "大小比",
//                    "value" => "size_ratio"
//                ], [
//                    "label" => "奇偶比",
//                    "value" => "parity_ratio"
//                ]
            ]
        ]);
        