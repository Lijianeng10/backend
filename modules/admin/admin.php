<?php

namespace app\modules\admin;

/**
 * admin module definition class
 */
class admin extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

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
                "class" => 'app\modules\core\filters\LoginFilter',
                "except" => [
                    'admin/login',
                ]
            ],
            "AuthFilter" => [
                "class" => 'app\modules\core\filters\AuthFilter',
                "except" => [
                    'admin/login',
                    'admin/logout',
                ],
                "children" =>[
                    /**
                     * 用户管理
                     */
                    "/admin/admin/readadmin" => ["/admin/admin"],
//                    "/admin/admin/editadmin" => ["/admin/admin"],
//                    "/admin/admin/saveadmin" => ["/admin/admin"],
//                    "/admin/admin/editstatus" => ["/admin/admin"],
//                    "/admin/admin/deletebyid" => ["/admin/admin"],
//                    "/admin/admin/addadmin" => ["/admin/admin"],
//                    "/admin/admin/deletebyids" => ["/admin/admin"],
                    /**
                     * 角色管理
                     */
//                    "/admin/role/access" => ["/admin/role"],
                    "/admin/role/adminrole" => ["/admin/role"],
//                    "/admin/role/editsta" => ["/admin/role"],
//                    "/admin/role/delbyid" => ["/admin/role"],
//                    "/admin/role/upauth" => ["/admin/role"],
//                    "/admin/role/addrole" => ["/admin/role"],
                    /**
                     * 权限管理
                     */
//                    "/admin/auth/changesort" => ["/admin/auth"],
//                    "/admin/auth/editauth" => ["/admin/auth"],
//                    "/admin/auth/saveauth" => ["/admin/auth"],
//                    "/admin/auth/deletebyid" => ["/admin/auth"],
//                    "/admin/auth/deletebyids" => ["/admin/auth"],
//                    "/admin/auth/addauth" => ["/admin/auth"],
                    /**
                     * 支付方式
                     */
//                    "/admin/paytype/set-pay-type" => ["/admin/paytype"],
//                    "/admin/paytype/set-default" => ["/admin/paytype"],
//                    "/admin/paytype/edit-pay-type" => ["/admin/paytype"],
//                    "/admin/paytype/delete-type" => ["/admin/paytype"],
//                    "/admin/paytype/add-pay-type" => ["/admin/paytype"],
                    /**
                     * 系统图片
                     */
//                    "/admin/picture/delete" => ["/admin/picture"],
//                    "/admin/picture/add-picture" => ["/admin/picture"],
//                    "/admin/picture/edit" => ["/admin/picture"],
                ]
            ],
        ];
    }

}
