<?php

namespace app\modules\agents;

/**
 * modules module definition class
 */
class agents extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\agents\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $this->SetContainer([
            'app\modules\agents\services\IAgentsService' => 'app\modules\agents\services\AgentsService',
        ]);
    }

    public function behaviors() {
        return [
            "LoginFilter" => [
                "class" => 'app\modules\core\filters\LoginFilter',
                 "except" => [
                    'login/index',
                    'login/login',
                ],
            ],
            "AuthFilter" => [
                "class" => 'app\modules\core\filters\AuthFilter',
                "except" => [
                    'login/index',
                    'login/logout',
                    'login/login',
                    'login/success'
                ],
                "children" => [
                    /**
                     * 下级代理商管理
                     */
//                    "/agents/subagents/addagents" => ["/agents/subagents"],
//                    "/agents/subagents/review-agents" => ["/agents/subagents"],
//                    "/agents/subagents/edituse" => ["/agents/subagents"],
                    "/agents/subagents/readagents" => ["/agents/subagents"],
//                    "/agents/subagents/editinfo" => ["/agents/subagents"],
//                    "/agents/subagents/add-agents-ip" => ["/agents/subagents"],
//                    "/agents/subagents/edit-ip-sta" => ["/agents/subagents"],
//                    "/agents/subagents/edit-agents-info" => ["/agents/subagents"],
                    "/agents/orderlist/readdetail12" => ["/agents/orderlist"],
                    "/agents/orderlist/readdetail3" => ["/agents/orderlist"],
                    "/agents/orderlist/readdetail4" => ["/agents/orderlist"],
                    "/agents/orderlist/lan-detail" => ["/agents/orderlist"],
                    "/agents/orderlist/get-deatail-list" => ["/agents/orderlist"],
                    "/agents/orderlist/get-more-detail" => ["/agents/orderlist"],
                    /**
                     * 门店信息管理
                     */
//                    "/agents/stores/addstore" => ["/agents/stores"],
//                    "/agents/stores/validate" => ["/agents/stores"],
//                    "/agents/stores/review-store" => ["/agents/stores"],
                    "/agents/stores/readstore" => ["/agents/stores"],
//                    "/agents/stores/editstore" => ["/agents/stores"],
//                    "/agents/stores/status-change" => ["/agents/stores"],
//                    "/agents/stores/delete-store" => ["/agents/stores"],
//                    "/agents/stores/flagship-store" => ["/agents/stores"],
//                    "/agents/stores/edit-consignee" => ["/agents/stores"],
                    "/agents/stores/get-user-info" => ["/agents/stores"],
//                     "/agents/stores/money-change" => ["/agents/stores"],
//                     "/agents/stores/savestore" => ["/agents/stores"],
//                     "/agents/stores/edit-operator-status" => ["/agents/stores"],
//                    "/agents/stores/business-change" => ["/agents/stores"],
//                    "/agents/stores/set-weight" => ["/agents/stores"],
                    /**
                     * 地图管理
                     */
//                    "/agents/amap/delete-amap" => ["/agents/amap"],
//                    "/agents/amap/create-amap" => ["/agents/amap"],
                    /**
                     * 合作商管理
                     */
//                    "/agents/bussiness/add-bussiness" => ["/agents/bussiness"],
//                    "/agents/bussiness/add-bussiness-ip" => ["/agents/bussiness"],
                    "/agents/bussiness/read-bussiness" => ["/agents/bussiness"],
//                    "/agents/bussiness/edituse" => ["/agents/bussiness"],
//                    "/agents/bussiness/edit-ip-sta" => ["/agents/bussiness"],
//                    "/agents/bussiness/allotment-api" => ["/agents/bussiness"],
//                    "/agents/bussiness/bind-user" => ["/agents/bussiness"],
                    "/agents/bussiness/get-user-info" => ["/agents/bussiness"],
//                    "/agents/bussiness/del-bussiness" => ["/agents/bussiness"],
//                    "/agents/bussiness/del-ip" => ["/agents/bussiness"],
                    /**
                     * 合作商接口管理
                     */
//                    "/agents/apilist/add-api" => ["/agents/apilist"],
//                    "/agents/apilist/edituse" => ["/agents/apilist"],
//                    "/agents/apilist/edit-api" => ["/agents/apilist"],
//                    "/agents/apilist/delete-api" => ["/agents/apilist"],
//                    "/agents/login/login" => ["/agents/login"],
//                    "/agents/login/success" => ["/agents/login"],
                    
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
