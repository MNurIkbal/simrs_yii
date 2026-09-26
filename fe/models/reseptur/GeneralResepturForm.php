<?php

/**
 * @Author: afil
 * @Date:   2018-01-18 13:35:28
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-22 17:26:08
 * @Description:
 */

namespace app\models\reseptur;

use Yii;

class GeneralResepturForm extends \yii\base\Model
{

    public $reseptur_id;
    public $pasienadmisi_id;
    public $ruangan_id;
    public $pasien_id;
    public $pegawai_id;
    public $pendaftaran_id;
    public $penjualanresep_id;
    public $tglreseptur;
    public $noresep;
    public $ruanganreseptur_id;
    public $fileresep;
    public $create_time;
    public $update_time;
    public $status_reseptur;
    public $antrian_id;
    public $create_ruangan;
    public $unitdosis_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    public $depo_id;
    public $pilih_template;
    public $iter;
    public $jenis_racikan;
    public $diagnosa_id;
    public $diagnosa_nama;
    public $dokter;
    public $is_hamil;
    public $berat_badan;
    public $tinggi_badan;
    public $luas_tubuh;
    public $catatan;
    public $waktu_pemberian;
    public $keterangan_pemberian;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reseptur_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reseptur_id'], 'required'],
            [['reseptur_id', 'pasienadmisi_id', 'ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'penjualanresep_id', 'ruanganreseptur_id', 'status_reseptur', 'antrian_id', 'create_ruangan', 'unitdosis_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['reseptur_id', 'pasienadmisi_id', 'ruangan_id', 'pasien_id', 'pegawai_id', 'pendaftaran_id', 'penjualanresep_id', 'ruanganreseptur_id', 'status_reseptur', 'antrian_id', 'create_ruangan', 'unitdosis_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglreseptur', 'create_time', 'update_time', 'created_date', 'last_modified_date', 'deleted_date', 'catatan'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['noresep'], 'string', 'max' => 50],
            [['fileresep'], 'string', 'max' => 500],
            [['reseptur_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'reseptur_id' => 'Reseptur ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'ruangan_id' => 'Ruangan ID',
            'pasien_id' => 'Pasien ID',
            'pegawai_id' => 'Pegawai ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'penjualanresep_id' => 'Penjualanresep ID',
            'tglreseptur' => 'Tanggal Reseptur',
            'noresep' => 'Noresep',
            'ruanganreseptur_id' => 'Ruanganreseptur ID',
            'fileresep' => 'Fileresep',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'status_reseptur' => 'Status Reseptur',
            'antrian_id' => 'Antrian ID',
            'create_ruangan' => 'Create Ruangan',
            'unitdosis_id' => 'Unitdosis ID',
            'additional_data' => Yii::t('fe', 'additional_data'),
            'created_date' => Yii::t('fe', 'created_date'),
            'created_by' => Yii::t('fe', 'created_by'),
            'modified_count' => Yii::t('fe', 'modified_count'),
            'last_modified_date' => Yii::t('fe', 'last_modified_date'),
            'last_modified_by' => Yii::t('fe', 'last_modified_by'),
            'is_deleted' => Yii::t('fe', 'is_deleted'),
            'is_active' => Yii::t('fe', 'is_active'),
            'deleted_date' => Yii::t('fe', 'deleted_date'),
            'deleted_by' => Yii::t('fe', 'deleted_by'),
            'diagnosa_nama' => 'Diagnosa',
            'dokter' => 'Dokter',
            'is_hamil' => 'Hamil',
            'berat_badan' => 'Berat Badan',
            'tinggi_badan' => 'Tinggi Badan',
            'luas_tubuh' => 'Luas Permukaan Tubuh',
            'iter' => 'Iterasi',
            'catatan' => 'Catatan'
        ];
    }
}
