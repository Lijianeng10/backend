<?php

namespace app\modules\report;

/**
 * modules module definition class
 */
class report extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\report\controllers';
//    public $quanAry=[
//         "/admin/admin/editadmin" => ["/admin/admin"],
//        "/website/bananer/edit-status" => ["/website/bananer"],
//    ];

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
                     * 门店统计-统计报表
                     */
                    "/report/report/get-report" => ["/report/report"],
                    "/report/report/get-month-report" => ["/report/report"],
                    "/report/report/get-lottery-report" => ["/report/report"],
                    "/report/report/get-sale-lottery" => ["/report/report"],
                    "/report/report/saledetail" => ["/report/report"],
                    "/report/report/get-sale-detail" => ["/report/report"],
                    "/report/report/agent-detail" => ["/report/report"],
                    "/report/report/get-agent-detail" => ["/report/report"],
                    "/report/report/get-plat-from" => ["/report/report"],
                    "/report/report/get-spread-user" => ["/report/report"],
                    /**
                     * 门店统计-销售明细
                     */
                    "/report/saleorder/get-sale-order-list" => ["/report/saleorder"],
                    /**
                     * 门店统计-门店订单金额统计
                     */
                    "/report/group-money/get-month-report" => ["/report/group-money"],
                    /**
                     * 推广销售统计
                     */
                    "/report/spread-report/get-spread-report" => ["/report/spread-report"],
                    "/report/spread-report/detail" => ["/report/spread-report"],
                    "/report/spread-report/get-settle-detail" => ["/report/spread-report"],
                    "/report/spread-report/print-report" => ["/report/spread-report"],
                    "/report/spread-report/store-detail" => ["/report/spread-report"],
                    "/report/spread-report/get-store-detail" => ["/report/spread-report"],
                    "/report/spread-report/day-detail" => ["/report/spread-report"],
                    "/report/spread-report/print-day-cust-report" => ["/report/spread-report"],
                    //订单汇总
                    "/report/order-statistics/get-lottery" => ["/report/order-statistics"],
                    "/report/order-statistics/index" => ["/report/order-statistics"],
                    /**
                     * 渠道统计统计-统计报表
                     */
                    "/report/channel-report/get-report" => ["/report/channel-report"],
                    "/report/channel-report/get-month-report" => ["/report/channel-report"],
                    "/report/channel-report/get-lottery-report" => ["/report/channel-report"],
                    "/report/channel-report/store-detail" => ["/report/channel-report"],
                    "/report/channel-report/get-store-detail" => ["/report/channel-report"],
                    /**
                     * 代理商统计-统计报表
                     */
                    "/report/agent-report/get-report" => ["/report/agent-report"],
                    "/report/agent-report/get-month-report" => ["/report/agent-report"],
                    "/report/agent-report/get-lottery-report" => ["/report/agent-report"],
                    "/report/agent-report/store-detail" => ["/report/agent-report"],
                    "/report/agent-report/get-store-detail" => ["/report/agent-report"],
                    //财务
                    "/report/finance-statistics/index" => ["/report/finance-statistics"],
                    "/report/finance-statistics/get-statistic" => ["/report/finance-statistics"]


                ]

            ],
        ];
    }

}
