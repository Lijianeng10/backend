<?php

namespace app\modules\expert\controllers;

use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\expert\models\ArticlesReportRecord;
use yii\db\Expression;

class ArticleController extends \yii\web\Controller {

    /**
     * 专家文章列表
     * @return type
     */
    public function actionIndex() {
        $get = \Yii::$app->request->get();
        $where = [];
        if (isset($get["article_type"]) && !empty($get["article_type"])) {
            if($get['article_type'] == 1) {
                $where = ['in', 'e.article_type', [1,3]];
            } else {
                $where = ['e.article_type' => $get['article_type']];
            }
//            $article_type = $get["article_type"];
        } else {
//            $article_type = 1;
            $where = ['in', 'e.article_type', [1,3]];
        }
        $loginType = \Yii::$app->session['type'];
        
        $query = (new Query())->select(["e.*","a.admin_name opt_name","r.month_red_nums", 
                new Expression('case when article_source = 1 or article_source = 5 then (select u.user_name from user u where u.user_id = e.user_id) when article_source = 2 or article_source = 3 or article_source = 4 then (select a.expert_name user_name from api_expert a where a.third_expert_id = e.user_id and a.third_type = e.article_source) end as user_name'),
                new Expression('case when article_source = 1 or article_source = 5 then (select u.cust_no from user u where u.user_id = e.user_id) end as cust_no')])
                ->from("expert_articles e")
                ->join("left join", "user u", "u.user_id=e.user_id")
                ->join("left join", "sys_admin a", "a.admin_id = e.opt_id")
                ->join("left join", "expert r", "r.user_id=e.user_id")
                ->where($where);
        if($loginType != 0) {
            $identityType = \Yii::$app->session['type_identity'];
            $query = $query->andWhere(['e.article_source' => $identityType ]);
        }
        if (isset($get["articleInfo"]) && !empty($get["articleInfo"])) {
            $query = $query->andWhere(["or", ["like", "e.article_title", $get["articleInfo"]], ["e.articles_code" => $get["articleInfo"]]]);
        }
        if (isset($get["createTimeStart"]) && !empty($get["createTimeStart"])) {
            $query = $query->andWhere([">=", "e.create_time", $get["createTimeStart"] . " 00:00:00"]);
        }else{
            $query = $query->andWhere([">=", "e.create_time", date("Y-m-01") . " 00:00:00"]);
        }
        if (isset($get["createTimeEnd"]) && !empty($get["createTimeEnd"])) {
            $query = $query->andWhere(["<=", "e.create_time", $get["createTimeEnd"] . " 23:59:59"]);
        }
        if (isset($get["reviewTimeStart"]) && !empty($get["reviewTimeStart"])) {
            $query = $query->andWhere([">=", "e.create_time", $get["reviewTimeStart"] . " 00:00:00"]);
        }
        if (isset($get["reviewTimeEnd"]) && !empty($get["reviewTimeEnd"])) {
            $query = $query->andWhere(["<=", "e.review_time", $get["reviewTimeEnd"] . " 23:59:59"]);
        }
        if (isset($get["articleStatus"]) && !empty($get["articleStatus"])) {
            $query = $query->andWhere(["e.article_status" => $get["articleStatus"]]);
        }
        if (isset($get["payType"]) && !empty($get["payType"])) {
            $query = $query->andWhere(["e.pay_type" => $get["payType"]]);
        }
        if (isset($get["stick"]) && !empty($get["stick"])) {
            if ($get["stick"] == "999") {
                $query = $query->andWhere(["e.stick" => $get["stick"]]);
            } else {
                $query = $query->andWhere(["<>", "e.stick", 999]);
            }
        }
        if (isset($get["source"]) && !empty($get["source"])) {
            $query = $query->andWhere(["e.article_source" => $get['source']]);
        }
        if (isset($get["buyStatus"]) && !empty($get["buyStatus"])) {
            if ($get["buyStatus"] == "1") {
                $query = $query->andWhere(["<>", "e.buy_nums", 0]);
            } else {
                $query = $query->andWhere(["e.buy_nums"=>0]);
            }
        }
        if (isset($get["expertInfo"]) && !empty($get["expertInfo"])) {
            $query = $query->andWhere(["or", ["like", "u.user_name", $get["expertInfo"]], ["u.cust_no" => $get["expertInfo"]], ["like", "u.user_tel", $get["expertInfo"]],]);
        }
        $sortArr = [];
//        if (isset($get["createTimeSort"]) && !empty($get["createTimeSort"])) {
//            if ($get["createTimeSort"] == "upSort") {
//                $sortArr["e.create_time"] = SORT_ASC;
//            }
//            if ($get["createTimeSort"] == "downSort") {
//                $sortArr["e.create_time"] = SORT_DESC;
//            }
//        }
        if (isset($get["readNumsSort"]) && !empty($get["readNumsSort"])) {
            if ($get["readNumsSort"] == "upSort") {
                $sortArr["e.read_nums"] = SORT_ASC;
            }
            if ($get["readNumsSort"] == "downSort") {
                $sortArr["e.read_nums"] = SORT_DESC;
            }
        }
        if (isset($get["buyNumsSort"]) && !empty($get["buyNumsSort"])) {
            if ($get["buyNumsSort"] == "upSort") {
                $sortArr["e.buy_nums"] = SORT_ASC;
            }
            if ($get["buyNumsSort"] == "downSort") {
                $sortArr["e.buy_nums"] = SORT_DESC;
            }
        }
        $sortArr["e.deal_status"] = SORT_ASC;
        $sortArr["e.stick"] = SORT_ASC;
        $sortArr["e.create_time"] = SORT_DESC;
        $sortArr["r.month_red_nums"] = SORT_DESC;
        $sortArr["e.expert_articles_id"] = SORT_DESC;
        $query = $query->orderBy($sortArr);
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        return $this->render("index", ["data" => $data, "get" => $get]);
    }

    /**
     * 文章审核
     * @return type
     */
    public function actionReview() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        $expArt = (new Query())->select("*")->from("expert_articles")->where(["expert_articles_id" => $get["expert_articles_id"]])->one();
        return $this->render("review", ["data" => $expArt]);
    }

    /**
     * 审核通过
     * @return type
     */
    public function actionPass() {//expert_status
        $get = \Yii::$app->request->post();
        if (!isset($get["reviewContent"]) || empty($get["reviewContent"])) {
            return $this->jsonResult(109, "审核原因");
        }
        $session = \Yii::$app->session;
        $expArt = (new Query())->select("*")->from("expert_articles")->where(["expert_articles_id" => $get["expert_articles_id"]])->one();
        $format = strtotime(date('Y-m-d H:i:s')) * 1000;
        if ($expArt['cutoff_time'] < $format) {
            return $this->jsonResult(109, "此篇文章预测场次已开赛，不可上线");
        }
        $ret = \Yii::$app->db->createCommand()->update("expert_articles", ["article_status" => 3, "remark" => $get["reviewContent"], "opt_id" => $session['admin_id'], "review_time" => date('Y-m-d H:i:s'), "modify_time" => date("Y-m-d H-i-s")], ["and", ["expert_articles_id" => $get["expert_articles_id"]], ["in", "article_status", [2, 5]]])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "修改失败");
        }
        \Yii::$app->db->createCommand("update expert set article_nums=article_nums+1 where user_id=" . $expArt["user_id"])->execute();
        $this->sendWechatReviewMsg($get["expert_articles_id"]);
        return $this->jsonResult(600, "审核通过");
    }

    /**
     * 审核不通过
     * @return type
     */
    public function actionNoPass() {//expert_status
        $get = \Yii::$app->request->post();
        if (!isset($get["reviewContent"]) || empty($get["reviewContent"])) {
            return $this->jsonResult(109, "审核原因");
        }
        $session = \Yii::$app->session;
        $ret = \Yii::$app->db->createCommand()->update("expert_articles", ["article_status" => 5, "remark" => $get["reviewContent"], "opt_id" => $session['admin_id'], "review_time" => date('Y-m-d H:i:s'), "modify_time" => date("Y-m-d H-i-s")], ["and", ["expert_articles_id" => $get["expert_articles_id"]], ["in", "article_status", [2, 5]]])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "修改失败");
        }
        $this->sendWechatReviewMsg($get["expert_articles_id"]);
        return $this->jsonResult(600, "审核未通过");
    }

    /**
     * 文章下线
     * @return type
     */
    public function actionOffLine() {//expert_status
        $get = \Yii::$app->request->post();
        $session = \Yii::$app->session;
        $ret = \Yii::$app->db->createCommand()->update("expert_articles", ["article_status" => 4, "opt_id" => $session['admin_id'], "review_time" => date('Y-m-d H:i:s'), "modify_time" => date("Y-m-d H-i-s")], ["expert_articles_id" => $get["expert_articles_id"], "article_status" => 3])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "修改失败");
        }
        $this->sendWechatReviewMsg($get["expert_articles_id"]);
        return $this->jsonResult(600, "下线成功");
    }

    /**
     * 文章上线
     * @return type
     */
    public function actionOnLine() {//expert_status
        $get = \Yii::$app->request->post();
        $session = \Yii::$app->session;
        $ret = \Yii::$app->db->createCommand()->update("expert_articles", ["article_status" => 3, "opt_id" => $session['admin_id'], "review_time" => date('Y-m-d H:i:s'), "modify_time" => date("Y-m-d H-i-s")], ["expert_articles_id" => $get["expert_articles_id"], "article_status" => 4])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "修改失败");
        }
        $this->sendWechatReviewMsg($get["expert_articles_id"]);
        return $this->jsonResult(600, "上线成功");
    }

    /**
     * 文章置顶
     * @return type
     */
    public function actionOnStick() {
        $post = \Yii::$app->request->post();
        $session = \Yii::$app->session;
//        date_default_timezone_set('Asia/Shanghai');
        $ret = \Yii::$app->db->createCommand()->update("expert_articles", ["modify_time" => date("Y-m-d H-i-s"), "stick" => $post["stick"]], ["expert_articles_id" => $post["expert_articles_id"]])->execute();

        if ($ret == false) {
            return $this->jsonResult(109, "修改失败");
        }
        return $this->jsonResult(600, "置顶成功");
    }

    /**
     * 文章取消置顶
     * @return type
     */
    public function actionOffStick() {
        $post = \Yii::$app->request->post();
        $session = \Yii::$app->session;
        date_default_timezone_set('Asia/Shanghai');
        $ret = \Yii::$app->db->createCommand()->update("expert_articles", ["modify_time" => date("Y-m-d H-i-s"), "stick" => 999], ["expert_articles_id" => $post["expert_articles_id"]])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "修改失败");
        }
        return $this->jsonResult(600, "取消置顶成功");
    }

    public function actionGetArticleContent() {
        $post = \Yii::$app->request->post();
        $expArt = (new Query())->select("*")->from("expert_articles")->where(["expert_articles_id" => $post["expert_articles_id"]])->one();
        return $this->jsonResult(600, "获取成功", $expArt);
    }

    /**
     * 
     * @return type
     */
    public function actionArticleContent() {
        $this->layout = false;
        $status = [
            "3" => "胜",
            "1" => "平",
            "0" => "负"
        ];
        $dxStatus = ['1' => '大分', '2' => '小分'];
        $get = \Yii::$app->request->get();
        $expArt = (new Query())->select("*")->from("expert_articles")->where(["expert_articles_id" => $get["expert_articles_id"]])->one();
        $articleBet = (new Query())->select("*")->from("articles_periods")->where(["articles_id" => $get["expert_articles_id"]])->all();
//        $newPeriods = [];
        $mid = array_unique(array_column($articleBet, 'periods'));
//        foreach ($articleBet as $v) {
//            array_push($newPeriods, $v["periods"]);
//        }
        if ($expArt['article_type'] == 1) {
            $scheduleResult = (new Query())->select("*")->from("schedule_result")->where(["in", "schedule_mid", $mid])->indexBy('schedule_mid')->all();
        } else {
            $scheduleResult = (new Query())->select("*, result_status status")->from("lan_schedule_result")->where(["in", "schedule_mid", $mid])->indexBy('schedule_mid')->all();
        }


        $newBet = [];
        foreach ($articleBet as $k => $v) {
            $preResults = explode(",", $v["pre_result"]);
            foreach ($preResults as &$val) {
                //<span class="letball">让</span>
                if ($expArt['article_type'] == 1) {
                    if ($v["lottery_code"] == "3006") {
                        if ($scheduleResult[$v['periods']]['status'] == 2 && $scheduleResult[$v['periods']]["schedule_result_3006"] == $val) {
                            $val = '<span class="letball">让</span><span style="color:red;">' . $status[$val] . '</span>';
                        } else {
                            $val = '<span class="letball">让</span>' . $status[$val];
                        }
                    } else {
                        if ($scheduleResult[$v['periods']]['status'] == 2 && $scheduleResult[$v['periods']]["schedule_result_3010"] == $val) {
                            $val = '<span style="color:red;">' . $status[$val] . '</span>';
                        } else {
                            $val = $status[$val];
                        }
                    }
                } elseif($expArt['article_type'] == 2) {
                    if ($scheduleResult[$v['periods']]['status'] == 2) {
                        $bfArr = explode(':', $scheduleResult[$v['periods']]['result_qcbf']);
                        $result = bccomp($bfArr[0], $bfArr[1]) == 1 ? 0 : 3;
                        $rfResult = bccomp($bfArr[0], bcadd($bfArr[1], $v['rq_nums'], 2), 2) == 1 ? 0 : 3;
                        $dxResult = bccomp($v['fen_cutoff'], bcadd($bfArr[1], $bfArr[0], 2), 2) == 2 ? 2 : 1;
                    }
                    if ($v["lottery_code"] == "3001") {
                        if ($scheduleResult[$v['periods']]['status'] == 2 && $result == $val) {
                            $val = '<span style="color:red;">' . $status[$val] . '</span>';
                        } else {
                            $val = $status[$val];
                        }
                    } elseif ($v['lottery_code'] == '3002') {
                        if ($scheduleResult[$v['periods']]['status'] == 2 && $rfResult == $val) {
                            $val = '<span class="letball">让</span><span style="color:red;">' . $status[$val] . '</span>';
                        } else {
                            $val = '<span class="letball">让</span>' . $status[$val];
                        }
                    } else {
                        if ($scheduleResult[$v['periods']]['status'] == 2 && $dxResult == $val) {
                            $val = '<span style="color:red;">' . $dxStatus[$val] . '</span>';
                        } else {
                            $val =  $dxStatus[$val];
                        }
                    }
                } 
            }
            $v["betVal"] = implode(" 、 ", $preResults);
            if ($scheduleResult[$v['periods']]['status'] != 2) {
                $v["result"] = "--";
                $v["bf"] = " VS ";
            } else {
                if ($expArt['article_type'] == 1) {
                    $v["bf"] = '<span style="color:red;margin-left:5px;margin-right:5px;">' . $scheduleResult[$v['periods']]["schedule_result_3007"] . '</span>';
                    $v["result"] = $status[$scheduleResult[$v['periods']]["schedule_result_3010"]];
                } else {
                    $v["bf"] = '<span style="color:red;margin-left:5px;margin-right:5px;">' . $scheduleResult[$v['periods']]["result_qcbf"] . '</span>';
                    $v["result"] = $status[$result];
                }
            }
            array_push($newBet, $v);
        }
//        print_r($newBet);die;
        return $this->render("articlecontent", ["data" => $expArt, "bet" => $newBet]);
    }

    /**
     * 文章修改保存
     * @return type
     */
    public function actionSaveArticleContent() {//expert_status
        $post = \Yii::$app->request->post();
        if (!isset($post["expertArticlesId"]) || empty($post["expertArticlesId"]) || !isset($post["articleContent"]) || empty($post["articleContent"]) || !isset($post["articleTitle"]) || empty($post["articleTitle"])) {
            return $this->jsonResult(109, "参数缺失！");
        }
        $session = \Yii::$app->session;
        $ret = \Yii::$app->db->createCommand()->update("expert_articles", ["article_title" => $post["articleTitle"], "article_content" => $post["articleContent"], "opt_id" => $session['admin_id'], "modify_time" => date("Y-m-d H-i-s")], ["expert_articles_id" => $post["expertArticlesId"]])->execute();
        if ($ret == false) {
            return $this->jsonResult(109, "文章修改失败");
        }
        return $this->jsonResult(600, "文章修改成功");
    }

    /**
     * 发送审核结果微信推送
     * @param type $expert_articles_id
     */
    public function sendWechatReviewMsg($expert_articles_id) {
        @file_get_contents(\Yii::$app->params["userDomain"] . "/api/cron/time/send-review-msg?expert_articles_id=" . $expert_articles_id);
    }

    /**
     * 查看被举报详情
     */
    public function actionReadReportRecord() {
        $get = \Yii::$app->request->get();
        $article_id = $get["expert_articles_id"];
        if (empty($article_id)) {
            return "参数缺失";
        }
        $query = (new Query())->select("a.*,u.user_name,u.user_tel,ea.articles_code")
                ->from("articles_report_record as a")
                ->leftJoin("user as u", "u.cust_no = a.cust_no")
                ->leftJoin("expert_articles as ea", "ea.expert_articles_id = a.article_id")
                ->where(["a.article_id" => $article_id])
                ->orderBy("a.articles_report_record_id desc");
        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        return $this->render("report-record", ["data" => $data]);
    }

    //查看文章URL
    public function actionReadUrl() {
        $this->layout = false;
        $get = \Yii::$app->request->get();
        $article_id = $get["expert_articles_id"];
        if (empty($article_id)) {
            return "参数缺失";
        }
        $url = \Yii::$app->params["userDomain"];
        $articleUrl = $url . "/find/expert/programmeDetail/" . $article_id;
        return $this->render("read-url", ["url" => $articleUrl]);
    }

}
