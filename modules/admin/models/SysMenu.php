<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "sys_menu".
 *
 * @property integer $menu_id
 * @property string $menu_name
 * @property string $menu_url
 * @property integer $menu_pid
 */
class SysMenu extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public $ids;

    public static function tableName() {
        return 'sys_menu';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['menu_name', 'menu_url'], 'required'],
            [['menu_pid'], 'integer'],
            [['menu_name'], 'string', 'max' => 50],
            [['menu_url'], 'string', 'max' => 255],
            ['ids', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'menu_id' => 'Menu ID',
            'menu_name' => 'Menu Name',
            'menu_url' => 'Menu Url',
            'menu_pid' => 'Menu Pid',
        ];
    }

    public function deleteByids() {
        if ($this->validate(["ids"])) {
            $menuDel = $this->deleteAll(['in', 'menu_id', $this->ids]);
            return $menuDel;
        }
        return false;
    }

    public function deleteByid() {
        if ($this->validate(["menu_id"])) {
            $menuDel = $this->deleteAll(["menu_id" => $this->menu_id]);
            return $menuDel;
        }
        return false;
    }

}
