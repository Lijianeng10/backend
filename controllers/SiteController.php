<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\db\Query;
use app\modules\helpers\Commonfun;
use app\modules\admin\models\SysAuth;

class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
            "LoginFilter" => [
                "class" => 'app\modules\core\filters\LoginFilter',
                "except" => [
                    'admin/login',
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        $session = Yii::$app->session;
        $this->layout = 'pageframe';
        $menus = Commonfun::getAuthurls();
        //需要隐藏模块
        $ret=Commonfun::getSysConf('admin_hidden_module');
        $closeMenu =explode(",",$ret["admin_hidden_module"]);
        $authIds = SysAuth::find()->select("auth_id")->where(["in","auth_url",$closeMenu])->asArray()->all();
        foreach ($authIds as $key => $value) {
            $menus = Commonfun::delCloseAuth($value["auth_id"], $menus);
        }
        $menus = Commonfun::getChildrens(0, $menus);
        $session["inPort"]="sys";
        return $this->render('index', ["admin_name" => $session["nickname"], "menus" => $menus]);
    }


    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
                    'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout() {
        return $this->render('about');
    }

    /**
     * 获取权限url
     * @return array
     */
//    public function getAuthurls() {
//        $session = Yii::$app->session;
//
//        $admin_role_model = new Query();
//        $roleIds = $admin_role_model->select('admin_role.role_id')
//                ->from(['admin_role' => 'sys_admin_role'])
//                ->leftJoin(['role' => 'sys_role'], 'role.role_id = admin_role.role_id')
//                ->where(['role.role_status' => '1'])
//                ->andWhere(['admin_role.admin_id' => $session['admin_id']]);
//        $role_auth_model = new Query();
//        $authIds = $role_auth_model->select('auth_id')
//                ->from('sys_role_auth')
//                ->where(['in', 'role_id', $roleIds]);
//
//        $auth_model = new Query();
//        $authUrls = $auth_model->select('auth_url,auth_name,auth_pid,auth_id')
//                ->from('sys_auth')
//                ->where(['auth_status' => '1'])
//                ->andWhere(['in', 'auth_id', $authIds])
//                ->orderBy("auth_sort desc,auth_id asc")
//                ->all();
//        return $authUrls;
//    }
}
