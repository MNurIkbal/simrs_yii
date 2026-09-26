<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-07 17:29:27
 */

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class ImunologiForm extends Model
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
    public $keluhan_utama;
    public $riwayat_penyakit_sekarang;
    public $riwayat_penyakit_sekarang_input;
    public $riwayat_penyakit_dahulu;
    public $riwayat_keturunan_kanker;
    public $riwayat_keturunan_kanker_input;
    public $riwayat_keturunan_imun;
    public $riwayat_keturunan_imun_input;
    public $riwayat_keturunan_alergi;
    public $riwayat_keturunan_alergi_input;
    public $pengkajian_psikologis;
    public $respon_penerimaan;
    public $keadaan_umum;
    public $kesadaran;
    public $gcs;
    public $gcs_e;
    public $gcs_v;
    public $gcs_m;
    public $berat_badan;
    public $tinggi_badan;
    public $tekanan_darah;
    public $frekuensi_nadi;
    public $frekuensi_nafas;
    public $suhu_tubuh;
    public $pemeriksaan_fisik;
    public $pemenuhan_adl_makan;
    public $pemenuhan_adl_mandi;
    public $pemenuhan_adl_buangair;
    public $pemenuhan_adl_berpakaian;
    public $pemenuhan_adl_istirahat;
    public $pemenuhan_adl_obat;
    public $diagnosa_keperawatan;
    public $rencana_keperawatan;

    // Asmed
    public $pengkajian_sistem;
    public $diagnosa_medis;
    public $rencana_terapi;
    public $saran_dokter;
    public $data_lab;
    public $data_radiologi;

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
                'pemeriksaan_spesialis',
                'formasesmen_code',
                'is_dokumen_eklaim',
                'asesmenmedis_id',
                // Askep
                'keluhan_utama',
                'riwayat_penyakit_sekarang',
                'riwayat_penyakit_sekarang_input',
                'riwayat_penyakit_dahulu',
                'riwayat_keturunan_kanker',
                'riwayat_keturunan_imun',
                'riwayat_keturunan_imun_input',
                'riwayat_keturunan_kanker_input',
                'riwayat_keturunan_alergi',
                'riwayat_keturunan_alergi_input',
                'pengkajian_psikologis',
                'respon_penerimaan',
                'keadaan_umum',
                'kesadaran',
                'gcs',
                'gcs_e',
                'gcs_v',
                'gcs_m',
                'berat_badan',
                'tinggi_badan',
                'tekanan_darah',
                'frekuensi_nadi',
                'frekuensi_nafas',
                'suhu_tubuh',
                'pemeriksaan_fisik',
                'pemenuhan_adl_makan',
                'pemenuhan_adl_mandi',
                'pemenuhan_adl_buangair',
                'pemenuhan_adl_berpakaian',
                'pemenuhan_adl_istirahat',
                'pemenuhan_adl_obat',
                'diagnosa_keperawatan',
                'rencana_keperawatan',

                // Asmed
                'pengkajian_sistem',
                'diagnosa_medis',
                'rencana_terapi',
                'saran_dokter',
                'data_lab',
                'data_radiologi'
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'gcs' => Yii::t('fe', 'GCS: '),
            'gcs_e' => Yii::t('fe', 'E'),
            'gcs_v' => Yii::t('fe', 'V'),
            'gcs_m' => Yii::t('fe', 'M'),
            'berat_badan' => Yii::t('fe', 'Berat Badan'),
            'tinggi_badan' => Yii::t('fe', 'Tinggi Badan'),
            'tekanan_darah' => Yii::t('fe', '1. Tekanan Darah'),
            'frekuensi_nadi' => Yii::t('fe', '2. Frekuensi Nadi'),
            'frekuensi_nafas' => Yii::t('fe', '3. Frekuensi Nafas'),
            'suhu_tubuh' => Yii::t('fe', '4. Suhu Badan'),
            'pemenuhan_adl_makan' => Yii::t('fe', 'a. Makan/Minum'),
            'pemenuhan_adl_mandi' => Yii::t('fe', 'b. Mandi'),
            'pemenuhan_adl_buangair' => Yii::t('fe', 'c. BAB/BAK'),
            'pemenuhan_adl_berpakaian' => Yii::t('fe', 'd. Berpakaian/Berhias'),
            'pemenuhan_adl_istirahat' => Yii::t('fe', 'e. Istirahat dan Tidur'),
            'pemenuhan_adl_obat' => Yii::t('fe', 'f. Pengunaan Obat'),
            'data_lab' => Yii::t('fe', 'a. Laboratorium'),
            'data_radiologi' => Yii::t('fe', 'b. Radiologi'),
        ];
    }
}
