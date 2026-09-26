<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-07 17:29:27
 */

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class InspeksiusForm extends Model
{
    /**
     * {@inheritdoc}
     */
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $formasesmen_code;
    public $pemeriksaan_spesialis;
    public $is_dokumen_eklaim;
    public $asesmenmedis_id;

    public $keluhan_utama;
    public $riwayat_penyakit_sekarang;
    public $riwayat_penyakit_sekarang_lainnya;
    public $riwayat_penyakit_dahulu;
    public $riwayat_penyakit_dahulu_lainnya;
    public $diagnosis_ditegakkan;
    public $pasien_mengetahui_penyakit;
    public $sumber_info;
    public $sumber_info_lainnya;
    public $info_jangka_waktu_pengobatan;
    public $info_jangka_waktu_pengobatan_text;
    public $pemeriksaan_rutin;
    public $pemeriksaan_rutin_tempat;
    public $cara_penularan;
    public $isolasi;
    public $isolasi_lainnya;
    public $penggunaan_apc;
    public $penggunaan_apc_lainnya;
    public $penyakit_penyerta;
    public $penyakit_penyerta_text;
    public $pengkajian_psikologi;
    public $respon_pasien;
    public $kesadaran;
    public $gcsE;
    public $gcsM;
    public $gcsV;
    public $berat_badan;
    public $tinggi_badan;
    public $tekanan_darah;
    public $frekuensi_nadi;
    public $frekuensi_nafas;
    public $suhu_badan;
    public $pemeriksaan_fisik;
    public $pemeriksaan_fisik_lainnya;
    public $adlMakanMinum;
    public $adlMandi;
    public $adlBuangAir;
    public $adlBerpakaian;
    public $adlIstirahat;
    public $adlPenggunaanObat;
    public $diagnosa_keperawatan;
    public $rencana_keperawatan_dan_tindakan;
    public $pengkajian_sistem;
    public $diagnosa_medis;
    public $rencana_terapi_dan_tindakan;
    public $saran_nasehat_dokter;
    public $laboratorium;
    public $radiologi;


    /**
     * @return array the validation rules.
     */
    public function rules(
    ) {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'frekuensi_nadi', 'frekuensi_nafas'], 'integer'],
            [
                [
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'pasien_id',
                    'keluhan_utama',
                    'riwayat_penyakit_sekarang',
                    'pemeriksaan_spesialis',
                    'is_dokumen_eklaim',
                    'asesmenmedis_id',
                    'formasesmen_code',
                    'riwayat_penyakit_sekarang_lainnya',
                    'riwayat_penyakit_dahulu',
                    'riwayat_penyakit_dahulu_lainnya',
                    'diagnosis_ditegakkan',
                    'pasien_mengetahui_penyakit',
                    'sumber_info',
                    'sumber_info_lainnya',
                    'info_jangka_waktu_pengobatan',
                    'info_jangka_waktu_pengobatan_text',
                    'pemeriksaan_rutin',
                    'pemeriksaan_rutin_tempat',
                    'cara_penularan',
                    'isolasi',
                    'isolasi_lainnya',
                    'penggunaan_apc',
                    'penggunaan_apc_lainnya',
                    'penyakit_penyerta',
                    'penyakit_penyerta_text',
                    'pengkajian_psikologi',
                    'respon_pasien',
                    'kesadaran',
                    'gcsE',
                    'gcsM',
                    'gcsV',
                    'berat_badan',
                    'tinggi_badan',
                    'tekanan_darah',
                    'frekuensi_nadi',
                    'frekuensi_nafas',
                    'suhu_badan',
                    'pemeriksaan_fisik',
                    'pemeriksaan_fisik_lainnya',
                    'adlMakanMinum',
                    'adlMandi',
                    'adlBuangAir',
                    'adlBerpakaian',
                    'adlIstirahat',
                    'adlPenggunaanObat',
                    'pengkajian_sistem',
                    'diagnosa_medis',
                    'rencana_terapi_dan_tindakan',
                    'saran_nasehat_dokter',
                    'laboratorium',
                    'radiologi',
                    'diagnosa_keperawatan',
                    'rencana_keperawatan_dan_tindakan',
                    'tekanan_darah'
                ],
                'safe'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kesadaran' => 'Kesadaran',
            'gcsE' => 'E',
            'gcsM' => 'M',
            'gcsV' => 'V',
            'berat_badan' => 'Berat Badan',
            'tinggi_badan' => 'Tinggi Badan',
            'tekanan_darah' => 'Tekanan Darah',
            'frekuensi_nadi' => 'Frekuensi Nadi',
            'frekuensi_nafas' => 'Frekuensi Nafas',
            'suhu_badan' => 'Suhu Badan',
            'adlMakanMinum' => 'Makan/Minum',
            'adlMandi' => 'Mandi',
            'adlBuangAir' => 'BAB/BAK',
            'adlBerpakaian' => 'Berpakaian/Berhias',
            'adlIstirahat' => 'Istirahat dan Tidur',
            'adlPenggunaanObat' => 'Penggunaan Obat',
        ];
    }
}
