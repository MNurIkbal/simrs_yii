<?php

namespace app\modules\v1\payload;

use Yii;

class GroupTarifEklaimPayload extends \yii\base\Model
{
    public $prosedur_non_bedah;
    public $prosedur_bedah;
    public $konsultasi;
    public $tenaga_ahli;
    public $keperawatan;
    public $penunjang;
    public $radiologi;
    public $laboratorium;
    public $pelayanan_darah;
    public $rehabilitasi;
    public $kamar_akomodasi;
    public $rawat_intensif;
    public $obat;
    public $obat_kronis;
    public $obat_kemoterapi;
    public $alkes;
    public $bmhp;
    public $sewa_alat;

    public function rules()
    {
        return [
            [[
                'prosedur_non_bedah',
                'prosedur_bedah',
                'konsultasi',
                'tenaga_ahli',
                'keperawatan',
                'penunjang',
                'radiologi',
                'laboratorium',
                'pelayanan_darah',
                'rehabilitasi',
                'kamar_akomodasi',
                'rawat_intensif',
                'obat',
                'obat_kronis',
                'obat_kemoterapi',
                'alkes',
                'bmhp',
                'sewa_alat'
            ],'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'prosedur_non_bedah' => 'Prosedur Non Bedah',
            'prosedur_bedah' => 'Prosedur Bedah',
            'konsultasi' => 'Konsultasi',
            'tenaga_ahli'=> 'Tenaga Ahli',
            'keperawatan' => 'Keperawatan',
            'penunjang'=> 'Penunjang',
            'radiologi'=> 'Radiologi',
            'laboratorium'=> 'Laboratorium',
            'pelayanan_darah'=> 'Pelayanan Darah',
            'rehabilitasi'=> 'Rehabilitasi',
            'kamar_akomodasi'=> 'Kamar Akomodasi',
            'rawat_intensif'=> 'Rawat Intensif',
            'obat'=> 'Obat',
            'obat_kronis' => 'Obat Kronis',
            'obat_kemoterapi' => 'Obat Kemoterapi',
            'alkes' => 'Alkes',
            'bmhp' => 'Bmhp',
            'sewa_alat' => 'Sewa Alat',
        ];
    }


}
