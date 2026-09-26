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

class PemeriksaanThtForm extends \yii\base\Model
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

    public $bagKepalaLeher;
    public $catatanKepalaLeher;
    public $bagTelinga;
    public $telingaKanan;
    public $telingaKiri;
    public $bagHidung;
    public $catatanHidung;
    public $bagMulut;
    public $catatanMulut;
    public $bagPharynx;
    public $catatanPharynx;
    public $bagEpipharynx;
    public $catatanEpipharynx;
    public $bagLarynx;
    public $catatanLarynx;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [[
                'bagKepalaLeher', 'catatanKepalaLeher', 'bagTelinga','telingaKanan','telingaKiri','bagHidung', 
                'catatanHidung','bagMulut','catatanMulut','bagPharynx','catatanPharynx', 'bagEpipharynx', 'catatanEpipharynx', 
                'bagLarynx','catatanLarynx', 'pegawaiperawat_id', 'ruangan_id', 'konsulpoli_id', 'pemeriksaan_spesialis', 'tglperiksafisik'], 
            'safe'],
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
        ];
    }
}
