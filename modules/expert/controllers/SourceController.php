<?php

namespace app\modules\expert\controllers;

use yii\web\Controller;
use app\modules\common\models\ExpertSource;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class SourceController extends Controller {
    
        
    public function actionGetSourceList() {
        $request = \Yii::$app->request;
        $get = $request->get();
        $sourceName = $request->get('sourceName', '');
        $query= (new Query())->select(['es.source_id', 'es.user_id', 'es.source_name', 'es.status', 'u.cust_no', 'u.user_tel', 'es.create_time'])
                ->from('expert_source es')
                ->leftJoin('user u', 'u.user_id = es.user_id');
        $where = ['and'];
        if (!empty($sourceName)) {
            $where[] = ['like', 'es.source_name', $sourceName];
        }
        
        $source =$query->where($where);
        $data = new ActiveDataProvider([
            'query' => $source,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('source-list', ['dataList' => $data,"get" => $get]);
    }
    
    public function actionAddBaseSource() {
        $this->layout = false;
        if(\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $sourceName = $request->post('sourceName');
            if(empty($sourceName) || ctype_space($sourceName)) {
                return $this->jsonError(109, '请输入有效的来源名称');
            }
            $exist = ExpertSource::findOne(['source_name' => trim($sourceName)]);
            if($exist) {
                return $this->jsonError(109, '该专家来源已存在');
            }
            $source = new ExpertSource();
            $source->source_name = trim($sourceName);
            $source->create_time = date('Y-m-d H:i:s');
            if(!$source->save()) {
                return $this->jsonError(109, '新增失败');
            }
            return $this->jsonResult(600, '新增成功', true);
        } else {
            return $this->render('add-base-source');
        }
    }
    
    public function actionEditBaseSource() {
        $this->layout = false;
        if(\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $sourceName = $request->post('sourceName');
            $sourceId = $request->post('sourceId');
            if(empty($sourceId)) {
                return $this->jsonError(109, '参数缺失');
            }
            if(empty($sourceName) || ctype_space($sourceName)) {
                return $this->jsonError(109, '请输入有效的来源名称');
            }
            $exist = ExpertSource::find()->select(['source_id'])->where(['and', ['source_name' => trim($sourceName)], ['!=', 'source_id' , $sourceId]])->asArray()->one();
            if($exist) {
                return $this->jsonError(109, '该专家来源已存在');
            }
            
            $source = ExpertSource::findOne(['source_id' => $sourceId]);
            $source->source_name = trim($sourceName);
            $source->modify_time = date('Y-m-d H:i:s');
            if(!$source->save()) {
                return $this->jsonError(109, '编辑失败失败');
            }
            return $this->jsonResult(600, '编辑成功', true);
        } else {
            $request = \Yii::$app->request;
            $sourceId = $request->get('sourceId');
            $sourceData = ExpertSource::find()->select(['source_id', 'source_name'])->where(['source_id' => $sourceId])->asArray()->one();
            if(empty($sourceData)) {
                echo '无效参数！请重新加载';
                exit;
            }
            return $this->render('edit-base-source', ['sourceData' => $sourceData]);
        }
    }
    
    public function actionEditBaseSta() {
        $request = \Yii::$app->request;
        $sourceId = $request->post('sourceId');
        $status = $request->post('status');
        $source = ExpertSource::findOne(['source_id' => $sourceId]);
        if(empty($source)) {
            return $this->jsonError(109, '无效参数');
        }
        $source->status = $status;
        $source->modify_time = date('Y-m-d H:i:s');
        if(!$source->save()) {
            return $this->jsonError(109, '修改失败');
        }
        return $this->jsonResult(600, '修改成功');
    }
}