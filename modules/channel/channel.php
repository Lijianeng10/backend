<?php

namespace app\modules\channel;

/**
 * channel module definition class
 */
class channel extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\channel\controllers';

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
                    "/channel/betlist/readdetail12" => ["/channel/betlist"],
                    "/channel/betlist/readdetail3" => ["/channel/betlist"],
                    "/channel/betlist/readdetail4" => ["/channel/betlist"],
                    "/channel/betlist/lan-detail" => ["/channel/betlist"],
                    "/channel/betlist/get-deatail-list" => ["/channel/betlist"],
                    "/channel/betlist/get-more-detail" => ["/channel/betlist"],
                     "/channel/betlist/read-bd-detail" => ["/channel/betlist"],
                    "/channel/betlist/bd-detail" => ["/channel/betlist"],
                    "/channel/betlist/get-bd-more-detail" => ["/channel/betlist"],
                ]
            ],
        ];
    }
}
