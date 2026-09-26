<?php

namespace app\modules\ranap\models;

use Yii;
use yii\base\Model;

class MataForm extends Model
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

    public $palpebra_dexter;
    public $palpebra_sinister;
    public $silia_dexter;
    public $silia_sinister;
    public $laksimal_dexter;
    public $laksimal_sinister;
    public $konjungtiva_dexter;
    public $konjungtiva_sinister;
    public $kornea_dexter;
    public $kornea_sinister;
    public $bilik_dexter;
    public $bilik_sinister;
    public $iris_dexter;
    public $iris_sinister;
    public $pupil_dexter;
    public $pupil_sinister;
    public $lensa_dexter;
    public $lensa_sinister;

    public $visus;
    public $tonometri;
    public $funduskopi;
    public $diagnosa;
    public $ringkasan;
    public $diagnosa_diferensial;
    public $kesan;

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
                    'palpebra_dexter',
                    'palpebra_sinister',
                    'silia_dexter',
                    'silia_sinister',
                    'laksimal_dexter',
                    'laksimal_sinister',
                    'konjungtiva_dexter',
                    'konjungtiva_sinister',
                    'kornea_dexter',
                    'kornea_sinister',
                    'bilik_dexter',
                    'bilik_sinister',
                    'iris_dexter',
                    'iris_sinister',
                    'pupil_dexter',
                    'pupil_sinister',
                    'lensa_dexter',
                    'lensa_sinister',
                    'visus',
                    'tonometri',
                    'funduskopi',
                    'diagnosa',
                    'ringkasan',
                    'diagnosa_diferensial',
                    'kesan',
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
