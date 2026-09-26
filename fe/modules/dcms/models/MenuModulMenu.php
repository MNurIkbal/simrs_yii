<?php

namespace Doco\dcms\models;

use Yii;

class MenuModulMenu extends \yii\base\Model
{
    public $kelmenu_id;
    public $menu_id;
    public $modul_id;
    public $menu_nama;
    public $menu_namalainnya;
    public $menu_key;
    public $menu_url;
    public $menu_fungsi;
    public $menu_urutan;
    public $menu_icon;
    public $menu_shortcut;
    public $parentmenu_id;
    public $groupmenu_id;


    public $list_api;
    public $controller_name = '-';

    public $akses;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelmenu_id', 'modul_id', 'menu_nama', 
              'menu_namalainnya','menu_key','menu_url',
              'menu_fungsi','menu_urutan','menu_icon',
              'menu_shortcut','parentmenu_id','groupmenu_id','list_api','controller_name','akses','menu_id'], 'safe'],
            [['menu_namalainnya','menu_url','controller_name'],'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'menu_id' => Yii::t('fe','Menu ID'),
            'kelmenu_id' => Yii::t('fe','Kelmenu ID'),
            'modul_id' => Yii::t('fe','Modul ID'),
            'menu_nama' => Yii::t('fe','Menu Nama'),
            'menu_namalainnya' => Yii::t('fe','Menu Namalainnya'),
            'menu_key' => Yii::t('fe','Menu Key'),
            'menu_url' => Yii::t('fe','Menu Url'),
            'menu_fungsi' => Yii::t('fe','Menu Fungsi'),
            'menu_urutan' => Yii::t('fe','Menu Urutan'),
            'menu_icon' => Yii::t('fe','Menu Icon'),
            'menu_shortcut' => Yii::t('fe','Menu Shortcut'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
            'list_api' => Yii::t('fe','Service'),
            'controller_name' => Yii::t('fe','Nama Controller'),
        ];
    }
}