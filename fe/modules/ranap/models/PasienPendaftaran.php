<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-03-04 17:07:17
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-08 11:03:15
 */
namespace app\modules\ranap\models;

use Yii;

class PasienPendaftaran extends \yii\base\Model
{
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $tgl_pendaftaran;
    public $penanggungjawab_id;
    public $penjamin_id;
    public $shift_id;
    public $pasien_id;
    public $persalinan_id;
    public $pegawai_id;
    public $instalasi_id;
    public $caramasuk_id;
    public $jeniskasuspenyakit_id;
    public $kelaspelayanan_id;
    public $carabayar_id;
    public $pasienadmisi_id;
    public $kelompokumur_id;
    public $golonganumur_id;
    public $rujukan_id;
    public $antrian_id;
    public $karcis_id;
    public $ruangan_id;
    public $no_urutantri;
    public $transportasi;
    public $keadaan_masuk;
    public $status_periksa;
    public $status_pasien;
    public $kunjungan;
    public $alih_status;
    public $by_phone;
    public $kunjungan_rumah;
    public $status_masuk;
    public $umur;
    public $tgl_selesaiperiksa;
    public $keterangan_pendaftaran;
    public $nopendaftaran_aktif;
    public $status_konfirmasi;
    public $tgl_konfirmasi;
    public $tgl_renkontrol;
    public $status_farmasi;
    public $panggil_antrian;
    public $asuransipasien_id;
    public $tgl_akandilayani;
    public $bpjs_id;

    // relations
    public $nama_pasien;
    public $no_rekam_medik;
    public $jeniskasuspenyakit_nama;
    public $nama_pegawai;
    public $carabayar_nama;
    public $penjamin_nama;
}