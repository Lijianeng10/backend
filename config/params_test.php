<?php
return [
    'adminEmail' => 'admin@example.com',
    'qiniu_accessKey' => '4HDEGg2wnuGFaak4MdaoKvgY1Jq5tNv9uBxnSE2C', //七牛access
    'qiniu_secretKey' => 'ens51TYyYiB3FAxHaqM0kojz30_fv7hbRm7PvRSr', //七牛secret
    'qiniu_bucket' => 'lottery-php-online', //七牛存储空间
    'qiniu_link_host' => 'http://ovjj0n6lg.bkt.clouddn.com/', //七牛外链地址
    'lottery_img_host' => 'http://php.javaframework.cn/img/', //图片服务器地址
    'test_amap_create' => 'http://yuntuapi.amap.com/datamanage/data/create', // 高德地图云存储 创建
    'test_amap_update' => 'http://yuntuapi.amap.com/datamanage/data/update', // 高德地图云存储 更新
    'test_amap_delete' => 'http://yuntuapi.amap.com/datamanage/data/delete', // 高德地图云存储 删除
    'test_amap_key' => '6988282bf5cdea91fc799059644a8cf9', // 高德地图云存储 客户唯一标识
    'test_amap_tableid' => '59b11748305a2a4ed727b3eb', // 高德地图云存储 数据表唯一标识
    'test_amap_loctype' => '1', // 高德地图云存储  定位方式 1：经纬度 2：地址
    'userDomain' => "http://php.javaframework.cn", //前台域名
    'java_getRealName' => 'http://114.116.53.168:8081/user/getRealName', // 获取获取会员实名信息
    'java_getAccountDetail' => 'http://114.116.53.168:8081/user/getAccountDetail', // 收款账户信息
    'java_getAuthInfo' => 'http://114.116.53.168:8081/user/getAuthInfo', // 获取实名认证信息
    'java_getAgentsAccount' => 'http://122.114.61.101:8091/new/user/preGenerateUser1', // 获取代理商账户
    'java_getStatus' => 'http://114.116.53.168:8081/user/getStatus', // 获取审核见证宝开户状态
    'sync_im_api' => '27.154.231.142:8081/add', // im api
    'sync_im_token' =>'edb04a228a564813a2b408e54a14a6bc',//im api token
    'jpush_ios' =>false,//极光推送ios开发程序
    'backup_sqlserver' => 'http://27.154.231.142:8088/',//对接sqlserver
	'kafka_borker' => '114.116.52.67:9092',
    'wechat' => [//微信公众号参数
        'token' => 'chenqiwei',
        'redirect_uri' => 'your redirect uri',
        'appid' => 'wx65da375080cf60f2',
        'appsecret' => 'e9678906d75d964e268aa35e1608ec15',
        'mchid' => 'your mchid',
        'key' => 'your key',
        'notifyUrl' => 'wechat notify url',
    ],
    'wechat_sms_tpl_id' => [
        'new_order' => 'XIsaVIzftMbFoDtDIm2ua7gXl748sEmfunlkGWx7v_Y', //门店接单通知
        'order_error' => '', //系统生成订单失败通知
        'sys_error' => 'NdBM0QKSoKtqpYg1V6jxiCCM3JcFnmYhudlDcE-0n5Q', //监控报警通知NdBM0QKSoKtqpYg1V6jxiCCM3JcFnmYhudlDcE-0n5Q
        'out_ticket' => 'QUHoZRTMLbemluLM6kTLTyWsnU9WdweHKqdpfNd4FsM', // 出票提醒购彩者
        'recharge_msg' => 'kYKA9h7wqlcKkpAD-DSqgySwChYx9Z1JV-ZUrwX4Z04', // 充值成功提醒充值者
        'award_order' => 'AvlxTIXUbXukujLznXdw1URrWeLUXJF6aOjC3ZpSdMg', // 投注单开奖结果推送
        'programme_cancel' => 'N2mwTK1_5QypO2lgztHoxDE2Dz7gPOM83EaCKvN8yMM', //合买撤单通知
        'article_review' => 'm1fZD-B6zHOl8uFfOOCiNPWh0wmEe-1dNQ2YGhfEUpM', // 方案文章审核通知
        'campaign_bonus' => 'eQVlnmzOALl0oLLyVlwXK16h7SHsufJJ2Vy4rDQL3_Y', // 发放活动奖金通知
        'untreated_bonus' => 'lWyfxkeAao4rdzq9_rm0VX-j0ODWVyisUccVZ-ayNdQ', // 未处理派奖订单通知
        'recharge_msg_apiuer' => 'WDAakQQxpn7cfV0hwZkXqp65OukeKFP1HD9F4eD7fR8', //流量方用户充值提现申请
    ],
    'chat_push_ip' => 'http://27.154.231.142:8000/publish_msg',//群消息推送
    'chat_sqlserver' => 'http://27.154.231.142:8000', // 聊天服务器
    'banner_url'=>'php.javaframework.cn',//广告判断内外部url
	
];
