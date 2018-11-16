<?php
if(YII_ENV_DEV){
    return [
            'class' => 'app\modules\components\redis\Connection',
            'hostname' => '114.115.148.102',
            'password' => 'goodluck',
            'port' => 63790,
            'database' => 1,
        ];
}else{
    return [
            'class' => 'app\modules\components\redis\Connection',
            'hostname' => '27.155.105.165',
            'password' => 'gula_lottery_redis',
            'port' => 63790,
            'database' => 1,
        ];
}


