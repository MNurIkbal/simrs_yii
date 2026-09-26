<?php

namespace app\modules\penatajasa\models;

use Yii;

class PenjaminForm extends \yii\base\Model
{
    public $carabayar_id;
    public $penjamin_id;
    public $id_penjamin;
    public $nokartuasuransi;
    public $nama_pemilik;
    public $nominal_dijamin;
    public $asuransipasien_id;

    public $pasien_id;
    public $namapemilikasuransi;
    public $nomorpokokperusahaan;
    public $kelastanggunganasuransi_id;
    public $namaperusahaan;
    public $tgl_konfirmasi;
    public $status_konfirmasi;
    public $pendaftaran_id;

    public $carabayar_nama;
    public $penjamin_nama;
    public $nama_pasien;
    public $penjamin_carabayar;

    public function rules()
    {
        return [
            [['carabayar_id', 'penjamin_id', 'nokartuasuransi', 'nominal_dijamin'], 'required', 'message' => '{attribute} Harus Diisi!'],
            [['carabayar_id', 'penjamin_carabayar', 'pendaftaran_id', 'penjamin_id', 'id_penjamin','nokartuasuransi','nama_pemilik','nominal_dijamin','namapemilikasuransi','nomorpokokperusahaan','kelastanggunganasuransi_id','namaperusahaan','tgl_konfirmasi','status_konfirmasi','pasien_id','asuransipasien_id','carabayar_nama','penjamin_nama','nama_pasien'], 'safe'],
            ['nominal_dijamin', 'compare','compareValue' => '0','operator' => '>', 'type' => 'number','message' => 'Nominal Dijamin Tidak Boleh 0 !']
        ];
    }
    public function attributeLabels()
    {
        return [
            'carabayar_id' => Yii::t('fe', 'Cara Bayar'),
            'penjamin_id' => Yii::t('fe', 'Penjamin'),
            'nokartuasuransi' => Yii::t('fe', 'No. Asuransi'),
            'nama_pemilik' => Yii::t('fe', 'Nama Pemilik'),
            'nominal_dijamin' => Yii::t('fe', 'Nominal Dijamin'),
            'namapemilikasuransi' => Yii::t('fe', 'Nama Pemilik'),
            'nomorpokokperusahaan' => Yii::t('fe', 'No. Pokok Perusahaan'),
            'kelastanggunganasuransi_id' => Yii::t('fe', 'Kelas Tanggungan'),
            'namaperusahaan' => Yii::t('fe', 'Nama Perusahaan'),
            'tgl_konfirmasi' => Yii::t('fe', 'Tanggal Konfirmasi'),
            'status_konfirmasi' => Yii::t('fe', 'Sudah Konfirmasi'),
        ];
    }
}