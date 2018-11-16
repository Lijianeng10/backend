<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Read';
?>
<?php
echo DetailView::widget([
    'model' => $data,
    'attributes' => [
        [
            'label' => '串关方式',
            'value' => function($model) {
                return $model["free_type"];
            }
        ], [
            'label' => '彩种编号',
            'value' => function($model) {
                return $model["lottery_code"];
            }
        ], [
            'label' => '玩法编号',
            'value' => function($model) {
                return $model["play_code"];
            }
       ], [
            'label' => '期数',
            'value' => function($model) {
                return $model["periods"];
            }
       ], [
            'label' => '投注内容',
            'value' => function($model) {
                return $model["bet_val"];
            }
       ], [
            'label' => '投注金额',
            'value' => function($model) {
                return $model["amount"];
            }
       ]
    ]
]);
echo Html::button("返回", ["class" => "am-btn am-btn-primary", "onclick" => "closeMask();"]);;
?>
