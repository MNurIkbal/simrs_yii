<?php

namespace app\modules\rajal\models;

class AsesmenMedisForm extends \yii\base\Model
{
    public $asesmenmedis_id;
    public $anamnesa;
    public $keluhan_utama;
    public $riwayat_penyakit_sekarang;
    public $riwayat_penyakit_dahulu;
    public $riwayat_penyakit_keluarga;
    public $riyawat_penyakit_lainnya;
    public $kesadaran;
    public $tekanandarah;
    public $detaknadi;
    public $suhutubuh;
    public $tinggibadan_cm;
    public $pernapasan;
    public $beratbadan_kg;
    public $imt;
    public $status_lokalis;
    public $pemeriksaan_penunjang;
    public $diagnosa_kerja;
    public $masalah_diagnosa_medis;
    public $rencana_laksana_medis;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'asesmenmedis_id',
                    'anamnesa',
                    'keluhan_utama',
                    'riwayat_penyakit_sekarang',
                    'riwayat_penyakit_dahulu',
                    'riwayat_penyakit_keluarga',
                    'riyawat_penyakit_lainnya',
                    'kesadaran',
                    'tekanandarah',
                    'detaknadi',
                    'suhutubuh',
                    'tinggibadan_cm',
                    'pernapasan',
                    'beratbadan_kg',
                    'imt',
                    'status_lokalis',
                    'pemeriksaan_penunjang',
                    'diagnosa_kerja',
                    'masalah_diagnosa_medis',
                    'rencana_laksana_medis',
                ],
                'safe'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'asesmenmedis_id' => 'Asesmenmedis ID',
            'anamnesa' => 'Anamnesa',
            'keluhan_utama' => 'Keluhan Utama',
            'riwayat_penyakit_sekarang' => 'Riwayat Penyakit Sekarang',
            'riwayat_penyakit_dahulu' => 'Riwayat Penyakit Dahulu',
            'riwayat_penyakit_keluarga' => '',
            'riyawat_penyakit_lainnya' => '',
            'kesadaran' => '',
            'tekanandarah' => 'Tekanan Darah',
            'detaknadi' => 'Nadi',
            'suhutubuh' => 'Suhu Tubuh',
            'tinggibadan_cm' => 'TB',
            'pernapasan' => 'RR',
            'beratbadan_kg' => 'BB',
            'imt' => 'IMT',
            'status_lokalis' => 'Status Lokalis',
            'pemeriksaan_penunjang' => 'Pemeriksaan Penunjang',
            'diagnosa_kerja' => 'Diagnosa Kerja',
            'masalah_diagnosa_medis' => '',
            'rencana_laksana_medis' => '',
        ];
    }
}
