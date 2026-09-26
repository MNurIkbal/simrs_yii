<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-07 17:29:27
 */

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class KekerasanForm extends Model
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

    // Askep
    public $tempat_tanggal_kejadian;
    public $alasan_masuk_rs;
    public $alasan_masuk_rs_input;
    public $keluhan_utama;
    public $riwayat_keluhan_utama;

    public $askep_kesadaran;
    public $askep_gcs;
    public $askep_gcs_e;
    public $askep_gcs_v;
    public $askep_gcs_m;
    public $berat_badan;
    public $tinggi_badan;
    public $tekanan_darah;
    public $frekuensi_nadi;
    public $frekuensi_nafas;
    public $suhu_tubuh;

    public $pengkajian_psikologis;
    public $diagnosa_keperawatan;
    public $rencana_keperawatan;

    // Asmed
    public $jalan_nafas;
    public $jalan_nafas_input;
    public $pernafasan;
    public $pernafasan_input;
    public $sirkulasi;
    public $sirkulasi_input;
    public $sirkulasi_crt;
    public $asmed_kesadaran;
    public $asmed_gcs;
    public $asmed_gcs_e;
    public $asmed_gcs_v;
    public $asmed_gcs_m;

    public $tindakan_pengobatan;
    public $saran_dokter;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [[
                'pendaftaran_id',
                'pasienadmisi_id',
                'pasien_id',
                'keluhan_utama',
                'riwayat_penyakit_sekarang',
                'pemeriksaan_spesialis',
                'formasesmen_code',
                'is_dokumen_eklaim',
                'asesmenmedis_id',

                // Askep
                'tempat_tanggal_kejadian',
                'alasan_masuk_rs',
                'alasan_masuk_rs_input',
                'keluhan_utama',
                'riwayat_keluhan_utama',

                'askep_kesadaran',
                'askep_gcs',
                'askep_gcs_e',
                'askep_gcs_v',
                'askep_gcs_m',
                'berat_badan',
                'tinggi_badan',
                'tekanan_darah',
                'frekuensi_nadi',
                'frekuensi_nafas',
                'suhu_tubuh',

                'pengkajian_psikologis',
                'diagnosa_keperawatan',
                'rencana_keperawatan',

                // Asmed
                'jalan_nafas',
                'jalan_nafas_input',
                'pernafasan',
                'pernafasan_input',
                'sirkulasi',
                'sirkulasi_input',
                'sirkulasi_crt',
                'asmed_kesadaran',
                'asmed_gcs',
                'asmed_gcs_e',
                'asmed_gcs_v',
                'asmed_gcs_m',

                'tindakan_pengobatan',
                'saran_dokter'
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tempat_tanggal_kejadian' => Yii::t('fe', 'Tempat/Tanggal Kejadian: '),
            'askep_kesadaran' => Yii::t('fe', 'Kesadaran: '),
            'askep_gcs' => Yii::t('fe', 'GCS: '),
            'askep_gcs_e' => Yii::t('fe', 'E'),
            'askep_gcs_v' => Yii::t('fe', 'V'),
            'askep_gcs_m' => Yii::t('fe', 'M'),
            'berat_badan' => Yii::t('fe', 'Berat Badan'),
            'tinggi_badan' => Yii::t('fe', 'Tinggi Badan'),
            'tekanan_darah' => Yii::t('fe', '1. Tekanan Darah'),
            'frekuensi_nadi' => Yii::t('fe', '2. Frekuensi Nadi'),
            'frekuensi_nafas' => Yii::t('fe', '3. Frekuensi Nafas'),
            'suhu_tubuh' => Yii::t('fe', '4. Suhu Badan'),

            'asmed_kesadaran' => Yii::t('fe', 'd. Kesadaran: '),
            'asmed_gcs' => Yii::t('fe', 'GCS: '),
            'asmed_gcs_e' => Yii::t('fe', 'E'),
            'asmed_gcs_v' => Yii::t('fe', 'V'),
            'asmed_gcs_m' => Yii::t('fe', 'M'),
            'sirkulasi_crt' => Yii::t('fe', 'CRT'),
        ];
    }
}
