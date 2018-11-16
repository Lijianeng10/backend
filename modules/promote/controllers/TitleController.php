<?php

namespace app\modules\promote\controllers;

use Yii;
use yii\db\Query;
use yii\db\Exception;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class TitleController extends Controller {
    /**
     * 
     * @return type
     */
    public function actionIndex() {
        $data["title"] = \Yii::redisGet("title");
        $data["content"] = \Yii::redisGet("content");
        return $this->render('index', ['data'=>$data]);
    }
    /**
     * 更新标题内容
     */
    public function actionAddTitle(){
        $this->layout = false;
        $request = \Yii::$app->request;
        if (\Yii::$app->request->isPost) {
            $title= $request->post('title', '');
            $content = $request->post('content', '');
            if (empty($title)||empty($content)) {
                return $this->jsonResult(109, '参数缺失');
            }
            \Yii::redisSet("title", $title);
            \Yii::redisSet("content",$content);
            $this->jsonResult(600,"新增成功");
           
        } else {
            return $this->render('add-title');
        }
        
    }
     /**
     * 生成兑换码
     */
    public function getRedeemMark($nums) {
        $str = md5(uniqid(microtime(true), true));
        $mark = strtoupper(substr($str, 0, $nums));
        return $mark;
    }
}
