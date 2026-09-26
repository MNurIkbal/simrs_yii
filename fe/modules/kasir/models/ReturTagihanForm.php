<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-22 11:02:41
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-22 15:19:57
 */

namespace app\modules\kasir\models;

use Yii;

class ReturTagihanForm extends \yii\base\Model
{
    public $nobuktibayar;
    public $no_pendaftaran;
    public $no_rekam_medik;
    public $nama_pasien;
    public $carabayar_nama;
    public $penjamin_nama;
    public $kelaspelayanan_nama;
    public $tglbuktibayar;
    public $jmlpembayaran;
    public $ruangan_id;
    public $tandabuktibayar_id;
    public $tgl_returpelayanan;
    public $total_biayaretur;
    public $biaya_administrasi;
    public $e_collection;
    public $namapemilik_rek;
    public $no_rek;
    public $keterangan_pembayaran;
    public $pembulatan;
    public $uang_diserahkan;
    public $tunai;
    public $nontunai;
    public $keterangan;

    protected $xssProtected = [
        'keterangan_pembayaran'
    ];

    public function rules()
    {
        return [
            [['nobuktibayar','no_pendaftaran','no_rekam_medik','nama_pasien','carabayar_nama','penjamin_nama','kelaspelayanan_nama','tglbuktibayar','jmlpembayaran','ruangan_id','tandabuktibayar_id','tgl_returpelayanan','total_biayaretur','biaya_administrasi','e_collection','namapemilik_rek','no_rek','keterangan_pembayaran','pembulatan','uang_diserahkan', 'tunai', 'nontunai', 'keterangan'], 'safe'],
            // [['total_biayaretur'], 'required'],
            [['total_biayaretur'],'checkJmlPembayaran'],
            [['total_biayaretur'],'checkTotalRetur'],
            ['total_biayaretur', 'integer', 'min' => 1,  'tooSmall'=>'{attribute} tidak boleh kurang dari {min}']
        ];
    }

    public function attributeLabels(){
        return [
            "nobuktibayar" => Yii::t("app", "No. Kwitansi"),
            "no_pendaftaran" => Yii::t("app", "No. Pendaftaran"),
            "no_rekam_medik" => Yii::t("app", "No. Rekam Medik"),
            "nama_pasien" => Yii::t("app", "Nama Pasien"),
            "carabayar_nama" => Yii::t("app", "Cara Bayar"),
            "penjamin_nama" => Yii::t("app", "Nama Penjamin"),
            "kelaspelayanan_nama" => Yii::t("app", "Kelas Pelayanan"),
            "tglbuktibayar" => Yii::t("app", "Tanggal Bukti Bayar"),
            "jmlpembayaran" => Yii::t("app", "Total Tagihan"),
            "ruangan_id" => Yii::t("app", "Ruangan ID"),
            "tandabuktibayar_id" => Yii::t("app", "Tanda Bukti Bayar ID"),
            "tgl_returpelayanan" => Yii::t("app", "Tanggal Retur Pelayanan"),
            "total_biayaretur" => Yii::t("app", "Total Retur"),
            "biaya_administrasi" => Yii::t("app", "Biaya Administrasi"),
            "namapemilik_rek" => Yii::t("app", "Nama Pemilik Rekening"),
            "no_rek" => Yii::t("app", "No. Rekening"),
            "keterangan_pembayaran" => Yii::t("app", "Keterangan Pembayaran"),
            "pembulatan" => Yii::t("app", "Pembulatan"),
            "uang_diserahkan" => Yii::t("app", "Uang Diserahkan"),
            "uang_diserahkan" => Yii::t("app", "Uang Diserahkan"),
            "e_collection" => Yii::t("app", "E-Collection"),
            "tunai" => Yii::t("app", "Tunai"),
            "nontunai" => Yii::t("app", "Non-Tunai"),
            "keterangan " => Yii::t("app", "Keterangan"),
        ];
    }

    public function checkJmlPembayaran($attribute, $param)
    {
        if ($this->total_biayaretur > $this->jmlpembayaran) {
            $this->addError('total_biayaretur',Yii::t('fe','Total Retur Tidak Boleh Lebih dari Total Tagihan'));
        }
    }

    public function checkTotalRetur($attribute, $param)
    {
        if ($this->total_biayaretur < 1) {
            $this->addError('total_biayaretur',Yii::t('fe','Total Retur Tidak Boleh Kurang Dari 0 (nol)'));
        }
    }
}