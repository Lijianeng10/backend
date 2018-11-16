<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\member\helpers\Constants;
?>
<form action="/member/list/read-user">
    <?php
    echo "<ul class='third_team_ul'>";
    echo Html::input("hidden", "cust_no", isset($get["cust_no"]) ? $get["cust_no"] : $cust_no);
    echo '<li>';
    echo Html::label("会员信息", "user_info", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["class" => "form-control", "placeholder" => "会员编号、名称、手机号", "style" => "width:200px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("推广员  ", "", ["style" => "margin-left:29px;"]);
    echo Html::dropDownList("spread_type", isset($get["spread_type"]) ? $get["spread_type"] : "", $spreadType, ["class" => "form-control", "style" => "width:70px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::submitButton("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;"]);
    echo Html::input("reset", '', '重置', ["class" => "am-btn am-btn-primary", "id" => "reset", "style" => "margin-left:5px;"]);
    echo Html::button("返回", ["class" => "am-btn am-btn-primary inputLimit", "id" => "backSubmit"]);
    echo Html::button("新增下级", ["class" => "am-btn am-btn-primary inputLimit", "id" => "addSpread"]);
    echo '</li>';
    echo "</ul>";
    ?>
</form>
<input type="hidden" value="<?php echo $cust_no;?>" id="cust_no">
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '咕啦编号',
            'value' => 'cust_no'
        ],
        [
            'label' => '昵称',
            'value' => 'user_name'
        ],
        [
            'label' => '手机号',
            'value' => 'user_tel'
        ],
        [
            'label' => '推广层级',
            'value' => function($model){
                return $model["spread_type"]==0?"否":$model["spread_type"]."级推广";
            }
        ],
        [
            'label' => '返点',
            'value' => 'rebate'
        ],
        [
            'label' => '关系树',
            'value' => 'p_tree'
        ], [
            'label' => '注册时间',
            'value' => 'create_time',
        ],[
            'label' => '操作',
            'format' => 'raw',
            'value' => function ($model) {
                $str = '<div class="am-btn-toolbar">
                            <div class="am-btn-group am-btn-group-xs opts">'.($model["spread_type"] != 0 ?'<span class="handle pointer" onclick="readUser(\'' . $model['cust_no'].'\');"> 我的推广 </span>':'').'</div>';
                return $str;
            }
        ]
    ],
])
?>
<script>
    $("#backSubmit").click(function () {
        history.go(-1);
    })
    //新增推广用户
    $("#addSpread").click(function(){
        var cust_no=$("#cust_no").val();
        if(cust_no==""){
            msgAlert("用户编号有误,操作错误")
        }else{
            modDisplay({title: '新增下级用户', url: '/member/list/add-spread?cust_no=' + cust_no, height: 200, width: 400});
        }

    })
    //重置
    $('#reset').click(function () {
        var cust_no=$("#cust_no").val();
        location.href = '/member/list/read-user?cust_no=' + cust_no;
    });
    function readUser(custNo) {
        location.href = '/member/list/read-user?cust_no=' + custNo;
    }
</script>
