<?php

namespace app\modules\cron\controllers;

use yii\web\Controller;

class BackupController extends Controller {

    public $defaultAction = 'main';
    private $userService = null;

    private $delArray =[
        [//篮球文字直播表
            'table_name'=>'lan_schedule_live',
            'num' => '1',
            'times' =>'MONTH'
        ],
        [//队列日志表
            'table_name'=>'queue',
            'num' => '1',
            'times' =>'MONTH'
        ],
        [//微信推送日志表
            'table_name'=>'wx_msg_record',
            'num' => '14',
            'times' =>'DAY'
        ],
        [//亚、欧盘
            'table_name'=>'asian_handicap',
            'num' => '1',
            'times' =>'MONTH'
        ],
        [//对奖记录表
            'table_name'=>'check_lottery_result_record',
            'num' => '1',
            'times' =>'MONTH'
        ],
        [//对奖记录表
            'table_name'=>'odds_3010',
            'num' => '1',
            'times' =>'MONTH'
        ],
    ];

    private $backUpArray =[
        [//投注订单表
            'table_name'=>'lottery_order',
            'num' => '3',
            'times' =>'MONTH'
        ],
        [//订单明细表
            'table_name'=>'betting_detail',
            'num' => '3',
            'times' =>'MONTH'
        ],

    ];

    public function __construct($id, $module, $config = []) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex() {
        echo 'this is /cron/backup/index';
        die;
    }

    /**
     * 说明:数据库表定时归档备份 3个月前的数据
     * @author chenqiwei
     * @date 2018/1/17 上午10:20
     * @param string table_name 备份的表名
     * @param int num 具体数值
     * @param string times 时间（DAY,MONTH)
     * @return
     */
    public function actionBackupTable(){
        $db = \Yii::$app->db;
        $request = \Yii::$app->request;
//        $tableName = $request->get('table_name');
//        if(empty($tableName)){
//            return $this->jsonError(109,'表名不能为空');
//        }
//        $num = $request->get('num',3);
//        $times = $request->get('times','MONTH');
        $sql ='';
        foreach ($this->backUpArray as $item){
            $sql .= "call time_backup('{$item['table_name']}',{$item['num']},'{$item['times']}');";
        }

        $ret = $db->createCommand($sql)->execute();
        return $this->jsonResult(600,'succ',$ret);
    }

    /**
     * 说明:数据库表定时删除
     * @author chenqiwei
     * @date 2018/1/17 上午10:20
     * @param string table_name 删除的表名
     * @param int num 具体数值
     * @param string times 时间（DAY,MONTH)
     * @return
     */
    public function actionDeleteTable(){
        $db = \Yii::$app->db;
        $sql = '';
        foreach ($this->delArray as $item){
            $sql .= "delete from {$item['table_name']} where  DATE_SUB(CURDATE(), INTERVAL {$item['num']} {$item['times']}) >date(create_time);";
        }
        $ret = $db->createCommand($sql)->execute();
        return $this->jsonResult(600,'succ',$ret);
    }

}
