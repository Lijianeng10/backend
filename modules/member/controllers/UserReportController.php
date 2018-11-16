<?php

namespace app\modules\member\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use app\modules\member\models\User;
use yii\db\Expression;

class UserReportController extends Controller{

    public $enableCsrfValidation = false;
    public function actionIndex() {
        return $this->render('index');
    }
    /**
     * 获取会员注册日统计数据
     */
    public function actionGetReport() {
        $request = \Yii::$app->request;
        $session = \Yii::$app->session;
        $post = \Yii::$app->request->post();
        $where =['and'];
        $where[] = ['in','u.register_from',[1,2,3,4,8]];
        //不是咕啦内部用户只能看到自己的统计数据
        if($session['type']!=0){
            $where[] = ['u.from_id'=>$session['admin_name']];
        }
        $field =["b.platName", "count(distinct u.user_id) count","DATE_FORMAT(u.create_time,'%Y-%m-%d') days"];
        $q1 = (new Query())->select("(8) as platform,('meitu') as code,('美图') as platName");
        $q2 = (new Query())->select("(8) as platform,('txty') as code,('腾讯体育') as platName");
        $q3 = (new Query())->select("(1),('GL'),('咕啦体育')");
        $q4 = (new Query())->select("(2),('GL'),('咕啦体育')");
        $q5 = (new Query())->select("(3),('GL'),('咕啦体育')");
        $q6 = (new Query())->select("(4),('GL'),('咕啦体育')");
        $q7 = (new Query())->select("(8) as platform,('qj') as code,('全景') as platName");
        $new = $q1->union($q2)->union($q3)->union($q4)->union($q5)->union($q6)->union($q7);
        $query = (new Query())->select($field)
            ->from('user as u')
            ->leftJoin(['b'=>$new],'CASE u.register_from WHEN 8 THEN u.from_id = b.`code` WHEN 1 THEN u.register_from = b.platform WHEN 2 THEN u.register_from = b.platform WHEN 3 THEN u.register_from = b.platform WHEN 4 THEN u.register_from = b.platform end')
            ->where($where);
        if (isset($post["from_id"]) && !empty($post["from_id"])) {
            if($post["from_id"]!='GL'){
                $query = $query->andWhere(['u.from_id'=>$post["from_id"]]);
            }else{
                $query = $query->andWhere(['<>','u.register_from',8]);
            }
        }
        if (isset($post["start_date"]) && !empty($post["start_date"])) {
                $query = $query->andWhere([">=", "u.create_time", $post["start_date"] . " 00:00:00"]);
        }
        if (isset($post["end_date"]) && !empty($post["end_date"])) {
            $query = $query->andWhere(["<=", "u.create_time", $post["end_date"] . " 23:59:59"]);
        }
        $result = $query->groupBy(["days","b.platName"])
            ->orderBy("days desc,b.platName desc")
            ->all();
        return $this->jsonResult(600, "获取成功", $result);
    }

    /**
     *  获取会员注册月统计数据
     */
    public function actionGetMonthReport() {
        $request = \Yii::$app->request;
        $session = \Yii::$app->session;
        $post = \Yii::$app->request->post();
        $years = $post['years'];
        $months = $post['months'];
        $where =['and'];
        $where[] = ['in','u.register_from',[1,2,3,4,8]];
        //不是咕啦内部用户只能看到自己的统计数据
        if($session['type']!=0){
            $where[] = ['u.from_id'=>$session['admin_name']];
        }
        $field =["b.platName", "count(distinct u.user_id) count","DATE_FORMAT(u.create_time,'%Y-%m') months"];
        $q1 = (new Query())->select("(8) as platform,('meitu') as code,('美图') as platName");
        $q2 = (new Query())->select("(8) as platform,('txty') as code,('腾讯体育') as platName");
        $q3 = (new Query())->select("(1),('GL'),('咕啦体育')");
        $q4 = (new Query())->select("(2),('GL'),('咕啦体育')");
        $q5 = (new Query())->select("(3),('GL'),('咕啦体育')");
        $q6 = (new Query())->select("(4),('GL'),('咕啦体育')");
        $q7 = (new Query())->select("(8) as platform,('qj') as code,('全景') as platName");
        $new = $q1->union($q2)->union($q3)->union($q4)->union($q5)->union($q6)->union($q7);
        $query = (new Query())->select($field)
            ->from('user as u')
            ->leftJoin(['b'=>$new],'CASE u.register_from WHEN 8 THEN u.from_id = b.`code` WHEN 1 THEN u.register_from = b.platform WHEN 2 THEN u.register_from = b.platform WHEN 3 THEN u.register_from = b.platform WHEN 4 THEN u.register_from = b.platform end')
            ->where($where);
        if (isset($post["from_id"]) && !empty($post["from_id"])) {
            if($post["from_id"]!='GL'){
                $query = $query->andWhere(['u.from_id'=>$post["from_id"]]);
            }else{
                $query = $query->andWhere(['<>','u.register_from',8]);
            }
        }
        $query = $query->andWhere(["DATE_FORMAT(u.create_time,'%Y-%m')" => $years . "-" . $months]);
        $result = $query->groupBy(["months","b.platName"])
            ->orderBy("months desc,b.platName desc")
            ->all();
        return $this->jsonResult(600, "获取成功", $result);
    }

}

