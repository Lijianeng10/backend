<?php

use yii\helpers\Html;
use yii\grid\GridView;
$pushStatus = [
    "" => "全部",
    "1" => "未推送",
    "2" => "已推送"
];
$status = [
    "" => "全部",
    "1" => "已添加",
    "2" => "未执行完",
    "3" => "执行成功",
    "4" => "执行失败"
];
$get = $_GET;
echo "<form name='queueForm'><ul class='third_team_ul'>";
echo '<li>';
echo Html::label("队列ID", "queue_id", ["style" => "margin-left:15px;"]);
echo Html::input("input", "queue_id", isset($get["queue_id"]) ? $get["queue_id"] : "", ["id" => "queue_id", "class" => "form-control", "placeholder" => "队列ID", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("参数内容", "args", ["style" => "margin-left:15px;"]);
echo Html::input("input", "args", isset($get["args"]) ? $get["args"] : "", ["id" => "args", "class" => "form-control", "placeholder" => "参数内容", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("推送状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("push_status", isset($get["push_status"]) ? $get["push_status"] : "0", $pushStatus, ["id" => "push_status", "class" => "form-control", "style" => "width:160px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("队列状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "0", $status, ["id" => "status", "class" => "form-control", "style" => "width:160px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("创建时间  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "create_time_start", isset($get["create_time_start"]) ? $get["create_time_start"] : "", ["id" => "create_time_start", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "create_time_end", isset($get["create_time_end"]) ? $get["create_time_end"] : "", ["id" => "create_time_end", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "search();"]);
echo Html::button("重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
echo '</li>';
echo "</ul></form>";
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '线程队列ID',
            'value' => 'queue_id'
        ],
        [
            'label' => '任务名称',
            'value' => function($model) {
                $queueNames = [
                    "lottery_job" => "生成子单",
                    "programme_job" => "合买出单",
                    "custom_made_job" => "定制跟单"
                ];
                return isset($queueNames[$model["job"]]) ? $queueNames[$model["job"]] : $model["job"];
            }
                ],
                [
                    'label' => '任务参数',
                    'value' => "args"
                ],
                [
                    'label' => '推送状态',
                    'value' => function($model) {
                        $pushStatus = [
                            "" => "全部",
                            "1" => "未推送",
                            "2" => "已推送"
                        ];
                        return $pushStatus[$model['push_status']];
                    }
                        ],
                        [
                            'label' => '线程状态',
                            'value' => function($model) {
                                $status = [
                                    "" => "全部",
                                    "1" => "已添加",
                                    "2" => "未执行完",
                                    "3" => "执行成功",
                                    "4" => "执行失败"
                                ];
                                return $status[$model['status']];
                            }
                                ],
                                [
                                    'label' => '创建时间',
                                    'value' => 'create_time'
                                ],
                                [
                                    'label' => '操作',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return '<div class="am-btn-group am-btn-group-xs">
                            <span class="handle pointer" onclick="reQueue(' . $model["queue_id"] . ')">重跑</span>
                        </div>';
                                    }
                                ]
                            ]
                        ]);
                        ?>
                        <script type="text/javascript">
                            function goReset() {
                                location.href = '/admin/queue';
                            }
                            function search() {
                                var con = $("form[name=queueForm]").serialize();
                                location.href = '/admin/queue?' + con;
                            }
                            function reQueue(queueId) {
                                if (confirm("确定生成子单?")) {
                                    $.ajax({
                                        url: "<?php echo \Yii::$app->params['userDomain']; ?>/api/cron/cron/re-queue",
                data: {queueId: queueId},
                type: "GET",
                dataType: "json",
                async: false,
                success: function (json) {
                    if (json["code"] == 600) {
                        alert(json["msg"]);
                        location.reload();
                    } else {
                        alert(json["msg"]);
                    }
                }
            });
        }
    }
</script>
