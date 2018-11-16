<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>
<div style="font-size:14px;">
    <?php
    $type = [ 
        "0" => "请选择",
        "1" => "门店出票通知",
        "2" => "投注结果通知",
        "3" => "充值结果通知",
        "4" => "合买未满员通知",
        "5" => "开奖结果通知",
        "6" => "文章审核结果通知",
        "7" => "发放活动奖金通知",
        "8" => "中奖未派奖通知门店"
    ];
    echo "<ul class='third_team_ul'>";
    echo '<li >';
    echo Html::label("用户信息", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "user_info", isset($get["user_info"]) ? $get["user_info"] : "", ["id" => "user_info", "class" => "form-control", "placeholder" => "用户信息", "style" => "width:200px;display:inline;margin-left:10px;"]);
    echo '</li>';
    echo '<li >';
    echo Html::label("订单编号", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "order_code", isset($get["order_code"]) ? $get["order_code"] : "", ["id" => "order_code", "class" => "form-control", "placeholder" => "订单编号", "style" => "width:200px;display:inline;margin-left:10px;"]);
    echo '</li>';
    echo '<li >';
    echo Html::label("门店信息", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "store_info", isset($get["store_info"]) ? $get["store_info"] : "", ["id" => "store_info", "class" => "form-control", "placeholder" => "门店名称，省份", "style" => "width:200px;display:inline;margin-left:10px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("类型  ", "", ["style" => "margin-left:46px;"]);
    echo Html::dropDownList("type", isset($get["type"]) ? $get["type"] : "", $type, ["id" => "type", "class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("推送日期  ", "", ["style" => "margin-left:15px;"]);
    echo Html::input("input", "startdate", isset($get["startdate"]) ? $get["startdate"] : "", ["id" => "startdate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "开始日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo "-";
    echo Html::input("input", "enddate", isset($get["enddate"]) ? $get["enddate"] : "", ["id" => "enddate","class" => "form-control", "data-am-datepicker" => "", "placeholder" => "结束日期", "style" => "width:80px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::label("推送结果  ", "", ["style" => "margin-left:15px;"]);
    echo Html::dropDownList("status", isset($get["status"]) ? $get["status"] : "", $status, ["id" => "status", "class" => "form-control", "style" => "width:120px;display:inline;margin-left:5px;"]);
    echo '</li>';
    echo '<li>';
    echo Html::button("搜索", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:24px;", "onclick" => "search();"]);
    echo Html::button("重置", ["class" => "search am-btn am-btn-primary", "style" => "margin-left:5px;", "onclick" => "goReset();"]);
    echo '</li>';
    echo "</ul>";
    ?>
</div>
<?php
echo GridView::widget([
    "dataProvider" => $data,
    "columns" => [
//        [
//            'label' => '',
//            'format' => 'raw',
//            'value' => function($model) {
//                return Html::input('checkbox', 'delSelect', $model["admin_id"], ["class" => 'delSelect']);
//            }
//                ], 
        [ 'class' => 'yii\grid\SerialColumn'],
        [
            'label' => '会员编号',
            'value' => 'cust_no'
        ],
        [
            'label' => '会员昵称',
            'value' => 'user_name'
        ], [
            'label' => '会员电话',
            'value' => 'user_tel'
        ],
        [
            'label' => '类型',
            'value' => function($model) {
                $type = [
                    "1" => "门店出票通知",
                    "2" => "投注结果通知",
                    "3" => "充值结果通知",
                    "4" => "合买未满员通知",
                    "5" => "开奖结果通知",
                    "6" => "文章审核结果通知",
                    "7" => "发放活动奖金通知",
                    "8" => "中奖未派奖通知门店"
                ];
                return $type[$model['type']];
            }
            ],[
                    'label' => '订单号',
                    'format' => 'raw',
                    'value' => function ($model){
                        $info=json_decode($model['msg_data']);
                        if(!empty( $info->orderId)){
                             return $info->orderId;
                        }else{
                            return "";
                        }
                    }
                ],[
                    'label' => '门店名称',
                    'value' => 'store_name'
                ],[
                    'label' => '所属省份',
                    'value' => 'province'
                ], [
                    'label' => '推送消息',
                    'format' => 'raw',
                    'value' => function ($model){
                        $info=json_decode($model['msg_data']);
                        if(!empty( $info->title)){
                             return $info->title;
                        }else{
                            return "";
                        }
                    }
                ],
                 [
                    'label' => '推送时间',
                    'value' => 'create_time'
                ], [
                    'label' => '推送状态消息',
                    'value' => function($model){
                        $info=json_decode($model['msg_data']);
                        return isset($info->errmsg)?$info->errmsg:"";
                    }
                ],[
                    'label' => '推送状态Code',
                    'value' => function($model){
                        $info=json_decode($model['msg_data']);
                        $data="";
                        $errAry=[
                            "-1"=>"系统繁忙",
                            "0"=>"请求成功",
                            "40001"=>"获取access_token时Secret错误，或者access_token无效",
                            "40002"=>"不合法的凭证类型",
                            "40003"=>"不合法的UserID",
                            "40004"=>"不合法的媒体文件类型",
                            "40005"=>"不合法的文件类型",
                            "40006"=>"不合法的文件大小",
                            "40007"=>"不合法的媒体文件id",
                            "40008"=>"不合法的消息类型",
                            "40013"=>"不合法的corpid",
                            "40014"=>"不合法的access_token",
                            "40015"=>"不合法的菜单类型",
                            "40016"=>"不合法的按钮个数",
                            "40017"=>"不合法的按钮类型",
                            "40018"=>"不合法的按钮名字长度",
                            "40019"=>"不合法的按钮KEY长度",
                            "40020"=>"不合法的按钮URL长度",
                            "40021"=>"不合法的菜单版本号",
                            "40022"=>"不合法的子菜单级数",
                            "40023"=>"不合法的子菜单按钮个数",
                            "40024"=>"不合法的子菜单按钮类型",
                            "40025"=>"不合法的子菜单按钮名字长度",
                            "40026"=>"不合法的子菜单按钮KEY长度",
                            "40027"=>"不合法的子菜单按钮URL长度",
                            "40028"=>"不合法的自定义菜单使用员工",
                            "40029"=>"不合法的oauth_code",
                            "40031"=>"不合法的UserID列表",
                            "40032"=>"不合法的UserID列表长度",
                            "40033"=>"不合法的请求字符，不能包含\\uxxxx格式的字符",
                            "40035"=>"不合法的参数",
                            "40037"=>"模板未找到",
                            "40038"=>"不合法的请求格式",
                            "40039"=>"不合法的URL长度",
                            "40040"=>"不合法的插件token",
                            "40041"=>"不合法的插件id",
                            "40042"=>"不合法的插件会话",
                            "40048"=>"url中包含不合法domain",
                            "40054"=>"不合法的子菜单url域名",
                            "40055"=>"不合法的按钮url域名",
                            "40056"=>"不合法的agentid",
                            "40057"=>"不合法的callbackurl",
                            "40058"=>"不合法的红包参数",
                            "40059"=>"不合法的上报地理位置标志位",
                            "40060"=>"设置上报地理位置标志位时没有设置callbackurl",
                            "40061"=>"设置应用头像失败",
                            "40062"=>"不合法的应用模式",
                            "40063"=>"红包参数为空",
                            "40064"=>"管理组名字已存在",
                            "40065"=>"不合法的管理组名字长度",
                            "40066"=>"不合法的部门列表",
                            "40067"=>"标题长度不合法",
                            "40068"=>"不合法的标签ID",
                            "40069"=>"不合法的标签ID列表",
                            "40070"=>"列表中所有标签（用户）ID都不合法",
                            "40071"=>"不合法的标签名字，标签名字已经存在",
                            "40072"=>"不合法的标签名字长度",
                            "40073"=>"不合法的openid",
                            "40074"=>"news消息不支持指定为高保密消息",
                            "41001"=>"缺少access_token参数",
                            "41002"=>"缺少corpid参数",
                            "41003"=>"缺少refresh_token参数",
                            "41004"=>"缺少secret参数",
                            "41005"=>"缺少多媒体文件数据",
                            "41006"=>"缺少media_id参数",
                            "41007"=>"缺少子菜单数据",
                            "41008"=>"缺少oauth code",
                            "41009"=>"缺少UserID",
                            "41010"=>"缺少url",
                            "41011"=>"缺少agentid",
                            "41012"=>"缺少应用头像mediaid",
                            "41013"=>"缺少应用名字",
                            "41014"=>"缺少应用描述",
                            "41015"=>"缺少Content",
                            "41016"=>"缺少标题",
                            "41017"=>"缺少标签ID",
                            "41018"=>"缺少标签名字",
                            "42001"=>"access_token超时",
                            "42002"=>"refresh_token超时",
                            "42003"=>"oauth_code超时",
                            "42004"=>"插件token超时",
                            "43001"=>"需要GET请求",
                            "43002"=>"需要POST请求",
                            "43003"=>"需要HTTPS",
                            "43004"=>"需要接收者关注",
                            "43005"=>"需要好友关系",
                            "43006"=>"需要订阅",
                            "43007"=>"需要授权",
                            "43008"=>"需要支付授权",
                            "43009"=>"需要员工已关注",
                            "43010"=>"需要处于回调模式",
                            "43011"=>"需要企业授权",
                            "44001"=>"多媒体文件为空",
                            "44002"=>"POST的数据包为空",
                            "44003"=>"图文消息内容为空",
                            "44004"=>"文本消息内容为空",
                            "45001"=>"多媒体文件大小超过限制",
                            "45002"=>"消息内容超过限制",
                            "45003"=>"标题字段超过限制",
                            "45004"=>"描述字段超过限制",
                            "45005"=>"链接字段超过限制",
                            "45006"=>"图片链接字段超过限制",
                            "45007"=>"语音播放时间超过限制",
                            "45008"=>"图文消息超过限制",
                            "45009"=>"接口调用超过限制",
                            "45010"=>"创建菜单个数超过限制",
                            "45015"=>"回复时间超过限制",
                            "45016"=>"系统分组，不允许修改",
                            "45017"=>"分组名字过长",
                            "45018"=>"分组数量超过上限",
                            "45024"=>"账号数量超过上限",
                            "46001"=>"不存在媒体数据",
                            "46002"=>"不存在的菜单版本",
                            "46003"=>"不存在的菜单数据",
                            "46004"=>"不存在的员工",
                            "47001"=>"解析JSON/XML内容错误",
                            "48002"=>"Api禁用",
                            "50001"=>"redirect_uri未授权",
                            "50002"=>"员工不在权限范围",
                            "50003"=>"应用已停用",
                            "50004"=>"员工状态不正确（未关注状态）",
                            "50005"=>"企业已禁用",
                            "60001"=>"部门长度不符合限制",
                            "60002"=>"部门层级深度超过限制",
                            "60003"=>"部门不存在",
                            "60004"=>"父亲部门不存在",
                            "60005"=>"不允许删除有成员的部门",
                            "60006"=>"不允许删除有子部门的部门",
                            "60007"=>"不允许删除根部门",
                            "60008"=>"部门名称已存在",
                            "60009"=>"部门名称含有非法字符",
                            "60010"=>"部门存在循环关系",
                            "60011"=>"管理员权限不足，（user/department/agent）无权限",
                            "60012"=>"不允许删除默认应用",
                            "60013"=>"不允许关闭应用",
                            "60014"=>"不允许开启应用",
                            "60015"=>"不允许修改默认应用可见范围",
                            "60016"=>"不允许删除存在成员的标签",
                            "60017"=>"不允许设置企业",
                            "60102"=>"UserID已存在",
                            "60103"=>"手机号码不合法",
                            "60104"=>"手机号码已存在",
                            "60105"=>"邮箱不合法",
                            "60106"=>"邮箱已存在",
                            "60107"=>"微信号不合法",
                            "60108"=>"微信号已存在",
                            "60109"=>"QQ号已存在",
                            "60110"=>"部门个数超出限制",
                            "60111"=>"UserID不存在",
                            "60112"=>"成员姓名不合法",
                            "60113"=>"身份认证信息（微信号/手机/邮箱）不能同时为空",
                            "60114"=>"性别不合法",
                        ];
                        if(isset($info->errcode)&&!empty($info->errcode)){
                            $data =$errAry[$info->errcode];
                            return $info->errcode."(".$data.")";
                        }else{
                            return "";
                        }
                        
                    }
                ],[
                    'label' => '推送结果',
                    'value' => function($model){
                        $res=[
                          "1"=>"成功",
                           "2"=>"失败"
                        ];
                        return isset($model["status"])?$res[$model["status"]]:"";
                    }
                ],
//                        [
//                    'label' => '操作',
//                    'format' => 'raw',
//                    'value' => function($model) {
//                        return '<div class="am-btn-group am-btn-group-xs">
//                            <span class="handle pointer" onclick="readAdmin(' . $model["admin_id"] . ');">查看用户</span>
//                            <span class="handle pointer" onclick="editAdmin(' . $model["admin_id"] . ');">| 编辑</span>
//                            <span class="handle pointer" onclick="statusAdmin(' . $model["admin_id"] . ');">| '.($model["status"]?"停用":"启用").'</span>
//                            <span class="handle pointer" onclick="deleteAdmin(' . $model["admin_id"] . ');">| 删除</span>
//                        </div>';
//                    }
//                ]
            ]
        ]);
                
        ?>
<script>
    function search(){
        var  user_info = $("#user_info").val();
        var  order_code = $("#order_code").val();
        var  type = $("#type").val();
        var  startdate = $("#startdate").val();
        var  enddate = $("#enddate").val();
        var  status = $("#status").val();
        var  store_info = $("#store_info").val();
        location.href = '/website/wxlog/index?user_info='+user_info+"&order_code="+order_code+"&type="+type+"&startdate="+startdate+"&enddate="+enddate+"&status="+status+"&store_info="+store_info;
    }
    function goReset(){
        location.href = '/website/wxlog/index';
    }
</script>