<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class KulitForm extends Model
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
    public $keluhan_tambahan;
    public $anamnese_terpimpin;
    public $penyakit_dahulu;
    public $penyakit_dahulu_lainnya;
    public $nama_obat;
    public $dosis;
    public $waktu_penggunaan;
    public $riwayat_penyakit_keluarga;
    public $riwayat_penyakit_keluarga_lainnya;
    public $nyeri;
    public $omset;
    public $pencetus;
    public $lokasi_nyeri;
    public $skala_nyeri;
    public $gambaran_nyeri;
    public $durasi;
    public $frekuensi;
    public $pernah_dirawat;
    public $kapan_dirawat;
    public $dimana_dirawat;
    public $diagnosis_dirawat;
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
    public $gambaran_klinis;
    public $rencana_pemeriksaan;
    public $diagnosa_kerja;
    public $hasil_penunjang;
    public $terapi;
    public $rencana_kerja;
    public $pemeriksaan_fisik;

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
                    'keluhan_tambahan',
                    'anamnese_terpimpin',
                    'penyakit_dahulu',
                    'penyakit_dahulu_lainnya',
                    'nama_obat',
                    'dosis',
                    'waktu_penggunaan',
                    'riwayat_penyakit_keluarga',
                    'riwayat_penyakit_keluarga_lainnya',
                    'nyeri',
                    'omset',
                    'pencetus',
                    'lokasi_nyeri',
                    'skala_nyeri',
                    'gambaran_nyeri',
                    'durasi',
                    'frekuensi',
                    'pernah_dirawat',
                    'kapan_dirawat',
                    'dimana_dirawat',
                    'diagnosis_dirawat',
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
                    'gambaran_klinis',
                    'rencana_pemeriksaan',
                    'diagnosa_kerja',
                    'hasil_penunjang',
                    'terapi',
                    'rencana_kerja',
                    'pemeriksaan_fisik'
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
