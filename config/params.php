<?php

return [
    'adminEmail' => 'admin@example.com',
    'qiniu_accessKey' => '4HDEGg2wnuGFaak4MdaoKvgY1Jq5tNv9uBxnSE2C', //七牛access
    'qiniu_secretKey' => 'ens51TYyYiB3FAxHaqM0kojz30_fv7hbRm7PvRSr', //七牛secret
    'qiniu_bucket' => 'lottery-php-online', //七牛存储空间
    'qiniu_link_host' => 'https://imglottery.goodluckchina.net/', //七牛外链地址
    'lottery_img_host' => 'https://caipiao-sys.goodluckchina.net/img/', //图片服务器地址
    'test_amap_create' => 'http://yuntuapi.amap.com/datamanage/data/create', // 高德地图云存储 创建
    'test_amap_update' => 'http://yuntuapi.amap.com/datamanage/data/update', // 高德地图云存储 更新
    'test_amap_delete' => 'http://yuntuapi.amap.com/datamanage/data/delete', // 高德地图云存储 删除
    'test_amap_key' => '6988282bf5cdea91fc799059644a8cf9', // 高德地图云存储 客户唯一标识
    'test_amap_tableid' => '5954b2b6afdf521e865f3711', // 高德地图云存储 数据表唯一标识
    'test_amap_loctype' => '1', // 高德地图云存储  定位方式 1：经纬度 2：地址
    'userDomain' => "https://caipiao.goodluckchina.net", //前台域名
    'java_getRealName' => 'http://27.155.105.155:8099/user/getRealName', // 获取会员实名信息 
    'java_getAccountDetail' => 'http://27.155.105.155:8099/user/getAccountDetail', // 收款账户信息
    'java_getAuthInfo' => 'http://27.155.105.155:8099/user/getAuthInfo', // 获取实名认证信息
    'java_getAgentsAccount' => 'http://27.155.105.155:8098/new/user/preGenerateUser1', // 获取代理商账户
    'java_getStatus' => 'http://27.155.105.155:8099/user/getStatus', // 获取审核见证宝开户状态
    'sync_im_api' => '10.155.105.176:8081/add', // im api
    'sync_im_token' =>'edb04a228a564813a2b408e54a14a6bc',//im api token
    'jpush_ios' =>true,//极光推送ios正式程序
	'zmf_venderId' => '18020601', //销售商代码
	'zmf_url' => 'http://120.77.204.131:8098/', //智魔方URL
	'zmf_key' => '56B06065B5237AF34DBBCBF8', //智魔方密钥
	'kafka_borker' => '27.155.105.61:9092,27.155.105.62:9092,27.155.105.63:9092',
    'backup_sqlserver' => 'http://10.155.105.177:8088/',//对接sqlserver
    'wechat' => [//微信公众号参数
        'token' => 'gula_lottery',
        'redirect_uri' => 'your redirect uri',
        'appid' => 'wxcf1a8e12305ba8bb',
        'appsecret' => '292c59031d420f766018407ae13cb05e',
        'mchid' => 'your mchid',
        'key' => 'your key',
        'notifyUrl' => 'wechat notify url',
    ],
    'wechat_sms_tpl_id' => [
        'new_order' => 'J_GNq3_JnTC-RVjWInqaxa83zrQaPh2syIi2Jdq7SFg', //门店接单通知
        'order_error' => '', //系统生成订单失败通知
        'sys_error' => '8HLAG3RQ467F_95BuDwuBd887joPmjU9FDqdzBplpD0', //监控报警通知8HLAG3RQ467F_95BuDwuBd887joPmjU9FDqdzBplpD0
        'out_ticket' => 'deHg-sNWmoWMNoqz1oQNaaCpNpjFdOIDW7LqGMbjvR0', // 出票提醒购彩者
        'recharge_msg' => 'UrEQ_MFER1t5Rv_TBVJhwKlj-jyB3-7qkaHPfttI_5E', // 充值成功提醒充值者
        'award_order' => '6EYDMTSa8RJaUgiiBkwki_tw0XwLXQe6QH2Lg71Vpv0', // 投注单开奖结果推送
        'programme_cancel' => 'APKhQBTATCw-WpBrIaDT-xNNKPNrgf3n3T5Pp0OfDSM', // 合买撤单通知
        'article_review' => 'm8tcsrFTSGcJ7FT5Jdv0S0NnnaoJAMTBHFKOUIneZnY', // 方案文章审核通知
        'campaign_bonus' => 'AsxwFkWxm2FPND1Wm1-Sy3rpCt1wqZ_pK997CXxxqNY', // 发放活动奖金通知
        'untreated_bonus' => 'qrWkxL4radpRQryLdpqbQLIbqZZjPDg0nBEmL_RAZpM', // 未处理派奖订单通知
        'recharge_msg_apiuer' => 'H40Fnwy68VPQBhFaHvh3lJrOVu2Hf3jrTCIsBTlcgEI', //流量方用户充值提现申请
    ],
    'chat_push_ip' => 'http://10.155.105.176/publish_msg',//群消息推送
    'chat_sqlserver' => 'http://10.155.105.176', // 聊天服务器
    'banner_url'=>'caipiao.goodluckchina.net',//广告判断内外部url
];
