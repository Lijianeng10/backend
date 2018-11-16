<?php

namespace app\modules\common\helpers;

require_once \Yii::$app->basePath . '/vendor/jpush/jpush/autoload.php';

use Yii;
use JPush\Client;
use JPush\PushPayload;

class Jpush {

    public function TestJpushTimeNotice($title, $msg, $url, $time) {
        $jpush = new Client();
        try {
            $payload = $jpush->push()
                    ->setPlatform(array('android'))
                    ->addAlias(array("gl00005091_test"))
                    ->androidNotification($msg, array(
                        "title" => $title,
                        'extras' => [
                            "url" => $url,
                        ]
                    ))
                    ->iosNotification(array(
                        "title" => $title,
                        "body" => $msg,
                            ), array(
                        'badge' => '+1',
                        'content-available' => true,
                        'mutable-content' => true,
                        'extras' => [
                            "url" => $url,
                        ]
                    ))
                    ->options(array(
                        // sendno: 表示推送序号，纯粹用来作为 API 调用标识，
                        // API 返回时被原样返回，以方便 API 调用方匹配请求与返回
                        // 这里设置为 100 仅作为示例
                        // 'sendno' => 100,
                        // time_to_live: 表示离线消息保留时长(秒)，
                        // 推送当前用户不在线时，为该用户保留多长时间的离线消息，以便其上线时再次推送。
                        // 默认 86400 （1 天），最长 10 天。设置为 0 表示不保留离线消息，只有推送当前在线的用户可以收到
                        // 这里设置为 1 仅作为示例
                        // 'time_to_live' => 1,
                        // apns_production: 表示APNs是否生产环境，
                        // True 表示推送生产环境，False 表示要推送开发环境；如果不指定则默认为推送生产环境
                        'apns_production' => false,
                            // big_push_duration: 表示定速推送时长(分钟)，又名缓慢推送，把原本尽可能快的推送速度，降低下来，
                            // 给定的 n 分钟内，均匀地向这次推送的目标用户推送。最大值为1400.未设置则不是定速推送
                            // 这里设置为 1 仅作为示例
                            // 'big_push_duration' => 1
                    ))
//                     ->send();
                    ->build();
            // 创建在指定时间点触发的定时任务
            $response = $jpush->schedule()->createSingleSchedule("指定时间点的定时任务", $payload, array("time" => $time));
            return $response;
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            return $e;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            return $e;
        }
    }

    public function TestJpushNotice($title, $msg, $url) {
        $jpush = new Client();
        try {
            $payload = $jpush->push()
                    ->setPlatform(array('android'))
                    ->addAlias(array("gl00005091_test"))
                    ->androidNotification($msg, array(
                        "title" => $title,
                        'extras' => [
                            "url" => $url,
                        ]
                    ))
                    ->iosNotification(array(
                        "title" => $title,
                        "body" => $msg,
                            ), array(
                        'badge' => '+1',
                        'content-available' => true,
                        'mutable-content' => true,
                        'extras' => [
                            "url" => $url,
                        ]
                    ))
                    ->options(array(
                        'apns_production' => false,
                    ))
                    ->send();
            return $payload;
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            return $e;
        } catch (\JPush\Exceptions\APIRequestException $e) {
            return $e;
        }
    }

    /**
     * 自定义立即推送通知
     * @param title 通知标题
     * @param msg 通知内容
     * @param $url 跳转url
     */
    public function AddJpushNotice($title, $msg, $url) {
        $iosFlag = \Yii::$app->params["jpush_ios"];
        $jpush = new Client();
        if ($title == "") {
            try {
                $response = $jpush->push()
                        ->setPlatform('all')
                        ->addAllAudience()
                        ->androidNotification($msg, array(
                            'extras' => [
                                "url" => $url,
                            ]
                        ))
                        ->iosNotification(array(
                            "body" => $msg,
                                ), array(
                            'badge' => '+1',
                            'content-available' => true,
                            'mutable-content' => true,
                            'extras' => [
                                "url" => $url,
                            ]
                        ))
                        ->options(array(
                            'apns_production' => $iosFlag,
                        ))
                        ->send();
                return $response;
            } catch (\JPush\Exceptions\APIConnectionException $e) {
                return $e;
            } catch (\JPush\Exceptions\APIRequestException $e) {
                return $e;
            }
        } else {
            try {
                $response = $jpush->push()
                        ->setPlatform('all')
                        ->addAllAudience()
                        ->androidNotification($msg, array(
                            "title" => $title,
                            'extras' => [
                                "url" => $url,
                            ]
                        ))
                        ->iosNotification(array(
                            "title" => $title,
                            "body" => $msg,
                                ), array(
                            'badge' => '+1',
                            'content-available' => true,
                            'mutable-content' => true,
                            'extras' => [
                                "url" => $url,
                            ]
                        ))
                        ->options(array(
                            'apns_production' => $iosFlag,
                        ))
                        ->send();
                return $response;
            } catch (\JPush\Exceptions\APIConnectionException $e) {
                return $e;
            } catch (\JPush\Exceptions\APIRequestException $e) {
                return $e;
            }
        }
    }

    /**
     * 自定义定时推送通知
     * @param title 通知标题
     * @param msg 通知内容
     * @param $url 跳转url
     * @param $time 推送时间
     */
    public function AppJpushTimeNotice($title, $msg, $url, $time) {
        $iosFlag = \Yii::$app->params["jpush_ios"];
        $jpush = new Client();
        if ($title == "") {
            try {
                $payload = $jpush->push()
                        ->setPlatform('all')
                        ->addAllAudience()
                        ->androidNotification($msg, array(
                            'extras' => [
                                "url" => $url,
                            ]
                        ))
                        ->iosNotification(array(
                            "body" => $msg,
                                ), array(
                            'badge' => '+1',
                            'content-available' => true,
                            'mutable-content' => true,
                            'extras' => [
                                "url" => $url,
                            ]
                        ))
                        ->options(array(
                            'apns_production' => $iosFlag,
                        ))
                        ->build();
                // 创建在指定时间点触发的定时任务
                $response = $jpush->schedule()->createSingleSchedule("指定时间点的定时任务", $payload, array("time" => $time));
                return $response;
            } catch (\JPush\Exceptions\APIConnectionException $e) {
                return $e;
            } catch (\JPush\Exceptions\APIRequestException $e) {
                return $e;
            }
        } else {
            try {
                $payload = $jpush->push()
                        ->setPlatform('all')
                        ->addAllAudience()
                        ->androidNotification($msg, array(
                            "title" => $title,
                            'extras' => [
                                "url" => $url,
                            ]
                        ))
                        ->iosNotification(array(
                            "title" => $title,
                            "body" => $msg,
                                ), array(
                            'badge' => '+1',
                            'content-available' => true,
                            'mutable-content' => true,
                            'extras' => [
                                "url" => $url,
                            ]
                        ))
                        ->options(array(
                            'apns_production' => $iosFlag,
                        ))
                        ->build();
                // 创建在指定时间点触发的定时任务
                $response = $jpush->schedule()->createSingleSchedule("指定时间点的定时任务", $payload, array("time" => $time));
                return $response;
            } catch (\JPush\Exceptions\APIConnectionException $e) {
                return $e;
            } catch (\JPush\Exceptions\APIRequestException $e) {
                return $e;
            }
        }
    }

}
