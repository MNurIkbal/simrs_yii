<?php

/**
 * @Author: afil
 * @Date:   2018-01-18 09:59:09
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-26 11:57:12
 * @Description: 
 */

namespace app\modules\rajal\models;

use Yii;

class PemeriksaanGigiForm extends \yii\base\Model
{
    public $ruangan_id;
    public $konsulpoli_id;
    public $pemeriksaan_spesialis;
    public $pemeriksaanfisik_id;
    public $pendaftaran_id;
    public $pegawaiperawat_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $tglperiksafisik;

    public $kategoriPasien;
    public $odonto11;
    public $odonto21;
    public $odonto12;
    public $odonto22;
    public $odonto13;
    public $odonto23;
    public $odonto14;
    public $odonto24;
    public $odonto15;
    public $odonto25;
    public $odonto16;
    public $odonto26;
    public $odonto17;
    public $odonto27;
    public $odonto18;
    public $odonto28;

    public $odonto48;
    public $odonto38;
    public $odonto47;
    public $odonto37;
    public $odonto46;
    public $odonto36;
    public $odonto45;
    public $odonto35;
    public $odonto44;
    public $odonto34;
    public $odonto43;
    public $odonto33;
    public $odonto42;
    public $odonto32;
    public $odonto41;
    public $odonto31;
    public $occulasi;
    public $torusPlatinus;
    public $torusMandibularis;
    public $palatum;
    public $diastema;
    public $keteranganDiastema;
    public $gigiAnomali;
    public $lainlain;
    public $d;
    public $m;
    public $f;
    public $jumlahPhoto;
    public $jenisPhoto;
    public $jumlahPhotoRontgen;
    public $jenisPhotoRontgen;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [
                [
                    'kategoriPasien',
                    'pegawaiperawat_id',
                    'ruangan_id',
                    'konsulpoli_id',
                    'pemeriksaan_spesialis',
                    'tglperiksafisik',
                    'odonto11',
                    'odonto21',
                    'odonto12',
                    'odonto22',
                    'odonto13',
                    'odonto23',
                    'odonto14',
                    'odonto24',
                    'odonto15',
                    'odonto25',
                    'odonto16',
                    'odonto26',
                    'odonto17',
                    'odonto27',
                    'odonto18',
                    'odonto28',
                    'odonto48',
                    'odonto38',
                    'odonto47',
                    'odonto37',
                    'odonto46',
                    'odonto36',
                    'odonto45',
                    'odonto35',
                    'odonto44',
                    'odonto34',
                    'odonto43',
                    'odonto33',
                    'odonto42',
                    'odonto32',
                    'odonto41',
                    'odonto31',
                    'occulasi',
                    'torusPlatinus',
                    'torusMandibularis',
                    'palatum',
                    'diastema',
                    'keteranganDiastema',
                    'gigiAnomali',
                    'lainlain',
                    'd',
                    'm',
                    'f',
                    'jumlahPhoto',
                    'jenisPhoto',
                    'jumlahPhotoRontgen',
                    'jenisPhotoRontgen',
                ],
                'safe'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemeriksaanfisik_id' => 'Pemeriksaanfisik ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'kategoriPasien' => 'Kategori',
            'jumlahPhoto' => 'Jumlah foto yang diambil',
            'jumlahPhotoRontgen' => 'Jumlah foto rontgen yang diambil',
            'lainlain' => 'Lain-lain',
        ];
    }
}
