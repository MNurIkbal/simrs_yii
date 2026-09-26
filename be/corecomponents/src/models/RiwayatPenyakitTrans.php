<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 11:41:38
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-15 11:41:43
 * @Description: 
 */

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "lookup_m".
 *
 * @property integer $lookup_id
 * @property string $lookup_type
 * @property string $lookup_name
 * @property string $lookup_value
 * @property integer $lookup_urutan
 * @property string $lookup_kode
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class RiwayatPenyakitTrans extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    
    public $keluhan;
    public $riwayat_diderita;
    public $riwayat_diderita_catatan;
    public $riwayat_alergi;
    public $riwayat_alergi_catatan;
    public $riwayat_dirawat_rs;
    public $riwayat_dirawat_rs_catatan;
    public $riwayat_operasi;
    public $riwayat_operasi_catatan;
    public $riwayat_imunisasi;
    public $riwayat_imunisasi_catatan;
    public $menstruasi;
    public $riwayat_kontrasepsi;
    public $riwayat_melahirkan;
    public $riwayat_keguguran;
    public $sedang_hamil;
    public $riwayat_pap_smear;
    public $riwayat_penyakit_keluarga;
    public $rokok;
    public $alkohol;
    public $kopi;
    public $olahraga;
    public $diet;
    public $tidur;
    public $obat_rutin;

    protected $xssProtected = [
        'keluhan',
        'riwayat_diderita_catatan',
        'riwayat_alergi_catatan',
        'riwayat_dirawat_rs_catatan',
        'riwayat_operasi_catatan',
        'riwayat_imunisasi_catatan',
        'menstruasi',
        'riwayat_kontrasepsi',
        'riwayat_melahirkan',
        'riwayat_keguguran',
        'sedang_hamil',
        'riwayat_pap_smear',
        'riwayat_penyakit_keluarga',
        'olahraga',
        'obat_rutin',
    ];
    
    public static function tableName()
    {
        return 'riwayatpenyakit_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['riwayat', 'riwayat_lainnya'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'riwayatpenyakit_id' => 'Riwayat Penyakit ID',
            'riwayat' => 'Riwayat',
            'riwayat_lainnya' => 'Riwayat Lainnya',
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
}
