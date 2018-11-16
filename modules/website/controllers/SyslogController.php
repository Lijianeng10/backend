<?php

namespace app\modules\website\controllers;

use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**系统日志
 * Default controller for the `index` module
 */
class SyslogController extends Controller{
    /**
     * Renders the index view for the module
     * @return string
     */
//    public $enableCsrfValidation = false;

    public function actionIndex(){
        $get= \Yii::$app->request->get();
        $where=['and'];
        (isset($get["type"])&&$get["type"])&&$where[]=['=','type',$get["type"]];
        (isset($get["startdate"])&&$get["startdate"])&&$where[]=['>=','log_ctime',strtotime($get["startdate"])];
        (isset($get["enddate"])&&$get["enddate"])&&$where[]=['<=','log_ctime',strtotime($get["enddate"])];
        (isset($get["content"])&&$get["content"])&&$where[]=['like','data',$get["content"]];
        $tableName='business_log';
        if(YII_ENV_DEV){
        	$tableName.='_dev';
        }
        if(count($where)==1){
        	$where=[];
        }
        //$sysData=\Yii::$app->db2->createCommand("select * from {$tableName}",$where)->queryAll();
        $sysData=(new \yii\db\Query())->select('*')->from($tableName)->where($where)->orderBy('log_ctime DESC');
        $logType=(new \yii\db\Query())->select('type')->from($tableName)->where([])->distinct('type')->all(\Yii::$app->db2);
        $logType=array_column($logType, 'type');
        //$logType = array_combine($logType, $logType);
        //array_unshift($logType,'全部');
        $data = new ActiveDataProvider([
            'query' => $sysData,
            'pagination' => [
                'pageSize' =>20
            ]
        ]);
        $data->db=\Yii::$app->db2;
        return $this->render('index', ['data' => $data,'get'=>$get,"logType"=>$logType]);
    }
}