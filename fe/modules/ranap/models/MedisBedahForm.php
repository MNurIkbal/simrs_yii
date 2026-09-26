<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class MedisBedahForm extends Model
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
    public $riwayat_penyakit_dahulu;
    public $riwayat_penyakit_dahulu_lainnya;
    public $nama_obat;
    public $dosis;
    public $waktu_penggunaan;
    public $riwayat_penyakit_keluarga;
    public $riwayat_penyakit_keluarga_lainnya;
    public $nyeri;
    public $lokasi_nyeri;
    public $intensitas_nyeri;
    public $jenis_nyeri;
    public $skor_nyeri;
    public $keadaan_umum;
    public $gizi;
    public $gcs_e;
    public $gcs_m;
    public $gcs_v;
    public $tindakan_resusitasi;
    public $berat_badan;
    public $tinggi_badan;
    public $tekanan_darah;
    public $frekuensi_nadi;
    public $frekuensi_nafas;
    public $suhu_badan;
    public $diagnosa;
    public $hasil_penunjang;
    public $terapi;
    public $hasil_pembedahan;
    public $diagnosa_akhir;

    /**
     * @return array the validation rules.
     */
    public function rules(
    ) {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [
                [
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'pasien_id',
                    'formasesmen_code',
                    'pemeriksaan_spesialis',
                    'is_dokumen_eklaim',
                    'asesmenmedis_id',
                    'keluhan_utama',
                    'riwayat_penyakit_sekarang',
                    'riwayat_penyakit_dahulu',
                    'riwayat_penyakit_dahulu_lainnya',
                    'nama_obat',
                    'dosis',
                    'waktu_penggunaan',
                    'riwayat_penyakit_keluarga',
                    'riwayat_penyakit_keluarga_lainnya',
                    'nyeri',
                    'lokasi_nyeri',
                    'intensitas_nyeri',
                    'jenis_nyeri',
                    'skor_nyeri',
                    'keadaan_umum',
                    'gizi',
                    'gcs_e',
                    'gcs_m',
                    'gcs_v',
                    'tindakan_resusitasi',
                    'berat_badan',
                    'tinggi_badan',
                    'tekanan_darah',
                    'frekuensi_nadi',
                    'frekuensi_nafas',
                    'suhu_badan',
                    'diagnosa',
                    'hasil_penunjang',
                    'terapi',
                    'hasil_pembedahan',
                    'diagnosa_akhir',
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
            
        ];
    }
}
