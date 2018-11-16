<?php

namespace app\modules\expert;

/**
 * expert module definition class
 */
class expert extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\expert\controllers';
    public $defaultRoute = 'expert';

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
                "class" => 'app\modules\core\filters\LoginFilter',
//                "except" => [
//                    'expert/login',
//                    'login/index',
//                ],
            ],
            "AuthFilter" => [
                "class" => 'app\modules\core\filters\AuthFilter',
//                "except" => [
//                    'expert/login',
//                    'expert/logout',
//                    'expert/index',
//                    'login/index',
//                ],
                "children" => [
                    /**
                     * 专家列表
                     */
                    "/expert/expert/read-detail" => ["/expert/expert/list"],
//                    "/expert/expert/review" => ["/expert/expert/list", "/expert/expert/list-sys"],
//                    "/expert/expert/no-pass" => ["/expert/expert/list", "/expert/expert/list-sys"],
//                    "/expert/expert/pass" => ["/expert/expert/list", "/expert/expert/list-sys"],
//                    "/expert/expert/pact-status" => ["/expert/expert/list", "/expert/expert/list-sys"],
//                    "/expert/expert/update-pact-status" => ["/expert/expert/list", "/expert/expert/list-sys"],
//                    "/expert/expert/cacel-expert-status" => ["/expert/expert/list", "/expert/expert/list-sys"],
//                    "/expert/expert/enable-expert-status" => ["/expert/expert/list", "/expert/expert/list-sys"],
                    /**
                     * 专家列表(系统后台)
                     */
                    "/expert/expert/read-detail-sys" => ["/expert/expert/list-sys"],
                    /**
                     * 内容管理
                     */
//                    "/expert/article/off-line" => ["/expert/article"],
//                    "/expert/article/on-line" => ["/expert/article"],
//                    "/expert/article/off-stick" => ["/expert/article"],
//                    "/expert/article/on-stick" => ["/expert/article"],
//                    "/expert/article/review" => ["/expert/article"],
//                    "/expert/article/no-pass" => ["/expert/article"],
//                    "/expert/article/pass" => ["/expert/article"],
//                    "/expert/article/article-content" => ["/expert/article"],
//                    "/expert/article/save-article-content" => ["/expert/article"],
                    "/expert/article/get-article-content" => ["/expert/article"],
                    "/expert/views/to-preview" => ["/expert/article"],
                    "/expert/article/read-url" => ["/expert/article"],
                    /**
                     * 统计报表
                     */
                    "/expert/article-report/get-report" => ["/expert/article-report"],
                    "/expert/article-report/get-month-report" => ["/expert/article-report"],
                ]
            ],
        ];
    }

}
