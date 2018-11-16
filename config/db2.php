<?php

if (YII_ENV == "dev") {

    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=114.116.52.67:3306;dbname=lottery_log',
        'username' => 'gula',
        'password' => 'guLA_27EcKelE9',
        'charset' => 'utf8mb4',
    ];
} else {

    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=114.116.52.67:3306;dbname=lottery_log',
        'username' => 'gula',
        'password' => 'guLA_27EcKelE9',
        'charset' => 'utf8mb4',
    ];
}
