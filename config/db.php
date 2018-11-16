<?php

if (YII_ENV == "dev") {

    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=114.115.148.102;dbname=gl_lottery_php',
        'username' => 'root',
        'password' => 'chenqiwei',
        'charset' => 'utf8mb4',
    ];
} else {

return [
        'class' => 'yii\db\Connection',
        // 主库的配置
        'dsn' => 'mysql:host=10.155.105.164;dbname=gl_lottery_php',
        'username' => 'coder',
        'password' => 'gula_lottery_coder',
        'charset' => 'utf8',

//        // 从库的通用配置
//        'slaveConfig' => [
//            'username' => 'root',
//            'password' => 'chenqiwei',
//            'charset' => 'utf8',
//            'attributes' => [
//                // use a smaller connection timeout
//                PDO::ATTR_TIMEOUT => 10,
//            ],
//        ],
//        // 从库配置列表
//        'slaves' => [
//            ['dsn' => 'mysql:host=10.155.105.178;dbname=gl_lottery_php'],
//        ],
    ];
	
}
