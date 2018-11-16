<?php

namespace app\modules\trading;

/**
 * modules module definition class
 */
class trading extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\trading\controllers';

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
//                    "/trading/activity/add-money" => ["/trading/activity"],
                ]
            ],
        ];
    }

}
