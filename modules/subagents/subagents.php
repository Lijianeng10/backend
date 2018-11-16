<?php

namespace app\modules\subagents;

/**
 * modules module definition class
 */
class subagents extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\subagents\controllers';

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
                "class" => 'app\modules\core\filters\LoginFilter'
            ],
            "AuthFilter" => [
                "class" => 'app\modules\core\filters\AuthFilter',
                "children" => [
                    "/subagents/userlist/view-member"=>["/subagents/userlist"],
                    "/subagents/orderlist/readdetail12" => ["/subagents/orderlist"],
                    "/subagents/orderlist/readdetail3" => ["/subagents/orderlist"],
                    "/subagents/orderlist/readdetail4" => ["/subagents/orderlist"],
                    "/subagents/orderlist/lan-detail" => ["/subagents/orderlist"],
                    "/subagents/orderlist/get-deatail-list" => ["/subagents/orderlist"],
                    "/subagents/orderlist/get-more-detail" => ["/subagents/orderlist"],
                    "/subagents/userlist/index_1" => ["/subagents/userlist"],
                    "/subagents/orderlist/print-report" => ["/subagents/orderlist"],
                    /**
                     * 代理商合买
                     */
                    "/subagents/chipped/get-programme-detail" => ["/subagents/chipped"],
                    "/subagents/chipped/readdetail" => ["/subagents/chipped"],
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
