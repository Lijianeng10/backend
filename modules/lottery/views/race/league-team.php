<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

echo "<form id='league_team'>";
echo Html::input('hidden', "league_id", $model["league"]['league_id']);
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            "label" => "联赛全称",
            "value" => function($model) {
                return $model["league"]['league_long_name'];
            }
        ], [
            "label" => "已有球队",
            "format" => "raw",
            "value" => function($model) {
                $html = "<div class='chosen_team'>";
                foreach ($model['league_team'] as $val) {
                    $html .= "<label  id='chosen_label_" . $val['team_id'] . "'>";
                    $html .= Html::checkbox("chosen[]", $val['team_id'], ["value" => $val["team_id"], "class" => "chosen", "id" => "chosen_" . $val['team_id']]) . $val['team_name'];
                    $html .= "&nbsp&nbsp";
                    $html .= "</label>";
                }
                $html .= "</div>";
                return $html;
            }
        ], [
            "label" => "全部球队",
            "format" => "raw",
            "value" => function($model) {
                $html = "<div class='choice_team' style='display:none'>";
                foreach ($model['team'] as $val) {
                    $html .= "<span  id='choice_label_" . $val['team_id'] . "'>";
                    $html .= Html::checkbox("choice[]", '', ["value" => $val["team_id"], "class" => "choice", "id" => "choice_" . $val['team_id'], "data-name" => $val['team_long_name']]) . $val["team_long_name"];
                    $html .= "&nbsp&nbsp";
                     $html .= "</span>";
                }
                $html .= "</div>";
                return $html;
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function() {
                $html = Html::button("提交", ["class" => "am-btn am-btn-primary", "onclick" => "submitForm();"]);
                $html .="&nbsp&nbsp&nbsp";
                $html .= Html::button("返回", ["class" => "am-btn am-btn-primary", "onclick" => "returnLast();"]);
                return $html;
            }
        ]
    ]
]);
 echo "</form>";
 ?>

<script>
    $(function () {
        var arrCh = {};
        var arrCh = $("input[name='chosen[]']:checked");
        $(".choice_team").css('display', 'block');
        $(arrCh).each(function () {
            $("#choice_label_" + this.value).css("display", "none");
        });
        $(".choice").click(function () {
            var id = $(this).val();
            var name = $(this).attr('data-name');
            $("#choice_label_" + id).css("display", "none");
            var html = "<label id='chosen_label_" + id + "'>" + "<input type='checkbox' id='chosen_" + id + "' class='chosen' name='chosen[]' checked value=" + id + ">" + name + "&nbsp&nbsp</label>";
            $(".chosen_team").append(html);
        });
        $(".chosen_team").on("click", ".chosen", function () {
            var id = $(this).val();
            $("#chosen_label_" + id).remove();
            $("#choice_label_" + id).css("display", "inline-block");
            $("input[name = 'choice[]']").prop("checked", false);
        });
    });

    function submitForm() {
        var data = $("#league_team").serializeArray();
        $.ajax({
            url: '/lottery/race/league-team',
            async: false,
            type: 'POST',
            data: $("#league_team").serializeArray(),
            dataType: 'json',
            success: function (data) {
                if (data['code'] != 1) {
                    msgAlert(data['msg']);
                } else {
                    msgAlert(data['msg'], function () {
                        location.href = '/lottery/race/index';
                    });
                }
            }
        });
    }
    function returnLast() {
        location.href = '/lottery/race/index';
    }
</script>
