<?php

namespace app\modules\index;

/**
 * index module definition class
 */
class index extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\index\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        // custom initialization code goes here
    }

    public function behaviors() {
        return [
//            "LoginFilter" => [
//                "class" => 'app\modules\core\filters\LoginFilter',
//                'except'=>[
//                    'index/test',
//                ]
//            ],
        ];
    }

}
