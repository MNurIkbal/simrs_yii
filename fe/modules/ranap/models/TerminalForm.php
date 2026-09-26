<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-07 17:29:27
 */

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class TerminalForm extends Model
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
    public $riwayat_sakit_lama;
    public $riwayat_sakit_lama_input;
    public $pengkajian_psikologis;
    public $pengkajian_psikologis_input;
    public $respon_penerimaan_pasien;
    public $respon_penerimaan_pasien_input;
    public $respon_penerimaan_keluarga;
    public $respon_penerimaan_keluarga_input;

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

    public $pemeriksaan_fisik;
    public $pemeriksaan_fisik_input;
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
                'keluhan_utama',
                'riwayat_penyakit_sekarang',
                'pemeriksaan_spesialis',
                'formasesmen_code',
                'is_dokumen_eklaim',
                'asesmenmedis_id',

                'keluhan_utama',
                'riwayat_sakit_lama',
                'riwayat_sakit_lama_input',
                'pengkajian_psikologis',
                'pengkajian_psikologis_input',
                'respon_penerimaan_pasien',
                'respon_penerimaan_pasien_input',
                'respon_penerimaan_keluarga',
                'respon_penerimaan_keluarga_input',

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

                'pemeriksaan_fisik',
                'pemeriksaan_fisik_input',
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
                'data_radiologi',
            ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'respon_penerimaan_pasien' => Yii::t('fe', '1. Pasien '),
            'respon_penerimaan_keluarga' => Yii::t('fe', '2. Keluarga '),
            
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
            'pemenuhan_adl_makan' => Yii::t('fe', 'a. Makan/Minum'),
            'pemenuhan_adl_mandi' => Yii::t('fe', 'b. Mandi'),
            'pemenuhan_adl_buangair' => Yii::t('fe', 'c. BAB/BAK'),
            'pemenuhan_adl_berpakaian' => Yii::t('fe', 'd. Berpakaian/Berhias'),
            'pemenuhan_adl_istirahat' => Yii::t('fe', 'e. Istirahat dan Tidur'),
            'pemenuhan_adl_obat' => Yii::t('fe', 'f. Pengunaan Obat'),
        ];
    }
}
