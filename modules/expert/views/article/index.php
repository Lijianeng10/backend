<style>
    .label-ccc{
        background-color: #E9ECF3;
    }
    .label-white{
        background-color: white;
    }
</style>
<?php

use yii\helpers\Html;
use yii\grid\GridView;

$articleStatus = [
    "" => "全部",
    "1" => "草稿",
//    "2" => "待审核",
    "3" => "上线",
    "4" => "下线",
//    "5" => "审核未通过"
];
$payType = [
    "" => "全部",
    "1" => "免费",
    "2" => "付费"
];
$stickType = [
    "" => "全部",
    "1" => "是",
    "999" => "否"
];
$buyStatus = [
    "" => "全部",
    "1" => "已购买",
    "2" => "未购买"
];
$sourceArr = [
    "" => "全部",
    "1" => "咕啦专家",
    "2" => "唧嗨专家",
    "3" => "网易专家",
    "4" => "同行专家",
    "5" => "全景专家"
];
echo '<ul class="nav nav-tabs" style="margin-bottom:10px;">
  <li role="presentation"' . ((!isset($_GET['article_type']) || $_GET['article_type'] == '1') ? ' class="active"' : '') . '><a href="/expert/article?article_type=1">足球方案</a></li>
  <li role="presentation"' . ((isset($_GET['article_type']) && $_GET['article_type'] == '2') ? ' class="active"' : '') . '><a href="/expert/article?article_type=2">篮球方案</a></li>
</ul>';
echo "<form id='filterForm'>";
echo Html::input("hidden", "createTimeSort", (isset($get["createTimeSort"]) ? $get["createTimeSort"] : ""), ["class" => "inputSort"]);
echo Html::input("hidden", "readNumsSort", (isset($get["readNumsSort"]) ? $get["readNumsSort"] : ""), ["class" => "inputSort"]);
echo Html::input("hidden", "buyNumsSort", (isset($get["buyNumsSort"]) ? $get["buyNumsSort"] : ""), ["class" => "inputSort"]);
echo "<ul class='third_team_ul'>";
echo '<li>';
echo Html::label("专家信息", "expertInfo", ["style" => "margin-left:15px;"]);
echo Html::input("input", "expertInfo", isset($get["expertInfo"]) ? $get["expertInfo"] : "", ["id" => "expertInfo", "class" => "form-control", "placeholder" => "咕啦编号、昵称、手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("创建时间  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "createTimeStart", isset($get["createTimeStart"]) ? $get["createTimeStart"] : date("Y-m-01"), ["id" => "createTimeStart", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "createTimeEnd", isset($get["createTimeEnd"]) ? $get["createTimeEnd"] : "", ["id" => "createTimeEnd", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("资讯状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("articleStatus", isset($get["articleStatus"]) ? $get["articleStatus"] : "", $articleStatus, ["id" => "expertStatus", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("方案信息", "articleInfo", ["style" => "margin-left:15px;"]);
echo Html::input("input", "articleInfo", isset($get["articleInfo"]) ? $get["articleInfo"] : "", ["id" => "articleInfo", "class" => "form-control", "placeholder" => "方案编号、标题", "style" => "width:200px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("审核时间  ", "", ["style" => "margin-left:15px;"]);
echo Html::input("text", "reviewTimeStart", isset($get["reviewTimeStart"]) ? $get["reviewTimeStart"] : "", ["id" => "reviewTimeStart", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo "-";
echo Html::input("text", "reviewTimeEnd", isset($get["reviewTimeEnd"]) ? $get["reviewTimeEnd"] : "", ["id" => "reviewTimeEnd", "class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("付费状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("payType", isset($get["payType"]) ? $get["payType"] : "", $payType, ["id" => "payType", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
if (\Yii::$app->session['type'] == 0) {
    echo '<li>';
    echo Html::label("文章来源  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("source", isset($get["source"]) ? $get["source"] : "", $sourceArr, ["id" => "source", "class" => "form-control", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo '</li>';
}
echo '<li>';
echo Html::label("置顶状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("stick", isset($get["stick"]) ? $get["stick"] : "", $stickType, ["id" => "payType", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::label("购买状态  ", "", ["style" => "margin-left:15px;"]);
echo Html::dropDownList("buyStatus", isset($get["buyStatus"]) ? $get["buyStatus"] : "", $buyStatus, ["id" => "buyStatus", "class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
echo '</li>';
echo '<li>';
echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;", "onclick" => "search();"]);
echo Html::button("重置", ["class" => "resetbtn am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
echo '</li>';
echo "</ul>";
echo "</form>";

echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '方案标题',
            'value' => 'article_title'
        ], [
            'label' => '方案编号',
            'value' => 'articles_code'
        ], [
            'label' => '创建时间',
//            'headerOptions' => ['class' => 'sortTh', 'id' => 'createTimeSort'],
            'value' => 'create_time'
        ], [
            'label' => '阅读',
            'headerOptions' => ['class' => 'sortTh', 'id' => 'readNumsSort'],
            'value' => 'read_nums'
        ], [
            'label' => '购买',
            'headerOptions' => ['class' => 'sortTh', 'id' => 'buyNumsSort'],
            'value' => 'buy_nums'
        ], [
            'label' => '专家编号',
            'value' => 'cust_no'
        ], [
            'label' => '专家昵称',
            'value' => 'user_name'
        ], [
            'label' => '文章来源',
            'format' => 'raw',
            'value' => function($model) {
                $sourceArr = [
                    "1" => "咕啦专家",
                    "2" => "唧嗨专家",
                    "3" => "网易专家",
                    "4" => "同行专家",
                    "5" => "全景专家"
                ];
                return $sourceArr[$model["article_source"]];
                
            }
        ], [
            'label' => '阅读费（元）',
            'value' => function($model) {
                if ($model['pay_type'] == 2) {
                    $item = $model['pay_money'];
                } else {
                    $item = '免费';
                }
                return $item;
            }
        ], [
            'label' => '资讯状态',
            'format' => 'raw',
            'value' => function($model) {
                $articleStatus = [
                    "1" => "草稿",
                    "2" => "待审核",
                    "3" => "上线",
                    "4" => "下线",
                    "5" => "审核未通过"
                ];
                if ($model["article_status"] == 5) {
                    return "<span class='handle pointer' data-am-popover=\"{content: '" . $model["remark"] . "', trigger: 'hover focus'}\" style='color:#55acee;'>审核未通过</span>";
                } else {
                    return isset($articleStatus[$model["article_status"]]) ? $articleStatus[$model["article_status"]] : "未知状态";
                }
            }
        ], [
            'label' => '置顶状态',
            'value' => function($model) {
                if ($model['stick'] == 999) {
                    $item = "否";
                } else {
                    $item = '是';
                }
                return $item;
            }
        ], [
            'label' => '置顶顺序',
            'value' => function($model) {
                if ($model["stick"] == 999) {
                    return 0;
                } else {
                    return $model["stick"];
                }
            }
        ], [
            'label' => '被举报(次)',
            'value' => 'report_num'
        ],
//                        [
//                    'label' => '审核人',
//                    'value' => 'opt_name'
//                ], [
//                    'label' => '审核时间',
//                    'value' => 'review_time'
//                ], 
        [
            'label' => '操作',
            'format' => 'raw',
            'value' => function($model) {
//                    . ($model["article_status"] == 4 ? ('<span class="handle pointer" onclick="onLine(' . $model["expert_articles_id"] . ')"> | 上线</span>') : "")
//                    (in_array($model["article_status"], [2, 5]) ? ('<span class="handle pointer" onclick="reviewArticle(\'/expert/article/review?expert_articles_id=' . $model["expert_articles_id"] . '\')"> | 审核</span>') : "") .
                return '<div class="am-btn-group am-btn-group-xs"> ' .
                        ($model['article_status'] != 1 && $model['article_type'] != 3 ? ( '<span class="handle pointer" onclick="acticle_preview(' . $model["expert_articles_id"] . ',' . $model['article_type'] . ')">预览 |</span>') : "") .
                        ($model['article_type'] != 3 ? ('<span class="handle pointer" onclick="acticle_edit(\'/expert/article/article-content?expert_articles_id=' . $model["expert_articles_id"] . '\')"> 内容编辑</span>') : "") .
                        ($model["article_status"] == 3 ? ('<span class="handle pointer" onclick="offLine(' . $model["expert_articles_id"] . ')"> | 下线</span>') : "") . '
                       ' . ($model["article_status"] == 3 ? ($model["stick"] == 999 ? ('<span class="handle pointer" onclick="onStick(' . $model["expert_articles_id"] . ',' . $model["stick"] . ')"> | 置顶</span>') : ('<span class="handle pointer" onclick="offStick(' . $model["expert_articles_id"] . ')"> | 取消置顶</span>')) : "") .
                        ($model["report_num"] != 0 ? '<span class="handle pointer" onclick="readRecord(' . $model["expert_articles_id"] . ')"> | 查看举报详情</span>' : "") .
                        '<span class="handle pointer" onclick="acticleUrl(' . $model["expert_articles_id"] . ')"> | 复制链接</span></div>';
            }
        ]
    ],
    'rowOptions' => function($model) {
        return ['class' => $model["report_num"] != 0 ? 'label-ccc' : 'label-white'];
    },
]);
?>
<script >
    function search() {
        var data = $("#filterForm").serialize();
        data += '&article_type=<?php echo isset($_GET['article_type']) ? $_GET['article_type'] : "1"; ?>';
        location.href = "/expert/article/index?" + data;
    }
    function goReset() {
        location.href = "/expert/article/index<?php echo isset($_GET['article_type']) ? ("?article_type=" . $_GET['article_type']) : ""; ?>";
    }
    function acticle_preview(article_id, article_type) {
        var url = '/expert/views/to-preview?article_id=' + article_id + '&article_type=' + article_type;
        modDisplay2({title: '文章预览', url: url, height: 500, width: 600});
    }

    function reviewArticle(url) {
        modDisplay({title: '文章审核', url: url, height: 300, width: 400});
    }
    function acticle_edit(url) {
        modDisplay({title: '内容编辑', url: url, height: 600, width: 800});
    }
    function offLine(expert_articles_id) {
        msgConfirm("提示", "确定下线该文章？", function () {
            $.ajax({
                url: "/expert/article/off-line",
                type: "POST",
                dataType: "json",
                aysnc: false,
                data: {expert_articles_id: expert_articles_id},
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        })
    }
    function onLine(expert_articles_id) {
        msgConfirm("提示", "确定上线该文章？", function () {
            $.ajax({
                url: "/expert/article/on-line",
                type: "POST",
                dataType: "json",
                aysnc: false,
                data: {expert_articles_id: expert_articles_id},
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        })
    }
    //置顶

    function onStick(expert_articles_id, value) {
        msgPrompt("提示", "确定置顶该文章?", value, function () {
            var stick = $("#num").val();
            if (isNaN(parseInt(stick))) {
                msgAlert("请输入合法的数字");
                return false;
            }
            if (stick <= 0 || stick >= 999) {
                msgAlert("请输入0-999的正整数");
                return false;
            }
            $.ajax({
                url: "/expert/article/on-stick",
                type: "POST",
                dataType: "json",
                aysnc: false,
                data: {expert_articles_id: expert_articles_id, stick: stick},
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        })

    }
    //取消置顶
    function offStick(expert_articles_id) {
        msgConfirm("提示", "确定取消置顶该文章？", function () {
            $.ajax({
                url: "/expert/article/off-stick",
                type: "POST",
                dataType: "json",
                aysnc: false,
                data: {expert_articles_id: expert_articles_id},
                success: function (json) {
                    if (json["code"] == 600) {
                        msgAlert(json["msg"], function () {
                            location.reload();
                        });
                    } else {
                        msgAlert(json["msg"]);
                    }
                }
            });
        })

    }

    $(function () {
        $.each($(".inputSort"), function () {
            var id = $(this).attr("name");
            var value = $(this).val();
            if (value != "") {
                $("#" + id).addClass(value);
            } else {
                $("#" + id).addClass("noSort");
            }
        });
        $(".sortTh").click(function () {
            var _this = $(this);
            var name = _this.attr("id");
            var value = "";
            if (_this.hasClass("noSort")) {
                _this.removeClass("noSort");
                _this.addClass("upSort");
                value = "upSort";
            } else if (_this.hasClass("upSort")) {
                _this.removeClass("upSort");
                _this.addClass("downSort");
                value = "downSort";
            } else if (_this.hasClass("downSort")) {
                _this.removeClass("downSort");
                _this.addClass("noSort");
                value = "noSort";
            }
            $("input[name=" + name + "]").val(value);
            search();
        });
    });
    //查看被举报详情
    function readRecord(expert_articles_id) {
        var url = '/expert/article/read-report-record?expert_articles_id=' + expert_articles_id;
        location.href = url;
//        modDisplay({title: '被举报详情', url: url, height: 500, width: 800});
    }
    //查看文章URL地址
    function acticleUrl(expert_articles_id) {
        var url = '/expert/article/read-url?expert_articles_id=' + expert_articles_id;
        modDisplay({title: '文章URL', url: url, height: 200, width: 600});
    }
</script>

