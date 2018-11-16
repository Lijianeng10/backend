<?php

namespace app\modules\subagents\controllers;

use Yii;
use yii\db\Query;
use yii\db\Exception;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\modules\member\helpers\Constants;
use app\modules\member\models\User;
use app\modules\member\models\UserLevels;
use app\modules\member\models\UserFunds;
use app\modules\member\models\IntergalRecord;
use app\modules\agents\services\IAgentsService;
use app\modules\agents\services\AgentsService;
use app\modules\common\models\UserFollow;
use app\modules\common\models\Store;

class UserlistController extends Controller {
    private $agentsService;

    public function __construct($id, $module, $config = [], IAgentsService $agentsService) {
        $this->agentsService = $agentsService;
        parent::__construct($id, $module, $config);
    }
    /**
     * 会员列表
     * @return type
     */
    public function actionIndex() {
        $authen = Constants::AUTHEN_STATUS;
        $memberType = Constants::MEMBR_TYPE;
        $compar = Constants::COMPAR;
        $vxstatus=[
            ""=>"请选择",
            "1"=>"已绑定",
            "2"=>"未绑定",
        ];
        $request = Yii::$app->request;
        $get = $request->get();
        $userInfo = $request->get('user_info', '');
        $agentsInfo = $request->get('agents_info', '');
        $userLevel = $request->get('user_level', '');
        $vxStatus = $request->get('vxstatus', '');
        $start = $request->get('startdate', '');
        $end = $request->get('enddate', '');
        $authenStatus = $request->get('authen_status', '');
        $glcoin = $request->get('glcoin', '');
        $user_glcoin = $request->get('user_glcoin', '');
        $balance = $request->get('balance', '');
        $balanceVal = $request->get('balance_val', '');
        $and5 = [];
        if ($vxStatus != '') {
            if($vxStatus==1){
                $and5="t.third_uid!=''and t.type = 1";
            }else{
                $and5="t.third_uid is null";
            }
        }
        $session = Yii::$app->session;
        $levels = (new UserLevels())->getLevelsList();
        //判断当前登录用户是代理商还是咕啦内部用户
        $where = [];
        if($session["type"]==1){
            if($session["agent_code"]=="gl00015788"){
               $where["u.agent_code"]=$session["admin_name"];  
            }else{
               $where["u.agent_code"]=$session["agent_code"]; 
            }
        }elseif($session["type"]==0){
            $where["u.agent_code"] = "gl00015788";
        }
        $query = new Query;
        $query = $query->select('u.*, f.all_funds,f.able_funds, f.ice_funds, f.user_integral, f.no_withdraw, f.user_glcoin, sum(p.pay_money) as all_pay,t.third_uid')
                ->from('user as u')
                ->leftJoin('user_funds as f', 'f.user_id = u.user_id')
                ->leftJoin('pay_record as p', 'p.user_id = u.user_id  and p.pay_type = 1')
                ->leftJoin('third_user as t', 't.uid = u.user_id  and t.type = 1')
                ->where($where)
                ->andWhere($and5);
        if ($userLevel != '' && $userLevel != '0') {
            $query = $query->andWhere(["u.level_id"=>$userLevel]);
        }
       
        if ($start != '') {
            $query = $query->andWhere(['>=', 'u.create_time', $start . ' 00:00:00']);
        }
        if ($end != '') {
            $query = $query->andWhere(['<=', 'u.create_time', $end . ' 23:59:59']);
        }
        if ($authenStatus != '') {
            $query = $query->andWhere(["u.authen_status"=>$authenStatus]);
        }     
        if ($userInfo != '') {
            $query = $query->andWhere(["or", ["u.cust_no" => $userInfo], ["u.user_name" => $userInfo], ["u.user_tel" => $userInfo]]);
        }
        if ($agentsInfo != '') {
            $query = $query->andWhere(["or", ["u.agent_code" => $agentsInfo], ["u.agent_name" => $agentsInfo]]);
        }
        if ($glcoin != '' && $glcoin != '0') {
            if ($user_glcoin != '') {
                $query = $query->andWhere([$glcoin,'f.user_glcoin',$user_glcoin]);
            }
        }
        if ($balance != '' && $balance != '0' && $balanceVal != '0') {
            if ($balanceVal != '') {
                $query = $query->andWhere([$balance,'f.all_funds',$balanceVal]);
            }
        }
        $data = $query->groupBy('u.user_id')
                ->orderBy('u.user_id');
        $provider = new ActiveDataProvider([
            'query' => $data,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['user_id'],
            ],
        ]);
        return $this->render('index', ['data' => $provider, 'levels' => $levels, 'authen' => $authen, 'user_type' => $memberType, 'compar' => $compar,'vxstatus'=>$vxstatus,'get' => $get]);
    }
     /**
     * 查看会员信息
     * @return array
     */
    public function actionViewMember() {
        if (Yii::$app->request->isGet) {
            $gender = Constants::GENDER;
            $status = Constants::MEMBER_STATUS;
            $authen = Constants::AUTHEN_STATUS;
            $session = Yii::$app->session;
            $levels = UserLevels::find()->where(['agent_code' => $session['admin_code']])->orderBy('user_level_id')->asArray()->all();
            $levelsList = [];
            foreach ($levels as $val) {
                $levelsList[$val['user_level_id']] = $val['level_name'];
            }
            $get = Yii::$app->request->get();
            if (!isset($get['user_id'])) {
                echo '参数错误';
                return $this->redirect('/agents/userlist/index');
            }
            $user = User::find()->select("user.*,f.all_funds,f.able_funds,f.ice_funds,f.no_withdraw,f.user_integral,f.user_glcoin,f.user_growth")
                    ->where(['user.user_id' => $get['user_id']])
                    ->leftJoin('user_funds as f', 'f.user_id = user.user_id')
                    ->asArray()
                    ->one();
            if (empty($user)){
                 return $this->jsonResult(109, '该会员不存在');
            }
            //获取会员收款账户信息
            $user["detail"] = $this->agentsService->javaGetUserAccountDetail($user["cust_no"]);
            //获取会员身份证信息
            $user["card"] = $this->agentsService->javaGetRealName($user["cust_no"]);
            //获取会员是否实名认证
            $user["javaStatus"] = $this->agentsService->javaGetStatus($user["cust_no"]);
            //获取会员绑定门店信息
            $storeInfo=  UserFollow::find()->select("user_follow.*,s.*")
                    ->where(["user_follow.cust_no"=>$user["cust_no"],"user_follow.follow_status"=>1])
                    ->leftJoin("store as s","s.store_code=user_follow.store_id")
                    ->asArray()
                    ->all();
            return $this->render('view-member', ['user_data' => $user,'storeInfo' => $storeInfo, 'gender' => $gender, 'user_status' => $status, 'levels' => $levelsList, 'authen' => $authen]);
        } else {
            return $this->jsonResult(109, '操作错误');
        }
    }
    /**
     * 会员列表
     * @return type
     */
    public function actionIndex_1() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
//        $itemid = isset($_POST['itemid']) ? mysql_real_escape_string($_POST['itemid']) : '';
//        $productid = isset($_POST['productid']) ? mysql_real_escape_string($_POST['productid']) : '';
        $offset = ($page-1)*$rows;
        $authen = Constants::AUTHEN_STATUS;
        $memberType = Constants::MEMBR_TYPE;
        $compar = Constants::COMPAR;
        $vxstatus=[
            ""=>"请选择",
            "1"=>"已绑定",
            "2"=>"未绑定",
        ];
        $request = Yii::$app->request;
        $get = $request->get();
        $userInfo = $request->get('user_info', '');
        $agentsInfo = $request->get('agents_info', '');
        $userLevel = $request->get('user_level', '');
        $vxStatus = $request->get('vxstatus', '');
        $start = $request->get('startdate', '');
        $end = $request->get('enddate', '');
        $authenStatus = $request->get('authen_status', '');
        $glcoin = $request->get('glcoin', '');
        $user_glcoin = $request->get('user_glcoin', '');
        $balance = $request->get('balance', '');
        $balanceVal = $request->get('balance_val', '');
        $and5 = [];
        if ($vxStatus != '') {
            if($vxStatus==1){
                $and5="t.third_uid!=''and t.type = 1";
            }else{
                $and5="t.third_uid is null";
            }
        }
        $session = Yii::$app->session;
        $levels = (new UserLevels())->getLevelsList();
        //判断当前登录用户是代理商还是咕啦内部用户
        $where = [];
        if($session["type"]==1){
            if($session["agent_code"]=="gl00015788"){
               $where["u.agent_code"]=$session["admin_name"];  
            }else{
               $where["u.agent_code"]=$session["agent_code"]; 
            }
        }else{
            $where["u.agent_code"] = "gl00015788";
        }
        $query = new Query;
        $query = $query->select('u.*, f.all_funds,f.able_funds, f.ice_funds, f.user_integral, f.no_withdraw, f.user_glcoin, sum(p.pay_money) as all_pay,t.third_uid')
                ->from('user as u')
                ->leftJoin('user_funds as f', 'f.user_id = u.user_id')
                ->leftJoin('pay_record as p', 'p.user_id = u.user_id  and p.pay_type = 1')
                ->leftJoin('third_user as t', 't.uid = u.user_id  and t.type = 1')
                ->where($where)
                ->andWhere($and5);
        if ($userLevel != '' && $userLevel != '0') {
            $query = $query->andWhere(["u.level_id"=>$userLevel]);
        }
        if ($start != '') {
            $query = $query->andWhere(['>=', 'u.create_time', $start . ' 00:00:00']);
        }
        if ($end != '') {
            $query = $query->andWhere(['<=', 'u.create_time', $end . ' 23:59:59']);
        }
        if ($authenStatus != '') {
            $query = $query->andWhere(["u.authen_status"=>$authenStatus]);
        }     
        if ($userInfo != '') {
            $query = $query->andWhere(["or", ["u.cust_no" => $userInfo], ["u.user_name" => $userInfo], ["u.user_tel" => $userInfo]]);
        }
        if ($agentsInfo != '') {
            $query = $query->andWhere(["or", ["u.agent_code" => $agentsInfo], ["u.agent_name" => $agentsInfo]]);
        }
        if ($glcoin != '' && $glcoin != '0') {
            if ($user_glcoin != '') {
                $query = $query->andWhere([$glcoin,'f.user_glcoin',$user_glcoin]);
            }
        }
        if ($balance != '' && $balance != '0' && $balanceVal != '0') {
            if ($balanceVal != '') {
                $query = $query->andWhere([$balance,'f.all_funds',$balanceVal]);
            }
        }
        $counts = $query->count();
        $result = array();
        $result["total"] = $counts;
        $data = $query->limit($rows)
                ->offset($offset)
                ->orderBy('u.user_id')
                ->all();
       $result["rows"] = $data;
       echo json_encode($result);
//        $provider = new ActiveDataProvider([
//            'query' => $data,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
//            'sort' => [
//                'attributes' => ['user_id'],
//            ],
//        ]);
//        return $this->render('index_1', ['data' => $provider, 'levels' => $levels, 'authen' => $authen, 'user_type' => $memberType, 'compar' => $compar,'vxstatus'=>$vxstatus,'get' => $get]);
    }
}

