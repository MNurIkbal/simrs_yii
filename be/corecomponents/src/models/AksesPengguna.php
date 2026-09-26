<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "aksespengguna_k".
 *
 * @property int $aksespengguna_id
 * @property int $peranpengguna_id
 * @property int $modul_id
 * @property int $loginpemakai_id
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 *
 */
class AksesPengguna extends \Doco\components\DocoActiveRecord
{
    public $peranpengguna_akses;
    public $peranpengguna_menu;
    public $is_exception;
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
    public $is_external_link;
    public $open_newtab;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aksespengguna_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['peranpengguna_id','loginpemakai_id'], 'required'],
            [['additional_data','created_date', 'last_modified_date', 'deleted_date',
            'created_date','created_by','modified_count','last_modified_date',
            'last_modified_by','is_deleted','is_active','deleted_date','deleted_by'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'aksespengguna_id' => 'Aksespengguna ID',
            'peranpengguna_id' => 'Peranpengguna ID',
            'modul_id' => 'Modul ID',
            'loginpemakai_id' => 'Loginpemakai ID',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

    public function getPeranPengguna()
    {
        return $this->hasOne(\Doco\models\PeranPengguna::classname(),['peranpengguna_id' => 'peranpengguna_id']);
    }
}