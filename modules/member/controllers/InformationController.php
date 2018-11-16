<?php

namespace app\modules\member\controllers;

use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\db\Query;

/**
 * Default controller for the `index` module
 */
class InformationController extends Controller{
    /**
     * Renders the index view for the module
     * @return string
     */
//    public $enableCsrfValidation = false;

    public function actionIndex(){
        return $this->render('index');
    }
}

