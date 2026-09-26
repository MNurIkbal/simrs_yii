<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class NyeriKronikForm extends Model
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
    public $riwayat_keluhan;
    public $pengkajian_nyeri_provokatif;
    public $pengkajian_nyeri_kuantitas;
    public $pengkajian_nyeri_kuantitas_lainnya;
    public $pengkajian_nyeri_region;
    public $skala_dewasa_intensitas_nyeri;
    public $skala_dewasa_tipe_nyeri;
    public $skala_anak;
    public $skala_faces;
    
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
                    'riwayat_keluhan',
                    'pengkajian_nyeri_provokatif',
                    'pengkajian_nyeri_kuantitas',
                    'pengkajian_nyeri_kuantitas_lainnya',
                    'pengkajian_nyeri_region',
                    'skala_dewasa_intensitas_nyeri',
                    'skala_dewasa_tipe_nyeri',
                    'skala_anak',
                    'skala_faces',
                    'pengkajian_sistem',
                    'diagnosa_medis',
                    'rencana_terapi_dan_tindakan',
                    'saran_nasehat_dokter',
                    'laboratorium',
                    'radiologi',
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
            'saran_nasehat_dokter' => 'Saran/Nasehat Dokter',
        ];
    }
}
