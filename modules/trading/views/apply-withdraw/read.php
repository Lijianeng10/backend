<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
echo DetailView::widget([
    'model' => $model,
    'options' => [
        'class' => 'table table-striped table-bordered modalTable',
        'style' => 'width:45%;float:left;'
    ],
    'attributes' => [
        [
            "label" => "申请方",
            "value" => function($model) {
                return $model["name"];
            }
        ],[
            "label" => "持卡人",
            "value" => function($model) {
                return $model["user_name"];
            }
        ], [
            "label" => "开户行",
            "value" => function($model) {
                return $model["bank_open"];
            }
        ], [
            "label" => "开户支行",
            "value" => function($model) {
                return $model["branch"];
            }
        ], [
            "label" => "银行卡号",
            "value" => function($model) {
                return $model["card_number"];
            }
        ], [
            "label" => "所属地区",
            "value" => function($model) {
                return $model["province"] . $model['city'];
            }
        ]
    ]]);
        echo DetailView::widget([
            'model' => $model,
            'options' => [
                'class' => 'table table-striped table-bordered modalTable tableTh100',
                'style' => 'width:92%;'
            ],
            'attributes' => [
                [
                    "label" => "提现凭证",
                    "format" => "raw",
                    "value" => function($model) {
                        $html = "";
                        if (!empty($model["voucher_pic"])) {
                            $html .= "<div style='display:inline-block' data-magnify='gallery' href={$model["voucher_pic"]} data-caption='提现凭证'>";
                            $html.= "<img class='orderImg' src='{$model["voucher_pic"]}' />";
                            $html.= "</div>";
                        }
                        return $html;
                    }
                ]
            ]
        ]);

        echo Html::button("返回", ["class" => "am-btn am-btn-primary", "onclick" => "closeMask();"]);
?>
