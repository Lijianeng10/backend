<?php

namespace app\modules\lottery;

/**
 * modules module definition class
 */
class lottery extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\lottery\controllers';

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
                    /**
                     * 投注记录
                     */
                    "/lottery/betting/readdetail12" => ["/lottery/betting"],
                    "/lottery/betting/readdetail3" => ["/lottery/betting/"],
                    "/lottery/betting/readdetail4" => ["/lottery/betting"],
                    "/lottery/betting/lan-detail" => ["/lottery/betting"],
                    "/lottery/betting/get-deatail-list" => ["/lottery/betting/"],
                    "/lottery/betting/get-more-detail" => ["/lottery/betting/"],
                    "/lottery/betting/read-bd-detail" => ["/lottery/betting/"],
                    "/lottery/betting/bd-detail" => ["/lottery/betting"],
                    "/lottery/betting/get-bd-more-detail" => ["/lottery/betting"],
                    "/lottery/betting/get-taking-detail" => ['/lottery/betting'], // 出票详情
                    "/lottery/betting/get-order-from" => ['/lottery/betting'], // 出票详情
//                    "/lottery/betting/do-award" => ["/lottery/betting"],
                    /**
                     * 追号管理
                     */
                    "/lottery/trace/readdetail" => ["/lottery/trace"],
                    /**
                     * 合买管理
                     */
                    "/lottery/chipped/get-programme-detail" => ["/lottery/chipped"],
                    "/lottery/chipped/readdetail" => ["/lottery/chipped"],
                    /**
                     * 彩种管理
                     */
//                    "/lottery/lottery/add-category" => ["/lottery/lottery"],
//                    "/lottery/lottery/editlottery" => ["/lottery/lottery"],
//                    "/lottery/lottery/edit" => ["/lottery/lottery"],
//                    "/lottery/lottery/editsta" => ["/lottery/lottery"],
//                    "/lottery/lottery/editsale" => ["/lottery/lottery"],
//                    "/lottery/lottery/edit-result" => ["/lottery/lottery"],
//                    "/lottery/lottery/dellottery" => ["/lottery/lottery"],
                    
                    /**
                     * 开奖时间设置
                     */
//                    "/lottery/time/setting" => ["/lottery/time"],
//                    "/lottery/time/savedata" => ["/lottery/time"],
                    /**
                     * 奖级设置
                     */ 
//                    "/lottery/awards/addawards" => ["/lottery/awards"],
//                    "/lottery/awards/edit" => ["/lottery/awards"],
//                    "/lottery/awards/editawards" => ["/lottery/awards"],
//                    "/lottery/awards/delaward" => ["/lottery/awards"],
                    /**
                     * 玩法编码/格式
                     */
//                    "/lottery/play/addplay" => ["/lottery/play"],
//                    "/lottery/play/edit" => ["/lottery/play"],
//                    "/lottery/play/editplay" => ["/lottery/play"],
//                    "/lottery/play/delplay" => ["/lottery/play"],
                    /**
                     * 足球联赛管理
                     */
//                    "/lottery/race/addrace" => ["/lottery/race"],
//                    "/lottery/race/league-team" => ["/lottery/race"],
//                    "/lottery/race/editrace" => ["/lottery/race"],
//                    "/lottery/race/do-edit-race" => ["/lottery/race"],
//                    "/lottery/play/edit-status" => ["/lottery/play"],
//                    "/lottery/play/delete-race" => ["/lottery/play"],
                    /**
                     * 足球球队管理
                     */
//                    "/lottery/team/addteam" => ["/lottery/team"],
//                    "/lottery/team/editteam" => ["/lottery/team"],
//                    "/lottery/team/do-edit-team" => ["/lottery/team"],
//                    "/lottery/team/status-team" => ["/lottery/team"],
//                    "/lottery/team/delete-team" => ["/lottery/team"],
                    /**
                     * 开奖记录
                     */
                    "/lottery/result/list" => ["/lottery/result"],
                    /**
                     * 竞彩赛程结果
                     */
//                    "/lottery/schresult/index" => ["/lottery/schresult"],
                    /**
                     * 足球赛程
                     */
//                    "/lottery/schedule/addschedule" => ["/lottery/schedule"],
//                    "/lottery/schedule/saveschedule" => ["/lottery/schedule"],
                    "/lottery/schedule/readbonus" => ["/lottery/schedule"],
//                    "/lottery/schedule/editschedule" => ["/lottery/schedule"],
//                    "/lottery/schedule/releaseschedule" => ["/lottery/schedule"],
//                    "/lottery/schedule/addodds3010" => ["/lottery/schedule"],
//                    "/lottery/schedule/editodds3010" => ["/lottery/schedule"],
//                    "/lottery/schedule/deleteodds3010" => ["/lottery/schedule"],
//                    "/lottery/schedule/addodds3007" => ["/lottery/schedule"],
//                    "/lottery/schedule/editodds3007" => ["/lottery/schedule"],
//                    "/lottery/schedule/deleteodds3007" => ["/lottery/schedule"],
//                    "/lottery/schedule/addrqspfbonus" => ["/lottery/schedule"],
//                    "/lottery/schedule/addzjqsbonus" => ["/lottery/schedule"],
//                    "/lottery/schedule/addbqcspfbonus" => ["/lottery/schedule"],
//                    "/lottery/schedule/editrqspfbonus" => ["/lottery/schedule"],
//                    "/lottery/schedule/editzjqsbonus" => ["/lottery/schedule"],
//                    "/lottery/schedule/editbqcspfbonus" => ["/lottery/schedule"],
//                    "/lottery/schedule/deleterqspfbonus" => ["/lottery/schedule"],
//                    "/lottery/schedule/deletezjqsbonus" => ["/lottery/schedule"],
//                    "/lottery/schedule/deletebqcspfbonus" => ["/lottery/schedule"],
                    /**
                     * 竞彩篮球赛程
                     */
//                    "/lottery/lanschedule/editschedule" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/save-lan-schedule" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/edit-lan-status" => ["/lottery/lanschedule"],
                    "/lottery/lanschedule/readlanbonus" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/editlanresult" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/addodds3001" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/editodds3001" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/deleteodds3001" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/addodds3002" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/editodds3002" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/deleteodds3002" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/addodds3003" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/editodds3003" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/deleteodds3003" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/addodds3004" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/editodds3004" => ["/lottery/lanschedule"],
//                    "/lottery/lanschedule/deleteodds3004" => ["/lottery/lanschedule"],
                    /**
                     * 随机订单
                     */
//                    "/lottery/random/addorder" => ["/lottery/random"],
                    //取消赛程对奖
//                    '/lottery/schresult/deal-delay-order' => ['/lottery/schresult'],
//                    '/lottery/schresult/deal-delay-award' => ['/lottery/schresult'],
                    //自动出票查看
                    "/lottery/autoticket/auto-order-read" => ["/lottery/autoticket"],
                ]
            ],
        ];
    }

}
