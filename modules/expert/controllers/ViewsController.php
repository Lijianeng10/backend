<?php

namespace app\modules\expert\controllers;

use yii\web\Controller;
use app\modules\expert\models\ExpertArticles;
use app\modules\expert\models\ArticlesPeriods;
use app\modules\lottery\models\Schedule;
use app\modules\common\models\LanSchedule;

class ViewsController extends Controller {

    //文章预测结果
    const ARTICLES_RESULT = [
        '1' => '待开',
        '2' => '黑单',
        '3' => '红单',
        '4' => '取消',
        '5' => '走盘'
    ];
    // 文章状态
    const ARTICLES_STATUS = [
        '1' => '草稿',
        '2' => '待审核',
        '3' => '上线',
        '4' => '下线',
        '5' => '审核失败 '
    ];
    //文章付费类型
    const ARTICLES_PAY = [
        '1' => '免费',
        '2' => '付费'
    ];
    const MADE_FOOTBALL_LOTTERY = [
        '0' => '3006',
        '1' => '3007',
        '2' => '3008',
        '3' => '3009',
        '4' => '3010',
        '5' => '3011'
    ];
    const MADE_BASKETBALL_LOTTERY = [
        '0' => '3001',
        '1' => '3002',
        '2' => '3003',
        '3' => '3004',
        '4' => '3005'
    ];

    public $enableCsrfValidation = false;

    public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);
    }

    /**
     * 说明: 跳转手机预览页面
     * @author  kevi
     * @date 2017年10月15日 下午1:49:33
     * @param
     * @return 
     */
    public function actionToPreview() {
        $request = \Yii::$app->request;
        $articleId = $request->get('article_id');
        $articleType = $request->get('article_type');
        $articlesResult = self::ARTICLES_RESULT;
        $articlesStatus = self::ARTICLES_STATUS;
        $payTypeName = self::ARTICLES_PAY;
        $football = self::MADE_FOOTBALL_LOTTERY;
        $basketball = self::MADE_BASKETBALL_LOTTERY;
        $where['expert_articles.expert_articles_id'] = $articleId;
        $field = ['expert_articles.expert_articles_id', 'e.expert_source', 'expert_articles.article_type', 'expert_articles.article_title', 'expert_articles.pay_type', 'expert_articles.pay_money', 'expert_articles.create_time',
            'expert_articles.buy_nums', 'expert_articles.read_nums', 'expert_articles.article_status', 'expert_articles.result_status', 'expert_articles.article_content', 'e.fans_nums',
            'u.user_name', 'u.user_pic', 'expert_articles.user_id as expert_id', 'expert_articles.buy_back', 'expert_articles.articles_code'];
        if ($articleType == 1) {
            array_push($field, 'e.article_nums', 'e.even_red_nums', 'e.month_red_nums', 'e.day_red_nums', 'e.day_nums', 'e.two_red_nums', 'e.three_red_nums', 'e.five_red_nums');
        } else {
            array_push($field, 'e.lan_article_nums article_nums', 'e.lan_even_red_nums even_red_nums', 'e.lan_month_red_nums month_red_nums', 'e.lan_day_red_nums day_red_nums', 'e.lan_day_nums day_nums', 'e.lan_two_red_nums two_red_nums', 'e.lan_three_red_nums three_red_nums', 'e.lan_five_red_nums five_red_nums');
        }
        $field2 = ['articles_periods.articles_id', 'articles_periods.periods', 'articles_periods.lottery_code', 'articles_periods.pre_result', 'articles_periods.pre_odds', 'articles_periods.status as pre_status', 'articles_periods.schedule_code', 'articles_periods.visit_short_name', 'articles_periods.home_short_name',
            'articles_periods.rq_nums', 'articles_periods.start_time', 'articles_periods.league_short_name', 'articles_periods.home_team_rank', 'articles_periods.visit_team_rank', 'articles_periods.home_team_img', 'articles_periods.visit_team_img', 'articles_periods.featured',
            'articles_periods.fen_cutoff', 'articles_periods.endsale_time'];
        if ($articleType == 1) {
            array_push($field2, 'sr.status', 'sr.schedule_result_3007 bf_result', 'sr.schedule_result_3010 sf_result', 'sr.schedule_result_3006 rfsf_result', 'sr.schedule_result_sbbf sbcbf_result');
        } else {
            array_push($field2, 'sr.result_status status', 'sr.result_3001 sf_result', 'sr.result_3002 rfsf_result', 'sr.result_qcbf bf_result', 'sr.result_zcbf sbcbf_result');
        }
        $query = ExpertArticles::find()->select($field)
                ->leftJoin('expert as e', 'e.user_id = expert_articles.user_id')
                ->leftJoin('user as u', 'u.user_id = expert_articles.user_id');
        $detail = $query->where($where)->indexBy('expert_articles_id')->asArray()->one();
        if (empty($detail)) {
            return ['code' => 109, 'msg' => '查询结果不存在'];
        }
        $scheData = ArticlesPeriods::find()->select($field2);

        if ($articleType == 1) {
            $scheData = $scheData->leftJoin('schedule_result as sr', 'sr.schedule_mid = articles_periods.periods')
                    ->where(['articles_id' => $detail['expert_articles_id']])
                    ->asArray()
                    ->all();
        } else {
            $scheData = $scheData->leftJoin('lan_schedule_result as sr', 'sr.schedule_mid = articles_periods.periods')
                    ->where(['articles_id' => $detail['expert_articles_id']])
                    ->asArray()
                    ->all();
        }
        $midArr = array_unique(array_column($scheData, 'periods'));
        if ($articleType == 1) {
            $oddStr = ['odds3006', 'odds3010'];
            $odds = Schedule::find()->select(['schedule_id', 'schedule_mid'])->with($oddStr)->where(['in', 'schedule_mid', $midArr])->indexBy('schedule_mid')->asArray()->all();
        } elseif ($articleType == 2) {
            $oddStr = ['odds3001', 'odds3002', 'odds3004'];
            $odds = LanSchedule::find()->select('schedule_mid')->with($oddStr)->where(['in', 'schedule_mid', $midArr])->indexBy('schedule_mid')->asArray()->all();
        }
        $list = [];
        $dayRed = [];
        foreach ($scheData as &$val) {
            $val['dx_result'] = 0;
            if (in_array($val['lottery_code'], $football) || in_array($val['lottery_code'], $basketball)) {
                $val['pre_result'] = explode(',', $val['pre_result']);
                $val['pre_odds'] = explode(',', $val['pre_odds']);
                $oddStr = ['odds' . $val['lottery_code']];
//                if($val['lottery_code'] == 3006 || $val['lottery_code'] == 3010) {
//                    $odds = Schedule::find()->with($oddStr)->where(['schedule_mid' => $val['periods']])->asArray()->one();
//                }  else {
//                    $odds = LanSchedule::find()->with($oddStr)->where(['schedule_mid' => $val['periods']])->asArray()->one();
//                }
                $val['odds'] = $odds[$val['periods']]['odds' . $val['lottery_code']];
                $bfArr = explode(':', $val['bf_result']);
                if (in_array($val['lottery_code'], $basketball)) {
                    if ($val['status'] == 2) {
                        $val['dx_result'] = bccomp(bcadd($bfArr[0], $bfArr[1]), $val['fen_cutoff'], 2) == 1 ? 1 : 2;
                        $val['sf_result'] = bccomp($bfArr[1], $bfArr[0]) == 1 ? 3 : 0;
                        $val['rfsf_result'] = bccomp(bcadd($bfArr[1], $val['rq_nums']), $bfArr[0], 1) == 1 ? 3 : 0;
                    }
                }
            } else {
                $val['pre_result'][] = $val['pre_result'];
                $val['pre_odds'] = [];
                $val['odds'] = [];
            }
            $artId = $val['articles_id'];
            if (array_key_exists($artId, $list)) {
                if (array_key_exists($val['periods'], $list[$artId]['pre_concent'])) {
                    $list[$artId]['pre_concent'][$val['periods']]['pre_lottery'][] = ['lottery_code' => $val['lottery_code'], 'pre_result' => $val['pre_result'], 'pre_odds' => $val['pre_odds'], 'pre_status' => $val['pre_status'], 'odds' => $val['odds'], 'featured' => $val['featured']];
                } else {
                    $list[$artId]['pre_concent'][$val['periods']] = ['periods' => $val['periods'], 'visit_short_name' => $val['visit_short_name'], 'home_short_name' => $val['home_short_name'], 'rq_nums' => $val['rq_nums'],
                        'start_time' => $val['start_time'], 'league_name' => $val['league_short_name'], 'schedule_status' => $val['status'], 'schedule_result_qcbf' => $val['bf_result'],
                        'schedule_code' => $val['schedule_code'], 'home_team_rank' => $val['home_team_rank'], 'visit_team_rank' => $val['visit_team_rank'], 'home_team_img' => $val['home_team_img'],
                        'visit_team_img' => $val['visit_team_img'], 'schedule_result_sbbf' => $val['sbcbf_result'], 'schedule_result' => $val['sf_result'], 'schedule_result_rqbf' => $val['rfsf_result'],
                        'endsale_time' => $val['endsale_time'], 'fen_cutoff' => $val['fen_cutoff'], 'schedule_result_dxf' => $val['dx_result'],
                        'pre_lottery' => [['lottery_code' => $val['lottery_code'], 'pre_result' => $val['pre_result'], 'pre_odds' => $val['pre_odds'], 'pre_status' => $val['pre_status'], 'odds' => $val['odds'], 'featured' => $val['featured']]]];
                }
            } else {
                $list[$artId]['pre_concent'][$val['periods']] = ['periods' => $val['periods'], 'visit_short_name' => $val['visit_short_name'], 'home_short_name' => $val['home_short_name'], 'rq_nums' => $val['rq_nums'],
                    'start_time' => $val['start_time'], 'league_name' => $val['league_short_name'], 'schedule_status' => $val['status'], 'schedule_result_qcbf' => $val['bf_result'],
                    'schedule_code' => $val['schedule_code'], 'home_team_rank' => $val['home_team_rank'], 'visit_team_rank' => $val['visit_team_rank'], 'home_team_img' => $val['home_team_img'],
                    'visit_team_img' => $val['visit_team_img'], 'schedule_result_sbbf' => $val['sbcbf_result'], 'schedule_result' => $val['sf_result'], 'schedule_result_rqbf' => $val['rfsf_result'],
                    'endsale_time' => $val['endsale_time'], 'fen_cutoff' => $val['fen_cutoff'], 'schedule_result_dxf' => $val['dx_result'],
                    'pre_lottery' => [['lottery_code' => $val['lottery_code'], 'pre_result' => $val['pre_result'], 'pre_odds' => $val['pre_odds'], 'pre_status' => $val['pre_status'], 'odds' => $val['odds'], 'featured' => $val['featured']]]];
                $list[$artId]['expert_articles_id'] = $detail['expert_articles_id'];
                $list[$artId]['article_type'] = $detail['article_type'];
                $list[$artId]['pay_type'] = $detail['pay_type'];
                $list[$artId]['article_status'] = $detail['article_status'];
                $list[$artId]['result_status'] = $detail['result_status'];
                $list[$artId]['pay_type_name'] = $payTypeName[$detail['pay_type']];
                $list[$artId]['article_status_name'] = $articlesStatus[$detail['article_status']];
                $list[$artId]['result_status_name'] = $articlesResult[$detail['result_status']];
                $list[$artId]['pay_money'] = $detail['pay_money'];
                $list[$artId]['buy_nums'] = $detail['buy_nums'];
                $list[$artId]['read_nums'] = $detail['read_nums'];
                $list[$artId]['article_title'] = $detail['article_title'];
                $list[$artId]['create_time'] = $detail['create_time'];
                $list[$artId]['article_nums'] = $detail['article_nums'];
                $list[$artId]['fans_nums'] = $detail['fans_nums'];
                $list[$artId]['even_red_nums'] = $detail['even_red_nums'];
                $list[$artId]['month_red_nums'] = $detail['month_red_nums'];
//                $list[$artId]['day_red_nums'] = $detail['day_red_nums'];
                $list[$artId]['user_name'] = $detail['user_name'];
                $list[$artId]['user_pic'] = $detail['user_pic'];
                $list[$artId]['article_title'] = $detail['article_title'];
//                $list[$artId]['day_nums'] = $detail['day_nums'];
                $list[$artId]['expert_id'] = $detail['expert_id'];
                $list[$artId]['article_content'] = $detail['article_content'];
                $list[$artId]['buy_back'] = $detail['buy_back'];
                $list[$artId]['articles_code'] = $detail['articles_code'];
                $dayRed[2] = ['nums' => $detail['two_red_nums'], 'pro' => floatval($detail['two_red_nums']) / 2];
                $dayRed[3] = ['nums' => $detail['three_red_nums'], 'pro' => floatval($detail['three_red_nums']) / 3];
                $dayRed[5] = ['nums' => $detail['five_red_nums'], 'pro' => floatval($detail['five_red_nums']) / 5];
                $dayRed[7] = ['nums' => $detail['day_red_nums'], 'pro' => floatval($detail['day_red_nums']) / 7];
                $tmpe = 0;
                foreach ($dayRed as $k => $v) {
                    if (round($v['pro'], 2) >= $tmpe) {
                        $tmpe = $v['pro'];
                        $nTmpe = $v['nums'];
                        $kTmpe = $k;
                    }
                }
                if ($tmpe < 0.5) {
                    $nTmpe = 0;
                    $kTmpe = 0;
                }
                $list[$artId]['day_red_nums'] = $nTmpe;
                $list[$artId]['day_nums'] = $kTmpe;
                if (array_key_exists('attent_status', $detail)) {
                    if (empty($detail['attent_status'])) {
                        $list[$artId]['attent_status'] = 2;
                    } else {
                        $list[$artId]['attent_status'] = $detail['attent_status'];
                    }
                } else {
                    $list[$artId]['attent_status'] = 2;
                }
            }
        }
        $list[$articleId]['pre_concent'] = array_values($list[$articleId]['pre_concent']);
        $detailData = $list[$articleId];
        return $this->render('/article/preview', ['detailData' => $detailData]);
    }

    public function actionToplatformview() {
        $title = "平台总览";
        $keywords = "";
        $description = "";


        return $this->render('/platform/platformview', $seoParams);
    }

}
