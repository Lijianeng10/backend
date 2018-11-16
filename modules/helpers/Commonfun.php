<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\helpers;

use Yii;
use yii\db\Query;
use app\modules\common\models\SysConf;

class Commonfun {

    /**
     * 获取权限url
     * @return array
     */
    public static function getAuthurls() {
//        $loginPort = "sys"
        $session = Yii::$app->session;
        $admin_role_model = new Query();
        $roleIds = $admin_role_model->select('admin_role.role_id')
                ->from(['admin_role' => 'sys_admin_role'])
                ->leftJoin(['role' => 'sys_role'], 'role.role_id = admin_role.role_id')
                ->where(['role.role_status' => '1'])
//                ->andWhere(["role.login_port" => $loginPort])
                ->andWhere(['admin_role.admin_id' => $session['admin_id']]);
        $role_auth_model = new Query();
        $authIds = $role_auth_model->select('auth_id')
                ->from('sys_role_auth')
                ->where(['in', 'role_id', $roleIds]);

        $auth_model = new Query();
        $authUrls = $auth_model->select('auth_url,auth_name,auth_pid,auth_id')
                ->from('sys_auth')
                ->where(['auth_status' => '1'])
                ->andWhere(['in', 'auth_id', $authIds])
                ->orderBy("auth_sort desc,auth_id asc")
                ->all();
        return $authUrls;
    }

    public static function getChildrens($pid, $menus) {
        $ret = [];
        foreach ($menus as $key => $value) {
            if ($value["auth_pid"] == $pid) {
                $value["childrens"] = self::getChildrens($value["auth_id"], $menus);
                $ret[] = $value;
                unset($menus[$key]);
            }
        }
        return $ret;
    }
    /**
     * 隐藏不显示模块
     */
    public static function delCloseAuth($mid,$menus){
        foreach ($menus as $key => $value) {
            if ($value["auth_id"] == $mid ||$value["auth_pid"] == $mid) {
                unset($menus[$key]);
            }
            if($value["auth_pid"] == $mid){
                $menus = self::delCloseAuth($value["auth_id"], $menus);
                unset($menus[$key]);
                
            }
        }
        return $menus;
    }
    /**
     * 说明:获取系统配置文件参数
     * @param   array $params //参数数组
     * @return
     */
    public static function getSysConf($params,$where = ['status'=>1]) {
        $configs = SysConf::find()->where(['in', 'code', $params])->andWhere($where)->asArray()->all();
        $res = [];
        foreach ($configs as $config) {
            $res[$config['code']] = $config['value'];
        }
        return $res;
    }
}
