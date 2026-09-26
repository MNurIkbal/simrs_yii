<?php

namespace Doco\master\models;

use Yii;

class ModulExternalForm extends \yii\base\Model
{
    public $modul_id;
    public $modul_nama;
    public $modul_namalainnya;
    public $modul_fungsi;
    public $url_modul;
    public $url_page;
    public $icon_modul;
    public $modul_key;
    public $modul_urutan;
    public $modul_kategori;
    public $imagemodul;
    public $additional_data;
    public $is_active;
    public $dashboard_id;
    public $open_newtab;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['modul_nama', 'url_modul'], 'required'],
            [['modul_fungsi', 'imagemodul', 'additional_data', 'url_page', 'dashboard_id'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','modul_namalainnya','modul_id','imagemodul','additional_data','is_active', 'open_newtab'], 'safe'],
            [['dashboard_id'], 'requiredDashboardID', 'skipOnEmpty' => false, 'skipOnError' => false],
            [['modul_nama', 'modul_nama'], 'string', 'max' => 50],
            [['url_modul'], 'string'],
            [['icon_modul', 'modul_kategori'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'modul_id' => Yii::t('fe','Modul ID'),
            'modul_nama' => Yii::t('fe','Nama Modul'),
            'modul_namalainnya' => Yii::t('fe','Nama Alias'),
            'modul_fungsi' => Yii::t('fe','Modul Fungsi'),
            'url_modul' => Yii::t('fe','Url Modul'),
            'url_page' => Yii::t('fe','Url Page'),
            'dashboard_id' => Yii::t('fe','Dashboard ID'),
            'icon_modul' => Yii::t('fe','Icon Modul'),
            'modul_key' => Yii::t('fe','Modul Key'),
            'modul_urutan' => Yii::t('fe','Modul Urutan'),
            'modul_kategori' => Yii::t('fe','Modul Kategori'),
            'imagemodul' => Yii::t('fe','Image Modul'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Status'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
            'open_newtab' => Yii::t('fe','Open in new tab'),
        ];
    }

    public function requiredDashboardID($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->url_page === '__dashboard__' && empty($this->dashboard_id)) {
                $this->addError($attribute, Yii::t('fe','Dashboard ID harus diisi.'));
            }
        }
    }
}
