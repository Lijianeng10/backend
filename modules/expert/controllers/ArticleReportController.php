<?php

namespace app\modules\expert\controllers;

use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use app\modules\lottery\models\Store;
use app\modules\lottery\helpers\Constant;
use app\modules\common\helpers\Constants;
use app\modules\agents\models\Agents;
use app\modules\common\models\Bussiness;
use app\modules\agents\models\User;
use yii\db\Expression;

class ArticleReportController extends Controller {

    public $enableCsrfValidation = false;

    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * 获取转件文章购买日统计数据
     */
    public function actionGetReport() {
        $request = \Yii::$app->request;
        $post = \Yii::$app->request->post();
//        $type = $request->post("type",1);
        $field =["DATE_FORMAT(ea.create_time,'%Y-%m-%d') as days", "e.cust_no", 
            new Expression('case when expert_source = 1 or expert_source = 5 then (select u.user_name from user u where u.user_id = e.user_id) when expert_source = 2 or expert_source = 3 or expert_source = 4 then (select a.expert_name user_name from api_expert a where a.third_expert_id = e.user_id and a.third_type = e.expert_source) end as user_name'),
            new Expression('case when expert_source = 1 or expert_source = 5 then (select u.user_tel from user u where u.user_id = e.user_id) end as user_tel'),
            "COUNT(p.pay_record_id) as num", "SUM(p.pay_pre_money) as pre_money", "SUM(p.pay_money) as money"];
//        if($type==1){
//            array_push($field,"DATE_FORMAT(l.out_time,'%Y-%m-%d') days");
//        }elseif($type==2){
//            array_push($field,"DATE_FORMAT(l.create_time,'%Y-%m-%d') days");
//        }
        
        $query = (new Query())->select($field)
            ->from("expert as e")
            ->leftJoin("expert_articles as ea","ea.user_id = e.user_id")
            ->leftJoin("user_article as ua","ua.article_id = ea.expert_articles_id")
            ->leftJoin("pay_record as p","p.order_code = ua.user_article_code AND p.pay_type = 17")
            ->where(["p.status"=>1]);
        $loginType = \Yii::$app->session['type'];
        if ($loginType != 0) {
            $identityType = \Yii::$app->session['type_identity'];
            $query = $query->andWhere(['e.expert_source' => $identityType]);
        }
        if (isset($post["expert_info"]) && !empty($post["expert_info"])) {
            $query = $query->andWhere(['or', ["u.user_tel" => $post["expert_info"]], ["u.cust_no" => $post["expert_info"]], ["like", "u.user_name", $post["expert_info"]]]);
        }
        //判断统计类型是出票时间 1 还是创建时间 2
        if (isset($post["start_date"]) && !empty($post["start_date"])) {
//            if($type==1){
//                $query = $query->andWhere([">=", "l.out_time", $post["start_date"] . " 00:00:00"]);
//            }else{
                $query = $query->andWhere([">=", "ea.create_time", $post["start_date"] ." 00:00:00"]);
//            }
        }
        if (isset($post["end_date"]) && !empty($post["end_date"])) {
//            if($type==1){
//                $query = $query->andWhere(["<=", "l.out_time", $post["end_date"] . " 23:59:59"]);
//            }else{
                $query = $query->andWhere(["<=", "ea.create_time", $post["end_date"] ." 23:59:59"]);
//            }
        }
        $result = $query->groupBy(["days", "e.cust_no"])
            ->orderBy("days desc")
            ->all();
        return $this->jsonResult(100, "获取成功", $result);
    }

    /**
     * 获取店铺报表月统计数据
     */
    public function actionGetMonthReport() {
        $request = \Yii::$app->request;
        $post = \Yii::$app->request->post();
        $years = $post['years'];
        $months = $post['months'];
        $field =["DATE_FORMAT(ea.create_time,'%Y-%m') as months", "e.cust_no", 
            new Expression('case when expert_source = 1 or expert_source = 5 then (select u.user_name from user u where u.user_id = e.user_id) when expert_source = 2 or expert_source = 3 or expert_source = 4 then (select a.expert_name user_name from api_expert a where a.third_expert_id = e.user_id and a.third_type = e.expert_source) end as user_name'),
            new Expression('case when expert_source = 1 or expert_source = 5 then (select u.user_tel from user u where u.user_id = e.user_id) end as user_tel'),
            "COUNT(p.pay_record_id) as num", "SUM(p.pay_pre_money) as pre_money", "SUM(p.pay_money) as money"];
        $query = (new Query())->select($field)
                ->from("expert as e")
                ->leftJoin("expert_articles as ea","ea.user_id = e.user_id")
                ->leftJoin("user_article as ua","ua.article_id = ea.expert_articles_id")
                ->leftJoin("pay_record as p","p.order_code = ua.user_article_code AND p.pay_type = 17")
                ->where(["p.status"=>1]);
        $loginType = \Yii::$app->session['type'];
        if ($loginType != 0) {
            $identityType = \Yii::$app->session['type_identity'];
            $query = $query->andWhere(['e.expert_source' => $identityType]);
        }
        if (isset($post["expert"]) && !empty($post["expert"])) {
            $query = $query->andWhere(['or', ["u.user_tel" => $post["expert"]], ["u.cust_no" => $post["expert"]], ["like", "u.user_name", $post["expert"]]]);
        }
//        if($type==1){
//            $query = $query->andWhere(["DATE_FORMAT(l.out_time,'%Y-%m')" => $years . "-" . $months]);
//        }else{
            $query = $query->andWhere(["DATE_FORMAT(ea.create_time,'%Y-%m')" => $years . "-" . $months]);
//        }
        $result = $query->groupBy(["months", "e.cust_no"])
            ->orderBy("months desc")
            ->all();
        return $this->jsonResult(100, "获取成功", $result);
    }
}
