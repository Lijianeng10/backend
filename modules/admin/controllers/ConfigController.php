<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/05/28
 * Time: 19:57:18
 */
namespace app\modules\admin\controllers;
use yii;
use yii\db\Query;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\common\models\SysConf;
use app\modules\common\helpers\Constants;

class ConfigController extends Controller {
    public function actionIndex() {
        $request = Yii::$app->request;
        $configStatus = Constants::CONFIG_STATUS;
        $configType = Constants::SYS_CONF_TYPE;
        $get = $request->get();
        $param = $request->get('param', '');
        $status = $request->get('status', '');
        $type = $request->get('type',0);
        $info=  SysConf::find();
        if(!empty($param)){
            $info = $info->andWhere(["or",["like","name",$param],["like","code",$param]]);
        }
        if($status!=""){
            $info = $info->andWhere(["status"=>$status]);
        }
        if(!empty($type)){
            $info = $info->andWhere(["type"=>$type]);
        }
        $info = $info->orderBy("id desc");
        $data = new ActiveDataProvider([
            'query' => $info,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);
        return $this->render('index', ['dataList' => $data,"get" => $get,"configStatus"=>$configStatus,"configType"=>$configType]);
    }
    /**
     * 新增参数
     */
    public function actionAddParam() {
        $this->layout = false;
        if (Yii::$app->request->isGet) {
            return $this->render("add-param");
        } elseif (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $code = $post["code"];
            $name = $post["name"];
            $type = $post["type"];
            $value = $post["value"];
            $remark = $post["remark"];
            if($code==""||$name==""||$type==""||$value==""){
                return $this->jsonResult(109, '参数有误，请检查重新填写');
            }
            $res = SysConf::find()->where(["or",["code"=>$code],["name"=>$name]])->one();
            if (!empty($res)) {
                return $this->jsonResult(109, "参数编号或者名称重复，请重新填写");
            }
            $sysConfig = new SysConf();
            $sysConfig->code = $code;
            $sysConfig->name = $name;
            $sysConfig->type = $type;
            $sysConfig->value = $value;
            $sysConfig->remark = $remark;
            $sysConfig->create_time = date("Y-m-d H:i:s");
            if ($sysConfig->validate()) {
                $res = $sysConfig->save();
                if ($res) {
                    return $this->jsonResult(600, "新增成功");
                } else {
                    return $this->jsonResult(109, "新增失败");
                }
            } else {
                return $this->jsonResult(109, "新增失败,初始化表单验证失败");
            }
        }
    }
    /**
     * 删除参数
     * @return json
     */
    public function actionDelParam() {
        $request = Yii::$app->request;
        $id = $request->post('id', '');
        if ($id == "") {
            return $this->jsonResult(109, '参数有误', '');
        }
        $result = SysConf::deleteAll(['id' => $id]);
        if ($result != false) {
            return $this->jsonResult(600, '删除成功', '');
        } else {
            return $this->jsonResult(109, '删除失败', '');
        }
    }
    /**
     * 编辑参数
     */
    public function actionEditParam(){
        if (Yii::$app->request->isGet) {
            $this->layout = false;
            $get=Yii::$app->request->get();
            $id=$get["id"];
            if(empty($id)){
                return $this->jsonResult(109, '参数有误', '');
            }
            $config = SysConf::find()->where(["id"=>$id])->asArray()->one();
            return $this->render('edit-param',["data"=>$config]);
        }elseif(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $id=$post["id"];
            $code = $post["code"];
            $name = $post["name"];
            $type = $post["type"];
            $value = $post["value"];
            $remark = $post["remark"];
            if($code==""||$name==""||$type==""||$value==""){
                return $this->jsonResult(109, '参数有误，请检查重新填写');
            }
            $res = SysConf::find()->where(["or",["code"=>$code],["name"=>$name]])->andWhere(["<>","id",$id])->one();
            if(!empty($res)){
                return $this->jsonResult(109, '参数编号重复，请重新填写');
            }
            $result = SysConf::updateAll(["code"=>$code,"name"=>$name,"type"=>$type,'value' => $value,"remark"=>$remark,"modify_time"=>date("Y-m-d H:i:s")], ['id' => $id]);
            if ($result != false) {
                return $this->jsonResult(600, '修改成功', '');
            } else {
                return $this->jsonResult(109, '修改失败', '');
            }
        }
    }
    /**
     * 修改银行启用禁用状态
     * @return json
     */
    public function actionEditStatus() {
        $request = Yii::$app->request;
        $id = $request->post('id', '');
        $status = $request->post('status', '');
        if ($id == "" || $status == "") {
            return $this->jsonResult(109, '参数有误', '');
        }
        $result = SysConf::updateAll(['status' => $status], ['id' => $id]);
        if ($result != false) {
            return $this->jsonResult(600, '状态修改成功', '');
        } else {
            return $this->jsonResult(109, '状态修改失败', '');
        }
    }
}
