<?php

namespace app\modules\subchannel;

/**
 * channel module definition class
 */
class subchannel extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\subchannel\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
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
                    "/subchannel/betlist/readdetail12" => ["/subchannel/betlist"],
                    "/subchannel/betlist/readdetail3" => ["/subchannel/betlist"],
                    "/subchannel/betlist/readdetail4" => ["/subchannel/betlist"],
                    "/subchannel/betlist/lan-detail" => ["/subchannel/betlist"],
                    "/subchannel/betlist/get-deatail-list" => ["/subchannel/betlist"],
                    "/subchannel/betlist/get-more-detail" => ["/subchannel/betlist"],
                     "/subchannel/betlist/read-bd-detail" => ["/subchannel/betlist"],
                    "/subchannel/betlist/bd-detail" => ["/subchannel/betlist"],
                    "/subchannel/betlist/get-bd-more-detail" => ["/subchannel/betlist"],
//                    提现管理
                    "/subchannel/withdraw/get-bussiness-info"=>["/subchannel/withdraw"],
//                    充值管理
                    "/subchannel/recharge/read"=>["/subchannel/recharge"]
                ]
            ],
        ];
    }
}
