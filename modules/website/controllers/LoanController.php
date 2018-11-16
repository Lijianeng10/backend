<?php

namespace app\modules\website\controllers;

use app\modules\common\models\ApiLoan;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\helpers\UploadForm;
use app\modules\tools\helpers\Uploadfile;

class LoanController extends Controller {

    public function actionIndex() {
        $request = \Yii::$app->request;
        $get = $request->get();
        $title = $request->get('title', '');
        $status = $request->get('status', '');
        $where = ['and'];
        $loanList = ApiLoan::find()->select(['loan_id', 'title', 'sub_title', 'quota', 'profit', 'profit_remark', 'loan_periods', 'pic_url', 'jump_url', 'status', 'sort', 'create_time']);
        if (!empty($title)) {
            $where[] = ['or', ['like', 'title', $title], ['like', 'sub_title', $title]];
        }
        if (isset($status) && $status != '') {
            $where[] = ['status' => $status];
        }
        $statusList = ['' => '全部', '0' => '禁用', '1' => '启用'];
        $loanList = $loanList->where($where)->orderBy('create_time desc');
        $data = new ActiveDataProvider([
            'query' => $loanList,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('index', ['dataList' => $data, "get" => $get, "status" => $statusList]);
    }

    /**
     * 新增贷款
     * @return type
     */
    public function actionAddLoan() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $title = $request->post('title', '');
            $subTitle = $request->post('subTitle', '');
            $quota = $request->post('quota', '');
            $profit = $request->post('profit', 0);
            $profitRemarkTop = $request->post('profitRemarkTop', '');
            $profitRemarkTail = $request->post('profitRemarkTail', '');
            $loanPeriods = $request->post('loanPeriods', '');
            $sort = $request->post('sort', 99);
            $jumpUrl = $request->post('jumpUrl', '');
            if(empty($title) || ctype_space($title)) {
                return $this->jsonError(109, '请输入有效标题');
            }
            if(empty($quota) || ctype_space($quota)) {
                return $this->jsonError(109, '请输入有效额度范围');
            }
            if(empty($profitRemarkTop) || ctype_space($profitRemarkTop)) {
                return $this->jsonError(109, '请输入有效利率说明');
            }
            if(empty($loanPeriods) || ctype_space($loanPeriods)) {
                return $this->jsonError(109, '请输入有效贷款期间');
            }
            if(empty($jumpUrl) || ctype_space($jumpUrl)) {
                return $this->jsonError(109, '请输入有效跳转链接');
            }
            $apiLoan = new ApiLoan();
            if (isset($_FILES['upfile'])) {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/loan/';
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $apiLoan->pic_url = $path['result']['ret_path'];
            } else {
                return $this->jsonResult(109, '请上传图片', '');
            }
            $apiLoan->title = trim($title);
            $apiLoan->sub_title = trim($subTitle);
            $apiLoan->quota = trim($quota);
            $apiLoan->profit = $profit;
            $apiLoan->profit_remark = trim($profitRemarkTop) . '{'. $profit .'%}' . trim($profitRemarkTail);
            $apiLoan->loan_periods = trim($loanPeriods);
            $apiLoan->jump_url = trim($jumpUrl);
            $apiLoan->sort = $sort;
            $apiLoan->create_time = date('Y-m-d H:i:s');
            if(!$apiLoan->save()) {
                return $this->jsonResult(109, '新增失败', $apiLoan->errors);
            }
            return $this->jsonResult(600, '新增成功', true);
        } else {
            return $this->render('add-loan');
        }
    }
    
    /**
     * 编辑贷款
     * @return type
     */
    public function actionEditLoan() {
        $this->layout = false;
        if (\Yii::$app->request->isPost) {
            $request = \Yii::$app->request;
            $loanId = $request->post('loanId', '');
            $title = $request->post('title', '');
            $subTitle = $request->post('subTitle', '');
            $quota = $request->post('quota', '');
            $profit = $request->post('profit', 0);
            $profitRemarkTop = $request->post('profitRemarkTop', '');
            $profitRemarkTail = $request->post('profitRemarkTail', '');
            $loanPeriods = $request->post('loanPeriods', '');
            $sort = $request->post('sort', 99);
            $jumpUrl = $request->post('jumpUrl', '');
            $apiLoan = ApiLoan::findOne(['loan_id' => $loanId]);
            if(empty($apiLoan)) {
                return $this->jsonError(109, '无效数据！请重新打开载入！');
            }
            if(empty($title) || ctype_space($title)) {
                return $this->jsonError(109, '请输入有效标题');
            }
            if(empty($quota) || ctype_space($quota)) {
                return $this->jsonError(109, '请输入有效额度范围');
            }
            if(empty($profitRemarkTop) || ctype_space($profitRemarkTop)) {
                return $this->jsonError(109, '请输入有效利率说明');
            }
            if(empty($loanPeriods) || ctype_space($loanPeriods)) {
                return $this->jsonError(109, '请输入有效贷款期间');
            }
            if(empty($jumpUrl) || ctype_space($jumpUrl)) {
                return $this->jsonError(109, '请输入有效跳转链接');
            }
            if (isset($_FILES['upfile'])) {
                $file = $_FILES['upfile'];
                $check = UploadForm::getUpload($file);
                if ($check['code'] != 600) {
                    return $this->jsonResult($check['code'], $check['msg']);
                }
                $saveDir = '/loan/';
                $pathJson = Uploadfile::pic_host_upload($file, $saveDir);
                $path = json_decode($pathJson, true);
                if ($path['code'] != 600) {
                    return $this->jsonResult($path['code'], $path['msg']);
                }
                $apiLoan->pic_url = $path['result']['ret_path'];
            } else {
                $apiLoan->pic_url = $apiLoan->pic_url;
            }
            $apiLoan->title = trim($title);
            $apiLoan->sub_title = trim($subTitle);
            $apiLoan->quota = trim($quota);
            $apiLoan->profit = $profit;
            $apiLoan->profit_remark = trim($profitRemarkTop) . '{'. $profit .'%}' . trim($profitRemarkTail);
            $apiLoan->loan_periods = trim($loanPeriods);
            $apiLoan->jump_url = trim($jumpUrl);
            $apiLoan->sort = $sort;
            $apiLoan->modify_time = date('Y-m-d H:i:s');
            if(!$apiLoan->save()) {
                return $this->jsonResult(109, '编辑失败', $apiLoan->errors);
            }
            return $this->jsonResult(600, '编辑成功', true);
        } else {
            $request = \Yii::$app->request;
            $id = $request->get('id', '');
            $loanData = ApiLoan::find()->select(['loan_id', 'title', 'sub_title', 'quota', 'profit', 'profit_remark', 'loan_periods', 'pic_url', 'jump_url', 'sort'])->where(['loan_id' => $id])->asArray()->one();
            if(empty($loanData)) {
                echo '无效数据！请刷新重新操作！';
                exit;
            }
            $tmp = explode('{', $loanData['profit_remark']);
            $tp = explode('}', $tmp[1]);
            $loanData['profit_remark_top'] = $tmp[0];
            $loanData['profit_remark_tail'] = $tp[1];
            return $this->render('edit-loan', ['loanData' => $loanData]);
        }
    }
    
    public function actionEditStatus() {
        $request = \Yii::$app->request;
        $loanId = $request->post('loanId', '');
        $status = $request->post('status', '');
        $loanData = ApiLoan::findOne(['loan_id' => $loanId]);
        if(empty($loanData)) {
            return $this->jsonError(109, '无效数据！请重新刷新载入');
        }
        $loanData->status = $status;
        $loanData->modify_time = date('Y-m-d H:i:s');
        if(!$loanData->save()) {
            return $this->jsonError(109, '状态修改失败');
        }
        return $this->jsonResult(600, '修改成功', true);
    }
}
