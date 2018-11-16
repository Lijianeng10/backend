<?php
use yii\helpers\Html;
use yii\bootstrap\Alert;
use yii\widgets\DetailView;
$this->title = 'AddOdds3007';

?>
<?php
echo '<div class="divContainer">';
echo '<form id="editBfBonus">';
echo Html::input('hidden', 'schedule_id', $model["schedule_id"], ['id'=>'scheduleId']);
echo Html::input('hidden', 'odds_score_id', $model["odds_score_id"]);
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            "label" => "胜赔率",
            "format" => "raw",
            "value" => function($model) {
                $html = "<table class='tableBorder'>";
                $html .= "<tr><th>1:0</th><th>2:0</th><th>2:1</th><th>3:0</th><th>3:1</th><th>3:2</th></tr>";
                $html .= "<tr><td><input name='wins_10' value=" .$model['score_wins_10'] ."></td><td><input name='wins_20' value=" .$model['score_wins_20'] ."></td><td><input name='wins_21' value=" .$model['score_wins_21'] ."></td><td><input name='wins_30' value=" .$model['score_wins_30'] ."></td><td><input name='wins_31' value=" .$model['score_wins_31'] ."></td><td><input name='wins_32' value=" .$model['score_wins_32'] ."></td></tr>";
                $html .= "<tr><th>4:0</th><th>4:1</th><th>4:2</th><th>5:0</th><th>5:1</th><th>5:2</th><th>胜其他</th></tr>";
                $html .= "<tr><td><input name='wins_40' value=" .$model['score_wins_40'] ."></td><td><input name='wins_41' value=" .$model['score_wins_41'] ."></td><td><input name='wins_42' value=" .$model['score_wins_42'] ."></td><td><input name='wins_50' value=" .$model['score_wins_50'] ."></td><td><input name='wins_51' value=" .$model['score_wins_51'] ."></td><td><input name='wins_52' value=" .$model['score_wins_52'] ."></td><td><input name='wins_other' value=" .$model['score_wins_90'] ."></td></tr>";      
                $html .= "</table>";
                return $html;
            }
        ], [
            "label" => "平赔率",
            "format" => "raw",
            "value" => function($model) {
                $html = "<table class='tableBorder'>";
                $html .= "<tr><th>0:0</th><th>1:1</th><th>2:2</th><th>3:3</th><th>平其他</th></tr>";
                $html .= "<tr><td><input name='level_00' value=" .$model['score_level_00'] ."></td><td><input name='level_11' value=" .$model['score_level_11'] ."></td><td><input name='level_22' value=" .$model['score_level_22'] ."></td><td><input name='level_33' value=" .$model['score_level_33'] ."></td><td><input name='level_other' value=" .$model['score_level_99'] ."></td></tr>";
                $html .= "</table>";
                return $html;
            }
        ], [
            "label" => "负赔率",
            "format" => "raw",
            "value" => function($model) {
                $html = "<table class='tableBorder'>";
                $html .= "<tr><th>0:1</th><th>0:2</th><th>1:2</th><th>0:3</th><th>1:3</th><th>2:3</th></tr>";
                $html .= "<tr><td><input name='negative_01' value=" .$model['score_negative_01'] ."></td><td><input name='negative_02' value=" .$model['score_negative_02'] ."></td><td><input name='negative_12' value=" .$model['score_negative_12'] ."></td><td><input name='negative_03' value=" .$model['score_negative_03'] ."></td><td><input name='negative_13' value=" .$model['score_negative_13'] ."></td><td><input name='negative_23' value=" .$model['score_negative_23'] ."></td></tr>";
                $html .= "<tr><th>0:4</th><th>1:4</th><th>2:4</th><th>0:5</th><th>1:5</th><th>2:5</th><th>负其他</th></tr>";
                $html .= "<tr><td><input name='negative_04' value=" .$model['score_negative_04'] ."></td><td><input name='negative_14' value=" .$model['score_negative_14'] ."></td><td><input name='negative_24' value=" .$model['score_negative_24'] ."></td><td><input name='negative_05' value=" .$model['score_negative_05'] ."></td><td><input name='negative_15' value=" .$model['score_negative_15'] ."></td><td><input name='negative_25' value=" .$model['score_negative_25'] ."></td><td><input name='negative_other' value=" .$model['score_negative_09'] ."></td></tr>";
                $html .= "</table>";
                return $html;
            }
        ], [
            "label" => "操作",
            "format" => "raw",
            "value" => function() {
                return Html::button('提交', ['class'=>'am-btn am-btn-primary', 'id'=>'editSubmit']) . '&nbsp&nbsp&nbsp' . Html::button('返回', ['class'=>'am-btn am-btn-primary', 'id'=>'reback']);
            }
        ]
    ]
]);
echo '</form>';
echo '</div>'
?>

<script type="text/javascript">
    $(function () {
        var scheduleId = $("#scheduleId").val();
        $('#editSubmit').click(function () {
            $.ajax({
                url: '/lottery/schedule/editodds3007',
                async: false,
                type: 'POST',
                data: $("#editBfBonus").serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            console.log(scheduleId)
                            location.href = "/lottery/schedule/readbonus?schedule_id=" + scheduleId;
                        });
                    }
                }
            });
        });
        
        $("#reback").click(function () {
            location.href = "/lottery/schedule/readbonus?schedule_id=" + scheduleId;
        });
    });
    
</script>