<?php

namespace app\modules\pendaftaran\models;
use app\components\DocoBaseModel;

use Yii;

class ManajemenBpjsForm extends DocoBaseModel
{
    public $jenis_pencarian;
    public $no_sep;
    public $poli_tujuan;
    public $asal_rujukan;
    public $kode_dpjp_melayani;
    public $no_telp;
    public $tanggal_rujukan;
    public $diagnosa_awal;
    public $ppk_rujukan;
    public $no_rujukan;
    public $no_rekam_medik;
    public $tanggal_sep;
    public $catatan_sep;
    public $katarak;
    public $jenis_peserta;
    public $kasus_kecelakaan;
    public $tanggal_kejadian;
    public $kode_provinsi;
    public $kode_kabupaten;
    public $kode_kecamatan;
    public $keterangan;
    public $cob;
    public $poli_eksekutif;
    public $no_sep_suplesi;
    public $kelas_rawat;
    public $status_suplesi;
    public $nama_dpjp_melayani;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_telp','kode_dpjp_melayani','tanggal_rujukan','diagnosa_awal','ppk_rujukan','no_rekam_medik','tanggal_sep','kasus_kecelakaan'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'poli_tujuan' => Yii::t('fe', 'Spesialis / SubSpesialis'),
            'no_rujukan' => Yii::t('fe', 'No. Rujukan'),
        ];
    }
}
