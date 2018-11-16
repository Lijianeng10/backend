<?php

namespace app\modules\promote;

/**
 * admin module definition class
 */
class promote extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\promote\controllers';

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
            ],
            "AuthFilter" => [
                "class" => 'app\modules\core\filters\AuthFilter',
                "children" =>[
                    "/promote/store/edit-status" => ["/promote/store"],
                    "/promote/store/del-user" => ["/promote/store"],
                    "/promote/record/add-code" => ["/promote/record"],
                    "/promote/title/add-title" => ["/promote/title"],
                ]
            ],
        ];
    }

}

