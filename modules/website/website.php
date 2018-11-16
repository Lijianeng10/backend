<?php

namespace app\modules\website;

/**
 * modules module definition class
 */
class website extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\website\controllers';
//    public $quanAry=[
//         "/admin/admin/editadmin" => ["/admin/admin"],
//        "/website/bananer/edit-status" => ["/website/bananer"],
//    ];

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        // custom initialization code goes here
    }

    public function behaviors() {
         return [
            "LoginFilter" => [
                "class" => 'app\modules\core\filters\LoginFilter'
            ],
            "AuthFilter" => [
                "class" => 'app\modules\core\filters\AuthFilter',
                "children" => [
                    /**
                     * App推送日志
                     */
                    "/website/applog/add-app-log" => ["/website/applog"],
                    /**
                     * 广告管理
                     */
//                    "/website/bananer/add-bananer" => ["/website/bananer"],
//                    "/website/bananer/edit-status" => ["/website/bananer"],
//                    "/website/bananer/del-bananer" => ["/website/bananer"],
//                    "/website/bananer/edit-bananer" => ["/website/bananer"],
                    /**
                     * 配置活动
                     */
                    "/website/activity/get-batch" => ["/website/activity"],
                    "/website/activity/view-coupons" => ["/website/activity"],
                    /**
                     * 电子优惠券
                     */
//                    "/member/coupons/addview" => ["/member/coupons"],
//                    "/website/coupons/view-detail" => ["/website/coupons"],
//                    "/member/coupons/editstatus" => ["/member/coupons"],
                    "/website/coupons/read-detail" => ["/website/coupons"],
//                    "/member/coupons/send-coupons" => ["/member/coupons"],
                    "/website/coupons/get-user-info" => ["/website/coupons"],
//                    "/member/coupons/edit-coupons" => ["/member/coupons"],
//                    "/member/coupons/edit-coupons-detail" => ["/member/coupons"],
                    "/website/coupons/get-custno" => ["/website/coupons"],
                    "/website/chat-push/read-chat-push" => ["/website/chat-push"],
                    /**
                     * 访问数据
                     */
                    "/website/loan-access/get-access" => ["/website/loan-access"],
                    "/website/loan-access/get-access-detail" => ["/website/loan-access"],
                    "/website/credit-access/get-access" => ["/website/credit-access"],
                    "/website/credit-access/get-access-detail" => ["/website/credit-access"],
                ]
            ],
        ];
    }

}
