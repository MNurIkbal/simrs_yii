<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class PraBedahForm extends Model
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

    public $data_subjektif;
    public $data_objektif;
    public $diagnosa_pra_bedah;
    public $rencana_tindakan;
    public $deskripsi_lokasi;
    public $tanggal_asesment;

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
                    'data_subjektif',
                    'data_objektif',
                    'diagnosa_pra_bedah',
                    'rencana_tindakan',
                    'deskripsi_lokasi',
                    'tanggal_asesment',
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
