<?php

namespace app\modules\v1\payload;

use Yii;


class TipePasien extends \yii\base\Model
{
    public $carabayar_id;
    public $penjamin_id;
    public $asalrujukan_id;
    public $no_rekam_medik;
    public $groupcarabayar_id;
    public $tipe_pasien;
    public $antrian_id;
    public $ruangan_id;
    public $is_aps;
    public $pendaftaranol_id;
    public $pendaftaran_id;
    public $dokter_perujuk;
    public $is_multi_payer;
    public $rujukandari_id;

    public function rules()
    {
        return [
            [[
                'carabayar_id',
                'penjamin_id',
                'asalrujukan_id',
                'ruangan_id',
                'no_rekam_medik',
                'groupcarabayar_id',
                'tipe_pasien',
                'antrian_id',
                'is_aps',
                'pendaftaranol_id',
                'pendaftaran_id',
                'dokter_perujuk',
                'is_multi_payer',
                'rujukandari_id',
            ],'safe'],
            [[
                'carabayar_id',
                'penjamin_id',
                'asalrujukan_id',
            ], 'required', "on" => 'default'],
            [
                [
                    'carabayar_id',
                    'penjamin_id',
                ],
                'required', "on" => 'pendaftaran-pasien-rs'
            ],
            [['no_rekam_medik'], 'noRekamMedikRules'],
        ];
    }

    /**
     * No rekam medik rules function
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function noRekamMedikRules()
    {
        if (isset($this->is_ranap) && $this->is_ranap && empty($this->no_rekam_medik)) {
            $this->addError('no_rekam_medik', 'No Rekam Medik tidak boleh kosong');
        }
    }

    public function attributeLabels()
    {
        return [
            'carabayar_id' => 'Cara bayar',
            'penjamin_id' => 'Penjamin',
            'asalrujukan_id' => 'Asal Rujukan',
            'no_rekam_medik' => 'No Rekam Medik',
        ];
    }

}
