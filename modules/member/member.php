<?php

namespace app\modules\member;


/**
 * member module definition class
 */
class member extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\member\controllers';

    public function init() {
        parent::init();
        $this->SetContainer([
            'app\modules\agents\services\IAgentsService' => 'app\modules\agents\services\AgentsService',
        ]);
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
                     * 会员等级
                     */
//                    "/member/grade/addlevels" => ["/member/grade"],
//                    "/member/grade/editlevels" => ["/member/grade"],
//                    "/member/grade/dellevels" => ["/member/grade"],
                    /**
                     * 会员成长机制
                     */
//                    "/member/growing/addgrowing" => ["/member/growing"],
//                    "/member/growing/editgrowing" => ["/member/growing"],
//                    "/member/growing/delgrowing" => ["/member/growing"],
                    /**
                     * 会员列表
                     */
//                    "/member/list/add-member" => ["/member/list"],
//                    "/member/list/view-member" => ["/member/list"],
//                    "/member/list/edit-member" => ["/member/list"],
//                    "/member/list/review-member" => ["/member/list"],
//                    "/member/list/change-user-type" => ["/member/list"],
//                    "/member/list/delete-member" => ["/member/list"],
//                    "/member/list/edit-status" => ["/member/list"],
//                    "/member/list/addstore" => ["/member/list"],
                    /**
                     * 礼品分类
                     */
//                    "/member/gift-category/addcate" => ["/member/gift-category"],
//                    "/member/gift-category/editcate" => ["/member/gift-category"],
//                    "/member/gift-category/delcate" => ["/member/gift-category"],
                    /**
                     * 礼品列表
                     */
//                    "/member/gift-list/addgift" => ["/member/gift-list"],
//                    "/member/gift-list/delgift" => ["/member/gift-list"],
//                    "/member/gift-list/editgift" => ["/member/gift-list"],
                    "/member/gift-list/get-coupons-detail"=>["/member/gift-list"],
//                    "/member/gift-list/edit-status" => ["/member/gift-list"],
                    "/member/gift-list/read-gift" => ["/member/gift-list"],
                    /**
                     * 咕币兑换
                     */
//                    "/member/redeem/add-exgift" => ["/member/redeem"],
                    "/member/redeem/search" => ["/member/redeem"],
                    /**
                     * 兑换审核
                     */
//                    "/member/exchange-check/review" => ["/member/exchange-check"],
                    "/member/exchange-check/view" => ["/member/exchange-check"],
                    /**
                     * 兑换记录
                     */
                    "/member/exchange-record/view" => ["/member/exchange-record"],
                    "/member/list/read-user-coupons" => ["/member/list"],
                    "/member/list/get-register-from"=>["/member/list"],
                    /**
                     * 会员统计
                     */
                    "/member/user-report/get-report" => ["/member/user-report"],
                    "/member/user-report/get-month-report" => ["/member/user-report"],
                ]
            ],
        ];
    }
    
    private function SetContainer($relation) {
        foreach ($relation as $key => $value) {
            \Yii::$container->set($key, $value);
        }
    }

}
