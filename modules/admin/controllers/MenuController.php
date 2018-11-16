<?php

namespace app\modules\admin\controllers;

class MenuController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

}
